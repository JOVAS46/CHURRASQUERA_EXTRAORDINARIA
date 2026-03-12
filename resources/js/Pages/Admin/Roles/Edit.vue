<template>
    <div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <Link :href="route('roles.index')" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                    ← Volver a Roles
                </Link>
                <h1 class="text-3xl font-bold text-gray-900 mt-4">Editar Rol: {{ role.nombre }}</h1>
            </div>

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow p-8">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Nombre -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">
                            Nombre del Rol *
                        </label>
                        <input
                            id="nombre"
                            v-model="form.nombre"
                            type="text"
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500"
                            :class="{ 'border-red-500': errors.nombre }"
                        />
                        <p v-if="errors.nombre" class="mt-2 text-sm text-red-600">
                            {{ errors.nombre }}
                        </p>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700">
                            Descripción
                        </label>
                        <textarea
                            id="descripcion"
                            v-model="form.descripcion"
                            rows="4"
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500"
                            :class="{ 'border-red-500': errors.descripcion }"
                        ></textarea>
                        <p v-if="errors.descripcion" class="mt-2 text-sm text-red-600">
                            {{ errors.descripcion }}
                        </p>
                    </div>

                    <!-- Activo -->
                    <div>
                        <label class="flex items-center">
                            <input
                                v-model="form.activo"
                                type="checkbox"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                            <span class="ml-2 text-sm text-gray-700">
                                Rol Activo
                            </span>
                        </label>
                        <p v-if="errors.activo" class="mt-2 text-sm text-red-600">
                            {{ errors.activo }}
                        </p>
                    </div>

                    <!-- Acciones -->
                    <div class="flex gap-3 pt-4">
                        <button
                            type="submit"
                            :disabled="processing"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400 font-medium"
                        >
                            <span v-if="!processing">Guardar Cambios</span>
                            <span v-else>Guardando...</span>
                        </button>
                        <Link
                            :href="route('roles.index')"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 font-medium"
                        >
                            Cancelar
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    role: Object,
    errors: Object,
});

const form = useForm({
    nombre: props.role.nombre,
    descripcion: props.role.descripcion || '',
    activo: props.role.activo,
});

const submitForm = () => {
    form.patch(route('roles.update', props.role.id_rol));
};
</script>
