<?php

namespace App\Http\Controllers;

use App\Core\Responses\ApiResponse;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;

class ClienteController extends Controller
{
    public function listarClientes(): JsonResponse
    {
        return ApiResponse::ok(Cliente::all());
    }
}
