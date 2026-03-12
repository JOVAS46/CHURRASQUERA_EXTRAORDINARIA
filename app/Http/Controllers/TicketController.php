<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Pedido;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketController extends Controller
{
    /**
     * Mostrar lista de tickets
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['pedido' => function ($q) {
            $q->with(['mesa', 'usuario']);
        }])->orderBy('fecha_emision', 'desc');

        // Filtros
        if ($request->has('tipo') && $request->tipo) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->has('numero_ticket') && $request->numero_ticket) {
            $query->where('numero_ticket', 'like', '%' . $request->numero_ticket . '%');
        }

        if ($request->has('fecha_desde') && $request->fecha_desde) {
            $query->whereDate('fecha_emision', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta') && $request->fecha_hasta) {
            $query->whereDate('fecha_emision', '<=', $request->fecha_hasta);
        }

        $tickets = $query->paginate(20);

        // Registrar en bitácora
        Bitacora::registrarAccion(
            accion: 'listar',
            tabla_afectada: 'tickets',
            detalles: 'Lista de tickets consultada'
        );

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
            'filtros' => $request->all(),
        ]);
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $pedidos = Pedido::where('estado', 'pendiente')
            ->whereDoesntHave('ticket')
            ->with(['mesa', 'usuario', 'detalles'])
            ->get();

        return Inertia::render('Tickets/Create', [
            'pedidos' => $pedidos,
        ]);
    }

    /**
     * Guardar nuevo ticket
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_ticket' => 'required|unique:tickets,numero_ticket',
            'tipo' => 'required|in:Normal,Especial,Cortesía',
            'id_pedido' => 'required|exists:pedidos,id_pedido',
        ]);

        $pedido = Pedido::findOrFail($validated['id_pedido']);

        // Verificar si ya existe ticket para este pedido
        if ($pedido->ticket()->exists()) {
            return back()->withErrors(['error' => 'Este pedido ya tiene un ticket asociado']);
        }

        $ticket = Ticket::create([
            'numero_ticket' => $validated['numero_ticket'],
            'tipo' => $validated['tipo'],
            'fecha_emision' => now(),
            'id_pedido' => $validated['id_pedido'],
            'estado' => 'pendiente',  // Ticket creado, esperando pago
        ]);

        // Cambiar estado del pedido
        $pedido->update(['estado' => 'completado']);

        // Registrar en bitácora
        Bitacora::registrarAccion(
            accion: 'crear',
            tabla_afectada: 'tickets',
            detalles: json_encode([
                'id_ticket' => $ticket->id_ticket,
                'numero_ticket' => $ticket->numero_ticket,
                'id_pedido' => $pedido->id_pedido,
                'tipo' => $ticket->tipo,
            ])
        );

        return redirect()->route('tickets.show', $ticket->id_ticket)
            ->with('success', 'Ticket generado correctamente: #' . $ticket->numero_ticket);
    }

    /**
     * Mostrar detalle del ticket
     */
    public function show(Ticket $ticket)
    {
        $ticket->load([
            'pedido' => function ($q) {
                $q->with(['mesa', 'usuario', 'detalles' => function ($q2) {
                    $q2->with('producto');
                }]);
            }
        ]);

        // Registrar acceso en bitácora
        Bitacora::registrarAccion(
            accion: 'ver_detalle',
            tabla_afectada: 'tickets',
            detalles: 'Detalle del ticket #' . $ticket->numero_ticket . ' consultado'
        );

        return Inertia::render('Tickets/Show', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Ticket $ticket)
    {
        $ticket->load('pedido');

        return Inertia::render('Tickets/Edit', [
            'ticket' => $ticket,
            'tipos' => ['Normal', 'Especial', 'Cortesía'],
        ]);
    }

    /**
     * Actualizar ticket
     */
    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:Normal,Especial,Cortesía',
        ]);

        $tipoAnterior = $ticket->tipo;
        $ticket->update($validated);

        // Registrar en bitácora
        Bitacora::registrarAccion(
            accion: 'actualizar',
            tabla_afectada: 'tickets',
            detalles: json_encode([
                'id_ticket' => $ticket->id_ticket,
                'tipo_anterior' => $tipoAnterior,
                'tipo_nuevo' => $ticket->tipo,
            ])
        );

        return redirect()->route('tickets.show', $ticket->id_ticket)
            ->with('success', 'Ticket actualizado correctamente');
    }

    /**
     * Eliminar ticket
     */
    public function destroy(Ticket $ticket)
    {
        $numeroTicket = $ticket->numero_ticket;
        $idTicket = $ticket->id_ticket;

        // Cambiar estado del pedido a pendiente
        $ticket->pedido()->update(['estado' => 'pendiente']);

        $ticket->delete();

        // Registrar en bitácora
        Bitacora::registrarAccion(
            accion: 'eliminar',
            tabla_afectada: 'tickets',
            detalles: 'Ticket #' . $numeroTicket . ' eliminado'
        );

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket eliminado correctamente');
    }

    /**
     * Generar ticket automáticamente para un pedido
     */
    public function generarAutomatico(Pedido $pedido)
    {
        // Verificar si ya tiene ticket
        if ($pedido->ticket()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Este pedido ya tiene un ticket asociado'
            ], 400);
        }

        // Generar número único
        $ultimoTicket = Ticket::max('numero_ticket') ?? 10000;
        $numeroTicket = $ultimoTicket + 1;

        $ticket = Ticket::create([
            'numero_ticket' => $numeroTicket,
            'tipo' => 'Normal',
            'fecha_emision' => now(),
            'id_pedido' => $pedido->id_pedido,
            'estado' => 'pendiente',  // Ticket creado, esperando pago
        ]);

        // Cambiar estado del pedido
        $pedido->update(['estado' => 'completado']);

        // Registrar en bitácora
        Bitacora::registrarAccion(
            accion: 'generar_automatico',
            tabla_afectada: 'tickets',
            detalles: 'Ticket #' . $numeroTicket . ' generado automáticamente para pedido #' . $pedido->id_pedido
        );

        return response()->json([
            'success' => true,
            'message' => 'Ticket generado correctamente',
            'ticket' => $ticket->load('pedido'),
        ]);
    }

    /**
     * Exportar ticket a PDF
     */
    public function exportarPDF(Ticket $ticket)
    {
        $ticket->load([
            'pedido' => function ($q) {
                $q->with(['mesa', 'usuario', 'detalles' => function ($q2) {
                    $q2->with('producto');
                }]);
            }
        ]);

        // Aquí iría la generación del PDF con una librería como TCPDF o DomPDF
        // Por ahora retornamos JSON con los datos para que el frontend lo maneje
        return response()->json([
            'ticket' => $ticket,
            'html' => $this->generarHTMLTicket($ticket),
        ]);
    }

    /**
     * Generar HTML para impresión/PDF
     */
    private function generarHTMLTicket(Ticket $ticket): string
    {
        $pedido = $ticket->pedido;
        $mesa = $pedido->mesa;
        $detalles = $pedido->detalles;

        $html = <<<HTML
            <div style="font-family: monospace; max-width: 400px; margin: 0 auto; padding: 20px;">
                <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px;">
                    <h2>TICKET</h2>
                    <p style="margin: 5px 0;">#{ $ticket->numero_ticket }</p>
                </div>

                <div style="margin: 15px 0;">
                    <p><strong>Fecha:</strong> { $ticket->fecha_emision->format('d/m/Y H:i') }</p>
                    <p><strong>Mesa:</strong> { $mesa->numero_mesa }</p>
                    <p><strong>Tipo:</strong> { $ticket->tipo }</p>
                </div>

                <div style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 10px 0; margin: 10px 0;">
HTML;

        foreach ($detalles as $detalle) {
            $html .= <<<HTML
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>{ $detalle->producto->nombre } x { $detalle->cantidad }</span>
                        <span>\${ number_format($detalle->subtotal, 2) }</span>
                    </div>
HTML;
        }

        $total = $pedido->total;
        $html .= <<<HTML
                </div>

                <div style="text-align: right; font-size: 18px; font-weight: bold; margin-top: 15px;">
                    <p>TOTAL: \${ number_format($total, 2) }</p>
                </div>

                <div style="text-align: center; margin-top: 20px; border-top: 2px solid #000; padding-top: 10px;">
                    <p style="font-size: 12px;">¡Gracias por su compra!</p>
                </div>
            </div>
HTML;

        return $html;
    }

    /**
     * Reporte de tickets por fecha
     */
    public function reporte(Request $request)
    {
        $query = Ticket::query();

        if ($request->has('fecha_desde') && $request->fecha_desde) {
            $query->whereDate('fecha_emision', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta') && $request->fecha_hasta) {
            $query->whereDate('fecha_emision', '<=', $request->fecha_hasta);
        }

        $tickets = $query->with('pedido')->get();

        $resumen = [
            'total_tickets' => $tickets->count(),
            'por_tipo' => $tickets->groupBy('tipo')->map->count(),
            'total_ventas' => $tickets->sum(function ($t) {
                return $t->pedido->total ?? 0;
            }),
        ];

        return Inertia::render('Tickets/Reporte', [
            'tickets' => $tickets,
            'resumen' => $resumen,
            'filtros' => $request->all(),
        ]);
    }
}
