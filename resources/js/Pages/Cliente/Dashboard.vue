<template>
    <Layout>
        <Head title="Mi Dashboard" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <!-- Saludo -->
                <div class="mb-8 bg-gradient-to-r from-orange-600 to-orange-700 rounded-lg shadow-lg p-6 text-white">
                    <h1 class="text-3xl font-bold mb-2">¡Bienvenido, {{ auth.user.nombre }}!</h1>
                    <p class="text-blue-100">El mejor servicio de churrasquería está en tus manos.</p>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium mb-2">🍡 Mis Reservas</h3>
                        <p class="text-3xl font-bold text-gray-900">{{ misReservas.length }}</p>
                        <Link href="/cliente/reservas" class="text-orange-600 text-sm mt-2 inline-block hover:text-orange-800">
                            Ver todas →
                        </Link>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium mb-2">🛒 Últimas Compras</h3>
                        <p class="text-3xl font-bold text-gray-900">{{ misPedidos.length }}</p>
                        <p class="text-sm text-gray-500 mt-1">Total gastado: {{ formatCurrency(totalGastado) }}</p>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium mb-2">⭐ Favoritos</h3>
                        <p class="text-3xl font-bold text-gray-900">5</p>
                        <Link href="/cliente/favoritos" class="text-orange-600 text-sm mt-2 inline-block hover:text-orange-800">
                            Explorar →
                        </Link>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <Link
                        href="/cliente/reservas/crear"
                        class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg shadow-lg p-8 text-white transition transform hover:scale-105 cursor-pointer block"
                    >
                        <h3 class="text-2xl font-bold mb-2">🗓️ Nueva Reserva</h3>
                        <p class="text-green-100">Reserva tu mesa para la próxima visita</p>
                    </Link>

                    <Link
                        href="/pedidos/create"
                        class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 rounded-lg shadow-lg p-8 text-white transition transform hover:scale-105 cursor-pointer block"
                    >
                        <h3 class="text-2xl font-bold mb-2">🍖 Hacer Pedido</h3>
                        <p class="text-orange-100">Ordena tus platos favoritos ahora</p>
                    </Link>
                </div>

                <!-- Próximas Reservas -->
                <div v-if="proximasReservas.length > 0" class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">📅 Próximas Reservas</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <div v-for="reserva in proximasReservas" :key="reserva.id_reserva" class="px-6 py-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Mesa para {{ reserva.numero_personas }} personas</h3>
                                    <p class="text-sm text-gray-500">{{ formatDate(reserva.fecha_reserva) }} a las {{ formatTime(reserva.hora_reserva) }}</p>
                                </div>
                                <span :class="estadoReservaClass(reserva.estado)" class="px-3 py-1 rounded-full text-sm font-semibold">
                                    {{ reserva.estado }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Últimos Pedidos -->
                <div v-if="ultimosPedidos.length > 0" class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">🛒 Últimos Pedidos</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pedido</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="pedido in ultimosPedidos" :key="pedido.id_pedido" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Link :href="`/pedidos/${pedido.id_pedido}`" class="text-orange-600 font-semibold hover:text-orange-900">
                                            #{{ pedido.id_pedido }}
                                        </Link>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(pedido.fecha_pedido) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        {{ formatCurrency(pedido.monto_total) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span :class="estadoPedidoClass(pedido.estado)" class="px-2 py-1 rounded text-xs font-semibold">
                                            {{ pedido.estado }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { computed } from 'vue';

const page = usePage();
const auth = computed(() => page.props.auth);

const props = defineProps({
    reservas: {
        type: Array,
        default: () => [],
    },
    pedidos: {
        type: Array,
        default: () => [],
    },
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
    }).format(value || 0);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-BO');
};

const formatTime = (time) => {
    return new Date(`2024-01-01 ${time}`).toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit' });
};

const misReservas = computed(() => props.reservas);

const proximasReservas = computed(() => {
    const hoy = new Date();
    return props.reservas
        .filter(r => new Date(`${r.fecha_reserva}`) >= hoy)
        .sort((a, b) => new Date(a.fecha_reserva) - new Date(b.fecha_reserva))
        .slice(0, 3);
});

const misPedidos = computed(() => props.pedidos);

const ultimosPedidos = computed(() => {
    return props.pedidos
        .sort((a, b) => new Date(b.fecha_pedido) - new Date(a.fecha_pedido))
        .slice(0, 5);
});

const totalGastado = computed(() => {
    return props.pedidos.reduce((sum, p) => sum + (p.monto_total || 0), 0);
});

const estadoReservaClass = (estado) => {
    const classes = {
        'confirmada': 'bg-green-100 text-green-800',
        'pendiente': 'bg-yellow-100 text-yellow-800',
        'cancelada': 'bg-red-100 text-red-800',
        'completada': 'bg-blue-100 text-blue-800',
    };
    return classes[estado] || 'bg-gray-100 text-gray-800';
};

const estadoPedidoClass = (estado) => {
    const classes = {
        'completado': 'bg-green-100 text-green-800',
        'pendiente': 'bg-yellow-100 text-yellow-800',
        'cancelado': 'bg-red-100 text-red-800',
        'preparacion': 'bg-blue-100 text-blue-800',
    };
    return classes[estado] || 'bg-gray-100 text-gray-800';
};
</script>
