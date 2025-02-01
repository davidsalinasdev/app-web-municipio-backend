<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\mercados\SectorController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\mercados\TitularController;
use App\Http\Controllers\mercados\PuestoController;
use App\Http\Controllers\mercados\RecibosController;
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

    /**** Rutas para manejo de SECCIÓN DE NOTICIAS ****/
    Route::resource('seccion-noticias', 'App\Http\Controllers\SeccionNoticiaController');

    // MERCADO
    /**** Rutas para manejo de TITULAR ****/
    Route::resource('mercado-titular', 'App\Http\Controllers\mercados\TitularController');
    Route::post('/index-post/mercado-titular', [TitularController::class, 'indexPOST']);

    /**** Rutas para manejo de SECTOR ****/
    Route::resource('mercado-sector', 'App\Http\Controllers\mercados\SectorController');
    Route::post('/index-post/mercado-sector', [SectorController::class, 'indexPOST']);

    /**** Rutas para manejo de PUESTO ****/
    Route::resource('mercado-puesto', 'App\Http\Controllers\mercados\PuestoController');
    Route::post('/index-post/mercado-puesto', [PuestoController::class, 'indexPOST']);

    /**** Ruta para generar multa ****/
    Route::resource('generar-multa', 'App\Http\Controllers\mercados\GenerarMultaReciboController');

    /**** Ruta para generar cobro por mes ****/
    Route::resource('generar-cobro', 'App\Http\Controllers\mercados\GenerarCobroController');

    /**** Ruta para generar Pagos cada ves ue se  haga un pago ****/
    Route::resource('generar-pagos', 'App\Http\Controllers\mercados\PagosController');

    /**** Ruta para todo reerente a recibos ****/
    Route::post('recibos-carnet', [RecibosController::class, 'recibosCarnet']);
    Route::post('recibos-puesto', [RecibosController::class, 'recibosPuesto']);
    Route::post('generar-recibo', [RecibosController::class, 'generarRecibo']);
});
