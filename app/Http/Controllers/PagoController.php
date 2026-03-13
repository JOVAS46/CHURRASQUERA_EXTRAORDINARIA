<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Ticket;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::with(['pedido.mesa', 'pedido.ticket', 'metodoPago'])
            ->orderBy('fecha_pago', 'desc')
            ->paginate(15);

        return Inertia::render('Pagos/Index', [
            'pagos' => $pagos,
        ]);
    }

    public function create()
    {
        $pedidos = Pedido::with(['mesa', 'usuario'])
            ->where('estado', '!=', 'cancelado')
            ->where('estado', '!=', 'completado')
            ->orderBy('fecha_pedido', 'desc')
            ->get();
        
        $metodos_pago = MetodoPago::where('activo', true)->get();

        return Inertia::render('Pagos/Create', [
            'pedidos' => $pedidos,
            'metodos_pago' => $metodos_pago,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pedido' => 'required|integer|exists:pedidos,id_pedido',
            'id_metodo_pago' => 'required|integer|exists:metodo_pagos,id_metodo_pago',
            'monto' => 'required|numeric|min:0.01',
            'referencia' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
        ]);

        try {
            // Crear el pago
            Pago::create([
                'id_venta' => $validated['id_pedido'],
                'id_metodo_pago' => $validated['id_metodo_pago'],
                'monto' => $validated['monto'],
                'fecha_pago' => now(),
                'estado' => 'completado',
                'nro_transaccion' => 'TXN-' . date('YmdHis'),
            ]);

            // Crear o actualizar ticket
            $ticket = Ticket::where('id_pedido', $validated['id_pedido'])->first();
            if (!$ticket) {
                // Si no existe, crear uno nuevo
                $pedido = Pedido::find($validated['id_pedido']);
                $ticket = Ticket::create([
                    'numero_ticket' => now()->format('YmdHis'),
                    'fecha_emision' => now(),
                    'tipo' => 'cliente',
                    'estado' => 'pagado',
                    'id_pedido' => $validated['id_pedido'],
                ]);
            } else {
                // Si existe, actualizar estado a pagado
                $ticket->update(['estado' => 'pagado']);
            }

            return redirect()->route('pagos.index')
                ->with('success', 'Pago registrado exitosamente y ticket marcado como pagado');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

    public function show(Pago $pago)
    {
        $pago->load(['venta', 'pedido.mesa', 'pedido.detalles.producto', 'metodoPago']);

        return Inertia::render('Pagos/Show', [
            'pago' => $pago,
        ]);
    }

    public function edit(Pago $pago)
    {
        $pago->load(['pedido']);
        $metodos_pago = MetodoPago::where('activo', true)->get();

        return Inertia::render('Pagos/Edit', [
            'pago' => $pago,
            'metodos_pago' => $metodos_pago,
        ]);
    }

    public function update(Request $request, Pago $pago)
    {
        $validated = $request->validate([
            'id_metodo_pago' => 'required|integer|exists:metodo_pagos,id_metodo_pago',
            'monto' => 'required|numeric|min:0.01',
            'referencia' => 'nullable|string|max:100',
            'estado' => 'required|string|in:pendiente,completado,cancelado',
            'observaciones' => 'nullable|string',
        ]);

        try {
            $pago->update($validated);

            return redirect()->route('pagos.show', $pago->id_pago)
                ->with('success', 'Pago actualizado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar el pago');
        }
    }

    public function destroy(Pago $pago)
    {
        try {
            $pago->delete();

            return redirect()->route('pagos.index')
                ->with('success', 'Pago eliminado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el pago');
        }
    }

    public function obtenerPedidoPendiente(Request $request)
    {
        $validated = $request->validate([
            'id_pedido' => 'required|integer|exists:pedidos,id_pedido',
        ]);

        $pedido = Pedido::with(['detalles', 'pagos'])
            ->find($validated['id_pedido']);

        if (!$pedido) {
            return response()->json(['error' => 'Pedido no encontrado'], 404);
        }

        $monto_pagado = $pedido->pagos->sum('monto');
        $pendiente = $pedido->total - $monto_pagado;

        return response()->json([
            'total' => $pedido->total,
            'pagado' => $monto_pagado,
            'pendiente' => max(0, $pendiente),
        ]);
    }
}

