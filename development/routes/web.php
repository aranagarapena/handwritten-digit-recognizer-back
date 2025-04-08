<?php

use App\Http\Controllers\ClientesController;

use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ClientesController::class, 'index']);
//Route::resource('/', 'ClientesController');


// Rutas USUARIO

// insertar un nuevo usuarios en la BDs  
Route::post('/registro', [UsuarioController::class, 'store']);

// recuperar los usuarios de la BDs
Route::get('/getusers', [UsuarioController::class, 'getAllUsers']);

