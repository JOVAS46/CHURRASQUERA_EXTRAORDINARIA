<template>
    <Layout>
        <Head title="Editar Usuario" />
        
        <div class="py-6">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 md:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Editar Usuario</h1>
                
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

                        <!-- Estado -->
                        <div>
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="form.estado"
                                    class="rounded border-gray-300 text-orange-600 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">Usuario Activo</span>
                            </label>
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
                            Guardar Cambios
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

const props = defineProps({
    usuario: Object,
    roles: Array,
});

const form = useForm({
    nombre: props.usuario.nombre,
    apellido: props.usuario.apellido,
    email: props.usuario.email,
    telefono: props.usuario.telefono,
    id_rol: props.usuario.id_rol,
    estado: props.usuario.estado,
});

const errors = ref(form.errors);

const submit = () => {
    form.patch(`/admin/usuarios/${props.usuario.id_usuario}`, {
        onSuccess: () => router.visit('/admin/usuarios'),
    });
};
</script>
