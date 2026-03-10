<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CajasController extends Controller
{
    public function index()
    {
        $pagos = Pago::with(['pedido', 'pedido.usuario', 'metodoPago'])
            ->orderBy('fecha_pago', 'desc')
            ->get();

        $pedidos = Pedido::with(['usuario', 'mesa', 'detalles', 'cliente'])
            ->orderBy('fecha_pedido', 'desc')
            ->get();

        return Inertia::render('Cajas/Index', [
            'pagos' => $pagos,
            'pedidos' => $pedidos,
        ]);
    }
}
