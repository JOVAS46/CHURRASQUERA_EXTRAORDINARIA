<template>
    <Layout>
        <Head title="Receta: {{ producto.nombre }}" />
        
        <div class="py-6">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 md:px-8">
                <Link href="/recetas" class="inline-block text-orange-600 hover:text-orange-900 mb-4">
                    ← Volver
                </Link>

                <div class="bg-white shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-3xl font-bold text-gray-900">Receta: {{ producto.nombre }}</h1>
                            <Link :href="`/recetas/producto/${producto.id_producto}/create`" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                                + Agregar Ingrediente
                            </Link>
                        </div>

                        <div v-if="$page.props.flash.success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                            {{ $page.props.flash.success }}
                        </div>

                        <div v-if="recetas.length > 0" class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Insumo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="receta in recetas" :key="receta.id_receta" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ receta.insumo?.nombre }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ receta.cantidad_requerida }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ receta.insumo?.unidad_medida }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <Link :href="`/recetas/producto/${producto.id_producto}/${receta.id_receta}/edit`" class="text-orange-600 hover:text-orange-900">Editar</Link>
                                            <button @click="eliminar(receta.id_receta)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-12">
                            <p class="text-lg text-gray-600 mb-4">No hay ingredientes definidos para este producto</p>
                            <Link :href="`/recetas/producto/${producto.id_producto}/create`" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                                Agregar Primer Ingrediente
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';

const props = defineProps({
    producto: Object,
    recetas: Array,
});

const eliminar = (id_receta) => {
    if (confirm('¿Eliminar este ingrediente?')) {
        router.delete(`/recetas/producto/${props.producto.id_producto}/${id_receta}`);
    }
};
</script>
