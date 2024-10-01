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

        // Obtener la fecha actual y extraer el mes y el año

        // $fechaActualMes = Carbon::now();
        // $mesActual = $fechaActualMes->month; // Obtiene el número del mes (1-12)
        // $añoActual = $fechaActualMes->year; // Obtiene el año

        // // Consulta para verificar si existe algún registro con el mismo mes y año
        // $existeFecha = Generar::whereMonth('fecha_generacion_cobro', $mesActual)
        //     ->whereYear('fecha_generacion_cobro', $añoActual)
        //     ->exists();



        // Obtener la fecha actual del servidor
        $fecha1 = Carbon::today(); // o Carbon::now() si también quieres comparar con hora

        // Hacer una consulta directa que compare mes y año en la base de datos
        $registros = Generar::whereYear('fecha_generacion_cobro', $fecha1->year)
            ->whereMonth('fecha_generacion_cobro', $fecha1->month)
            ->get();


        // echo count($registros);
        // echo "</br>";

        // // Mostrar los registros que coinciden
        // foreach ($registros as $registro) {
        //     echo "El registro con ID: " . $registro->id . " tiene el mismo mes y año.\n";
        // }
        // die();

        if (count($registros) >= 1) {   // Cuando es true no pasa
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
                DB::beginTransaction();

                try {
                    // Recorrer todos los puestos activos
                    $puestos = Puesto::where('estado', 1)->get();

                    foreach ($puestos as $element) {

                        $mesCobro = Carbon::parse($element->mes_inicio_cobro);

                        // Obtener la fecha actual, al inicio del mes
                        $fechaActual = Carbon::now()->startOfMonth()->toDateString(); // Formato 'YYYY-MM-DD'

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
                            $detalleFactura->puesto_id = $element->id;
                            $detalleFactura->periodo = $mesCobro->format('Y-m');
                            $detalleFactura->precio = $element->precio_mensual;
                            $detalleFactura->save();

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
                    DB::commit();

                    $data = array(
                        'status' => 'success',
                        'code' => 201,
                        'message' => 'Periodo de cobro generado correctamente',
                    );
                } catch (Exception $e) {
                    // Rollback de la transacción en caso de error
                    DB::rollBack();

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



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
