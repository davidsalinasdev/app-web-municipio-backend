<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    function register(Request $request)
    {

        // 1.-Recoge datos por post
        $params = (object) $request->all(); // Devuelve un obejto

        // 2.-Validar datos
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed'
        ]);

        // Comprobar si los datos son validos
        if ($validate->fails()) { // en caso si los datos fallan la validacion

            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Los datos enviados no son correctos',
                'errors' => $validate->errors()
            );
        } else { //Retornar una respuesta exitosa

            try {
                // Crear el usuario y guardarlo en la base de datos
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    // 'password' => bcrypt($request->password)
                    'password' => Hash::make($request->password)
                ]);

                $token = JWTAuth::fromUser($user);

                // Retornar una respuesta exitosa
                $data = array(
                    'status' => 'success',
                    'code' => 201,
                    'message' => 'El usuario se ha registrado correctamente',
                    'user' => $user,
                    'token' => $token
                );
            } catch (Exception $e) {
                // Manejo de excepciones
                $data = array(
                    'status' => 'Error',
                    'code' => 500,
                    'message' => 'Ocurrió un error al registrar el usuario',
                    'error' => $e->getMessage()
                );
            }
            return response()->json($data, $data['code']);
        }
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
}
