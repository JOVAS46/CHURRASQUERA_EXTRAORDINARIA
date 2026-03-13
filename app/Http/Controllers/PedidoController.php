<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Ticket;
use App\Models\DetallePedido;
use App\Models\Producto;
use App\Models\Mesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with(['mesa', 'usuario', 'detalles'])
            ->orderBy('fecha_pedido', 'desc')
            ->paginate(15);

        return Inertia::render('Pedidos/Index', [
            'pedidos' => $pedidos,
        ]);
    }

    public function create()
    {
        $mesas = Mesa::all();
        $productos = Producto::with('categoria')->get();

        return Inertia::render('Pedidos/Create', [
            'mesas' => $mesas,
            'productos' => $productos,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_mesa' => 'required|integer|exists:mesas,id_mesa',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.id_producto' => 'required|integer|exists:productos,id_producto',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.observaciones' => 'nullable|string',
        ]);

        try {
            // Crear pedido
            $pedido = Pedido::create([
                'fecha_pedido' => now(),
                'estado' => 'pendiente',
                'total' => 0,
                'observaciones' => $validated['observaciones'],
                'id_mesa' => $validated['id_mesa'],
                'id_mesero' => Auth::id(),
            ]);

            // Crear detalles
            $total = 0;
            foreach ($validated['detalles'] as $detalle) {
                $producto = Producto::find($detalle['id_producto']);
                $subtotal = $producto->precio * $detalle['cantidad'];
                $total += $subtotal;

                DetallePedido::create([
                    'id_pedido' => $pedido->id_pedido,
                    'id_producto' => $detalle['id_producto'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $subtotal,
                    'observaciones' => $detalle['observaciones'],
                ]);
            }

            // Actualizar total
            $pedido->update(['total' => $total]);

            // Marcar mesa como ocupada
            Mesa::find($validated['id_mesa'])->update(['estado' => 'ocupada']);

            return redirect('/pedidos')
                ->with('success', '✅ Pedido #' . $pedido->id_pedido . ' creado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al crear el pedido: ' . $e->getMessage());
        }
    }

    public function show(Pedido $pedido)
    {
        $pedido->load(['mesa', 'usuario', 'detalles.producto', 'pagos']);

        return Inertia::render('Pedidos/Show', [
            'pedido' => $pedido,
        ]);
    }

    public function edit(Pedido $pedido)
    {
        $productos = Producto::where('estado', 'activo')->with('categoria')->get();

        return Inertia::render('Pedidos/Edit', [
            'pedido' => $pedido->load('detalles.producto'),
            'productos' => $productos,
        ]);
    }

    public function update(Request $request, Pedido $pedido)
    {
        $validated = $request->validate([
            'estado' => 'required|string|in:pendiente,preparando,listo,completado,cancelado',
            'observaciones' => 'nullable|string',
            'detalles' => 'nullable|array',
            'detalles.*.id_producto' => 'required_with:detalles|integer|exists:productos,id_producto',
            'detalles.*.cantidad' => 'required_with:detalles|integer|min:1',
        ]);

        try {
            $pedido->update([
                'estado' => $validated['estado'],
                'observaciones' => $validated['observaciones'],
            ]);

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

            // Si el pedido se completa, liberar mesa
            if ($validated['estado'] === 'completado') {
                Mesa::where('id_mesa', $pedido->id_mesa)
                    ->update(['estado' => 'disponible']);
            }

            return redirect()->route('pedidos.show', $pedido->id_pedido)
                ->with('success', 'Pedido actualizado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar el pedido');
        }
    }

    public function destroy(Pedido $pedido)
    {
        try {
            // Liberar mesa
            Mesa::where('id_mesa', $pedido->id_mesa)
                ->update(['estado' => 'disponible']);

            // Eliminar detalles
            DetallePedido::where('id_pedido', $pedido->id_pedido)->delete();

            // Eliminar pedido
            $pedido->delete();

            return redirect()->route('pedidos.index')
                ->with('success', 'Pedido eliminado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el pedido');
        }
    }

    public function cambiarEstado(Request $request, Pedido $pedido)
    {
        $validated = $request->validate([
            'estado' => 'required|string|in:pendiente,preparando,listo,completado,cancelado',
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

        // Si se completa, liberar mesa
        if ($validated['estado'] === 'completado') {
            Mesa::where('id_mesa', $pedido->id_mesa)
                ->update(['estado' => 'disponible']);
        }

        return back()->with('success', 'Estado del pedido actualizado');
    }
}

