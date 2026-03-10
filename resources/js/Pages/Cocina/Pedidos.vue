<template>
    <Layout>
        <div class="min-h-screen bg-gradient-to-br from-orange-50 to-gray-100 p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Encabezado -->
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">🍳 Cocina - Tablero de Pedidos</h1>
                    <p class="text-gray-600">Arrastra los pedidos para cambiar su estado</p>
                </div>

                <!-- Mensaje de éxito -->
                <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg animate-bounce">
                    ✅ {{ $page.props.flash.success }}
                </div>

                <!-- Kanban Board -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Columna: Pendiente -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-all">
                        <div class="bg-yellow-500 text-white px-6 py-4">
                            <h2 class="text-xl font-bold">⏳ Pendiente</h2>
                            <p class="text-sm text-yellow-100">{{ pedidos.pendiente?.length || 0 }} pedidos</p>
                        </div>
                        <div 
                            @dragover.prevent="dragoverEstado = 'pendiente'"
                            @dragleave.prevent="dragoverEstado = null"
                            @drop.prevent="handleDrop($event, 'pendiente')"
                            class="p-4 min-h-96 bg-yellow-50 transition-all"
                            :class="{ 'ring-4 ring-yellow-300 bg-yellow-100': dragoverEstado === 'pendiente' }"
                        >
                            <div v-if="pedidos.pendiente?.length === 0" class="text-center py-8 text-gray-500">
                                Sin pedidos
                            </div>
                            <div v-for="pedido in pedidos.pendiente" :key="pedido.id_pedido" class="mb-4">
                                <PedidoCard :pedido="pedido" estado-actual="pendiente" />
                            </div>
                        </div>
                    </div>

                    <!-- Columna: En Preparación -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-all">
                        <div class="bg-blue-500 text-white px-6 py-4">
                            <h2 class="text-xl font-bold">👨‍🍳 En Preparación</h2>
                            <p class="text-sm text-blue-100">{{ pedidos.en_preparacion?.length || 0 }} pedidos</p>
                        </div>
                        <div 
                            @dragover.prevent="dragoverEstado = 'en_preparacion'"
                            @dragleave.prevent="dragoverEstado = null"
                            @drop.prevent="handleDrop($event, 'en_preparacion')"
                            class="p-4 min-h-96 bg-blue-50 transition-all"
                            :class="{ 'ring-4 ring-blue-300 bg-blue-100': dragoverEstado === 'en_preparacion' }"
                        >
                            <div v-if="pedidos.en_preparacion?.length === 0" class="text-center py-8 text-gray-500">
                                Sin pedidos
                            </div>
                            <div v-for="pedido in pedidos.en_preparacion" :key="pedido.id_pedido" class="mb-4">
                                <PedidoCard :pedido="pedido" estado-actual="en_preparacion" />
                            </div>
                        </div>
                    </div>

                    <!-- Columna: Listo -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-all">
                        <div class="bg-green-500 text-white px-6 py-4">
                            <h2 class="text-xl font-bold">✅ Listo para Servir</h2>
                            <p class="text-sm text-green-100">{{ pedidos.listo?.length || 0 }} pedidos</p>
                        </div>
                        <div 
                            @dragover.prevent="dragoverEstado = 'listo'"
                            @dragleave.prevent="dragoverEstado = null"
                            @drop.prevent="handleDrop($event, 'listo')"
                            class="p-4 min-h-96 bg-green-50 transition-all"
                            :class="{ 'ring-4 ring-green-300 bg-green-100': dragoverEstado === 'listo' }"
                        >
                            <div v-if="pedidos.listo?.length === 0" class="text-center py-8 text-gray-500">
                                Sin pedidos
                            </div>
                            <div v-for="pedido in pedidos.listo" :key="pedido.id_pedido" class="mb-4">
                                <PedidoCard :pedido="pedido" estado-actual="listo" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import PedidoCard from './PedidoCard.vue';

const props = defineProps({
    pedidos: Object,
});

const page = usePage();
const dragoverEstado = ref(null);

const handleDrop = (event, nuevoEstado) => {
    event.preventDefault();
    event.stopPropagation();
    
    dragoverEstado.value = null;

    // Intentar leer de diferentes formatos
    const pedidoId = event.dataTransfer.getData('application/pedidoId') || 
                     event.dataTransfer.getData('pedidoId') || 
                     event.dataTransfer.getData('text/plain');
                     
    const estadoActual = event.dataTransfer.getData('application/estadoActual') || 
                         event.dataTransfer.getData('estadoActual');

    if (!pedidoId) {
        console.error('No se obtuvo pedidoId del drag');
        return;
    }

    console.log(`Moviendo pedido ${pedidoId} de ${estadoActual} a ${nuevoEstado}`);

    // Evitar soltar en la misma columna
    if (estadoActual === nuevoEstado) {
        return;
    }

    // Usar URL absoluta en lugar de route
    router.patch(`/cocina/pedidos/${pedidoId}`, {
        estado: nuevoEstado,
    }, {
        onSuccess: () => {
            // Recargar para ver cambios
            router.reload();
        },
        onError: (error) => {
            console.error('Error actualizando pedido:', error);
        }
    });
};
</script>
