<template>
    <Layout>
        <Head title="Registrar Pago" />
        
        <div class="py-6">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 md:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Registrar Nuevo Pago</h1>

                <form @submit.prevent="crearPago" class="bg-white rounded-lg shadow-sm p-6">
                    <!-- Pedido -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Selecciona un Pedido *</label>
                        <select 
                            v-model="formulario.id_pedido"
                            @change="obtenerDetallesPedido"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            required
                        >
                            <option value="">Selecciona un pedido pendiente</option>
                            <option v-for="pedido in pedidos" :key="pedido.id_pedido" :value="pedido.id_pedido">
                                Pedido #{{ pedido.id_pedido }} - Mesa {{ pedido.mesa?.numero_mesa }} - Bs. {{ parseFloat(pedido.total).toFixed(2) }}
                            </option>
                        </select>
                        <p v-if="errores.id_pedido" class="text-red-600 text-sm mt-1">{{ errores.id_pedido[0] }}</p>
                    </div>

                    <!-- Información del Pedido -->
                    <div v-if="detallesPedido" class="bg-orange-50 rounded-lg p-4 mb-6 border border-orange-200">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 font-semibold">Total Pedido</p>
                                <p class="text-lg font-bold text-gray-900">Bs. {{ parseFloat(detallesPedido.total).toFixed(2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-semibold">Ya Pagado</p>
                                <p class="text-lg font-bold text-green-600">Bs. {{ parseFloat(detallesPedido.pagado).toFixed(2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-semibold">Pendiente</p>
                                <p class="text-lg font-bold text-orange-600">Bs. {{ parseFloat(detallesPedido.pendiente).toFixed(2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Método de Pago -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Método de Pago *</label>
                        <select 
                            v-model.number="formulario.id_metodo_pago"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            required
                        >
                            <option value="">Selecciona un método</option>
                            <option v-for="metodo in metodos_pago" :key="metodo.id_metodo_pago" :value="metodo.id_metodo_pago">
                                {{ metodo.nombre }}
                            </option>
                        </select>
                        <p v-if="errores.id_metodo_pago" class="text-red-600 text-sm mt-1">{{ errores.id_metodo_pago[0] }}</p>
                    </div>

                    <!-- Monto -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Monto a Pagar *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-700 font-semibold">Bs.</span>
                            <input 
                                v-model.number="formulario.monto" 
                                type="number"
                                min="0.01"
                                step="0.01"
                                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                                required
                            />
                        </div>
                        <div v-if="detallesPedido" class="mt-2 text-sm">
                            <p v-if="formulario.monto > detallesPedido.pendiente" class="text-orange-600">
                                <i class="fas fa-exclamation-circle mr-1 text-yellow-600"></i> Vuelto: Bs. {{ (formulario.monto - detallesPedido.pendiente).toFixed(2) }}
                            </p>
                            <p v-else-if="formulario.monto < detallesPedido.pendiente" class="text-yellow-600">
                                ⓘ Falta: Bs. {{ (detallesPedido.pendiente - formulario.monto).toFixed(2) }}
                            </p>
                            <p v-else class="text-green-600 font-semibold">
                                <i class="fas fa-check text-green-500 mr-1"></i> Pago exacto
                            </p>
                        </div>
                        <p v-if="errores.monto" class="text-red-600 text-sm mt-1">{{ errores.monto[0] }}</p>
                    </div>

                    <!-- Referencia -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Referencia (Opcional)</label>
                        <input 
                            v-model="formulario.referencia" 
                            type="text"
                            placeholder="N° cheque, transferencia, etc."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                        />
                        <p v-if="errores.referencia" class="text-red-600 text-sm mt-1">{{ errores.referencia[0] }}</p>
                    </div>

                    <!-- Observaciones -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Observaciones</label>
                        <textarea 
                            v-model="formulario.observaciones"
                            rows="3"
                            placeholder="Notas o detalles adicionales..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                        ></textarea>
                        <p v-if="errores.observaciones" class="text-red-600 text-sm mt-1">{{ errores.observaciones[0] }}</p>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-4">
                        <button 
                            type="submit"
                            class="flex-1 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-semibold transition"
                        >
                            Registrar Pago
                        </button>
                        <Link 
                            href="/pagos"
                            class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-semibold text-center transition"
                        >
                            Cancelar
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { ref, reactive } from 'vue';

const props = defineProps({
    pedidos: {
        type: Array,
        required: true,
    },
    metodos_pago: {
        type: Array,
        required: true,
    },
});

const formulario = reactive({
    id_pedido: '',
    id_metodo_pago: '',
    monto: 0,
    referencia: '',
    observaciones: '',
});

const errores = ref({});
const detallesPedido = ref(null);

const obtenerDetallesPedido = async () => {
    if (formulario.id_pedido) {
        try {
            const response = await fetch(`/api/pedido-pendiente?id_pedido=${formulario.id_pedido}`);
            const data = await response.json();
            detallesPedido.value = data;
            formulario.monto = parseFloat(data.pendiente.toFixed(2));
        } catch (error) {
            console.error('Error:', error);
        }
    }
};

const crearPago = () => {
    errores.value = {};
    
    router.post('/pagos', {
        id_pedido: parseInt(formulario.id_pedido),
        id_metodo_pago: parseInt(formulario.id_metodo_pago),
        monto: formulario.monto,
        referencia: formulario.referencia,
        observaciones: formulario.observaciones,
    }, {
        onError: (erroresApi) => {
            errores.value = erroresApi;
        }
    });
};
</script>

<style scoped>
</style>
