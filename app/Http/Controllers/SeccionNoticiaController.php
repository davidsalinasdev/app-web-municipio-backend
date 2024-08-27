<?php

namespace App\Http\Controllers;

use App\Models\SeccionNoticia;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SeccionNoticiaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $seccion = SeccionNoticia::orderBy('id', 'DESC')->paginate(5);

        $data = array(
            'code' => 200,
            'status' => 'success',
            'secciones' => $seccion
        );
        return response()->json($data, $data['code']);
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

        $validate = Validator::make($request->all(), [

            'nombres' => 'required',
            'apellidos' => 'required',
            '' => 'required',

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
                $seccion = SeccionNoticia::create([
                    'seccion' => $params->seccion,
                    'descripcion' => $params->descripcion
                ]);

                // Commit de la transacción
                DB::commit();

                // Retornar una respuesta exitosa
                $data = array(
                    'status' => 'success',
                    'code' => 201,
                    'message' => 'Sección registrada correctamente',
                    'seccion' => $seccion
                );
            } catch (Exception $e) {
                // Rollback de la transacción en caso de error
                DB::rollBack();

                // Manejo de excepciones
                $data = array(
                    'status' => 'Error',
                    'code' => 500,
                    'message' => 'Error al registrar la sección',
                    'error' => $e->getMessage()
                );
            }
            return response()->json($data, $data['code']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SeccionNoticia  $seccionNoticia
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $seccion = SeccionNoticia::findOrFail($id);

            $data = array(
                'code' => 200,
                'status' => 'success',
                'seccion' => $seccion
            );
        } catch (ModelNotFoundException $e) {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'Seccion no encontrada'
            );
        } catch (Exception $e) {
            $data = array(
                'code' => 500,
                'status' => 'error',
                'message' => 'Ocurrió un error al intentar recuperar la seccion',
                'error' => $e->getMessage()
            );
        }
        return response()->json($data, $data['code']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SeccionNoticia  $seccionNoticia
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Recoge datos por PUT o PATCH
        $params = (object) $request->all(); // Devuelve un objeto

        $validate = Validator::make($request->all(), [

            'seccion' => 'required',
            'descripcion' => 'required',
            'estado' => 'required',

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
                $seccion = SeccionNoticia::findOrFail($id);

                // Actualizar los datos de la seccion
                $seccion->seccion = $params->seccion;
                $seccion->descripcion = $params->descripcion;
                $seccion->estado = $params->estado;
                $seccion->save();

                // Commit de la transacción
                DB::commit();

                // Retornar una respuesta exitosa
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'La seccion se actualizo correctamente',
                    'seccion' => $seccion
                );
            } catch (ModelNotFoundException $e) {
                // Rollback de la transacción en caso de que no se encuentre la persona
                DB::rollBack();

                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Ocurrio un error al intentar actualizar la seccion noticias',
                );
            } catch (Exception $e) {
                // Rollback de la transacción en caso de cualquier otro error
                DB::rollBack();

                $data = array(
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Ocurrió un error al actualizar la persona',
                    'error' => $e->getMessage()
                );
            }
            return response()->json($data, $data['code']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SeccionNoticia  $seccionNoticia
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Iniciar una transacción
        DB::beginTransaction();

        try {
            // Buscar la persona en la base de datos
            $seccion = SeccionNoticia::findOrFail($id);

            // Actualizar el estado de la seccion a "inactivo"
            $seccion->estado = 0;
            $seccion->save();

            // Commit de la transacción
            DB::commit();

            // Retornar una respuesta exitosa
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Sección de noticias desactivada correctamente',
                'seccion' => $seccion
            );
        } catch (ModelNotFoundException $e) {
            // Rollback de la transacción en caso de que no se encuentre la persona
            DB::rollBack();

            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Sección de noticias no encontrada'
            );
        } catch (Exception $e) {
            // Rollback de la transacción en caso de cualquier otro error
            DB::rollBack();

            $data = array(
                'status' => 'error',
                'code' => 500,
                'message' => 'Ocurrió un error al desactivar la sección de noticias',
                'error' => $e->getMessage()
            );
        }
        return response()->json($data, $data['code']);
    }
}
