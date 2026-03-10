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
                <div class="text-lg font-bold text-orange-100 mb-1">CHURRASCERÍA</div>
                <div class="h-0.5 w-20 bg-gradient-to-r from-orange-500 to-red-600 mx-auto mb-4"></div>
                <p class="text-orange-200 text-sm">Sistema de Gestión</p>
            </div>

            <!-- Error Messages -->
            <div v-if="$page.props.errors.email" class="p-4 bg-red-900 border border-red-600 text-red-200 rounded">
                {{ $page.props.errors.email }}
            </div>

            <!-- Login Form -->
            <form @submit.prevent="enviarFormulario" class="bg-gray-900 border-2 border-orange-600 rounded-lg shadow-2xl p-8 space-y-6">
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
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-orange-300 font-semibold mb-2">Contraseña</label>
                    <input 
                        type="password" 
                        v-model="formulario.password"
                        placeholder="••••••••"
                        class="w-full px-4 py-3 bg-gray-800 border border-orange-500 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400 focus:ring-opacity-50"
                        required
                    >
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        v-model="formulario.remember"
                        class="h-4 w-4 bg-gray-800 border border-orange-500 rounded cursor-pointer accent-orange-600"
                    >
                    <label class="ml-2 text-orange-200 cursor-pointer text-sm">Recuérdame</label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full mt-6 bg-gradient-to-r from-orange-600 to-red-700 hover:from-orange-700 hover:to-red-800 text-white font-bold py-3 px-4 rounded-lg transition duration-200"
                >
                    Iniciar Sesión
                </button>
            </form>

            <!-- Register Link -->
            <div class="text-center">
                <p class="text-orange-200 text-sm">
                    ¿No tienes cuenta?
                    <Link href="/register" class="text-orange-400 hover:text-orange-300 font-semibold underline">
                        Registrarse
                    </Link>
                </p>
            </div>

            <!-- Demo Credentials -->
            <div class="bg-gray-900 border border-orange-600 border-opacity-50 rounded-lg p-4 text-orange-100 text-xs">
                <p class="font-semibold mb-2 text-orange-400">🔑 Prueba Admin:</p>
                <p>Email: admin@example.com</p>
                <p>Contraseña: password123</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const formulario = reactive({
    email: '',
    password: '',
    remember: false,
});

const enviarFormulario = async () => {
    router.post('/login', {
        email: formulario.email,
        password: formulario.password,
        remember: formulario.remember,
    });
};
</script>

