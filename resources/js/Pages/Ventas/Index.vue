<template>
    <Layout>
        <Head title="Ventas" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Resumen de Ventas</h1>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium mb-2">Ingresos Hoy</h3>
                        <p class="text-3xl font-bold text-gray-900">{{ formatCurrency(todaySales.total) }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ todaySales.count }} pedidos</p>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium mb-2">Ingresos Este Mes</h3>
                        <p class="text-3xl font-bold text-gray-900">{{ formatCurrency(monthSales.total) }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ monthSales.count }} pedidos</p>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium mb-2">Ticket Promedio</h3>
                        <p class="text-3xl font-bold text-gray-900">{{ formatCurrency(averageTicket) }}</p>
                        <p class="text-sm text-gray-500 mt-1">Por pedido</p>
                    </div>
                </div>

                <!-- Recent Sales -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Últimas Ventas</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID Pedido</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mesa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="venta in recientesVentas" :key="venta.id_pedido" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-orange-600">
                                        <Link :href="`/pedidos/${venta.id_pedido}`">{{ venta.id_pedido }}</Link>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ venta.usuario?.nombre }} {{ venta.usuario?.apellido }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Mesa {{ venta.id_mesa || '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ formatCurrency(venta.monto_total || 0) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(venta.fecha_pedido) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span :class="statusClass(venta.estado)" class="px-2 py-1 rounded text-xs font-semibold">
                                            {{ venta.estado }}
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
import { Head, Link } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { computed } from 'vue';

const props = defineProps({
    ventas: Array,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
    }).format(value);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-BO');
};

const today = new Date().toDateString();
const currentMonth = new Date().getMonth();
const currentYear = new Date().getFullYear();

const todaySales = computed(() => {
    const ventas_hoy = props.ventas.filter(v => 
        new Date(v.fecha_pedido).toDateString() === today
    );
    return {
        total: ventas_hoy.reduce((sum, v) => sum + (v.monto_total || 0), 0),
        count: ventas_hoy.length,
    };
});

const monthSales = computed(() => {
    const ventas_mes = props.ventas.filter(v => {
        const date = new Date(v.fecha_pedido);
        return date.getMonth() === currentMonth && date.getFullYear() === currentYear;
    });
    return {
        total: ventas_mes.reduce((sum, v) => sum + (v.monto_total || 0), 0),
        count: ventas_mes.length,
    };
});

const averageTicket = computed(() => {
    if (props.ventas.length === 0) return 0;
    const total = props.ventas.reduce((sum, v) => sum + (v.monto_total || 0), 0);
    return total / props.ventas.length;
});

const recientesVentas = computed(() => {
    return props.ventas.sort((a, b) => 
        new Date(b.fecha_pedido) - new Date(a.fecha_pedido)
    ).slice(0, 10);
});

const statusClass = (estado) => {
    const classes = {
        'completado': 'bg-green-100 text-green-800',
        'pendiente': 'bg-yellow-100 text-yellow-800',
        'cancelado': 'bg-red-100 text-red-800',
    };
    return classes[estado] || 'bg-gray-100 text-gray-800';
};
</script>
