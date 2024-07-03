<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        try {
            // Validar el token de acceso verificca que el usuario esta autenticado
            JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {

            // Manejar las excepciones
            return $this->handleTokenException($e);
        }

        return $next($request);
    }

    // Metodo para manejar las excepciones
    protected function handleTokenException(Exception $e)
    {
        if ($e instanceof TokenInvalidException) {
            return response()->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'El token no es válido',
                'errors' => $e->getMessage()
            ], 401);
        }

        if ($e instanceof TokenExpiredException) {
            return response()->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'El token ya ha expirado',
                'errors' => $e->getMessage()
            ], 401);
        }

        return response()->json([
            'status' => 'error',
            'code' => 401,
            'message' => 'Token no encontrado',
            'errors' => $e->getMessage()
        ], 401);
    }
}
