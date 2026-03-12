<template>
    <Layout>
        <Head title="Crear Insumo" />
        
        <div class="py-6">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 md:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Crear Insumo</h1>
                    <Link href="/insumos" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                        ← Volver
                    </Link>
                </div>

                <div class="bg-white shadow-sm rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6 p-6">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre <span class="text-red-600">*</span></label>
                            <input
                                id="nombre"
                                v-model="form.nombre"
                                type="text"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm px-3 py-2"
                                placeholder="Nombre del insumo"
                            />
                            <div v-if="form.errors.nombre" class="text-red-600 text-sm mt-1">{{ form.errors.nombre }}</div>
                        </div>
                        
                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                            <textarea
                                id="descripcion"
                                v-model="form.descripcion"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm px-3 py-2"
                                rows="3"
                                placeholder="Descripción del insumo"
                            ></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="unidad_medida" class="block text-sm font-medium text-gray-700">Unidad de Medida <span class="text-red-600">*</span></label>
                                <input
                                    id="unidad_medida"
                                    v-model="form.unidad_medida"
                                    type="text"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm px-3 py-2"
                                    placeholder="kg, l, u, etc."
                                />
                                <div v-if="form.errors.unidad_medida" class="text-red-600 text-sm mt-1">{{ form.errors.unidad_medida }}</div>
                            </div>
                            
                            <div>
                                <label for="precio_unitario" class="block text-sm font-medium text-gray-700">Precio Unitario <span class="text-red-600">*</span></label>
                                <input
                                    id="precio_unitario"
                                    v-model="form.precio_unitario"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm px-3 py-2"
                                    placeholder="0.00"
                                />
                                <div v-if="form.errors.precio_unitario" class="text-red-600 text-sm mt-1">{{ form.errors.precio_unitario }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="stock_actual" class="block text-sm font-medium text-gray-700">Stock Actual <span class="text-red-600">*</span></label>
                                <input
                                    id="stock_actual"
                                    v-model="form.stock_actual"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm px-3 py-2"
                                    placeholder="0"
                                />
                                <div v-if="form.errors.stock_actual" class="text-red-600 text-sm mt-1">{{ form.errors.stock_actual }}</div>
                            </div>

                            <div>
                                <label for="stock_minimo" class="block text-sm font-medium text-gray-700">Stock Mínimo <span class="text-red-600">*</span></label>
                                <input
                                    id="stock_minimo"
                                    v-model="form.stock_minimo"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm px-3 py-2"
                                    placeholder="0"
                                />
                                <div v-if="form.errors.stock_minimo" class="text-red-600 text-sm mt-1">{{ form.errors.stock_minimo }}</div>
                            </div>
                        </div>

                        <div>
                            <label for="id_proveedor" class="block text-sm font-medium text-gray-700">Proveedor <span class="text-red-600">*</span></label>
                            <select
                                id="id_proveedor"
                                v-model="form.id_proveedor"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm px-3 py-2"
                            >
                                <option value="">Seleccionar proveedor</option>
                                <option v-for="proveedor in proveedores" :key="proveedor.id_proveedor" :value="proveedor.id_proveedor">
                                    {{ proveedor.nombre }}
                                </option>
                            </select>
                            <div v-if="form.errors.id_proveedor" class="text-red-600 text-sm mt-1">{{ form.errors.id_proveedor }}</div>
                        </div>

                        <div class="flex justify-end space-x-4 pt-6 border-t">
                            <Link href="/insumos" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-orange-600 text-white rounded-md text-sm font-medium hover:bg-orange-700 disabled:opacity-50"
                            >
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';

defineProps({
    proveedores: Array,
});

const form = useForm({
    nombre: '',
    descripcion: '',
    unidad_medida: '',
    stock_actual: '',
    stock_minimo: '',
    precio_unitario: '',
    id_proveedor: '',
});

const submit = () => {
    form.post('/insumos');
};
</script>
