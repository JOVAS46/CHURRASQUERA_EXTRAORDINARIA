<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Pago;
use App\Models\Venta;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VentasController extends Controller
{
    public function index()
    {
        $ventas = Venta::with(['usuario', 'cliente', 'pedido', 'pedido.mesa', 'pagos'])
            ->orderBy('fecha_venta', 'desc')
            ->get()
            ->map(function($venta) {
                return [
                    'id_venta' => $venta->id_venta,
                    'id_pedido' => $venta->id_pedido,
                    'fecha_pedido' => $venta->fecha_venta,
                    'fecha_venta' => $venta->fecha_venta,
                    'monto_total' => (float) $venta->total,
                    'total' => (float) $venta->total,
                    'estado' => $venta->estado,
                    'usuario' => $venta->usuario ? [
                        'id' => $venta->usuario->id_usuario,
                        'nombre' => $venta->usuario->nombre,
                        'apellido' => $venta->usuario->apellido ?? '',
                    ] : null,
                    'id_mesa' => $venta->pedido?->id_mesa,
                    'numero_venta' => $venta->numero_venta,
                ];
            });

        // Traer todos los pedidos para contar tickets generados al cocinero
        $pedidos = Pedido::with(['mesa', 'usuario'])
            ->orderBy('fecha_pedido', 'desc')
            ->get();

        return Inertia::render('Ventas/Index', [
            'ventas' => $ventas,
            'pedidos' => $pedidos,
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
