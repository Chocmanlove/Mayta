<?php

use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;

Route::get('/clientes', [ClienteController::class, 'listarClientes']);
Route::post('/clientes', [ClienteController::class, 'guardarCliente']);
