<?php

namespace App\Http\Controllers\mercados;

use App\Http\Controllers\Controller;
use App\Models\mercados\Factura;
use App\Models\mercados\Puesto;
use App\Models\mercados\Titular;
use Illuminate\Http\Request;

use Dompdf\Dompdf;

class RecibosController extends Controller
{

    function recibosCarnet(Request $request)
    {
        $params = (object) $request->all(); // Convierte los parámetros a un objeto

        // Inicializamos la variable de facturas para usarla más adelante
        $facturas = collect();

        try {

            // Validación de tipo 'carnet'
            if (isset($params->tipo) && $params->tipo == 'carnet') {
                if (isset($params->carnet)) {
                    // Busca el titular por el carnet
                    $titular = Titular::where('carnet', $params->carnet)->firstOrFail();
                    // Busca el puesto activo asociado al titular
                    $puesto = Puesto::where('titular_id', $titular->id)->where('estado', 1)->firstOrFail();
                    // Busca todas las facturas sin pagar asociadas a ese puesto
                    $facturas = Factura::with(['puesto.titular', 'puesto.sector', 'detalleFactura'])
                        ->where('puesto_id', $puesto->id)
                        ->where('estado_pago', 0)
                        ->get();
                } else {
                    return response()->json(['code' => 400, 'status' => 'error', 'message' => 'Carnet no proporcionado'], 400);
                }
            }

            // Si no se encontró ninguna factura
            if ($facturas->isEmpty()) {
                return response()->json(['code' => 404, 'status' => 'error', 'message' => 'No se encontraron facturas sin pago'], 404);
            }

            // Respuesta exitosa
            $data = array(
                'code' => 200,
                'status' => 'success',
                'facturas' => $facturas,
                // 'precio_total' => $totalPrecio
            );
            return response()->json($data, $data['code']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Maneja excepciones cuando no se encuentra el registro
            return response()->json(['code' => 404, 'status' => 'error', 'message' => 'Registro no encontrado: ' . $e->getMessage()], 404);
        } catch (\Exception $e) {
            // Manejo general de excepciones
            return response()->json(['code' => 500, 'status' => 'error', 'message' => 'Ocurrió un error: ' . $e->getMessage()], 500);
        }
    }

    function recibosPuesto(Request $request)
    {
        $params = (object) $request->all(); // Convierte los parámetros a un objeto

        // Inicializamos la variable de facturas para usarla más adelante
        $facturas = collect();

        try {
            // Validación de tipo 'puesto'
            if (isset($params->tipo) && $params->tipo == 'puesto') {
                if (isset($params->puesto)) {
                    // Busca el puesto
                    $puesto = Puesto::where('nro_puesto', $params->puesto)->firstOrFail();
                    // Busca todas las facturas sin pagar asociadas a ese puesto
                    $facturas = Factura::with(['puesto.titular', 'puesto.sector', 'detalleFactura'])->where('puesto_id', $puesto->id)->where('estado_pago', 0)->get();
                } else {
                    return response()->json([
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'Número de puesto no proporcionado'
                    ], 400);
                }
            }


            // Si no se encontró ninguna factura
            if ($facturas->isEmpty()) {
                return response()->json(['code' => 404, 'status' => 'error', 'message' => 'No se encontraron facturas sin pago'], 404);
            }

            // Respuesta exitosa
            $data = array(
                'code' => 200,
                'status' => 'success',
                'facturas' => $facturas,
                // 'precio_total' => $totalPrecio
            );
            return response()->json($data, $data['code']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Maneja excepciones cuando no se encuentra el registro
            return response()->json(['code' => 404, 'status' => 'error', 'message' => 'Registro no encontrado: ' . $e->getMessage()], 404);
        } catch (\Exception $e) {
            // Manejo general de excepciones
            return response()->json(['code' => 500, 'status' => 'error', 'message' => 'Ocurrió un error: ' . $e->getMessage()], 500);
        }
    }

    public function generarRecibo(Request $request)
    {
        // Generar el PDF utilizando Dompdf
        try {
            $dompdf = new Dompdf();
            $reciboData = $request->all(); // Datos enviados desde Angular

            // Generar el HTML con los datos del recibo
            $html = view('recibo_template', compact('reciboData'))->render();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('letter', 'portrait');
            $dompdf->render();

            // Obtener el contenido del PDF
            $pdfContent = $dompdf->output();

            // Crear la respuesta
            $response = response()->stream(function () use ($pdfContent) {
                echo $pdfContent;
            }, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="recibo.pdf"',
            ]);

            // Establecer encabezados CORS
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With');

            return $response;
        } catch (\Exception $e) {
            // Manejo de excepciones en la generación del PDF
            return response()->json(['code' => 500, 'status' => 'error', 'message' => 'Error al generar el recibo: ' . $e->getMessage()], 500)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With');
        }
    }
}
