<?php

namespace App\Http\Controllers\mercados;

use App\Http\Controllers\Controller;
use App\Models\mercados\Detalle_factura;
use App\Models\mercados\Factura;
use App\Models\mercados\Generar;
use App\Models\mercados\Multa;
use App\Models\mercados\Puesto;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GenerarCobroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        // Convertir fecha a objeto Carbon
        $fecha = Carbon::parse($request->fecha_actual);

        // Consulta a la base de datos
        $registros = Generar::whereYear('fecha_generacion_cobro', $fecha->year)
            ->whereMonth('fecha_generacion_cobro', $fecha->month)
            ->get();

        // return response()->json([
        //     'status' => 'success',
        //     'code' => 200,
        //     'message' => 'Llego Correctamente',
        //     'id' => $request->id,
        //     'fecha' => $fecha,
        //     'registros' => $registros,
        // ]);

        if (count($registros) > 0) {   // Cuando es true no pasa

            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Ya se generó el cobro para este mes.',
            );
            return response()->json($data, $data['code']);
        } else {
            // La fecha no existe
            // Recoge datos por post
            $params = (object) $request->all();

            $validate = Validator::make($request->all(), [
                'id' => 'required',
                'fecha_actual' => 'required'
            ]);

            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Los datos enviados no son correctos.',
                    'errors' => $validate->errors()
                );
                return response()->json($data, $data['code']);
            } else {


                // Iniciar una transacción
                DB::beginTransaction(); //1.- Iniciar una transacción

                try {
                    // Recorrer todos los puestos activos
                    $puestos = Puesto::where('estado', 1)->get();

                    foreach ($puestos as $element) {

                        $mesCobro = Carbon::parse($element->mes_inicio_cobro); // Example Mes a cobrar 2025-01-01'

                        // Obtener la fecha actual, al inicio del mes
                        $fechaActual = Carbon::now()->startOfMonth()->toDateString(); // Formato 'YYYY-MM-DD' **tests 0000-12-01** Siempre el 1 de cada mes actual


                        /**
                         * método lessThan() de un objeto, generalmente se utiliza para comparar dos fechas y determinar 
                         * si la primera es menor que la segunda.  (2025-01-01->lessThan 025-02-01)
                         */
                        if ($mesCobro->lessThan($fechaActual)) {



                            // Crear una nueva factura
                            $factura = new Factura();
                            $factura->puesto_id = $element->id;
                            $factura->nro_recibo = null;
                            $factura->fecha_emision = null;
                            $factura->usuario_id = null;
                            $factura->save();


                            // Crear el detalle de factura
                            $detalleFactura = new Detalle_factura();
                            $detalleFactura->factura_id = $factura->id;
                            // $detalleFactura->factura_id = 1;
                            $detalleFactura->puesto_id = $element->id;
                            $detalleFactura->periodo = $mesCobro->format('Y-m');
                            $detalleFactura->precio = $element->precio_mensual;
                            $detalleFactura->save();

                            // AQUI EL ERROR ESTÁ EN EL DETALLE DE FACTURA(SE TIENE QUE INICIALIZAR EN HEIDI num_detalle AUTOINCREMENT)
                            // return response()->json([
                            //     'status' => 'success',
                            //     'code' => 200,
                            //     'prueba' => 'Los datos son correctos',
                            //     'detalle' => $detalleFactura
                            // ], 200);



                            // Actualizar el mes de cobro al actual
                            $element->mes_inicio_cobro = Carbon::now()->startOfMonth()->format('Y-m-d');
                            $element->save();


                            // Verificar si el puesto tiene más de dos facturas sin pagar y sin analizar
                            $facturasSinPagar = DB::table('facturas as f')
                                ->join('detalle_facturas as df', 'f.id', '=', 'df.factura_id')  // Join con detalle_factura
                                ->where('f.puesto_id', $element->id)  // Reemplaza $puesto_id con el valor del puesto
                                ->where('f.estado_pago', 0)  // Facturas no pagadas
                                ->where('f.analizado_multa', 0)  // Facturas no analizadas
                                ->orderBy('f.created_at', 'asc')  // Ordenar por fecha de creación de manera ascendente
                                ->select('f.*', 'df.periodo')  // Seleccionar todos los campos de facturas y el campo periodo
                                ->get();  // Obtener los resultados


                            // Agrupar facturas en pares de dos
                            $facturasAgrupadas = $facturasSinPagar->chunk(2);


                            foreach ($facturasAgrupadas as $grupoFacturas) {
                                // Asegúrate de que el grupo tenga exactamente 2 facturas
                                if ($grupoFacturas->count() == 2) {
                                    // Crear un registro de multa
                                    $montoMulta = 50;  // Por ejemplo, puedes calcular este valor según tu lógica

                                    $periodosAfectados = $grupoFacturas->pluck('periodo')->implode(', ');  // Obtener los periodos afectados de las facturas

                                    // Insertar la multa en la base de datos
                                    DB::table('multas')->insert([
                                        'puesto_id' => $element->id,
                                        'monto_multa' => $montoMulta,
                                        'fecha_generacion' => Carbon::now(),
                                        'periodos_afectados' => $periodosAfectados,
                                        'estado_multa' => 'Pendiente',
                                    ]);

                                    // Opcional: Marcar estas facturas como "analizadas" o agregar algún flag
                                    DB::table('facturas')
                                        ->whereIn('id', $grupoFacturas->pluck('id'))
                                        ->update(['analizado_multa' => 1]);  // Usar un valor especial para indicar que ya se han procesado
                                }
                            }
                        }
                    }

                    // Registrar la generación del cobro
                    $generar = new Generar();
                    $generar->usuario_id = $params->id;
                    $generar->fecha_generacion_cobro = Carbon::now();
                    $generar->save();

                    // Confirmar la transacción
                    DB::commit(); // 2.- No se necesita confirmar la transacción

                    $data = array(
                        'status' => 'success',
                        'code' => 201,
                        'message' => 'Periodo de cobro generado correctamente',
                    );
                } catch (Exception $e) {
                    // Rollback de la transacción en caso de error
                    DB::rollBack(); // 3.- No se necesita confirmar la transacción

                    // Manejo de excepciones
                    $data = array(
                        'status' => 'error',
                        'code' => 500,
                        'message' => 'Error al registrar el cobro',
                        'error' => $e->getMessage()
                    );
                }
                return response()->json($data, $data['code']);
            }
        }
    }
}
