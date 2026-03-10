<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 16px;
            margin-bottom: 5px;
            color: #1a1a1a;
        }
        .header p {
            font-size: 10px;
            color: #666;
        }
        .fecha-rango {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
            font-size: 10px;
        }
        .kpis {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .kpi {
            display: table-cell;
            width: 25%;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .kpi-label {
            font-weight: bold;
            font-size: 10px;
            color: #555;
            margin-bottom: 5px;
        }
        .kpi-value {
            font-size: 14px;
            font-weight: bold;
            color: #1a1a1a;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            font-size: 12px;
            background-color: #f5f5f5;
            padding: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }
        table td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 9px;
        }
        table tr:nth-child(even) {
            background-color: #fafafa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .estado-box {
            display: inline-block;
            width: 22%;
            margin-right: 2%;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            vertical-align: top;
        }
        .estado-box:last-child {
            margin-right: 0;
        }
        .estado-label {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .estado-numero {
            font-size: 16px;
            font-weight: bold;
            color: #1a1a1a;
        }
        .footer {
            text-align: center;
            font-size: 9px;
            color: #999;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
        .estado-listo { color: #f59e0b; }
        .estado-cocinando { color: #3b82f6; }
        .estado-entregado { color: #10b981; }
        .estado-cancelado { color: #ef4444; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 REPORTE DE VENTAS</h1>
        <p>Restaurante - Sistema de Caja</p>
    </div>

    <div class="fecha-rango">
        Período: {{ $fecha_inicio }} - {{ $fecha_fin }}
    </div>

    <!-- KPIs Principales -->
    <div class="kpis">
        <div class="kpi">
            <div class="kpi-label">Total Ventas</div>
            <div class="kpi-value">Bs. {{ number_format($totalVentas, 2) }}</div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Pagos Recibidos</div>
            <div class="kpi-value">Bs. {{ number_format($totalPagos, 2) }}</div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Por Cobrar</div>
            <div class="kpi-value">Bs. {{ number_format($pendienteCobro, 2) }}</div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Promedio/Venta</div>
            <div class="kpi-value">Bs. {{ number_format($promedioPorVenta, 2) }}</div>
        </div>
    </div>

    <!-- Estado de Pedidos -->
    <div class="section">
        <div class="section-title">📦 Estado de Pedidos</div>
        <div style="margin-bottom: 15px;">
            <div class="estado-box">
                <div class="kpi-label estado-listo">Listos</div>
                <div class="estado-numero">{{ $pedidosPorEstado['listo'] }}</div>
            </div>
            <div class="estado-box">
                <div class="kpi-label estado-cocinando">Cocinando</div>
                <div class="estado-numero">{{ $pedidosPorEstado['cocinando'] }}</div>
            </div>
            <div class="estado-box">
                <div class="kpi-label estado-entregado">Entregados</div>
                <div class="estado-numero">{{ $pedidosPorEstado['entregado'] }}</div>
            </div>
            <div class="estado-box">
                <div class="kpi-label estado-cancelado">Cancelados</div>
                <div class="estado-numero">{{ $pedidosPorEstado['cancelado'] }}</div>
            </div>
        </div>
    </div>

    <!-- Top Productos -->
    <div class="section">
        <div class="section-title">🍽️ Top 10 Productos Vendidos</div>
        <table>
            <tr>
                <th style="width: 70%;">Producto</th>
                <th style="width: 30%;" class="text-right">Cantidad</th>
            </tr>
            @forelse($topProductos as $producto)
                <tr>
                    <td>{{ $producto['nombre'] }}</td>
                    <td class="text-right">{{ $producto['cantidad'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">Sin datos</td>
                </tr>
            @endforelse
        </table>
    </div>

    <!-- Pagos Completados -->
    <div class="section">
        <div class="section-title">💳 Últimos Pagos ({{ count($pagos) }})</div>
        <table>
            <tr>
                <th style="width: 15%;">Fecha</th>
                <th style="width: 20%;">Venta</th>
                <th style="width: 15%;">Método</th>
                <th style="width: 20%;">Monto</th>
                <th style="width: 30%;">Estado</th>
            </tr>
            @forelse($pagos->take(10) as $pago)
                <tr>
                    <td>{{ $pago->fecha_pago?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                    <td>#{{ $pago->venta?->numero_venta ?? 'N/A' }}</td>
                    <td>{{ $pago->metodoPago?->nombre ?? 'N/A' }}</td>
                    <td class="text-right">Bs. {{ number_format($pago->monto, 2) }}</td>
                    <td class="text-center">
                        @if($pago->estado == 'completado')
                            ✅ Completado
                        @else
                            ⏳ {{ ucfirst($pago->estado) }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Sin datos</td>
                </tr>
            @endforelse
        </table>
    </div>

    <div class="footer">
        <p>Reporte generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Sistema de Caja - Restaurante</p>
    </div>
</body>
</html>
