<?php

namespace App\Http\Controllers;

use App\Core\Errors\DomainException;
use App\Core\Errors\ErrorCode;
use App\Core\Responses\ApiResponse;
use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;

class ClienteController extends Controller
{
    public function listarClientes(): JsonResponse
    {
        return ApiResponse::ok(Cliente::all());
    }

    public function traerCliente(int $id): JsonResponse
    {
        return ApiResponse::ok($this->buscarCliente($id));
    }

    public function guardarCliente(ClienteRequest $request): JsonResponse
    {
        return ApiResponse::created(Cliente::create($request->validated()));
    }

    public function actualizarCliente(ClienteRequest $request, int $id): JsonResponse
    {
        $cliente = $this->buscarCliente($id);
        $cliente->update($request->validated());

        return ApiResponse::ok($cliente);
    }

    private function buscarCliente(int $id): Cliente
    {
        return Cliente::find($id) ?? throw new DomainException(ErrorCode::NotFound);
    }
}
