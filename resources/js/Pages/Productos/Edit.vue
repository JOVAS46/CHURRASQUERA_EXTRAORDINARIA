<template>
    <Layout>
        <!-- Page Title -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Editar Producto</h1>
            <p class="text-gray-600 mt-2">Modifica la información del producto</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-2xl">
            <form @submit.prevent="enviarFormulario">
                <!-- Nombre -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Nombre del Producto *</label>
                    <input 
                        v-model="formulario.nombre"
                        type="text" 
                        placeholder="Ej: Milanesa de Pollo"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                        required
                    />
                    <p v-if="errores.nombre" class="text-red-600 text-xs mt-1">{{ errores.nombre[0] }}</p>
                </div>

                <!-- Descripción -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Descripción</label>
                    <textarea 
                        v-model="formulario.descripcion"
                        placeholder="Describe el producto..."
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                    ></textarea>
                    <p v-if="errores.descripcion" class="text-red-600 text-xs mt-1">{{ errores.descripcion[0] }}</p>
                </div>

                <!-- Categoría -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Categoría *</label>
                    <select 
                        v-model.number="formulario.id_categoria"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                        required
                    >
                        <option :value="null">Seleccionar categoría...</option>
                        <option v-for="cat in categorias" :key="cat.id_categoria" :value="cat.id_categoria">
                            {{ cat.nombre }}
                        </option>
                    </select>
                    <p v-if="errores.id_categoria" class="text-red-600 text-xs mt-1">{{ errores.id_categoria[0] }}</p>
                </div>

                <!-- Precio y Costo -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Precio (Bs) *</label>
                        <input 
                            v-model.number="formulario.precio"
                            type="number" 
                            step="0.01"
                            placeholder="0.00"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            required
                        />
                        <p v-if="errores.precio" class="text-red-600 text-xs mt-1">{{ errores.precio[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Disponibilidad</label>
                        <select 
                            v-model.boolean="formulario.disponible"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                        >
                            <option :value="true">Disponible</option>
                            <option :value="false">No disponible</option>
                        </select>
                        <p v-if="errores.disponible" class="text-red-600 text-xs mt-1">{{ errores.disponible[0] }}</p>
                    </div>
                </div>

                <!-- Tiempo de Preparación -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Tiempo de Preparación (minutos)</label>
                    <input 
                        v-model.number="formulario.tiempo_preparacion"
                        type="number" 
                        placeholder="15"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                    />
                    <p v-if="errores.tiempo_preparacion" class="text-red-600 text-xs mt-1">{{ errores.tiempo_preparacion[0] }}</p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <button 
                        type="submit"
                        class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg font-semibold"
                    >
                        Guardar Cambios
                    </button>
                    <Link 
                        href="/productos"
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

const props = defineProps({
    producto: {
        type: Object,
        required: true,
    },
    categorias: {
        type: Array,
        required: true,
    },
});

const formulario = reactive({
    nombre: props.producto.nombre,
    descripcion: props.producto.descripcion,
    precio: parseFloat(props.producto.precio),
    id_categoria: parseInt(props.producto.id_categoria),
    disponible: props.producto.disponible,
    tiempo_preparacion: props.producto.tiempo_preparacion,
});

const errores = ref({});

const enviarFormulario = () => {
    router.put(`/productos/${props.producto.id_producto}`, formulario, {
        onError: (errors) => {
            errores.value = errors;
        },
    });
};
</script>

<style scoped>
</style>
