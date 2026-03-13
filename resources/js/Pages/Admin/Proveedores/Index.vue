<template>
    <Layout>
        <Head title="Gestión de Proveedores" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Gestión de Proveedores</h1>
                    <Link href="/proveedores/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                        + Nuevo Proveedor
                    </Link>
                </div>

                <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                    {{ $page.props.flash.success }}
                </div>

                <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
                    {{ $page.props.flash.error }}
                </div>

                <div class="bg-white shadow-sm rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="proveedor in proveedores.data" :key="proveedor.id_proveedor" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ proveedor.nombre }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ proveedor.contacto || 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ proveedor.telefono || 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ proveedor.email || 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span v-if="proveedor.estado === 'activo'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check text-green-500 mr-1"></i> Activo
                                        </span>
                                        <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times text-red-600 mr-1"></i> Inactivo
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <Link :href="`/proveedores/${proveedor.id_proveedor}/edit`" class="text-orange-600 hover:text-orange-900">Editar</Link>
                                        <button @click="eliminar(proveedor.id_proveedor)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Paginación -->
                <div v-if="proveedores.links" class="mt-6 flex justify-center gap-2">
                    <template v-for="link in proveedores.links" :key="link.label">
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
    proveedores: Object,
});

const eliminar = (id) => {
    if (confirm('¿Está seguro?')) {
        router.delete(`/proveedores/${id}`);
    }
};
</script>
