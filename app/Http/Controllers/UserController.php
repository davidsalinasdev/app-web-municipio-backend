<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            // Obtener todos los usuarios
            $users = User::all();

            // Retornar una respuesta exitosa
            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Se obtuvieron todos los usuarios con éxito',
                'users' => $users
            ];

            // Retornar una respuesta de esta forma 1
            return response()->json($data, $data['code']);
        } catch (Exception $e) { // Manejar cualquier excepción que ocurra

            // Retornar una respuesta de esta forma 2
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Ocurrió un error al obtener la lista de usuarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
