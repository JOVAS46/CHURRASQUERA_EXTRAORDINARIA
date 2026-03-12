<template>
    <Layout>
        <Head title="Editar Proveedor" />
        
        <div class="py-6">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 md:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Editar Proveedor</h1>
                    <Link href="/proveedores" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                        ← Volver
                    </Link>
                </div>

                <div class="bg-white shadow-sm rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6 p-6">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre <span class="text-red-600">*</span></label>
                            <input
                                id="nombre"
                                v-model="form.nombre"
                                type="text"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm px-3 py-2"
                                placeholder="Nombre del proveedor"
                            />
                            <div v-if="form.errors.nombre" class="text-red-600 text-sm mt-1">{{ form.errors.nombre }}</div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="contacto" class="block text-sm font-medium text-gray-700">Contacto</label>
                                <input
                                    id="contacto"
                                    v-model="form.contacto"
                                    type="text"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm px-3 py-2"
                                    placeholder="Nombre del contacto"
                                />
                            </div>

                            <div>
                                <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                <input
                                    id="telefono"
                                    v-model="form.telefono"
                                    type="text"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm px-3 py-2"
                                    placeholder="Número de teléfono"
                                />
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm px-3 py-2"
                                placeholder="correo@ejemplo.com"
                            />
                        </div>

                        <div>
                            <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección</label>
                            <input
                                id="direccion"
                                v-model="form.direccion"
                                type="text"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm px-3 py-2"
                                placeholder="Dirección del proveedor"
                            />
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">Estado <span class="text-red-600">*</span></label>
                            <select
                                id="estado"
                                v-model="form.estado"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm px-3 py-2"
                            >
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                            <div v-if="form.errors.estado" class="text-red-600 text-sm mt-1">{{ form.errors.estado }}</div>
                        </div>

                        <div class="flex justify-end space-x-4 pt-6 border-t">
                            <Link href="/proveedores" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-orange-600 text-white rounded-md text-sm font-medium hover:bg-orange-700 disabled:opacity-50"
                            >
                                Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';

const props = defineProps({
    proveedor: Object,
});

const form = useForm({
    nombre: props.proveedor.nombre,
    contacto: props.proveedor.contacto || '',
    telefono: props.proveedor.telefono || '',
    email: props.proveedor.email || '',
    direccion: props.proveedor.direccion || '',
    estado: props.proveedor.estado,
});

const submit = () => {
    form.patch(`/proveedores/${props.proveedor.id_proveedor}`);
};
</script>
