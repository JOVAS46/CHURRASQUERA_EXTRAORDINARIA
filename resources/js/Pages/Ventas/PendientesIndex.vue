<template>
    <Layout>
        <Head title="Cuentas por Cobrar" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Cuentas por Cobrar</h1>

                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium mb-2">Total por Cobrar</h3>
                        <p class="text-3xl font-bold text-red-600">{{ formatCurrency(totalPorCobrar) }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ pendientesPago.length }} pedidos</p>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium mb-2">Cobros Hoy</h3>
                        <p class="text-3xl font-bold text-green-600">{{ formatCurrency(cobrosHoy) }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ pagosHoy }} transacciones</p>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium mb-2">Retraso Promedio</h3>
                        <p class="text-3xl font-bold text-orange-600">{{ retrasoPromedio }} días</p>
                        <p class="text-sm text-gray-500 mt-1">Sin pagar</p>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Filtros</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="filtro_estado" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                            <select
                                id="filtro_estado"
                                v-model="filtroEstado"
                                class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                            >
                                <option value="">Todos</option>
                                <option value="pendiente">Pendiente de Pago</option>
                                <option value="parcial">Pago Parcial</option>
                                <option value="vencido">Vencido</option>
                            </select>
                        </div>
                        <div>
                            <label for="filtro_cliente" class="block text-sm font-medium text-gray-700 mb-2">Cliente</label>
                            <input
                                id="filtro_cliente"
                                v-model="filtroCliente"
                                type="text"
                                placeholder="Buscar cliente..."
                                class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
                            <button
                                @click="exportarCSV"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700"
                            >
                                📥 Exportar CSV
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pedidos Pendientes -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Pedidos Pendientes de Pago</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID Pedido</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Días Retraso</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="pedido in pedidosFiltrados" :key="pedido.id_pedido" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-orange-600">
                                        <Link :href="`/pedidos/${pedido.id_pedido}`">{{ pedido.id_pedido }}</Link>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ pedido.usuario?.nombre }} {{ pedido.usuario?.apellido }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        {{ formatCurrency(pedido.monto_total) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(pedido.fecha_pedido) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" :class="diasRetraso(pedido.fecha_pedido) > 7 ? 'text-red-600 font-semibold' : 'text-gray-900'">
                                        {{ diasRetraso(pedido.fecha_pedido) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span :class="statusClass(pedido.estado)" class="px-2 py-1 rounded text-xs font-semibold">
                                            {{ pedido.estado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button
                                            @click="registrarPago(pedido)"
                                            class="text-green-600 hover:text-green-900 font-semibold"
                                        >
                                            Registrar Pago
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="pedidosFiltrados.length === 0" class="text-center py-8">
                            <p class="text-gray-500">No hay pedidos pendientes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    pedidos: {
        type: Array,
        default: () => [],
    },
    pagos: {
        type: Array,
        default: () => [],
    },
});

const filtroEstado = ref('pendiente');
const filtroCliente = ref('');

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
    }).format(value || 0);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-BO');
};

const diasRetraso = (fecha) => {
    const hoy = new Date();
    const fechaPedido = new Date(fecha);
    const diferencia = hoy - fechaPedido;
    return Math.floor(diferencia / (1000 * 60 * 60 * 24));
};

const statusClass = (estado) => {
    const classes = {
        'pendiente': 'bg-red-100 text-red-800',
        'parcial': 'bg-yellow-100 text-yellow-800',
        'vencido': 'bg-red-200 text-red-900',
        'pagado': 'bg-green-100 text-green-800',
    };
    return classes[estado] || 'bg-gray-100 text-gray-800';
};

const pendientesPago = computed(() => {
    // Get unique pedidos that haven't been fully paid
    const pagosMap = {};
    props.pagos.forEach(p => {
        pagosMap[p.id_venta] = (pagosMap[p.id_venta] || 0) + p.monto;
    });

    return props.pedidos.filter(p => {
        const pagado = pagosMap[p.id_pedido] || 0;
        return pagado < p.monto_total;
    });
});

const totalPorCobrar = computed(() => {
    return pendientesPago.value.reduce((sum, p) => {
        const pagado = props.pagos
            .filter(pago => pago.id_venta === p.id_pedido)
            .reduce((s, pago) => s + pago.monto, 0);
        return sum + (p.monto_total - pagado);
    }, 0);
});

const cobrosHoy = computed(() => {
    const hoy = new Date().toDateString();
    return props.pagos
        .filter(p => new Date(p.fecha_pago).toDateString() === hoy)
        .reduce((sum, p) => sum + p.monto, 0);
});

const pagosHoy = computed(() => {
    const hoy = new Date().toDateString();
    return props.pagos.filter(p => new Date(p.fecha_pago).toDateString() === hoy).length;
});

const retrasoPromedio = computed(() => {
    if (pendientesPago.value.length === 0) return 0;
    const totalDias = pendientesPago.value.reduce((sum, p) => sum + diasRetraso(p.fecha_pedido), 0);
    return Math.round(totalDias / pendientesPago.value.length);
});

const pedidosFiltrados = computed(() => {
    return pendientesPago.value.filter(p => {
        const cliente = `${p.usuario?.nombre} ${p.usuario?.apellido}`.toLowerCase();
        const coincideCliente = !filtroCliente.value || cliente.includes(filtroCliente.value.toLowerCase());
        return coincideCliente;
    });
});

const registrarPago = (pedido) => {
    router.visit(`/pagos/create?id_pedido=${pedido.id_pedido}`);
};

const exportarCSV = () => {
    // Future: Implement CSV export
    alert('Exportación CSV - Próximamente');
};
</script>
