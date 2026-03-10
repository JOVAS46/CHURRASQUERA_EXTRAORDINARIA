<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CocinaController extends Controller
{
    public function pedidos()
    {
        // Obtener pedidos agrupados por estado
        $pedidos = Pedido::with(['mesa', 'detalles.producto', 'usuario'])
            ->whereIn('estado', ['pendiente', 'en_preparacion', 'listo'])
            ->orderBy('fecha_pedido', 'asc')
            ->get()
            ->groupBy('estado');

        // Asegurar que todas las categorías existan aunque estén vacías
        $estados = ['pendiente', 'en_preparacion', 'listo'];
        foreach ($estados as $estado) {
            if (!isset($pedidos[$estado])) {
                $pedidos[$estado] = collect();
            }
        }

        return Inertia::render('Cocina/Pedidos', [
            'pedidos' => $pedidos,
        ]);
    }

    public function updateEstado(Request $request, Pedido $pedido)
    {
        $validated = $request->validate([
            'estado' => 'required|string|in:pendiente,en_preparacion,listo,entregado,cancelado',
        ]);

        $pedido->update(['estado' => $validated['estado']]);

        // Si está entregado, liberar la mesa
        if ($validated['estado'] === 'entregado') {
            $pedido->mesa->update(['estado' => 'disponible']);
        }

        return back()->with('success', "✅ Pedido #{$pedido->id_pedido} actualizado a {$validated['estado']}");
    }
}
