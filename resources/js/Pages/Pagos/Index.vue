<template>
    <Layout>
        <Head title="Gestión de Pagos" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Gestión de Pagos</h1>
                    <p class="text-gray-600 text-sm mt-2">Los pagos se registran desde <strong>Cuentas por Cobrar</strong> en el módulo del Cajero</p>
                </div>

                <!-- Mensaje de éxito -->
                <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    ✅ {{ $page.props.flash.success }}
                </div>

                <!-- Tabla de Pagos -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-orange-500 to-red-600 text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">ID Pago</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Ticket</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Mesa</th>
                                    <th class="px-6 py-4 text-right text-sm font-semibold">Total</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Método</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Fecha</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="pago in pagos.data" :key="pago.id_pago" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm font-mono font-semibold text-orange-600">#{{ pago.id_pago }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <div>
                                            <p class="font-semibold">TIC-{{ pago.venta?.pedido?.ticket?.numero_ticket || 'N/A' }}</p>
                                            <p class="text-xs text-gray-500">{{ pago.venta?.pedido?.ticket?.tipo || 'Sin ticket' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <span class="font-semibold text-gray-900">Mesa {{ pago.venta?.pedido?.mesa?.numero_mesa }}</span>
                                        <p class="text-xs text-gray-500">{{ pago.venta?.pedido?.mesa?.ubicacion }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-orange-600">
                                        Bs. {{ parseFloat(pago.monto).toFixed(2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ pago.metodo_pago?.nombre || 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ formatDate(pago.fecha_pago) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex gap-2 justify-center">
                                            <button 
                                                @click="imprimirPago(pago)"
                                                title="Imprimir comprobante"
                                                class="text-purple-600 hover:text-purple-900 hover:bg-purple-50 p-2 rounded transition"
                                            >
                                                <i class="fas fa-print"></i>
                                            </button>
                                            <Link 
                                                :href="`/pagos/${pago.id_pago}/edit`"
                                                title="Editar pago"
                                                class="text-orange-600 hover:text-orange-900 hover:bg-orange-50 p-2 rounded transition"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                            <button 
                                                @click="eliminarPago(pago)"
                                                title="Eliminar pago"
                                                class="text-red-600 hover:text-red-900 hover:bg-red-50 p-2 rounded transition"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mensaje vacío -->
                    <div v-if="pagos.data.length === 0" class="text-center py-12">
                        <p class="text-gray-500 text-lg">No hay pagos registrados</p>
                    </div>
                </div>

                <!-- Paginación -->
                <div v-if="pagos.links && pagos.links.length > 0" class="mt-6 flex justify-center gap-2">
                    <template v-for="link in pagos.links" :key="link.label">
                        <Link 
                            v-if="link.url"
                            :href="link.url"
                            :class="[
                                'px-4 py-2 rounded border text-sm',
                                link.active
                                    ? 'bg-orange-600 text-white border-orange-600'
                                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                            ]"
                            v-html="link.label"
                        ></Link>
                        <span 
                            v-else
                            :class="[
                                'px-4 py-2 rounded border text-sm',
                                'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                            ]"
                            v-html="link.label"
                        ></span>
                    </template>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';

defineProps({
    pagos: {
        type: Object,
        required: true,
    },
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-BO', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const estadoBadge = (estado) => {
    const colores = {
        'completado': 'bg-green-100 text-green-800',
        'pendiente': 'bg-yellow-100 text-yellow-800',
        'cancelado': 'bg-red-100 text-red-800',
    };
    return colores[estado] || 'bg-gray-100 text-gray-800';
};

const estadoTicketBadge = (estado) => {
    const colores = {
        'pendiente': 'bg-yellow-100 text-yellow-800',
        'impreso': 'bg-blue-100 text-blue-800',
        'pagado': 'bg-green-100 text-green-800',
        'anulado': 'bg-red-100 text-red-800',
    };
    return colores[estado] || 'bg-gray-100 text-gray-800';
};

const eliminarPago = (pago) => {
    if (confirm(`¿Estás seguro de que deseas eliminar el pago #${pago.id_pago}?`)) {
        router.delete(`/pagos/${pago.id_pago}`);
    }
};

const imprimirPago = (pago) => {
    if (!pago) return;

    const fechaFormato = new Date(pago.fecha_pago).toLocaleString('es-BO', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });

    const contenidoImpresion = `
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Pago #${pago.id_pago}</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: 'Courier New', monospace;
                    background: white;
                    padding: 20px;
                }
                
                .pago-container {
                    max-width: 400px;
                    margin: 0 auto;
                    background: white;
                    border: 2px solid #000;
                    padding: 20px;
                }
                
                .encabezado {
                    text-align: center;
                    margin-bottom: 20px;
                    border-bottom: 2px dashed #000;
                    padding-bottom: 15px;
                }
                
                .encabezado h1 {
                    font-size: 24px;
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                
                .encabezado p {
                    font-size: 14px;
                    margin: 3px 0;
                }
                
                .fecha {
                    font-size: 12px;
                    color: #333;
                    margin-top: 5px;
                }
                
                .seccion {
                    margin: 15px 0;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                }
                
                .seccion-titulo {
                    font-weight: bold;
                    font-size: 14px;
                    margin-bottom: 8px;
                    text-decoration: underline;
                }
                
                .fila {
                    display: flex;
                    justify-content: space-between;
                    font-size: 13px;
                    margin: 5px 0;
                }
                
                .etiqueta {
                    font-weight: bold;
                    color: #333;
                }
                
                .valor {
                    text-align: right;
                    color: #666;
                }
                
                .total {
                    font-size: 16px;
                    font-weight: bold;
                    margin-top: 10px;
                    padding: 10px;
                    background: #fff3cd;
                    border: 2px solid #ffc107;
                    text-align: right;
                    border-radius: 5px;
                }
                
                .estado-badge {
                    display: inline-block;
                    background: #86EFAC;
                    color: #000;
                    padding: 8px 12px;
                    border-radius: 5px;
                    font-weight: bold;
                    font-size: 14px;
                    margin: 15px 0;
                    text-align: center;
                    width: 100%;
                    border: 2px solid #000;
                }
                
                .pie {
                    text-align: center;
                    margin-top: 20px;
                    padding-top: 15px;
                    border-top: 2px dashed #000;
                    font-size: 12px;
                    color: #666;
                }
                
                @media print {
                    body {
                        padding: 0;
                        margin: 0;
                    }
                    .pago-container {
                        border: none;
                        max-width: 100%;
                        padding: 0;
                    }
                }
            </style>
        </head>
        <body>
            <div class="pago-container">
                <div class="encabezado">
                    <h1>💳 COMPROBANTE DE PAGO</h1>
                    <p>#${pago.id_pago}</p>
                    <p class="fecha">${fechaFormato}</p>
                </div>
                
                <div class="seccion">
                    <div class="seccion-titulo">📋 Información del Ticket</div>
                    <div class="fila">
                        <span class="etiqueta">Ticket:</span>
                        <span class="valor">#${pago.venta?.pedido?.ticket?.numero_ticket || 'N/A'}</span>
                    </div>
                    <div class="fila">
                        <span class="etiqueta">Tipo:</span>
                        <span class="valor">${pago.venta?.pedido?.ticket?.tipo || 'Sin ticket'}</span>
                    </div>
                </div>
                
                <div class="seccion">
                    <div class="seccion-titulo">🏠 Información de Pedido</div>
                    <div class="fila">
                        <span class="etiqueta">Pedido:</span>
                        <span class="valor">#${pago.venta?.pedido?.id_pedido || 'N/A'}</span>
                    </div>
                    <div class="fila">
                        <span class="etiqueta">Mesa:</span>
                        <span class="valor">${pago.venta?.pedido?.mesa?.numero_mesa || 'N/A'}</span>
                    </div>
                    <div class="fila">
                        <span class="etiqueta">Ubicación:</span>
                        <span class="valor">${pago.venta?.pedido?.mesa?.ubicacion || 'N/A'}</span>
                    </div>
                </div>

                ${(() => {
                    if (!pago.venta?.pedido?.detalles || pago.venta.pedido.detalles.length === 0) {
                        return '';
                    }
                    
                    const productosHTML = pago.venta.pedido.detalles.map(detalle => {
                        const precioUnitario = parseFloat(detalle.precio_unitario || 0);
                        const cantidad = parseInt(detalle.cantidad || 1);
                        const subtotal = precioUnitario * cantidad;
                        return `<tr><td style="padding: 8px; border: 1px solid #000;">${detalle.producto?.nombre || 'Producto'}</td><td style="padding: 8px; border: 1px solid #000; text-align: center;">${cantidad}</td><td style="padding: 8px; border: 1px solid #000; text-align: right;">Bs. ${precioUnitario.toFixed(2)}</td><td style="padding: 8px; border: 1px solid #000; text-align: right;">Bs. ${subtotal.toFixed(2)}</td></tr>`;
                    }).join('');
                    
                    return `<div style="margin: 20px 0; border: 1px solid #000;"><table style="width: 100%; border-collapse: collapse; font-size: 12px;"><thead><tr style="border: 1px solid #000;"><th style="background: #f0f0f0; padding: 8px; border: 1px solid #000; font-weight: bold;">PRODUCTO</th><th style="background: #f0f0f0; padding: 8px; border: 1px solid #000; font-weight: bold; text-align: center;">CANT</th><th style="background: #f0f0f0; padding: 8px; border: 1px solid #000; font-weight: bold; text-align: right;">P.U.</th><th style="background: #f0f0f0; padding: 8px; border: 1px solid #000; font-weight: bold; text-align: right;">SUBTOTAL</th></tr></thead><tbody>${productosHTML}</tbody></table></div>`;
                })()}
                
                <div class="seccion">
                    <div class="seccion-titulo">💰 Información del Pago</div>
                    <div class="fila">
                        <span class="etiqueta">Método:</span>
                        <span class="valor">${pago.metodo_pago?.nombre || 'N/A'}</span>
                    </div>
                    <div class="fila">
                        <span class="etiqueta">Transacción:</span>
                        <span class="valor">${pago.nro_transaccion || 'N/A'}</span>
                    </div>
                </div>
                
                <div class="estado-badge">
                    ✅ PAGADO
                </div>
                
                <div class="total">
                    TOTAL: Bs. ${parseFloat(pago.monto).toFixed(2)}
                </div>
                
                <div class="pie">
                    <p>Gracias por su compra 🙏</p>
                    <p style="margin-top: 5px;">Comprobante de pago</p>
                </div>
            </div>
        </body>
        </html>
    `;

    // Abrir en ventana de impresión
    const ventana = window.open('', '_blank');
    ventana.document.write(contenidoImpresion);
    ventana.document.close();
    
    // Esperar a que cargue y luego imprimir
    setTimeout(() => {
        ventana.print();
    }, 250);
};
</script>

<style scoped>
</style>
