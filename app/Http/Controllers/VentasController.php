<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Pago;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VentasController extends Controller
{
    public function index()
    {
        $ventas = Pedido::with(['usuario', 'mesa', 'cliente', 'detalles'])
            ->orderBy('fecha_pedido', 'desc')
            ->get();

        return Inertia::render('Ventas/Index', [
            'ventas' => $ventas,
        ]);
    }

    public function pendientes()
    {
        $pedidos = Pedido::with(['usuario', 'cliente', 'detalles'])
            ->orderBy('fecha_pedido', 'desc')
            ->get();

        $pagos = Pago::with(['pedido', 'pedido.usuario', 'metodoPago'])
            ->orderBy('fecha_pago', 'desc')
            ->get();

        return Inertia::render('Ventas/PendientesIndex', [
            'pedidos' => $pedidos,
            'pagos' => $pagos,
        ]);
    }
}
