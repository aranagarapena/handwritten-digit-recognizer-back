<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ClientesController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\NumberDrawerController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Ruta para obtener todos los clientes
Route::get('/clientes/clientes', [ClientesController::class, 'index']);

// Rutas de USUARIO

// Insertar un nuevo usuario en la BD
Route::post('/usuarios/signin', [UsuarioController::class, 'store']);

// Recuperar todos los usuarios de la BD
Route::get('/usuarios/getusers', [UsuarioController::class, 'getAllUsers']);

// Rutas del JUEGO
Route::post('/game/storenumber', [NumberDrawerController::class, 'storeNumber']);

