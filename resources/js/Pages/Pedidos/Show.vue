<template>
    <Layout>
        <div class="container mx-auto px-4 py-8 max-w-4xl">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Detalle del Pedido</h1>
                <Link href="/pedidos" class="text-orange-600 hover:text-orange-800 font-bold">← Volver</Link>
            </div>

            <div v-if="$page.props.flash.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ $page.props.flash.success }}
            </div>

            <!-- Información General -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Pedido</h2>
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-600">N° Pedido:</span>
                            <p class="font-mono font-semibold">{{ pedido.numero_pedido || `PED-${pedido.id_pedido}` }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Mesa:</span>
                            <p class="font-semibold">Mesa {{ pedido.mesa?.numero }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Mesero:</span>
                            <p class="font-semibold">{{ pedido.usuario?.nombre }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Fecha/Hora:</span>
                            <p class="font-semibold">{{ formatDate(pedido.fecha_pedido) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Estado y Total</h2>
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-600">Estado:</span>
                            <p class="mt-1">
                                <span :class="getStatusBadge(pedido.estado)" class="px-3 py-1 rounded font-semibold">
                                    {{ pedido.estado }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <span class="text-gray-600">Total:</span>
                            <p class="text-2xl font-bold text-orange-600">Bs. {{ parseFloat(pedido.total).toFixed(2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles del Pedido -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Productos Ordenados</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold">Producto</th>
                                <th class="px-4 py-2 text-center font-semibold">Cantidad</th>
                                <th class="px-4 py-2 text-right font-semibold">Precio Unit.</th>
                                <th class="px-4 py-2 text-right font-semibold">Subtotal</th>
                                <th class="px-4 py-2 text-left font-semibold">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="detalle in pedido.detalles" :key="detalle.id_detalle" class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">{{ detalle.producto?.nombre }}</td>
                                <td class="px-4 py-3 text-center">{{ detalle.cantidad }}</td>
                                <td class="px-4 py-3 text-right">Bs. {{ parseFloat(detalle.precio_unitario).toFixed(2) }}</td>
                                <td class="px-4 py-3 text-right font-semibold">Bs. {{ parseFloat(detalle.subtotal).toFixed(2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ detalle.observaciones || '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Observaciones -->
            <div v-if="pedido.observaciones" class="bg-yellow-50 rounded-lg border border-yellow-200 p-4 mb-6">
                <h3 class="font-semibold text-yellow-800 mb-2">Observaciones Generales</h3>
                <p class="text-yellow-700">{{ pedido.observaciones }}</p>
            </div>

            <!-- Pagos -->
            <div v-if="pedido.pagos && pedido.pagos.length > 0" class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Pagos Registrados</h2>
                <table class="min-w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold">Fecha</th>
                            <th class="px-4 py-2 text-left font-semibold">Método</th>
                            <th class="px-4 py-2 text-right font-semibold">Monto</th>
                            <th class="px-4 py-2 text-left font-semibold">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="pago in pedido.pagos" :key="pago.id_pago" class="border-b">
                            <td class="px-4 py-3">{{ formatDate(pago.fecha_hora) }}</td>
                            <td class="px-4 py-3">{{ pago.metodo_pago?.nombre }}</td>
                            <td class="px-4 py-3 text-right font-semibold">Bs. {{ parseFloat(pago.monto).toFixed(2) }}</td>
                            <td class="px-4 py-3">
                                <span :class="getPaymentStatus(pago.estado)" class="px-2 py-1 rounded text-sm font-semibold">
                                    {{ pago.estado }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Acciones -->
            <div class="flex gap-4">
                <Link v-if="pedido.estado !== 'completado'" :href="`/pedidos/${pedido.id_pedido}/edit`" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Editar Estado
                </Link>
                <button v-if="pedido.estado !== 'completado'" @click="confirmarEliminar" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Cancelar Pedido
                </button>
                <Link href="/pedidos" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Volver
                </Link>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';

defineProps({
    pedido: Object,
});

const formatDate = (date) => {
    return new Date(date).toLocaleString('es-BO', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusBadge = (status) => {
    const colors = {
        'pendiente': 'bg-yellow-100 text-yellow-700',
        'preparando': 'bg-blue-100 text-blue-700',
        'listo': 'bg-green-100 text-green-700',
        'completado': 'bg-green-500 text-white',
        'cancelado': 'bg-red-100 text-red-700'
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
};

const getPaymentStatus = (status) => {
    const colors = {
        'pendiente': 'bg-yellow-100 text-yellow-700',
        'completado': 'bg-green-100 text-green-700',
        'cancelado': 'bg-red-100 text-red-700'
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
};

const confirmarEliminar = () => {
    if (confirm('¿Estás seguro de que deseas cancelar este pedido?')) {
        router.delete(`/pedidos/${pedido.id_pedido}`);
    }
};
</script>
