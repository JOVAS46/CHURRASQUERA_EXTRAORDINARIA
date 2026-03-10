<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClienteDashboardController extends Controller
{
    public function index()
    {
        $usuarioId = Auth::id();

        $reservas = Reserva::where('id_usuario', $usuarioId)
            ->orderBy('fecha_reserva', 'desc')
            ->get();

        $pedidos = Pedido::where('id_usuario', $usuarioId)
            ->orderBy('fecha_pedido', 'desc')
            ->get();

        return Inertia::render('Cliente/Dashboard', [
            'reservas' => $reservas,
            'pedidos' => $pedidos,
        ]);
    }
}
