<template>
    <Layout>
        <Head title="Gestión de Usuarios" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Gestión de Usuarios</h1>
                
                <div class="bg-white shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <Link href="/admin/usuarios/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                            + Nuevo Usuario
                        </Link>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="usuario in usuarios" :key="usuario.id_usuario" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ usuario.nombre }} {{ usuario.apellido }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ usuario.email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="rolClass(usuario.id_rol)">
                                            {{ usuario.rol?.nombre_rol }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ usuario.telefono || '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span v-if="usuario.estado" class="text-green-800 bg-green-100 px-2 py-1 rounded">Activo</span>
                                        <span v-else class="text-red-800 bg-red-100 px-2 py-1 rounded">Inactivo</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <Link :href="`/admin/usuarios/${usuario.id_usuario}/edit`" class="text-orange-600 hover:text-orange-900">Editar</Link>
                                        <button @click="deleteUsuario(usuario.id_usuario)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
    usuarios: Array,
});

const rolClass = (rol_id) => {
    const classes = {
        1: 'bg-purple-100 text-purple-800',
        2: 'bg-green-100 text-green-800',
        3: 'bg-yellow-100 text-yellow-800',
        4: 'bg-orange-100 text-orange-800',
        5: 'bg-blue-100 text-blue-800',
    };
    return classes[rol_id] || 'bg-gray-100 text-gray-800';
};

const deleteUsuario = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
        router.delete(`/admin/usuarios/${id}`);
    }
};
</script>
