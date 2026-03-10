<template>
    <Layout>
        <Head title="Pedidos en Cocina" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">🍳 Pedidos en Cocina</h1>

                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium mb-2">Pendientes Prepararar</h3>
                        <p class="text-3xl font-bold text-red-600">{{ pendientes.length }}</p>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium mb-2">En Preparación</h3>
                        <p class="text-3xl font-bold text-yellow-600">{{ enPreparacion.length }}</p>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium mb-2">Listos para Servir</h3>
                        <p class="text-3xl font-bold text-green-600">{{ listos.length }}</p>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-gray-500 text-sm font-medium mb-2">Promedio Tiempo</h3>
                        <p class="text-3xl font-bold text-orange-600">{{ tiempoPromedio }} min</p>
                    </div>
                </div>

                <!-- Filtro -->
                <div class="mb-6">
                    <div class="flex gap-4">
                        <button
                            v-for="filtro in filtros"
                            :key="filtro"
                            @click="filtroActual = filtro"
                            :class="[
                                'px-4 py-2 rounded-lg font-semibold transition',
                                filtroActual === filtro
                                    ? 'bg-orange-600 text-white'
                                    : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                            ]"
                        >
                            {{ filtro === 'pendiente' ? '⏳ Pendientes' : filtro === 'preparacion' ? '👨‍🍳 En Cocina' : '✓ Listos' }}
                        </button>
                    </div>
                </div>

                <!-- Pedidos -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div v-for="pedido in pedidosFiltrados" :key="pedido.id_pedido" class="bg-white rounded-lg shadow-lg border-l-4" :class="borderColor(pedido.estado)">
                        <div class="p-6">
                            <!-- Header -->
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">Pedido #{{ pedido.id_pedido }}</h3>
                                    <p class="text-sm text-gray-500">Mesa {{ pedido.id_mesa || 'Mostrador' }}</p>
                                </div>
                                <span :class="badgeColor(pedido.estado)" class="px-3 py-1 rounded-full text-sm font-semibold">
                                    {{ estadoLabel(pedido.estado) }}
                                </span>
                            </div>

                            <!-- Items -->
                            <div class="mb-6 bg-gray-50 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Items a Preparar:</h4>
                                <div v-for="detalle in pedido.detalles" :key="detalle.id_detalle_pedido" class="flex justify-between items-center py-2 border-b border-gray-200 last:border-0">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ detalle.producto?.nombre }}</p>
                                        <p class="text-sm text-gray-500">{{ detalle.cantidad }}x</p>
                                    </div>
                                    <p v-if="detalle.observaciones" class="text-sm text-orange-600 font-semibold">
                                        📝 {{ detalle.observaciones }}
                                    </p>
                                </div>
                            </div>

                            <!-- Tiempo -->
                            <div class="mb-6 grid grid-cols-2 gap-4">
                                <div class="bg-blue-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-600 uppercase">Hora Pedido</p>
                                    <p class="text-lg font-bold text-orange-600">{{ formatTime(pedido.fecha_pedido) }}</p>
                                </div>
                                <div :class="['rounded-lg p-3', tiempoTranscurrido(pedido.fecha_pedido) > 30 ? 'bg-red-50' : 'bg-green-50']">
                                    <p class="text-xs text-gray-600 uppercase">Tiempo Transcurrido</p>
                                    <p :class="['text-lg font-bold', tiempoTranscurrido(pedido.fecha_pedido) > 30 ? 'text-red-600' : 'text-green-600']">
                                        {{ tiempoTranscurrido(pedido.fecha_pedido) }} min
                                    </p>
                                </div>
                            </div>

                            <!-- Botones de Acción -->
                            <div class="flex gap-3">
                                <button
                                    v-if="pedido.estado === 'pendiente'"
                                    @click="cambiarEstado(pedido, 'preparacion')"
                                    class="flex-1 bg-yellow-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-yellow-700 transition"
                                >
                                    👨‍🍳 Comenzar Preparación
                                </button>
                                
                                <button
                                    v-if="pedido.estado === 'preparacion'"
                                    @click="cambiarEstado(pedido, 'listo')"
                                    class="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-green-700 transition"
                                >
                                    ✓ Listo para Servir
                                </button>

                                <button
                                    v-if="pedido.estado !== 'listo'"
                                    @click="verDetalles(pedido)"
                                    class="flex-1 bg-orange-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-orange-700 transition"
                                >
                                    👁️ Detalles
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="pedidosFiltrados.length === 0" class="text-center py-12 bg-white rounded-lg shadow">
                    <p class="text-gray-500 text-lg">😊 ¡No hay pedidos en este estado!</p>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    pedidos: {
        type: Array,
        default: () => [],
    },
});

const filtroActual = ref('pendiente');
const filtros = ['pendiente', 'preparacion', 'listo'];

const formatTime = (fecha) => {
    const date = new Date(fecha);
    return date.toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit' });
};

const tiempoTranscurrido = (fecha) => {
    const ahora = new Date();
    const fechaPedido = new Date(fecha);
    const diferencia = ahora - fechaPedido;
    return Math.floor(diferencia / (1000 * 60)); // Minutos
};

const borderColor = (estado) => {
    return {
        'pendiente': 'border-red-500',
        'preparacion': 'border-yellow-500',
        'listo': 'border-green-500',
    }[estado] || 'border-gray-300';
};

const badgeColor = (estado) => {
    return {
        'pendiente': 'bg-red-100 text-red-800',
        'preparacion': 'bg-yellow-100 text-yellow-800',
        'listo': 'bg-green-100 text-green-800',
    }[estado] || 'bg-gray-100 text-gray-800';
};

const estadoLabel = (estado) => {
    return {
        'pendiente': '⏳ Pendiente',
        'preparacion': '👨‍🍳 En Preparación',
        'listo': '✓ Listo',
    }[estado] || estado;
};

const pendientes = computed(() => props.pedidos.filter(p => p.estado === 'pendiente'));
const enPreparacion = computed(() => props.pedidos.filter(p => p.estado === 'preparacion'));
const listos = computed(() => props.pedidos.filter(p => p.estado === 'listo'));

const tiempoPromedio = computed(() => {
    const enCocina = [...enPreparacion.value, ...listos.value];
    if (enCocina.length === 0) return 0;
    const totalTiempo = enCocina.reduce((sum, p) => sum + tiempoTranscurrido(p.fecha_pedido), 0);
    return Math.round(totalTiempo / enCocina.length);
});

const pedidosFiltrados = computed(() => {
    const mapeo = {
        'pendiente': pendientes,
        'preparacion': enPreparacion,
        'listo': listos,
    };
    return mapeo[filtroActual.value]?.value || [];
});

const cambiarEstado = (pedido, nuevoEstado) => {
    router.patch(`/pedidos/${pedido.id_pedido}`, {
        estado: nuevoEstado,
    });
};

const verDetalles = (pedido) => {
    router.visit(`/pedidos/${pedido.id_pedido}`);
};
</script>
