<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Ticket;
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

        // Si se marca como listo, crear ticket automáticamente
        if ($validated['estado'] === 'listo') {
            // Verificar que no exista ticket ya
            $ticketExiste = Ticket::where('id_pedido', $pedido->id_pedido)->first();
            
            if (!$ticketExiste) {
                // Generar número de ticket
                $ultimoTicket = Ticket::orderBy('id_ticket', 'desc')->first();
                $nuevoNumero = ($ultimoTicket ? $ultimoTicket->numero_ticket : 0) + 1;
                
                Ticket::create([
                    'numero_ticket' => $nuevoNumero,
                    'tipo' => 'cocina',
                    'fecha_emision' => now(),
                    'id_pedido' => $pedido->id_pedido,
                    'estado' => 'pendiente',
                ]);
            }
        }

        // Si está entregado, liberar la mesa
        if ($validated['estado'] === 'entregado') {
            $pedido->mesa->update(['estado' => 'disponible']);
        }

        return back()->with('success', "✅ Pedido #{$pedido->id_pedido} actualizado a {$validated['estado']}");
    }
}
