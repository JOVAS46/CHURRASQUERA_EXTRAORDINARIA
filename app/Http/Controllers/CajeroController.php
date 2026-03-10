<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Venta;
use App\Models\Pago;
use App\Models\MetodoPago;
use App\Services\PagoFacilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CajeroController extends Controller
{
    public function cuentasPorCobrar()
    {
        // Obtener pedidos listos para cobrar (estado=listo)
        $pedidos = Pedido::with(['mesa', 'usuario', 'detalles.producto', 'ventas.pagos'])
            ->where('estado', 'listo')
            ->orderBy('fecha_pedido', 'desc')
            ->paginate(15);

        // Obtener métodos de pago disponibles
        $metodosPago = MetodoPago::where('activo', 1)->get();

        return Inertia::render('Cajero/CuentasPorCobrar', [
            'pedidos' => $pedidos,
            'metodosPago' => $metodosPago,
        ]);
    }

    public function registrarPago(Request $request, $id)
    {
        try {
            // Obtener el pedido por ID
            $pedido = Pedido::findOrFail($id);
            
            $validated = $request->validate([
                'id_metodo_pago' => 'required|integer|exists:metodo_pagos,id_metodo_pago',
                'monto' => 'required|numeric|min:0.01',
                'nro_transaccion' => 'nullable|string|max:100',
            ]);

            // Verificar que el pedido esté en estado 'listo'
            if ($pedido->estado !== 'listo') {
                return back()->with('error', '❌ El pedido no está listo para cobrar');
            }

            // Verificar si ya tiene venta
            $venta = $pedido->ventas()->first();
            
            if (!$venta) {
                // Crear nueva venta
                $venta = Venta::create([
                    'id_pedido' => $pedido->id_pedido,
                    'fecha_venta' => now(),
                    'total' => $pedido->total,
                    'estado' => 'pendiente',
                    'id_usuario' => Auth::id(),
                    'id_cliente' => $pedido->id_cliente,
                ]);
            }

            // Registrar pago
            $pago = Pago::create([
                'id_venta' => $venta->id_venta,
                'fecha_pago' => now(),
                'monto' => $validated['monto'],
                'estado' => 'completado',
                'id_metodo_pago' => $validated['id_metodo_pago'],
                'nro_transaccion' => $validated['nro_transaccion'],
            ]);

            // Actualizar estado de venta a completada
            $venta->update(['estado' => 'completada']);

            // Actualizar estado de pedido a entregado
            $pedido->update(['estado' => 'entregado']);

            // Liberar mesa
            $pedido->mesa->update(['estado' => 'disponible']);

            return back()->with('success', "✅ Pago registrado exitosamente. Pedido #{$pedido->id_pedido} entregado");
        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al registrar pago: ' . $e->getMessage());
        }
    }

    public function ventasDelDia()
    {
        // Obtener ventas del día actual
        $ventas = Venta::with(['pedido', 'usuario', 'pagos'])
            ->whereDate('fecha_venta', today())
            ->orderBy('fecha_venta', 'desc')
            ->paginate(20);

        // Cálculos
        $totalVentas = $ventas->sum('total');
        $totalPagado = Pago::whereHas('venta', function($q) {
            $q->whereDate('fecha_venta', today());
        })->sum('monto');

        return Inertia::render('Cajero/VentasDelDia', [
            'ventas' => $ventas,
            'totalVentas' => $totalVentas,
            'totalPagado' => $totalPagado,
            'pendiente' => $totalVentas - $totalPagado,
        ]);
    }

    public function detallePago(Pago $pago)
    {
        $pago->load(['venta.pedido.mesa', 'metodoPago']);

        return Inertia::render('Cajero/DetallePago', [
            'pago' => $pago,
        ]);
    }

    public function generarQR(Request $request, $id)
    {
        try {
            // Obtener el pedido por ID
            $pedido = Pedido::findOrFail($id);
            
            $validated = $request->validate([
                'cliente_email' => 'nullable|email',
            ]);

            // Verificar que el pedido esté en estado 'listo'
            if ($pedido->estado !== 'listo') {
                return response()->json(['success' => false, 'message' => 'Pedido no está listo'], 400);
            }

            // Crear venta si no existe
            $venta = $pedido->ventas()->first();
            if (!$venta) {
                $venta = Venta::create([
                    'id_pedido' => $pedido->id_pedido,
                    'fecha_venta' => now(),
                    'total' => $pedido->total,
                    'estado' => 'pendiente',
                    'id_usuario' => Auth::id() ?? 1, // Usuario por defecto
                    'id_cliente' => $pedido->id_cliente,
                ]);
            }

            // Generar QR con PagoFácil
            $pagoFacil = new PagoFacilService();
            $clienteEmail = $validated['cliente_email'] ?? 'cajero@restaurante.com';
            
            $resultado = $pagoFacil->generarQR(
                $venta->id_venta,
                (float) $pedido->total,
                $clienteEmail
            );

            if (!$resultado['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['message']
                ], 400);
            }

            // Guardar QR en la venta
            $venta->update([
                'qr_image' => $resultado['qr_image'],
            ]);

            // Crear registro de pago pendiente
            // Buscar metodo de pago QR o usar el primer método disponible
            $metodoPago = MetodoPago::where('nombre', 'like', '%QR%')->first() 
                ?? MetodoPago::first();
            
            $pago = Pago::create([
                'id_venta' => $venta->id_venta,
                'fecha_pago' => now(),
                'monto' => (float) $pedido->total,
                'estado' => 'pendiente',
                'id_metodo_pago' => $metodoPago->id_metodo_pago ?? 1,
                'nro_transaccion' => $resultado['nro_transaccion'],
            ]);

            return response()->json([
                'success' => true,
                'qr_image' => $resultado['qr_image'],
                'nro_transaccion' => $resultado['nro_transaccion'],
                'venta_id' => $venta->id_venta,
                'pago_id' => $pago->id_pago,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en generarQR:', [
                'id_pedido' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    public function verificarEstadoPedido($id)
    {
        try {
            // Obtener el pedido por ID
            $pedido = Pedido::findOrFail($id);

            // Obtener la venta asociada al pedido
            $venta = $pedido->ventas()->first();

            if (!$venta) {
                return response()->json([
                    'id_pedido' => $pedido->id_pedido,
                    'estado' => $pedido->estado,
                    'completado' => false,
                ]);
            }

            // Obtener el pago asociado a la venta
            $pago = $venta->pagos()->first();

            if (!$pago || !$pago->nro_transaccion) {
                return response()->json([
                    'id_pedido' => $pedido->id_pedido,
                    'estado' => $pedido->estado,
                    'completado' => false,
                ]);
            }

            // Consultar estado en PagoFácil
            $pagoFacil = new PagoFacilService();
            $resultado = $pagoFacil->consultarEstado($pago->nro_transaccion);

            // Revisar si la transacción está completada (paymentStatus = 2 = "Pagado")
            if ($resultado['success'] && $resultado['completed']) {
                // Actualizar venta a completada
                $venta->update(['estado' => 'completada']);

                // Actualizar pago a completado
                $pago->update(['estado' => 'completado']);

                // Actualizar pedido a entregado
                $pedido->update(['estado' => 'entregado']);

                // Liberar mesa
                $pedido->mesa->update(['estado' => 'disponible']);

                return response()->json([
                    'id_pedido' => $pedido->id_pedido,
                    'estado' => 'entregado',
                    'completado' => true,
                    'message' => 'Pago confirmado por PagoFácil',
                ]);
            } else {
                return response()->json([
                    'id_pedido' => $pedido->id_pedido,
                    'estado' => $pedido->estado,
                    'completado' => false,
                    'paymentStatus' => $resultado['paymentStatus'] ?? null,
                    'message' => 'Pago pendiente o rechazado',
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'completado' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function callbackPagoFacil(Request $request)
    {
        try {
            // Procesar callback de PagoFácil
            $data = $request->all();
            
            // Buscar transacción y actualizar pago
            $pagoFacil = new PagoFacilService();
            $nroTransaccion = $data['transactionId'] ?? null;

            if (!$nroTransaccion) {
                return response()->json(['success' => false], 400);
            }

            // Consultar estado en PagoFácil
            $estado = $pagoFacil->consultarEstado($nroTransaccion);

            if ($estado['success'] && $estado['status'] == 'aprobado') {
                // Buscar pago pendiente con este nro_transaccion
                $pago = Pago::where('nro_transaccion', $nroTransaccion)->first();

                if ($pago) {
                    // Actualizar pago
                    $pago->update(['estado' => 'completado']);

                    // Actualizar venta
                    $pago->venta->update(['estado' => 'completada']);

                    // Actualizar pedido
                    $pago->venta->pedido->update(['estado' => 'entregado']);

                    // Liberar mesa
                    $pago->venta->pedido->mesa->update(['estado' => 'disponible']);
                }
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar estado de pago por número de transacción
     * GET /cajero/verificar-transaccion/{nro_transaccion}
     * 
     * Consulta PagoFácil y actualiza BD si está pagado
     */
    public function verificarTransaccion($nro_transaccion)
    {
        try {
            \Log::info('verificarTransaccion iniciado', ['nro_transaccion' => $nro_transaccion]);
            
            // Buscar pago con este nro_transaccion
            $pago = Pago::where('nro_transaccion', $nro_transaccion)->first();

            if (!$pago) {
                \Log::warning('Pago no encontrado', ['nro_transaccion' => $nro_transaccion]);
                return response()->json([
                    'completado' => false,
                    'error' => 'Transacción no encontrada en BD',
                ], 404);
            }

            \Log::info('Pago encontrado', ['id_pago' => $pago->id_pago, 'id_venta' => $pago->id_venta]);

            // Consultar estado en PagoFácil
            $pagoFacil = new PagoFacilService();
            $resultado = $pagoFacil->consultarEstado($nro_transaccion);

            \Log::info('Respuesta de PagoFácil:', [
                'success' => $resultado['success'],
                'completed' => $resultado['completed'],
                'paymentStatus' => $resultado['paymentStatus'] ?? null
            ]);

            // Si la transacción está pagada (paymentStatus = 2)
            if ($resultado['success'] && $resultado['completed']) {
                \Log::info('Pago completado - Actualizando BD');
                
                // ✅ Actualizar Pago a completado
                $pago->update(['estado' => 'completado']);

                // ✅ Actualizar Venta a completada
                $venta = $pago->venta;
                if ($venta) {
                    $venta->update(['estado' => 'completada']);

                    // ✅ Actualizar Pedido a entregado
                    $pedido = $venta->pedido;
                    if ($pedido) {
                        $pedido->update(['estado' => 'entregado']);

                        // ✅ Liberar Mesa
                        if ($pedido->mesa) {
                            $pedido->mesa->update(['estado' => 'disponible']);
                        }
                    }
                }

                return response()->json([
                    'completado' => true,
                    'paymentStatus' => $resultado['paymentStatus'],
                    'statusDescription' => $resultado['statusDescription'],
                    'message' => '✅ Pago confirmado',
                ]);
            }

            // Si aún está pendiente
            \Log::info('Pago aún pendiente', ['paymentStatus' => $resultado['paymentStatus'] ?? null]);
            
            return response()->json([
                'completado' => false,
                'paymentStatus' => $resultado['paymentStatus'] ?? null,
                'statusDescription' => $resultado['statusDescription'] ?? null,
                'message' => '⏳ Transacción pendiente',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en verificarTransaccion:', [
                'nro_transaccion' => $nro_transaccion,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'completado' => false,
                'error' => $e->getMessage(),
                'debug' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }

    /**
     * DEBUG: Endpoint para probar la consulta de transacción
     * GET /cajero/debug-verificar/{nro_transaccion}
     */
    public function debugVerificarTransaccion($nro_transaccion)
    {
        try {
            $pagoFacil = new PagoFacilService();
            $resultado = $pagoFacil->consultarEstado($nro_transaccion);

            return response()->json([
                'message' => '🔍 Resultado de consultarEstado()',
                'nro_transaccion' => $nro_transaccion,
                'resultado' => $resultado,
                'timestamp' => now()->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => '❌ Error en debug',
                'message' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString(),
            ], 500);
        }
    }
}


