<?php

namespace App\Http\Controllers\mercados;

use App\Http\Controllers\Controller;
use App\Models\mercados\Pago;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GenerarMultaReciboController extends Controller
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
        // 1.-Recoge datos por post
        $params = (object) $request->all(); // Devuelve un objeto

        // print_r($params);
        // die();

        $validate = Validator::make($request->all(), [

            'factura_id' => 'required',
            'usuario_id' => 'required',
            'monto_pago' => 'required',
            'fecha_pago' => 'required'

        ]);

        // 3.- SI LA VALIDACION FUE CORRECTA
        // Comprobar si los datos son validos
        if ($validate->fails()) { // en caso si los datos fallan la validacion

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

                $pago = new Pago();
                $pago->factura_id = $params->factura_id;
                $pago->usuario_id = $params->usuario_id;
                $pago->monto_pago = $params->monto_pago;
                $pago->fecha_pago = $params->fecha_pago;
                $pago->save();


                // Commit de la transacción
                DB::commit();

                // Retornar una respuesta exitosa
                $data = array(
                    'status' => 'success',
                    'code' => 201,
                    'message' => 'El pago se genero correctamente',
                );
            } catch (Exception $e) {
                // Rollback de la transacción en caso de error
                DB::rollBack();

                // Manejo de excepciones
                $data = array(
                    'status' => 'Error',
                    'code' => 500,
                    'message' => 'Error al registrar el puesto',
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
