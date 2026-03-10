<template>
    <Layout>
        <!-- Page Title -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Crear Nueva Mesa</h1>
            <p class="text-gray-600 mt-2">Añade una nueva mesa al restaurante</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-2xl">
            <form @submit.prevent="enviarFormulario">
                <!-- Número de Mesa -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Número de Mesa *</label>
                    <input 
                        v-model.number="formulario.numero_mesa"
                        type="number" 
                        placeholder="Ej: 1, 2, 3..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                        required
                        min="1"
                    />
                    <p v-if="errores.numero_mesa" class="text-red-600 text-xs mt-1">{{ errores.numero_mesa[0] }}</p>
                </div>

                <!-- Capacidad -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Capacidad (personas) *</label>
                    <input 
                        v-model.number="formulario.capacidad"
                        type="number" 
                        placeholder="Ej: 2, 4, 6..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                        required
                        min="1"
                        max="20"
                    />
                    <p v-if="errores.capacidad" class="text-red-600 text-xs mt-1">{{ errores.capacidad[0] }}</p>
                </div>

                <!-- Ubicación -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Ubicación *</label>
                    <textarea 
                        v-model="formulario.ubicacion"
                        placeholder="Ej: Terraza Central - Zona 1"
                        rows="2"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                        required
                    ></textarea>
                    <p v-if="errores.ubicacion" class="text-red-600 text-xs mt-1">{{ errores.ubicacion[0] }}</p>
                </div>

                <!-- Estado -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Estado Inicial *</label>
                    <select 
                        v-model="formulario.estado"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                        required
                    >
                        <option value="">Seleccionar estado...</option>
                        <option value="disponible">Disponible</option>
                        <option value="ocupada">Ocupada</option>
                        <option value="mantenimiento">Mantenimiento</option>
                    </select>
                    <p v-if="errores.estado" class="text-red-600 text-xs mt-1">{{ errores.estado[0] }}</p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <button 
                        type="submit"
                        class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg font-semibold"
                    >
                        Guardar Mesa
                    </button>
                    <Link 
                        href="/mesas"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg font-semibold"
                    >
                        Cancelar
                    </Link>
                </div>
            </form>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const formulario = reactive({
    numero_mesa: '',
    capacidad: '',
    ubicacion: '',
    estado: 'disponible',
});

const errores = ref({});

const enviarFormulario = () => {
    router.post('/mesas', formulario, {
        onError: (errors) => {
            errores.value = errors;
        },
    });
};
</script>

<style scoped>
</style>
