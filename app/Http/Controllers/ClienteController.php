<?php

namespace App\Http\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Requests\GuardarClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;

class ClienteController extends Controller
{
    public function listarClientes(): JsonResponse
    {
        return ApiResponse::ok(Cliente::all());
    }

    public function guardarCliente(GuardarClienteRequest $request): JsonResponse
    {
        return ApiResponse::created(Cliente::create($request->validated()));
    }
}
