<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Pago;
use App\Models\Venta;
use App\Models\MetodoPago;
use App\Models\Usuario;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportesController extends Controller
{
    public function index(Request $request)
    {
        $fecha_inicio = $request->query('fecha_inicio') ? Carbon::parse($request->query('fecha_inicio')) : Carbon::now()->subDays(30);
        $fecha_fin = $request->query('fecha_fin') ? Carbon::parse($request->query('fecha_fin')) : Carbon::now();

        // Pedidos en el rango de fechas
        $pedidos = Pedido::with(['usuario', 'mesa', 'detalles.producto', 'ventas.pagos'])
            ->whereBetween('fecha_pedido', [$fecha_inicio, $fecha_fin])
            ->orderBy('fecha_pedido', 'desc')
            ->get();

        // Ventas completadas
        $ventas = Venta::with(['pedido', 'pagos'])
            ->whereBetween('created_at', [$fecha_inicio, $fecha_fin])
            ->get();

        // Pagos completados
        $pagos = Pago::with(['venta', 'metodoPago'])
            ->whereBetween('fecha_pago', [$fecha_inicio, $fecha_fin])
            ->where('estado', 'completado')
            ->orderBy('fecha_pago', 'desc')
            ->get();

        // Estadísticas principales
        $totalVentas = $ventas->sum('total');
        $totalPagos = $pagos->sum('monto');
        $pendienteCobro = $totalVentas - $totalPagos;

        // Conteos por estado
        $pedidosPorEstado = [
            'listo' => $pedidos->where('estado', 'listo')->count(),
            'cocinando' => $pedidos->where('estado', 'cocinando')->count(),
            'entregado' => $pedidos->where('estado', 'entregado')->count(),
            'cancelado' => $pedidos->where('estado', 'cancelado')->count(),
        ];

        // Métodos de pago más usados
        $metodosPago = $pagos->groupBy('id_metodo_pago')
            ->map(function($items) {
                $metodo = MetodoPago::find($items->first()->id_metodo_pago);
                return [
                    'nombre' => $metodo?->nombre ?? 'N/A',
                    'monto' => $items->sum('monto'),
                    'cantidad' => $items->count(),
                ];
            })
            ->values();

        // Top productos vendidos
        $topProductos = [];
        $detallesCounts = [];
        foreach ($pedidos as $pedido) {
            foreach ($pedido->detalles as $detalle) {
                if ($detalle->producto_id) {
                    $productoId = $detalle->producto_id;
                    $detallesCounts[$productoId] = ($detallesCounts[$productoId] ?? 0) + $detalle->cantidad;
                }
            }
        }
        arsort($detallesCounts);
        foreach (array_slice($detallesCounts, 0, 10) as $productoId => $cantidad) {
            if ($productoId) {
                $producto = Producto::find($productoId);
                if ($producto) {
                    $topProductos[] = [
                        'nombre' => $producto->nombre,
                        'cantidad' => $cantidad,
                    ];
                }
            }
        }

        // Usuarios con más ventas (mozos/meseros)
        $topUsuarios = $pedidos->groupBy('id_mesero')
            ->map(function($userPedidos) {
                $usuario = $userPedidos->first()->usuario;
                $monto = $userPedidos->sum(function($p) {
                    return $p->ventas->sum('total');
                });
                return [
                    'nombre' => $usuario?->nombre ?? 'N/A',
                    'monto' => $monto,
                    'pedidos' => $userPedidos->count(),
                ];
            })
            ->values()
            ->sortBy(function($u) { return -$u['monto']; })
            ->take(5);

        // Ingresos diarios
        $ingresoDiario = [];
        for ($d = clone $fecha_inicio; $d <= $fecha_fin; $d->addDay()) {
            $fecha = $d->format('Y-m-d');
            $monto = $ventas->filter(function($v) use ($fecha) {
                return $v->created_at->format('Y-m-d') == $fecha;
            })->sum('total');
            $ingresoDiario[] = [
                'fecha' => $fecha,
                'monto' => (float)$monto,
            ];
        }

        return Inertia::render('Admin/Reportes', [
            'pedidos' => $pedidos,
            'ventas' => $ventas,
            'pagos' => $pagos,
            'estadisticas' => [
                'totalVentas' => (float)$totalVentas,
                'totalPagos' => (float)$totalPagos,
                'pendienteCobro' => (float)$pendienteCobro,
                'pedidosCount' => $pedidos->count(),
                'pagosCount' => $pagos->count(),
                'ventasCount' => $ventas->count(),
                'promedioPorVenta' => $ventas->count() > 0 ? (float)($totalVentas / $ventas->count()) : 0,
            ],
            'pedidosPorEstado' => $pedidosPorEstado,
            'metodosPago' => $metodosPago,
            'topProductos' => $topProductos,
            'topUsuarios' => $topUsuarios,
            'ingresoDiario' => $ingresoDiario,
            'filtros' => [
                'fecha_inicio' => $fecha_inicio->format('Y-m-d'),
                'fecha_fin' => $fecha_fin->format('Y-m-d'),
            ],
        ]);
    }
}
