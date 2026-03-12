<template>
    <Layout>
        <Head title="Gestión de Insumos" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Gestión de Insumos</h1>
                    <Link href="/insumos/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                        + Nuevo Insumo
                    </Link>
                </div>

                
                <div v-if="$page.props.flash.success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                    {{ $page.props.flash.success }}
                </div>

                <div class="bg-white shadow-sm rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mínimo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proveedor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="insumo in insumos.data" :key="insumo.id_insumo" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ insumo.nombre }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ insumo.unidad_medida }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ insumo.stock_actual }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ insumo.stock_minimo }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ insumo.proveedor?.nombre || 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <Link :href="`/insumos/${insumo.id_insumo}/edit`" class="text-orange-600 hover:text-orange-900">Editar</Link>
                                        <button @click="eliminar(insumo.id_insumo)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Paginación -->
                <div v-if="insumos.links" class="mt-6 flex justify-center gap-2">
                    <template v-for="link in insumos.links" :key="link.label">
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
    insumos: Object,
});

const eliminar = (id) => {
    if (confirm('¿Está seguro?')) {
        router.delete(`/insumos/${id}`);
    }
};
</script>
