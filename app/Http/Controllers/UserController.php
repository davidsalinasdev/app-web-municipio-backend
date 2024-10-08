<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Usuario\UsuarioStoreRequest;
use App\Http\Requests\Usuario\UsuarioUpdateRequest;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usuarios = User::with('persona')->orderBy('id', 'DESC')->get();

        $data = array(
            'code' => 200,
            'status' => 'success',
            'usuarios' => $usuarios
        );
        return response()->json($data, $data['code']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Usuario\UsuarioStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UsuarioStoreRequest $request)
    {
        // 1.-Recoge datos por post
        $params = (object) $request->all(); // Devuelve un objeto con los datos validados


        // Iniciar una transacción
        DB::beginTransaction();

        try {
            // Crear el usuario y guardarlo en la base de datos
            $usuario = User::create([
                'email' => $params->email,
                'password' => Hash::make($params->password),
                'persona_id' => $params->persona_id
            ]);

            // Commit de la transacción
            DB::commit();

            // Retornar una respuesta exitosa
            $data = array(
                'status' => 'success',
                'code' => 201,
                'message' => 'Usuario registrado correctamente',
                'usuario' => $usuario
            );
        } catch (Exception $e) {
            // Rollback de la transacción en caso de error
            DB::rollBack();

            // Manejo de excepciones
            $data = array(
                'status' => 'error',
                'code' => 500,
                'message' => 'Ocurrió un error al registrar al usuario',
                'error' => $e->getMessage()
            );
        }
        return response()->json($data, $data['code']);
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
            $usuario = User::findOrFail($id);

            $data = array(
                'code' => 200,
                'status' => 'success',
                'usuario' => $usuario
            );
        } catch (ModelNotFoundException $e) {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            );
        } catch (Exception $e) {
            $data = array(
                'code' => 500,
                'status' => 'error',
                'message' => 'Ocurrió un error al intentar recuperar el usuario',
                'error' => $e->getMessage()
            );
        }
        return response()->json($data, $data['code']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Usuario\UsuarioUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Recoge datos por PUT o PATCH
        $params = (object) $request->all(); // Devuelve un objeto con los datos validados

        // 2.-Validar datos
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:usuarios,email,',
            'password' => 'nullable|min:6|max:255',
            'persona_id' => 'required|exists:personas,id',
            'estado' => 'required',
        ]);

        // Iniciar una transacción
        DB::beginTransaction();

        try {
            // Buscar el usuario en la base de datos
            $usuario = User::findOrFail($id);

            // Actualizar los datos del usuario
            $usuario->email = $params->email;
            if (!empty($params->password)) {
                $usuario->password = bcrypt($params->password);
            }
            $usuario->persona_id = $params->persona_id;
            $usuario->estado = $params->estado;
            $usuario->save();

            // Commit de la transacción
            DB::commit();

            // Retornar una respuesta exitosa
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Usuario actualizado correctamente',
                'usuario' => $usuario
            );
        } catch (ModelNotFoundException $e) {
            // Rollback de la transacción en caso de que no se encuentre el usuario
            DB::rollBack();

            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Usuario no encontrado'
            );
        } catch (Exception $e) {
            // Rollback de la transacción en caso de cualquier otro error
            DB::rollBack();

            $data = array(
                'status' => 'error',
                'code' => 500,
                'message' => 'Ocurrió un error al actualizar el usuario',
                'error' => $e->getMessage()
            );
        }
        return response()->json($data, $data['code']);
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
            // Buscar el usuario en la base de datos
            $usuario = User::findOrFail($id);

            // Actualizar el estado del usuario a "inactivo"
            $usuario->estado = 0;
            $usuario->save();

            // Commit de la transacción
            DB::commit();

            // Retornar una respuesta exitosa
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Usuario desactivado correctamente',
                'usuario' => $usuario
            );
        } catch (ModelNotFoundException $e) {
            // Rollback de la transacción en caso de que no se encuentre el usuario
            DB::rollBack();

            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Usuario no encontrado'
            );
        } catch (Exception $e) {
            // Rollback de la transacción en caso de cualquier otro error
            DB::rollBack();

            $data = array(
                'status' => 'error',
                'code' => 500,
                'message' => 'Ocurrió un error al desactivar el usuario',
                'error' => $e->getMessage()
            );
        }
        return response()->json($data, $data['code']);
    }
}
