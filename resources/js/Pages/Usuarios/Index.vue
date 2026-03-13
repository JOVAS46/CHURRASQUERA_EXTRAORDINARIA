<template>
    <Layout>
        <Head title="Gestión de Usuarios" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Gestión de Usuarios</h1>
                    <Link href="/admin/usuarios/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                        + Nuevo Usuario
                    </Link>
                </div>

                <!-- Mensaje de éxito -->
                <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i> {{ $page.props.flash.success }}
                </div>

                <!-- Tabla de Usuarios -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="usuario in usuarios.data" :key="usuario.id_usuario" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm font-semibold text-gray-900">{{ usuario.nombre }} {{ usuario.apellido }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ usuario.email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="['px-3 py-1 rounded-full text-xs font-semibold', rolBadge(usuario.rol?.nombre_rol)]">
                                            {{ usuario.rol?.nombre_rol || 'Sin rol' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ usuario.telefono || '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="['px-3 py-1 rounded-full text-xs font-semibold', usuario.estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                            {{ usuario.estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <Link :href="`/admin/usuarios/${usuario.id_usuario}/edit`" class="text-orange-600 hover:text-orange-900 font-semibold">
                                            <i class="fas fa-edit mr-1"></i> Editar
                                        </Link>
                                        <button @click="eliminarUsuario(usuario)" class="text-red-600 hover:text-red-900 font-semibold">
                                            <i class="fas fa-trash mr-1"></i> Eliminar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mensaje vacío -->
                    <div v-if="usuarios.data.length === 0" class="text-center py-12">
                        <p class="text-gray-500 text-lg">No hay usuarios registrados</p>
                    </div>
                </div>

                <!-- Paginación -->
                <div v-if="usuarios.links && usuarios.links.length > 0" class="mt-6 flex justify-center gap-2">
                    <template v-for="link in usuarios.links" :key="link.label">
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
    usuarios: {
        type: Object,
        required: true,
    },
});

const rolBadge = (rol) => {
    const colores = {
        'gerente': 'bg-purple-100 text-purple-800',
        'mesero': 'bg-blue-100 text-blue-800',
        'cajero': 'bg-green-100 text-green-800',
        'cocinero': 'bg-orange-100 text-orange-800',
        'cliente': 'bg-gray-100 text-gray-800',
    };
    return colores[rol] || 'bg-gray-100 text-gray-800';
};

const eliminarUsuario = (usuario) => {
    if (confirm(`¿Estás seguro de que deseas eliminar al usuario ${usuario.nombre} ${usuario.apellido}?`)) {
        router.delete(`/admin/usuarios/${usuario.id_usuario}`);
    }
};
</script>

<style scoped>
</style>
