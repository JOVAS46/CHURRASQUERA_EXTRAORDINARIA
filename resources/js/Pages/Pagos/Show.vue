<template>
    <Layout>
        <div class="container mx-auto px-4 py-8 max-w-4xl">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Detalle del Pago</h1>
                <Link href="/pagos" class="text-orange-600 hover:text-orange-800 font-bold">← Volver</Link>
            </div>

            <div v-if="$page.props.flash.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ $page.props.flash.success }}
            </div>

            <!-- Información General -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Pago</h2>
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-600">N° Pago:</span>
                            <p class="font-mono font-semibold">{{ props.pago.numero_pago }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Pedido:</span>
                            <p class="font-mono">{{ props.pago.pedido?.numero_pedido }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Método de Pago:</span>
                            <p class="font-semibold">{{ props.pago.metodo_pago?.nombre }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Monto:</span>
                            <p class="text-2xl font-bold text-green-600">Bs. {{ parseFloat(props.pago.monto).toFixed(2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Pedido</h2>
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-600">Mesa:</span>
                            <p class="font-semibold">Mesa {{ props.pago.pedido?.mesa?.numero }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Total del Pedido:</span>
                            <p class="font-semibold">Bs. {{ parseFloat(props.pago.pedido?.total).toFixed(2) }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Fecha/Hora:</span>
                            <p class="font-semibold">{{ formatDate(props.pago.fecha_pago) }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Estado:</span>
                            <p class="mt-1">
                                <span :class="getStatusBadge(props.pago.estado)" class="px-3 py-1 rounded font-semibold">
                                    {{ props.pago.estado }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles del Pedido -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Productos del Pedido</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold">Producto</th>
                                <th class="px-4 py-2 text-center font-semibold">Cantidad</th>
                                <th class="px-4 py-2 text-right font-semibold">Precio Unit.</th>
                                <th class="px-4 py-2 text-right font-semibold">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="detalle in props.pago.pedido?.detalles" :key="detalle.id_detalle" class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">{{ detalle.producto?.nombre }}</td>
                                <td class="px-4 py-3 text-center">{{ detalle.cantidad }}</td>
                                <td class="px-4 py-3 text-right">Bs. {{ parseFloat(detalle.precio_unitario).toFixed(2) }}</td>
                                <td class="px-4 py-3 text-right font-semibold">Bs. {{ parseFloat(detalle.subtotal).toFixed(2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50 font-bold">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right">Total:</td>
                                <td class="px-4 py-3 text-right">Bs. {{ parseFloat(props.pago.pedido?.total).toFixed(2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div v-if="props.pago.referencia" class="bg-blue-50 rounded-lg border border-blue-200 p-4">
                    <h3 class="font-semibold text-blue-900 mb-2">Referencia</h3>
                    <p class="font-mono text-blue-800">{{ props.pago.referencia }}</p>
                </div>
                <div v-if="props.pago.observaciones" class="bg-yellow-50 rounded-lg border border-yellow-200 p-4">
                    <h3 class="font-semibold text-yellow-900 mb-2">Observaciones</h3>
                    <p class="text-yellow-800">{{ props.pago.observaciones }}</p>
                </div>
            </div>

            <!-- Usuario que registró -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <p class="text-gray-600">
                    Registrado por: <span class="font-semibold">{{ props.pago.usuario?.nombre }}</span>
                </p>
            </div>

            <!-- Acciones -->
            <div class="flex gap-4">
                <Link :href="`/pagos/${props.pago.id_pago}/edit`" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Editar Pago
                </Link>
                <button @click="confirmarEliminar" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Eliminar Pago
                </button>
                <Link href="/pagos" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Volver
                </Link>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';

const props = defineProps({
    pago: Object,
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
        'completado': 'bg-green-100 text-green-700',
        'cancelado': 'bg-red-100 text-red-700'
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
};

const confirmarEliminar = () => {
    if (confirm('¿Estás seguro de que deseas eliminar este pago?')) {
        router.delete(`/pagos/${props.pago.id_pago}`);
    }
};
</script>
