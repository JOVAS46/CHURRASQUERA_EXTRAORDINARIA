<template>
    <Layout>
        <!-- Page Title -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-orange-600 mt-2">🔥 Bienvenido a ROBERTO CHURRASQUERIA | Sistema de Gestión</p>
        </div>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Pedidos -->
            <div class="bg-white rounded-lg shadow-lg border-2 border-orange-600 p-6 hover:shadow-xl transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Total Pedidos</p>
                        <p class="text-4xl font-bold text-orange-600 mt-2">{{ estadisticas.total_pedidos }}</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-red-600 rounded-lg flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Mesas Ocupadas -->
            <div class="bg-white rounded-lg shadow-lg border-2 border-orange-600 p-6 hover:shadow-xl transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Mesas Ocupadas</p>
                        <p class="text-4xl font-bold text-orange-600 mt-2">{{ estadisticas.mesas_ocupadas }}</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-red-700 rounded-lg flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Productos -->
            <div class="bg-white rounded-lg shadow-lg border-2 border-orange-600 p-6 hover:shadow-xl transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Productos</p>
                        <p class="text-4xl font-bold text-orange-600 mt-2">{{ estadisticas.total_productos }}</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-orange-600 rounded-lg flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Usuarios -->
            <div class="bg-white rounded-lg shadow-lg border-2 border-orange-600 p-6 hover:shadow-xl transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Usuarios</p>
                        <p class="text-4xl font-bold text-orange-600 mt-2">{{ estadisticas.total_usuarios }}</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-600 to-red-800 rounded-lg flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 0a2 2 0 11-4 0 2 2 0 014 0zM5 20a2 2 0 002-2v-2a3 3 0 010-6 2 2 0 00-2-2 3 3 0 00-3 3v2a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Orders Alert -->
        <div v-if="estadisticas.pedidos_pendientes > 0" class="bg-red-50 border-2 border-red-600 rounded-lg p-4 mb-8">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-red-800 font-bold">⚠️ {{ estadisticas.pedidos_pendientes }} Pedidos Pendientes</p>
                    <p class="text-red-700 text-sm">Hay pedidos en espera de preparación</p>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-lg border-2 border-orange-600">
            <div class="px-6 py-4 border-b-2 border-orange-600 bg-gradient-to-r from-orange-50 to-red-50">
                <h2 class="text-lg font-bold text-orange-600">📋 Pedidos Recientes</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900 border-b-2 border-orange-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-orange-400 uppercase">Número</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-orange-400 uppercase">Mesa</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-orange-400 uppercase">Mesero</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-orange-400 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-orange-400 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-orange-400 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-orange-200">
                        <tr v-for="pedido in pedidos_recientes" :key="pedido.id_pedido" class="hover:bg-orange-50 transition">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">#{{ pedido.numero_pedido }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ pedido.mesa?.numero_mesa }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ pedido.usuario?.nombre }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ formatDate(pedido.fecha_pedido) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span :class="getStatusColor(pedido.estado)" class="px-3 py-1 rounded-full text-xs font-bold">
                                    {{ pedido.estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-orange-600">Bs. {{ pedido.total }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { defineProps } from 'vue';

defineProps({
    estadisticas: {
        type: Object,
        required: true,
    },
    pedidos_recientes: {
        type: Array,
        required: true,
    },
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-BO', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusColor = (status) => {
    const colors = {
        'pendiente': 'bg-red-100 text-red-800',
        'preparando': 'bg-orange-100 text-orange-800',
        'listo': 'bg-green-100 text-green-800',
        'completado': 'bg-gray-100 text-gray-800',
        'cancelado': 'bg-red-200 text-red-900',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<style scoped>
</style>
