<template>
    <Layout>
        <div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Editar Perfil</h1>
                <p class="text-gray-600 mt-2">Actualiza tu información personal</p>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-lg border-2 border-orange-600 p-8">
                <form @submit.prevent="submitting = true; router.patch('/profile', formData)">
                    <!-- Nombre -->
                    <div class="mb-6">
                        <label class="block text-orange-600 font-semibold mb-2">Nombre</label>
                        <input 
                            v-model="formData.nombre"
                            type="text" 
                            placeholder="Tu nombre"
                            class="w-full px-4 py-3 bg-gray-100 border-2 border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400 focus:ring-opacity-50"
                            required
                        >
                        <p v-if="errors.nombre" class="text-red-500 text-sm mt-1">{{ errors.nombre[0] }}</p>
                    </div>

                    <!-- Apellido -->
                    <div class="mb-6">
                        <label class="block text-orange-600 font-semibold mb-2">Apellido</label>
                        <input 
                            v-model="formData.apellido"
                            type="text" 
                            placeholder="Tu apellido"
                            class="w-full px-4 py-3 bg-gray-100 border-2 border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400 focus:ring-opacity-50"
                            required
                        >
                        <p v-if="errors.apellido" class="text-red-500 text-sm mt-1">{{ errors.apellido[0] }}</p>
                    </div>

                    <!-- Email -->
                    <div class="mb-6">
                        <label class="block text-orange-600 font-semibold mb-2">Email</label>
                        <input 
                            v-model="formData.email"
                            type="email" 
                            placeholder="tu@email.com"
                            class="w-full px-4 py-3 bg-gray-100 border-2 border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400 focus:ring-opacity-50"
                            required
                        >
                        <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email[0] }}</p>
                    </div>

                    <!-- Teléfono -->
                    <div class="mb-8">
                        <label class="block text-orange-600 font-semibold mb-2">Teléfono (Opcional)</label>
                        <input 
                            v-model="formData.telefono"
                            type="tel" 
                            placeholder="+591 7123456"
                            class="w-full px-4 py-3 bg-gray-100 border-2 border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400 focus:ring-opacity-50"
                        >
                        <p v-if="errors.telefono" class="text-red-500 text-sm mt-1">{{ errors.telefono[0] }}</p>
                    </div>

                    <!-- Botones -->
                    <div class="space-y-3">
                        <button 
                            type="submit"
                            :disabled="submitting"
                            class="w-full bg-gradient-to-r from-orange-600 to-red-700 hover:from-orange-700 hover:to-red-800 text-white font-bold py-3 px-4 rounded-lg transition disabled:opacity-50"
                        >
                            {{ submitting ? 'Guardando...' : '💾 Guardar Cambios' }}
                        </button>
                        
                        <Link href="/profile" class="w-full block text-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded-lg transition">
                            ← Cancelar
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';

const page = usePage();
const props = defineProps({
    user: Object,
});

const submitting = ref(false);
const errors = ref({});

const formData = reactive({
    nombre: props.user.nombre || '',
    apellido: props.user.apellido || '',
    email: props.user.email || '',
    telefono: props.user.telefono || '',
});

// Escuchar errores de validación
if (page.props.errors) {
    errors.value = page.props.errors;
}
</script>
