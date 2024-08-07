<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


/**** Rutas para manejo de AUTENTICACIÓN SIN MIDDLEWARE ****/
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

/**** Rutas para manejo de USUARIOS CON MIDDLEWARE JWT PARA CADA RUTA QUE NECESITA VERIFICAR EL TOKEN *****/
// Route::resource('users', 'App\Http\Controllers\UserController')->middleware('jwt.verify');
// Route::get('users', [UserController::class, 'index'])->middleware('jwt.verify');


Route::get('logout', [AuthController::class, 'logout']);
// MIDDLEWARE JWT PARA TODAS LAS RUTAS
Route::middleware('jwt.verify')->group(function () {

    /**** Rutas para manejo de AUTENTICACIÓN ****/

    /**** Rutas para manejo de USUARIOS ****/
    // Route::get('users', [UserController::class, 'index']);

    /**** Rutas para manejo de PERSONAS ****/
    Route::resource('personas', 'App\Http\Controllers\PersonaController');
    Route::get('/estado/personas', [PersonaController::class, 'funcionarioEstado']);

    /**** Rutas para manejo de USUARIOS ****/
    Route::resource('usuario', 'App\Http\Controllers\UserController');
});
