<?php

use App\Http\Controllers\Auth\AuthController;
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


// MIDDLEWARE JWT PARA TODAS LAS RUTAS
Route::middleware('jwt.verify')->group(function () {

    /**** Rutas para manejo de AUTENTICACIÓN ****/
    Route::post('logout', [AuthController::class, 'logout']);

    /**** Rutas para manejo de USUARIOS ****/
    Route::get('users', [UserController::class, 'index']);
});
