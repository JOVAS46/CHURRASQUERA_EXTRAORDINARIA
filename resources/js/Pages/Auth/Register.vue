<template>
    <div class="min-h-screen bg-gradient-to-br from-red-900 via-orange-900 to-black flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <!-- Flame decorative elements -->
        <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute top-10 right-10 w-32 h-32 bg-orange-600 rounded-full blur-3xl opacity-20"></div>
            <div class="absolute bottom-10 left-10 w-40 h-40 bg-red-700 rounded-full blur-3xl opacity-20"></div>
        </div>

        <div class="relative z-10 max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="text-5xl font-bold text-orange-500 mb-2 tracking-wider">
                    🔥 ROBERTO
                </div>
                <div class="text-lg font-bold text-orange-100 mb-1">CHURRASQUERIA</div>
                <div class="h-0.5 w-20 bg-gradient-to-r from-orange-500 to-red-600 mx-auto mb-4"></div>
                <h1 class="text-xl font-bold text-orange-100">Crear Cuenta</h1>
            </div>

            <!-- Register Form -->
            <form @submit.prevent="enviarFormulario" class="bg-gray-900 border-2 border-orange-600 rounded-lg shadow-2xl p-8 space-y-4">
                <!-- Nombre -->
                <div>
                    <label class="block text-orange-300 font-semibold mb-2">Nombre</label>
                    <input 
                        type="text" 
                        v-model="formulario.nombre"
                        placeholder="Juan"
                        class="w-full px-4 py-3 bg-gray-800 border border-orange-500 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400 focus:ring-opacity-50"
                        required
                    >
                    <p v-if="errors.nombre" class="text-red-400 text-sm mt-1">{{ errors.nombre }}</p>
                </div>

                <!-- Apellido -->
                <div>
                    <label class="block text-orange-300 font-semibold mb-2">Apellido</label>
                    <input 
                        type="text" 
                        v-model="formulario.apellido"
                        placeholder="Pérez"
                        class="w-full px-4 py-3 bg-gray-800 border border-orange-500 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400 focus:ring-opacity-50"
                        required
                    >
                    <p v-if="errors.apellido" class="text-red-400 text-sm mt-1">{{ errors.apellido }}</p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-orange-300 font-semibold mb-2">Email</label>
                    <input 
                        type="email" 
                        v-model="formulario.email"
                        placeholder="tu@example.com"
                        class="w-full px-4 py-3 bg-gray-800 border border-orange-500 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400 focus:ring-opacity-50"
                        required
                    >
                    <p v-if="errors.email" class="text-red-400 text-sm mt-1">{{ errors.email }}</p>
                </div>

                <!-- Teléfono -->
                <div>
                    <label class="block text-orange-300 font-semibold mb-2">Teléfono (Opcional)</label>
                    <input 
                        type="tel" 
                        v-model="formulario.telefono"
                        placeholder="+591 7123456"
                        class="w-full px-4 py-3 bg-gray-800 border border-orange-500 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400 focus:ring-opacity-50"
                    >
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-orange-300 font-semibold mb-2">Contraseña</label>
                    <input 
                        type="password" 
                        v-model="formulario.password"
                        placeholder="Mínimo 8 caracteres"
                        class="w-full px-4 py-3 bg-gray-800 border border-orange-500 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400 focus:ring-opacity-50"
                        required
                    >
                    <p v-if="errors.password" class="text-red-400 text-sm mt-1">{{ errors.password }}</p>
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label class="block text-orange-300 font-semibold mb-2">Confirmar Contraseña</label>
                    <input 
                        type="password" 
                        v-model="formulario.password_confirmation"
                        placeholder="Confirma tu contraseña"
                        class="w-full px-4 py-3 bg-gray-800 border border-orange-500 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400 focus:ring-opacity-50"
                        required
                    >
                    <p v-if="errors.password_confirmation" class="text-red-400 text-sm mt-1">{{ errors.password_confirmation }}</p>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full mt-6 bg-gradient-to-r from-orange-600 to-red-700 hover:from-orange-700 hover:to-red-800 text-white font-bold py-3 px-4 rounded-lg transition duration-200"
                >
                    Crear Cuenta
                </button>
            </form>

            <!-- Login Link -->
            <div class="text-center">
                <p class="text-orange-200 text-sm">
                    ¿Ya tienes cuenta?
                    <Link href="/login" class="text-orange-400 hover:text-orange-300 font-semibold underline">
                        Iniciar sesión
                    </Link>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const formulario = reactive({
    nombre: '',
    apellido: '',
    email: '',
    telefono: '',
    password: '',
    password_confirmation: '',
});

const errors = ref({});

const enviarFormulario = async () => {
    router.post('/register', {
        nombre: formulario.nombre,
        apellido: formulario.apellido,
        email: formulario.email,
        telefono: formulario.telefono,
        password: formulario.password,
        password_confirmation: formulario.password_confirmation,
    }, {
        onError: (errores) => {
            errors.value = errores;
        }
    });
};
</script>
