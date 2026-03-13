<template>
    <Layout>
        <Head title="Gestión de Productos" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Gestión de Productos</h1>
                    <Link href="/productos/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                        + Nuevo Producto
                    </Link>
                </div>
                
                <div class="bg-white shadow-sm rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preparación</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disponibilidad</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="producto in productos.data" :key="producto.id_producto" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ producto.nombre }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                        {{ producto.descripcion || '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-orange-600">
                                        {{ formatCurrency(producto.precio) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ producto.tiempo_preparacion ? `${producto.tiempo_preparacion} min` : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span v-if="producto.disponible" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check text-green-500 mr-1"></i> Disponible
                                        </span>
                                        <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            ✗ No disponible
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <Link :href="`/productos/${producto.id_producto}/edit`" class="text-orange-600 hover:text-orange-900">Editar</Link>
                                        <button @click="deleteProducto(producto.id_producto)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Paginación -->
                <div v-if="productos.links" class="mt-6 flex justify-center gap-2">
                    <template v-for="link in productos.links" :key="link.label">
                        <Link 
                            v-if="link.url"
                            :href="link.url"
                            :class="[
                                'px-4 py-2 rounded border text-sm',
                                link.active
                                    ? 'bg-orange-600 text-white border-orange-600'
                                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                            ]"
                            v-html="link.label"
                        ></Link>
                        <span 
                            v-else
                            :class="[
                                'px-4 py-2 rounded border text-sm',
                                'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                            ]"
                            v-html="link.label"
                        ></span>
                    </template>
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

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
    }).format(value);
};

const deleteProducto = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
        router.delete(`/productos/${id}`);
    }
};
</script>

<style scoped>
</style>
