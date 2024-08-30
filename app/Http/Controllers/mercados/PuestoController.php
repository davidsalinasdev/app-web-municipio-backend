<?php

namespace App\Http\Controllers\mercados;

use App\Http\Controllers\Controller;
use App\Models\mercados\Puesto;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PuestoController extends Controller
{
    // Busqueda con data tables
    // function indexPOST()
    // {

    //     return datatables()->eloquent(Puesto::query())->filter(function ($query) {
    //         if (request()->has('search') && request('search')) {
    //             $searchTerm = request('search');
    //             $query->where(function ($q) use ($searchTerm) {
    //                 $q->where('nro_puesto', 'like', '%' . $searchTerm . '%')
    //                     ->orWhere('nro_contrato', 'like', '%' . $searchTerm . '%')
    //                     ->orWhere('titular_id', 'like', '%' . $searchTerm . '%')
    //                     ->orWhere('fecha_ingreso', 'like', '%' . $searchTerm . '%');
    //             });
    //         }
    //     })->toJson();
    // }

    function indexPOST()
    {
        return datatables()->eloquent(
            Puesto::with('titular') // Aquí se incluye la relación
                ->select('puestos.*') // Selecciona las columnas del modelo `Puesto`
        )->filter(function ($query) {
            if (request()->has('search') && request('search')) {
                $searchTerm = request('search');
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nro_puesto', 'like', '%' . $searchTerm . '%')
                        ->orWhere('nro_contrato', 'like', '%' . $searchTerm . '%')
                        ->orWhereHas('titular', function ($q) use ($searchTerm) {
                            $q->where('nombres', 'like', '%' . $searchTerm . '%')
                                ->orWhere('apellidos', 'like', '%' . $searchTerm . '%')
                                ->orWhere('carnet', 'like', '%' . $searchTerm . '%');
                        })
                        ->orWhere('fecha_ingreso', 'like', '%' . $searchTerm . '%');
                });
            }
        })->toJson();
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

        // var_dump($params);
        // die();

        $validate = Validator::make($request->all(), [

            'nro_puesto' => 'required',

            'sector_id' => 'required',
            'titular_id' => 'required',
            'usuario_id' => 'required',

            'precio_mensual' => 'required',
            'fecha_ingreso' => 'required',
            'observaciones' => 'required'

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
                // Crear el usuario y guardarlo en la base de datos
                $puesto = new Puesto();
                $puesto->nro_puesto = $params->nro_puesto;
                $puesto->sector_id = $params->sector_id;
                $puesto->titular_id = $params->titular_id;
                $puesto->usuario_id = $params->usuario_id;
                $puesto->precio_mensual = $params->precio_mensual;
                $puesto->nro_contrato = $params->nro_contrato;
                $puesto->fecha_ingreso = $params->fecha_ingreso;
                $puesto->observaciones = $params->observaciones;
                $puesto->save();

                // Commit de la transacción
                DB::commit();

                // Retornar una respuesta exitosa
                $data = array(
                    'status' => 'success',
                    'code' => 201,
                    'message' => 'Puesto registrado correctamente',
                    'puesto' => $puesto
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
        try {
            $puesto = Puesto::findOrFail($id);

            $data = array(
                'code' => 200,
                'status' => 'success',
                'puesto' => $puesto
            );
        } catch (ModelNotFoundException $e) {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'Puesto no encontrado'
            );
        } catch (Exception $e) {
            $data = array(
                'code' => 500,
                'status' => 'error',
                'message' => 'Ocurrió un error al intentar recuperar el puesto',
                'error' => $e->getMessage()
            );
        }
        return response()->json($data, $data['code']);
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
        // Recoge datos por PUT o PATCH
        $params = (object) $request->all(); // Devuelve un objeto

        $validate = Validator::make($request->all(), [

            'nro_puesto' => 'required',

            'sector_id' => 'required',
            'titular_id' => 'required',
            'usuario_id' => 'required',

            'precio_mensual' => 'required',
            'fecha_ingreso' => 'required',
            'observaciones' => 'required',
            'estado' => 'required'
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
                // Buscar la persona en la base de datos
                $puesto = Puesto::findOrFail($id);

                $puesto->nro_puesto = $params->nro_puesto;
                $puesto->sector_id = $params->sector_id;
                $puesto->titular_id = $params->titular_id;
                $puesto->usuario_id = $params->usuario_id;
                $puesto->precio_mensual = $params->precio_mensual;
                $puesto->fecha_ingreso = $params->fecha_ingreso;
                $puesto->nro_contrato = $params->nro_contrato;
                $puesto->observaciones = $params->observaciones;
                $puesto->estado = $params->estado;
                $puesto->save();

                // Commit de la transacción
                DB::commit();

                // Retornar una respuesta exitosa
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Puesto actualizado correctamente',
                    'puesto' => $puesto
                );
            } catch (ModelNotFoundException $e) {
                // Rollback de la transacción en caso de que no se encuentre la persona
                DB::rollBack();

                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Ocurrio un error al intentar actualizar los datos del puesto',
                );
            } catch (Exception $e) {
                // Rollback de la transacción en caso de cualquier otro error
                DB::rollBack();

                $data = array(
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Ocurrió un error al actualizar el puesto',
                    'error' => $e->getMessage()
                );
            }
            return response()->json($data, $data['code']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Iniciar una transacción
        DB::beginTransaction();

        try {
            // Buscar la persona en la base de datos
            $puesto = Puesto::findOrFail($id);

            // Actualizar el estado de la seccion a "inactivo"
            $puesto->estado = 0;
            $puesto->save();

            // Commit de la transacción
            DB::commit();

            // Retornar una respuesta exitosa
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Se desactivó correctamente',
                'puesto' => $puesto
            );
        } catch (ModelNotFoundException $e) {
            // Rollback de la transacción en caso de que no se encuentre la persona
            DB::rollBack();

            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Puesto no encontrado',
            );
        } catch (Exception $e) {
            // Rollback de la transacción en caso de cualquier otro error
            DB::rollBack();
            $data = array(
                'status' => 'error',
                'code' => 500,
                'message' => 'Ocurrió un error al desactivar puesto del mercado',
                'error' => $e->getMessage()
            );
        }
        return response()->json($data, $data['code']);
    }
}
