<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $estadisticas = [
            'total_pedidos' => Pedido::count(),
            'total_mesas' => Mesa::count(),
            'total_productos' => Producto::count(),
            'total_usuarios' => User::count(),
            'mesas_ocupadas' => Mesa::where('estado', 'ocupada')->count(),
            'pedidos_pendientes' => Pedido::where('estado', 'pendiente')->count(),
        ];

        $pedidos_recientes = Pedido::with(['mesa', 'usuario'])
            ->orderBy('fecha_pedido', 'desc')
            ->limit(10)
            ->get();

        return Inertia::render('Dashboard', [
            'estadisticas' => $estadisticas,
            'pedidos_recientes' => $pedidos_recientes,
        ]);
    }
}

