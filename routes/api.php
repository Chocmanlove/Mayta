<?php

use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;

Route::get('/clientes', [ClienteController::class, 'listarClientes']);
Route::get('/clientes/{id}', [ClienteController::class, 'traerCliente'])->whereNumber('id');
Route::post('/clientes', [ClienteController::class, 'guardarCliente']);
Route::put('/clientes/{id}', [ClienteController::class, 'actualizarCliente'])->whereNumber('id');
