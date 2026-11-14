<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
class ClienteController extends Controller
{
    public function listarClientes()
    {
    $clientes = Cliente::all();
   return response()->json($clientes);
    
          
    }
}
