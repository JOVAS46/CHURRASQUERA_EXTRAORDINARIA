<template>
    <div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Gestión de Roles</h1>
                <p class="mt-2 text-gray-600">Administra los roles del sistema</p>
            </div>

            <!-- Alert de éxito -->
            <div v-if="$page.props.flash.success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-800">{{ $page.props.flash.success }}</p>
            </div>

            <!-- Alert de error -->
            <div v-if="$page.props.flash.error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-800">{{ $page.props.flash.error }}</p>
            </div>

            <!-- Tabla de roles -->
            <div class="overflow-hidden shadow rounded-lg">
                <table class="w-full divide-y divide-gray-200 bg-white">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nombre
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Descripción
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Usuarios Asignados
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th v-if="isGerente" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="role in roles" :key="role.id_rol" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ role.nombre }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ role.descripcion || 'Sin descripción' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ role.usuarios.length }} usuario(s)
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span v-if="role.activo" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Activo
                                </span>
                                <span v-else class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Inactivo
                                </span>
                            </td>
                            <td v-if="isGerente" class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <Link
                                    :href="route('roles.edit', role.id_rol)"
                                    class="text-blue-600 hover:text-blue-900"
                                >
                                    Editar
                                </Link>
                                <button
                                    v-if="role.usuarios.length === 0"
                                    @click="eliminarRol(role.id_rol)"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    Eliminar
                                </button>
                                <span v-else class="text-gray-400 cursor-not-allowed">
                                    (No se puede eliminar - tiene usuarios)
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mensaje si no hay roles -->
            <div v-if="roles.length === 0" class="mt-6 p-6 bg-white rounded-lg shadow text-center">
                <p class="text-gray-600">No hay roles disponibles</p>
            </div>

            <!-- Info para no-Gerente -->
            <div v-if="!isGerente" class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-yellow-800">
                    <strong>Nota:</strong> Solo los usuarios con rol "Gerente" pueden editar o eliminar roles.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    roles: Array,
    isGerente: Boolean,
});

const eliminarRol = (id) => {
    if (confirm('¿Está seguro de que desea eliminar este rol?')) {
        router.delete(route('roles.destroy', id));
    }
};
</script>
