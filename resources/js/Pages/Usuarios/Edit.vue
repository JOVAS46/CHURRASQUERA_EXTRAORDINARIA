<template>
    <Layout>
        <Head title="Editar Usuario" />
        
        <div class="py-6">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 md:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Editar Usuario</h1>

                <form @submit.prevent="actualizarUsuario" class="bg-white rounded-lg shadow-sm p-6">
                    <!-- Información de Usuario (readonly) -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">ID Usuario</p>
                                <p class="font-mono font-semibold text-gray-900">{{ usuario.id_usuario }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Registrado</p>
                                <p class="font-semibold text-gray-900">{{ formatDate(usuario.created_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Nombre -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Nombre *</label>
                        <input 
                            v-model="formulario.nombre" 
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            required
                        />
                        <p v-if="errores.nombre" class="text-red-600 text-sm mt-1">{{ errores.nombre[0] }}</p>
                    </div>

                    <!-- Apellido -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Apellido *</label>
                        <input 
                            v-model="formulario.apellido" 
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            required
                        />
                        <p v-if="errores.apellido" class="text-red-600 text-sm mt-1">{{ errores.apellido[0] }}</p>
                    </div>

                    <!-- Email -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Email *</label>
                        <input 
                            v-model="formulario.email" 
                            type="email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            required
                        />
                        <p v-if="errores.email" class="text-red-600 text-sm mt-1">{{ errores.email[0] }}</p>
                    </div>

                    <!-- Rol -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Rol *</label>
                        <select 
                            v-model.number="formulario.id_rol"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            required
                        >
                            <option value="">Selecciona un rol</option>
                            <option v-for="rol in roles" :key="rol.id_rol" :value="rol.id_rol">
                                {{ rol.nombre_rol }}
                            </option>
                        </select>
                        <p v-if="errores.id_rol" class="text-red-600 text-sm mt-1">{{ errores.id_rol[0] }}</p>
                    </div>

                    <!-- Teléfono -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Teléfono (Opcional)</label>
                        <input 
                            v-model="formulario.telefono" 
                            type="tel"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                        />
                        <p v-if="errores.telefono" class="text-red-600 text-sm mt-1">{{ errores.telefono[0] }}</p>
                    </div>

                    <!-- Estado -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Estado</label>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input 
                                    v-model="formulario.estado" 
                                    type="checkbox"
                                    class="w-4 h-4 text-orange-600 rounded focus:outline-none"
                                />
                                <span class="text-sm text-gray-700">Usuario Activo</span>
                            </label>
                        </div>
                        <p v-if="errores.estado" class="text-red-600 text-sm mt-1">{{ errores.estado[0] }}</p>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-4">
                        <button 
                            type="submit"
                            class="flex-1 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-semibold transition"
                        >
                            <i class="fas fa-check-circle mr-2"></i> Actualizar Usuario
                        </button>
                        <Link 
                            href="/admin/usuarios"
                            class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-semibold text-center transition"
                        >
                            Cancelar
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { reactive, ref } from 'vue';

const props = defineProps({
    usuario: {
        type: Object,
        required: true,
    },
    roles: {
        type: Array,
        required: true,
    },
});

const formulario = reactive({
    nombre: props.usuario.nombre,
    apellido: props.usuario.apellido,
    email: props.usuario.email,
    id_rol: props.usuario.id_rol,
    telefono: props.usuario.telefono || '',
    estado: props.usuario.estado,
});

const errores = ref({});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-BO', {
        year: 'numeric',
        month: 'short',
        day: '2-digit'
    });
};

const actualizarUsuario = () => {
    errores.value = {};
    
    router.put(`/admin/usuarios/${props.usuario.id_usuario}`, formulario, {
        onError: (erroresApi) => {
            errores.value = erroresApi;
        }
    });
};
</script>

<style scoped>
</style>
