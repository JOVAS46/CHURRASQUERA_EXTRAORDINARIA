<template>
    <Layout>
        <Head title="Crear Usuario" />
        
        <div class="py-6">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 md:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Crear Nuevo Usuario</h1>

                <form @submit.prevent="crearUsuario" class="bg-white rounded-lg shadow-sm p-6">
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

                    <!-- Contraseña -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Contraseña *</label>
                        <input 
                            v-model="formulario.password" 
                            type="password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            required
                        />
                        <p class="text-gray-600 text-xs mt-1">Mínimo 8 caracteres</p>
                        <p v-if="errores.password" class="text-red-600 text-sm mt-1">{{ errores.password[0] }}</p>
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Confirmar Contraseña *</label>
                        <input 
                            v-model="formulario.password_confirmation" 
                            type="password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            required
                        />
                        <p v-if="errores.password_confirmation" class="text-red-600 text-sm mt-1">{{ errores.password_confirmation[0] }}</p>
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
                            Crear Usuario
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

defineProps({
    roles: {
        type: Array,
        required: true,
    },
});

const formulario = reactive({
    nombre: '',
    apellido: '',
    email: '',
    password: '',
    password_confirmation: '',
    id_rol: '',
    telefono: '',
    estado: true,
});

const errores = ref({});

const crearUsuario = () => {
    errores.value = {};
    
    router.post('/admin/usuarios', formulario, {
        onError: (erroresApi) => {
            errores.value = erroresApi;
        }
    });
};
</script>

<style scoped>
</style>
