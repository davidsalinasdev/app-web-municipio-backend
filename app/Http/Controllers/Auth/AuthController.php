<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    function register(Request $request)
    {
        // 1.-Recoge datos por post
        $params = (object) $request->all(); // Devuelve un objeto

        // 2.-Validar datos
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'persona_id' => 'required|integer'
        ]);

        // Comprobar si los datos son válidos
        if ($validate->fails()) {
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Los datos enviados no son correctos',
                'errors' => $validate->errors()
            );
        } else {
            // Iniciar una transacción
            DB::beginTransaction();

            try {
                // Crear el usuario y guardarlo en la base de datos
                $user = User::create([
                    'email' => $params->email,
                    'password' => Hash::make($params->password),
                    'persona_id' => $params->persona_id
                ]);

                $token = JWTAuth::fromUser($user);

                // Commit de la transacción
                DB::commit();

                // Retornar una respuesta exitosa
                $data = array(
                    'status' => 'success',
                    'code' => 201,
                    'message' => 'El usuario se ha registrado correctamente',
                    'user' => $user,
                    'token' => $token
                );
            } catch (Exception $e) {
                // Rollback de la transacción en caso de error
                DB::rollBack();

                // Manejo de excepciones
                $data = array(
                    'status' => 'Error',
                    'code' => 500,
                    'message' => 'Ocurrió un error al registrar el usuario',
                    'error' => $e->getMessage()
                );
            }
        }

        return response()->json($data, $data['code']);
    }

    // Metodo de logueo
    function login(LoginRequest $request)
    {
        // 1.-Recoge todos los datos por POST
        $params = (object) $request->all(); // Devuelve un obejto

        // 2 .- Solo obtenemos los datos que necesitamos
        $credenciales = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credenciales)) {
                return response()->json([
                    'status' => 'Error',
                    'code' => 400,
                    'message' => 'Credenciales incorrectas',
                ], 400);
            }
        } catch (JWTException $e) {
            return
                response()->json([
                    'status' => 'Error',
                    'code' => 500,
                    'message' => 'Error al generar el token',
                    'error' => $e->getMessage()
                ], 500);
        }

        // Retornar una respuesta exitosa
        $data = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'Login exitoso',
            'user' => auth()->user(),
            'token' => $token
        );
        return response()->json($data, $data['code']);
    }

    function logout(Request $request)
    {
        try {

            // // Obtener el token del encabezado Authorization
            // $token = $request->header('Authorization');

            // $data = array(
            //     'status' => 'success',
            //     'code' => 200,
            //     'message' => 'Logout exitoso',
            //     'token' => $token
            // );
            // return response()->json($data, $data['code']);

            // Invalidar el token del usuario autenticado
            JWTAuth::invalidate(JWTAuth::getToken());

            // Retornar una respuesta exitosa
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Logout exitoso'
            );
            return response()->json($data, $data['code']);
        } catch (JWTException $e) {
            // Manejo de excepciones si ocurre un error al invalidar el token
            return response()->json([
                'status' => 'Error',
                'code' => 500,
                'message' => 'Error al realizar logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
