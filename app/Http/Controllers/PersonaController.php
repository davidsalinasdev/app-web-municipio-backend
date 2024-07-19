<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $persona = Persona::orderBy('id', 'DESC')->paginate(5);

        $data = array(
            'code' => 200,
            'status' => 'success',
            'persona' => $persona
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

        // Iniciar una transacción
        DB::beginTransaction();

        try {
            // Crear el usuario y guardarlo en la base de datos
            $persona = Persona::create([
                'nombres' => $params->nombres,
                'apellidos' => $params->apellidos,
                'carnet' => $params->carnet
            ]);

            // Commit de la transacción
            DB::commit();

            // Retornar una respuesta exitosa
            $data = array(
                'status' => 'success',
                'code' => 201,
                'message' => 'Persona registrado correctamente',
                'persona' => $persona
            );
        } catch (Exception $e) {
            // Rollback de la transacción en caso de error
            DB::rollBack();

            // Manejo de excepciones
            $data = array(
                'status' => 'Error',
                'code' => 500,
                'message' => 'Ocurrió un error al registrar a la persona',
                'error' => $e->getMessage()
            );
        }
        return response()->json($data, $data['code']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Persona  $persona
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $persona = Persona::findOrFail($id);

            $data = array(
                'code' => 200,
                'status' => 'success',
                'persona' => $persona
            );
        } catch (ModelNotFoundException $e) {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'Persona no encontrada'
            );
        } catch (Exception $e) {
            $data = array(
                'code' => 500,
                'status' => 'error',
                'message' => 'Ocurrió un error al intentar recuperar la persona',
                'error' => $e->getMessage()
            );
        }
        return response()->json($data, $data['code']);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Persona  $persona
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Recoge datos por PUT o PATCH
        $params = (object) $request->all(); // Devuelve un objeto

        // Iniciar una transacción
        DB::beginTransaction();

        try {
            // Buscar la persona en la base de datos
            $persona = Persona::findOrFail($id);

            // Actualizar los datos de la persona
            $persona->nombres = $params->nombres;
            $persona->apellidos = $params->apellidos;
            $persona->carnet = $params->carnet;
            $persona->estado = $params->estado;
            $persona->save();

            // Commit de la transacción
            DB::commit();

            // Retornar una respuesta exitosa
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Persona actualizada correctamente',
                'persona' => $persona
            );
        } catch (ModelNotFoundException $e) {
            // Rollback de la transacción en caso de que no se encuentre la persona
            DB::rollBack();

            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Persona no encontrada'
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Persona  $persona
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Iniciar una transacción
        DB::beginTransaction();

        try {
            // Buscar la persona en la base de datos
            $persona = Persona::findOrFail($id);

            // Actualizar el estado de la persona a "inactivo"
            $persona->estado = 0;
            $persona->save();

            // Commit de la transacción
            DB::commit();

            // Retornar una respuesta exitosa
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Persona desactivada correctamente',
                'persona' => $persona
            );
        } catch (ModelNotFoundException $e) {
            // Rollback de la transacción en caso de que no se encuentre la persona
            DB::rollBack();

            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Persona no encontrada'
            );
        } catch (Exception $e) {
            // Rollback de la transacción en caso de cualquier otro error
            DB::rollBack();

            $data = array(
                'status' => 'error',
                'code' => 500,
                'message' => 'Ocurrió un error al desactivar la persona',
                'error' => $e->getMessage()
            );
        }
        return response()->json($data, $data['code']);
    }


    public function funcionarioEstado()
    {
        $personas = Persona::where('estado', 1)
            ->orderBy('id', 'DESC')->get();

        $data = array(
            'code' => 200,
            'status' => 'success',
            'personas' => $personas
        );
        return response()->json($data, $data['code']);
    }
}
