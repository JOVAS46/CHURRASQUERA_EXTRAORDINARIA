<template>
    <Layout>
        <Head title="Crear Usuario" />
        
        <div class="py-6">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 md:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Crear Nuevo Usuario</h1>
                
                <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6 space-y-6">
                        <!-- Nombre -->
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input
                                type="text"
                                id="nombre"
                                v-model="form.nombre"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                            <span v-if="errors.nombre" class="text-red-600 text-sm">{{ errors.nombre }}</span>
                        </div>

                        <!-- Apellido -->
                        <div>
                            <label for="apellido" class="block text-sm font-medium text-gray-700">Apellido</label>
                            <input
                                type="text"
                                id="apellido"
                                v-model="form.apellido"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                            <span v-if="errors.apellido" class="text-red-600 text-sm">{{ errors.apellido }}</span>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input
                                type="email"
                                id="email"
                                v-model="form.email"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                            <span v-if="errors.email" class="text-red-600 text-sm">{{ errors.email }}</span>
                        </div>

                        <!-- Contraseña -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                            <input
                                type="password"
                                id="password"
                                v-model="form.password"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                            <span v-if="errors.password" class="text-red-600 text-sm">{{ errors.password }}</span>
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input
                                type="text"
                                id="telefono"
                                v-model="form.telefono"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                            <span v-if="errors.telefono" class="text-red-600 text-sm">{{ errors.telefono }}</span>
                        </div>

                        <!-- Rol -->
                        <div>
                            <label for="id_rol" class="block text-sm font-medium text-gray-700">Rol</label>
                            <select
                                id="id_rol"
                                v-model="form.id_rol"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Selecciona un rol</option>
                                <option v-for="rol in roles" :key="rol.id_rol" :value="rol.id_rol">
                                    {{ rol.nombre_rol }}
                                </option>
                            </select>
                            <span v-if="errors.id_rol" class="text-red-600 text-sm">{{ errors.id_rol }}</span>
                        </div>
                    </div>

                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 space-x-3">
                        <Link href="/admin/usuarios" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700"
                        >
                            Crear Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { ref } from 'vue';

const props = defineProps({
    roles: Array,
});

const form = useForm({
    nombre: '',
    apellido: '',
    email: '',
    password: '',
    telefono: '',
    id_rol: '',
});

const errors = ref(form.errors);

const submit = () => {
    form.post('/admin/usuarios', {
        onSuccess: () => router.visit('/admin/usuarios'),
    });
};
</script>
