<template>
    <Layout>
        <div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Mi Perfil</h1>
                <p class="text-gray-600 mt-2">Información de tu cuenta</p>
            </div>

            <!-- Perfil Card -->
            <div class="bg-white rounded-lg shadow-lg border-2 border-orange-600 p-8">
                <!-- Avatar -->
                <div class="flex items-center justify-center mb-8">
                    <div class="w-24 h-24 bg-gradient-to-br from-orange-400 to-red-600 rounded-full flex items-center justify-center text-white text-4xl shadow-lg">
                        {{ initials }}
                    </div>
                </div>

                <!-- Información Personal -->
                <div class="space-y-6">
                    <!-- Nombre -->
                    <div class="border-b pb-4">
                        <label class="text-gray-600 text-sm font-semibold">Nombre</label>
                        <p class="text-gray-900 text-lg font-semibold">{{ user.nombre }}</p>
                    </div>

                    <!-- Apellido -->
                    <div class="border-b pb-4">
                        <label class="text-gray-600 text-sm font-semibold">Apellido</label>
                        <p class="text-gray-900 text-lg font-semibold">{{ user.apellido }}</p>
                    </div>

                    <!-- Email -->
                    <div class="border-b pb-4">
                        <label class="text-gray-600 text-sm font-semibold">Email</label>
                        <p class="text-gray-900 text-lg">{{ user.email }}</p>
                    </div>

                    <!-- Teléfono -->
                    <div class="border-b pb-4">
                        <label class="text-gray-600 text-sm font-semibold">Teléfono</label>
                        <p class="text-gray-900 text-lg">{{ user.telefono || 'No registrado' }}</p>
                    </div>

                    <!-- Rol -->
                    <div class="border-b pb-4">
                        <label class="text-gray-600 text-sm font-semibold">Rol</label>
                        <p class="text-gray-900 text-lg font-semibold text-orange-600">{{ user.rol.nombre }}</p>
                    </div>

                    <!-- Fecha de Registro -->
                    <div class="pb-4">
                        <label class="text-gray-600 text-sm font-semibold">Registrado desde</label>
                        <p class="text-gray-900 text-lg">{{ formatDate(user.created_at) }}</p>
                    </div>
                </div>

                <!-- Estado -->
                <div class="mt-8 p-4 rounded-lg" :class="user.estado ? 'bg-green-50 border border-green-600' : 'bg-red-50 border border-red-600'">
                    <p class="text-sm font-semibold" :class="user.estado ? 'text-green-800' : 'text-red-800'">
                        {{ user.estado ? '✓ Cuenta Activa' : '✗ Cuenta Inactiva' }}
                    </p>
                </div>

                <!-- Botones de Acción -->
                <div class="mt-8 space-y-3">
                    <Link href="/profile/edit" class="w-full block text-center bg-gradient-to-r from-orange-600 to-red-700 hover:from-orange-700 hover:to-red-800 text-white font-bold py-3 px-4 rounded-lg transition">
                        ✏️ Editar Perfil
                    </Link>
                    
                    <form method="POST" action="/logout" class="w-full">
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition">
                            🚪 Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';

const props = defineProps({
    user: Object,
});

const initials = computed(() => {
    if (props.user) {
        return (props.user.nombre[0] + props.user.apellido[0]).toUpperCase();
    }
    return 'U';
});

const formatDate = (date) => {
    if (!date) return '';
    const d = new Date(date);
    return d.toLocaleDateString('es-BO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>
