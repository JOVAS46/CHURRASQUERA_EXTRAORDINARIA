<template>
    <Layout>
        <Head title="Crear Reserva" />
        
        <div class="py-6">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 md:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Crear Nueva Reserva</h1>
                
                <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6 space-y-6">
                        <!-- Fecha -->
                        <div>
                            <label for="fecha_reserva" class="block text-sm font-medium text-gray-700 mb-2">Fecha de la Reserva</label>
                            <input
                                type="date"
                                id="fecha_reserva"
                                v-model="form.fecha_reserva"
                                :min="minDate"
                                class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                required
                            />
                            <span v-if="errors.fecha_reserva" class="text-red-600 text-sm">{{ errors.fecha_reserva }}</span>
                        </div>

                        <!-- Hora -->
                        <div>
                            <label for="hora_reserva" class="block text-sm font-medium text-gray-700 mb-2">Hora de la Reserva</label>
                            <input
                                type="time"
                                id="hora_reserva"
                                v-model="form.hora_reserva"
                                class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                required
                            />
                            <span v-if="errors.hora_reserva" class="text-red-600 text-sm">{{ errors.hora_reserva }}</span>
                            <p class="text-xs text-gray-500 mt-1">Atendemos de 11:00 a 22:00</p>
                        </div>

                        <!-- Número de Personas -->
                        <div>
                            <label for="numero_personas" class="block text-sm font-medium text-gray-700 mb-2">Número de Personas</label>
                            <div class="flex gap-2 items-center">
                                <button
                                    type="button"
                                    @click="form.numero_personas = Math.max(1, form.numero_personas - 1)"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-50"
                                >
                                    −
                                </button>
                                <input
                                    type="number"
                                    id="numero_personas"
                                    v-model.number="form.numero_personas"
                                    min="1"
                                    max="20"
                                    class="flex-1 text-center border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-2xl font-bold"
                                />
                                <button
                                    type="button"
                                    @click="form.numero_personas = Math.min(20, form.numero_personas + 1)"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-50"
                                >
                                    +
                                </button>
                            </div>
                            <span v-if="errors.numero_personas" class="text-red-600 text-sm">{{ errors.numero_personas }}</span>
                        </div>

                        <!-- Observaciones -->
                        <div>
                            <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">Observaciones Especiales</label>
                            <textarea
                                id="observaciones"
                                v-model="form.observaciones"
                                rows="4"
                                placeholder="Ej: Cumpleaños, mesas largas, alergias, etc."
                                class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            ></textarea>
                            <span v-if="errors.observaciones" class="text-red-600 text-sm">{{ errors.observaciones }}</span>
                        </div>

                        <!-- Info adicional -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800">
                                <strong>💡 Nota:</strong> Tu reserva será confirmada por nuestro equipo. Te enviaremos un email de confirmación.
                            </p>
                        </div>
                    </div>

                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 space-x-3">
                        <Link href="/cliente/reservas" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="isSubmitting"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ isSubmitting ? 'Guardando...' : 'Reservar Ahora' }}
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
import { ref, computed } from 'vue';

const form = useForm({
    fecha_reserva: '',
    hora_reserva: '12:00',
    numero_personas: 2,
    observaciones: '',
});

const errors = ref(form.errors);
const isSubmitting = ref(false);

const minDate = computed(() => {
    const mañana = new Date();
    mañana.setDate(mañana.getDate() + 1);
    return mañana.toISOString().split('T')[0];
});

const submit = () => {
    isSubmitting.value = true;
    form.post('/cliente/reservas', {
        onSuccess: () => {
            router.visit('/cliente/reservas');
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
        onError: (newErrors) => {
            errors.value = newErrors;
        }
    });
};
</script>
