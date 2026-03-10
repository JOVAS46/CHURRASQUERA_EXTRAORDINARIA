<template>
    <Layout>
        <Head title="Arqueo de Caja" />
        
        <div class="py-6">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 md:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Arqueo de Caja</h1>
                    <button @click="newArqueo" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                        + Nuevo Arqueo
                    </button>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium">Efectivo Actual</h3>
                        <p class="text-3xl font-bold text-gray-900">{{ formatCurrency(totalEfectivo) }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium">Total Vendido</h3>
                        <p class="text-3xl font-bold text-green-600">{{ formatCurrency(totalVendido) }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium">Total Pagado</h3>
                        <p class="text-3xl font-bold text-orange-600">{{ formatCurrency(totalPagado) }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium">Diferencia</h3>
                        <p :class="diferencia >= 0 ? 'text-green-600' : 'text-red-600'" class="text-3xl font-bold">
                            {{ formatCurrency(diferencia) }}
                        </p>
                    </div>
                </div>

                <!-- Arqueos History -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Historico de Arqueos</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Efectivo Reportado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Efectivo Sistema</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diferencia</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-if="arqueos.length === 0" class="hover:bg-gray-50">
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Sin registros</td>
                                </tr>
                                <tr v-for="arqueo in arqueos" :key="arqueo.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatDate(arqueo.fecha) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ arqueo.usuario_nombre }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatCurrency(arqueo.efectivo_reportado) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatCurrency(arqueo.efectivo_sistema) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" :class="arqueo.diferencia >= 0 ? 'text-green-600' : 'text-red-600'">
                                        {{ formatCurrency(arqueo.diferencia) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">
                                            Completado
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
import { Head, router } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { computed } from 'vue';

const props = defineProps({
    cajas: { type: Array, default: () => [] },
    pagos: { type: Array, default: () => [] },
    pedidos: { type: Array, default: () => [] },
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

const totalEfectivo = computed(() => {
    return props.cajas.reduce((sum, caja) => sum + (caja.saldo || 0), 0);
});

const totalVendido = computed(() => {
    return props.pedidos.reduce((sum, pedido) => sum + (pedido.monto_total || 0), 0);
});

const totalPagado = computed(() => {
    return props.pagos.reduce((sum, pago) => sum + (pago.monto || 0), 0);
});

const diferencia = computed(() => {
    return totalEfectivo.value - totalVendido.value;
});

const arqueos = computed(() => {
    if (props.cajas.length === 0) return [];
    return props.cajas.map(caja => ({
        ...caja,
        diferencia: caja.saldo - (totalVendido.value / props.cajas.length),
    }));
});

const newArqueo = () => {
    // Future: Implement new arqueo modal
    console.log('Nuevo arqueo');
};
</script>
