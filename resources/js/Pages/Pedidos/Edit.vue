<template>
    <Layout>
        <div class="container mx-auto px-4 py-8 max-w-2xl">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Editar Estado del Pedido</h1>

            <form @submit.prevent="enviarFormulario" class="bg-white rounded-lg shadow-md p-6">
                <!-- N° Pedido (readonly) -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">N° Pedido</label>
                    <input type="text" :value="pedido.numero_pedido || `PED-${pedido.id_pedido}`" readonly class="w-full px-4 py-2 border border-gray-300 rounded bg-gray-100 text-gray-600">
                </div>

                <!-- Mesa (readonly) -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Mesa</label>
                    <input type="text" :value="`Mesa ${pedido.mesa?.numero}`" readonly class="w-full px-4 py-2 border border-gray-300 rounded bg-gray-100 text-gray-600">
                </div>

                <!-- Total (readonly) -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Total</label>
                    <input type="text" :value="`Bs. ${parseFloat(pedido.total).toFixed(2)}`" readonly class="w-full px-4 py-2 border border-gray-300 rounded bg-gray-100 text-gray-600 font-semibold">
                </div>

                <!-- Estado -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Estado del Pedido *</label>
                    <select v-model="formulario.estado" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                        <option value="pendiente">Pendiente</option>
                        <option value="preparando">En Preparación</option>
                        <option value="listo">Listo para Servir</option>
                        <option value="completado">Completado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                    <p v-if="errors.estado" class="text-red-600 text-sm mt-1">{{ errors.estado }}</p>
                </div>

                <!-- Información de Estados -->
                <div class="bg-blue-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-blue-900 mb-3">Flujo de Estados</h3>
                    <div class="space-y-2 text-sm text-blue-800">
                        <p><strong>Pendiente:</strong> Pedido recién creado, esperando cocina</p>
                        <p><strong>En Preparación:</strong> Cocina está preparando el pedido</p>
                        <p><strong>Listo para Servir:</strong> Platos listos, esperando mesero</p>
                        <p><strong>Completado:</strong> Pedido servido, mesa se libera</p>
                        <p><strong>Cancelado:</strong> Pedido cancelado</p>
                    </div>
                </div>

                <!-- Detalles Actuales -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Productos en el Pedido</h3>
                    <div class="space-y-2">
                        <div v-for="detalle in pedido.detalles" :key="detalle.id_detalle" class="flex justify-between items-center">
                            <span>{{ detalle.producto?.nombre }} (x{{ detalle.cantidad }})</span>
                            <span class="font-semibold">Bs. {{ parseFloat(detalle.subtotal).toFixed(2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-orange-600 to-red-700 hover:from-orange-700 hover:to-red-800 text-white font-bold py-3 px-4 rounded-lg shadow-lg">
                        ✅ Actualizar Estado
                    </button>
                    <Link :href="`/pedidos/${pedido.id_pedido}`" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center">
                        Cancelar
                    </Link>
                </div>
            </form>
        </div>
    </Layout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';

const props = defineProps({
    pedido: Object,
});

const formulario = reactive({
    estado: props.pedido.estado,
});

const errors = ref({});

const enviarFormulario = async () => {
    try {
        router.put(`/pedidos/${props.pedido.id_pedido}`, {
            estado: formulario.estado,
        }, {
            onError: (errores) => {
                errors.value = errores;
            }
        });
    } catch (error) {
        console.error('Error:', error);
    }
};
</script>
