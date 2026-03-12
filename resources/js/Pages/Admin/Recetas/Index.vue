<template>
    <Layout>
        <Head title="Gestión de Recetas" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Gestión de Recetas</h1>

                <div class="space-y-6">
                    <div v-for="producto in productos.data" :key="producto.id_producto" class="bg-white shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-semibold text-gray-900">{{ producto.nombre }}</h2>
                                <Link :href="`/recetas/producto/${producto.id_producto}/create`" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                                    + Agregar Ingrediente
                                </Link>
                            </div>

                            <div v-if="producto.recetas.length > 0" class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Insumo</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200 hover:bg-gray-50">
                                        <tr v-for="receta in producto.recetas" :key="receta.id_receta" class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ receta.insumo?.nombre }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ receta.cantidad_requerida }} {{ receta.insumo?.unidad_medida }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <Link :href="`/recetas/producto/${producto.id_producto}/${receta.id_receta}/edit`" class="text-orange-600 hover:text-orange-900">Editar</Link>
                                                <button @click="eliminar(producto.id_producto, receta.id_receta)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else class="text-center py-8 text-gray-500">
                                <p class="text-sm italic">Sin ingredientes definidos</p>
                            </div>
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

defineProps({
    productos: Object,
});

const eliminar = (id_producto, id_receta) => {
    if (confirm('¿Eliminar este ingrediente?')) {
        router.delete(`/recetas/producto/${id_producto}/${id_receta}`);
    }
};
</script>
