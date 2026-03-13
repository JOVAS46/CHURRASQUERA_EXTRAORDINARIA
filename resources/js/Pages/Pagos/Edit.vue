<template>
    <Layout>
        <div class="container mx-auto px-4 py-8 max-w-2xl">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Editar Pago</h1>

            <form @submit.prevent="enviarFormulario" class="bg-white rounded-lg shadow-md p-6">
                <!-- N° Pago (readonly) -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">N° Pago</label>
                    <input type="text" :value="props.pago.numero_pago" readonly class="w-full px-4 py-2 border border-gray-300 rounded bg-gray-100 text-gray-600 font-mono">
                </div>

                <!-- Pedido (readonly) -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Pedido</label>
                    <input type="text" :value="props.pago.pedido?.numero_pedido" readonly class="w-full px-4 py-2 border border-gray-300 rounded bg-gray-100 text-gray-600 font-mono">
                </div>

                <!-- Monto (readonly) -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Monto</label>
                    <input type="text" :value="`Bs. ${parseFloat(props.pago.monto).toFixed(2)}`" readonly class="w-full px-4 py-2 border border-gray-300 rounded bg-gray-100 text-gray-600 font-semibold">
                </div>

                <!-- Método de Pago -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Método de Pago *</label>
                    <select v-model="formulario.id_metodo_pago" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                        <option value="">Selecciona un método</option>
                        <option v-for="metodo in props.metodos_pago" :key="metodo.id_metodo_pago" :value="metodo.id_metodo_pago">
                            {{ metodo.nombre }}
                        </option>
                    </select>
                    <p v-if="errors.id_metodo_pago" class="text-red-600 text-sm mt-1">{{ errors.id_metodo_pago }}</p>
                </div>

                <!-- Referencia -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Referencia (opcional)</label>
                    <input type="text" v-model="formulario.referencia" placeholder="N° cheque, N° transacción, etc." class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                </div>

                <!-- Estado -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Estado *</label>
                    <select v-model="formulario.estado" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                        <option value="pendiente">Pendiente</option>
                        <option value="completado">Completado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                    <p v-if="errors.estado" class="text-red-600 text-sm mt-1">{{ errors.estado }}</p>
                </div>

                <!-- Observaciones -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Observaciones</label>
                    <textarea v-model="formulario.observaciones" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded" placeholder="Notas adicionales..."></textarea>
                </div>

                <!-- Botones -->
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-orange-600 to-red-700 hover:from-orange-700 hover:to-red-800 text-white font-bold py-3 px-4 rounded-lg shadow-lg">
                        <i class="fas fa-check-circle mr-2"></i> Actualizar Pago
                    </button>
                    <Link :href="`/pagos/${props.pago.id_pago}`" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center">
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
    pago: Object,
    metodos_pago: Array,
});

const formulario = reactive({
    id_metodo_pago: props.pago.id_metodo_pago,
    referencia: props.pago.referencia,
    estado: props.pago.estado,
    observaciones: props.pago.observaciones,
});

const errors = ref({});

const enviarFormulario = async () => {
    try {
        router.put(`/pagos/${props.pago.id_pago}`, {
            id_metodo_pago: parseInt(formulario.id_metodo_pago),
            monto: parseFloat(props.pago.monto),
            referencia: formulario.referencia,
            estado: formulario.estado,
            observaciones: formulario.observaciones,
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
