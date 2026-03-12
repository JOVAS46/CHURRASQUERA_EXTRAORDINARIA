<template>
    <Layout>
        <Head title="Agregar Ingrediente" />
        
        <div class="py-6">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 md:px-8">
                <Link :href="`/recetas/producto/${producto.id_producto}`" class="inline-block text-orange-600 hover:text-orange-900 mb-6">
                    ← Volver a Receta
                </Link>

                <div class="bg-white shadow-sm rounded-lg">
                    <div class="p-6">
                        <h1 class="text-3xl font-bold text-gray-900 mb-6">Agregar Ingrediente a: {{ producto.nombre }}</h1>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label for="id_insumo" class="block text-sm font-medium text-gray-700">Insumo <span class="text-red-600">*</span></label>
                                <select 
                                    id="id_insumo"
                                    v-model="form.id_insumo" 
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm px-3 py-2">
                                    <option value="">Seleccionar insumo</option>
                                    <option v-for="insumo in insumos" :key="insumo.id_insumo" :value="insumo.id_insumo">
                                        {{ insumo.nombre }} ({{ insumo.unidad_medida }})
                                    </option>
                                </select>
                                <div v-if="form.errors.id_insumo" class="text-red-600 text-sm mt-1">{{ form.errors.id_insumo }}</div>
                            </div>

                            <div>
                                <label for="cantidad_requerida" class="block text-sm font-medium text-gray-700">Cantidad Requerida <span class="text-red-600">*</span></label>
                                <input 
                                    id="cantidad_requerida"
                                    v-model="form.cantidad_requerida" 
                                    type="number" 
                                    step="0.01" 
                                    min="0.01" 
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm px-3 py-2"
                                    placeholder="0.00"
                                />
                                <div v-if="form.errors.cantidad_requerida" class="text-red-600 text-sm mt-1">{{ form.errors.cantidad_requerida }}</div>
                            </div>

                            <div class="flex justify-end space-x-4 pt-6 border-t">
                                <Link :href="`/recetas/producto/${producto.id_producto}`" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Cancelar
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-4 py-2 bg-orange-600 text-white rounded-md text-sm font-medium hover:bg-orange-700 disabled:opacity-50"
                                >
                                    Agregar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';

const props = defineProps({
    producto: Object,
    insumos: Array,
});

const form = useForm({
    id_insumo: '',
    cantidad_requerida: '',
});

const submit = () => {
    form.post(`/recetas/producto/${props.producto.id_producto}`, {
        onSuccess: () => {
            router.visit(`/recetas/producto/${props.producto.id_producto}`);
        }
    });
};
</script>
