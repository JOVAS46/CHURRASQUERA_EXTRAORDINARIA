<template>
    <Layout>
        <Head title="Gestión de Pedidos" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">📋 Gestión de Pedidos</h1>
                    <Link href="/pedidos/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                        + Nuevo Pedido
                    </Link>
                </div>

                <!-- Mensaje de éxito -->
                <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    ✅ {{ $page.props.flash.success }}
                </div>

                <!-- Tabla de Pedidos -->
                <div v-if="pedidos.data && pedidos.data.length > 0" class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID Pedido</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mesa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="pedido in pedidos.data" :key="pedido.id_pedido" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm font-mono font-semibold text-gray-900">{{ pedido.numero_pedido || `PED-${pedido.id_pedido}` }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <div>
                                            <p class="font-semibold">Mesa {{ pedido.mesa?.numero_mesa }}</p>
                                            <p class="text-xs text-gray-500">📍 {{ pedido.mesa?.ubicacion }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ formatDate(pedido.fecha_pedido) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm font-bold text-orange-600">
                                            Bs. {{ parseFloat(pedido.total).toFixed(2) }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="['px-3 py-1 rounded-full text-xs font-semibold', estadoBadge(pedido.estado)]">
                                            {{ pedido.estado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                        <Link :href="`/pedidos/${pedido.id_pedido}/edit`" class="text-orange-600 hover:text-orange-700 font-semibold">
                                            ✏️ Editar
                                        </Link>
                                        <button @click="deletePedido(pedido)" class="text-red-600 hover:text-red-700 font-semibold">
                                            🗑️ Eliminar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mensaje Vacío -->
                <div v-else class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <p class="text-gray-500 text-lg mb-6">No hay pedidos registrados</p>
                    <Link href="/pedidos/create" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                        📋 Crear Pedido
                    </Link>
                </div>

                <!-- Paginación -->
                <div v-if="pedidos.links" class="mt-6 flex justify-center gap-2">
                    <template v-for="link in pedidos.links" :key="link.label">
                        <Link 
                            v-if="link.url"
                            :href="link.url"
                            :class="[
                                'px-3 py-2 rounded-lg text-sm border transition',
                                link.active
                                    ? 'bg-orange-600 text-white border-orange-600'
                                    : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                            ]"
                        >
                            <span v-html="link.label"></span>
                        </Link>
                        <span 
                            v-else
                            :class="[
                                'px-3 py-2 text-sm border cursor-not-allowed',
                                link.active 
                                    ? 'bg-orange-600 text-white border-orange-600'
                                    : 'border-gray-300 text-gray-500'
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
    pedidos: {
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
        'abierto': 'bg-yellow-100 text-yellow-800',
        'pendiente': 'bg-yellow-100 text-yellow-800',
        'cerrado': 'bg-blue-100 text-blue-800',
        'completado': 'bg-green-100 text-green-800',
        'cancelado': 'bg-red-100 text-red-800',
    };
    return colores[estado] || 'bg-gray-100 text-gray-800';
};

const deletePedido = (pedido) => {
    if (confirm(`¿Estás seguro de que deseas eliminar el pedido #${pedido.numero_pedido || pedido.id_pedido}?`)) {
        router.delete(`/pedidos/${pedido.id_pedido}`);
    }
};
</script>

<style scoped>
</style>
