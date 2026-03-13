<template>
    <Layout>
        <Head title="Gestión de Pagos" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Gestión de Pagos</h1>
                    <Link href="/pagos/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                        + Nuevo Pago
                    </Link>
                </div>

                <!-- Mensaje de éxito -->
                <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    ✅ {{ $page.props.flash.success }}
                </div>

                <!-- Tabla de Pagos -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID Pago</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ticket</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pedido/Mesa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado Ticket</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="pago in pagos.data" :key="pago.id_pago" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm font-mono font-semibold text-gray-900">{{ pago.id_pago }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <div>
                                            <p class="font-semibold">Ticket #{{ pago.pedido?.ticket?.numero_ticket || 'N/A' }}</p>
                                            <p class="text-xs text-gray-500">{{ pago.pedido?.ticket?.tipo || 'Sin ticket' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <div>
                                            <p class="font-semibold">Pedido #{{ pago.pedido?.id_pedido }}</p>
                                            <p class="text-xs text-gray-500">Mesa {{ pago.pedido?.mesa?.numero_mesa }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm font-bold text-orange-600">
                                            Bs. {{ parseFloat(pago.monto).toFixed(2) }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ pago.metodo_pago?.nombre || 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ formatDate(pago.fecha_pago) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="['px-3 py-1 rounded-full text-xs font-semibold', estadoTicketBadge(pago.pedido?.ticket?.estado)]">
                                            {{ pago.pedido?.ticket?.estado || 'Sin ticket' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <Link :href="`/pagos/${pago.id_pago}/edit`" class="text-orange-600 hover:text-orange-900 font-semibold">
                                            ✏️ Editar
                                        </Link>
                                        <button @click="eliminarPago(pago)" class="text-red-600 hover:text-red-900 font-semibold">
                                            🗑️ Eliminar
                                        </button>
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
</script>

<style scoped>
</style>
