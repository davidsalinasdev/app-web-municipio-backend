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
                    $fechaActual = Carbon::now()->startOfMonth();

                    if ($mesCobro->lessThan($fechaActual)) {

                        // Crear una nueva factura
                        $factura = new Factura();
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

                        // Verificar facturas no pagadas en los últimos 2 meses para generar multas
                        $facturasSinPagar = DB::table('facturas')
                            ->leftJoin('pagos', 'facturas.id', '=', 'pagos.factura_id')
                            ->whereNull('pagos.id') // Sin pago
                            ->where('facturas.id', $factura->id)
                            ->where('facturas.created_at', '<=', Carbon::now()->subMonths(2))
                            ->get();

                        foreach ($facturasSinPagar as $facturaPendiente) {
                            // Verificar si ya existe una multa para esta factura
                            $multaExistente = Multa::where('factura_id', $facturaPendiente->id)->first();

                            if (!$multaExistente) {
                                // Crear la multa
                                $multa = new Multa();
                                $multa->factura_id = $facturaPendiente->id;
                                $multa->fecha_multa = Carbon::now();
                                $multa->monto_multa = 50; // Ajusta el monto según la lógica de tu negocio
                                $multa->save();
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
                    'status' => 'Error',
                    'code' => 500,
                    'message' => 'Error al registrar el cobro',
                    'error' => $e->getMessage()
                );
            }
            return response()->json($data, $data['code']);
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
