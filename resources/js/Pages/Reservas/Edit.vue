<template>
    <Layout>
        <Head title="Editar Reserva" />
        
        <div class="py-6">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 md:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Editar Reserva</h1>

                <form @submit.prevent="actualizarReserva" class="bg-white rounded-lg shadow-sm p-6">
                    <!-- Información de Reserva (readonly) -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">ID Reserva</p>
                                <p class="font-mono font-semibold text-gray-900">{{ reserva.id_reserva }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Creada</p>
                                <p class="font-semibold text-gray-900">{{ formatDate(reserva.created_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Fecha de Reserva -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Fecha de Reserva *</label>
                        <input 
                            v-model="formulario.fecha_reserva" 
                            type="date"
                            :min="hoyFormato"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            required
                        />
                        <p v-if="errores.fecha_reserva" class="text-red-600 text-sm mt-1">{{ errores.fecha_reserva[0] }}</p>
                    </div>

                    <!-- Horarios (Inicio y Fin) -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Hora Inicio *</label>
                            <input 
                                v-model="formulario.hora_inicio" 
                                type="time"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                                required
                            />
                            <p v-if="errores.hora_inicio" class="text-red-600 text-sm mt-1">{{ errores.hora_inicio[0] }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Hora Fin *</label>
                            <input 
                                v-model="formulario.hora_fin" 
                                type="time"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                                required
                            />
                            <p v-if="errores.hora_fin" class="text-red-600 text-sm mt-1">{{ errores.hora_fin[0] }}</p>
                        </div>
                    </div>

                    <!-- Mesa -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Selecciona una Mesa *</label>
                        <select 
                            v-model="formulario.id_mesa"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            required
                        >
                            <option value="">Selecciona una mesa</option>
                            <option v-for="mesa in mesas" :key="mesa.id_mesa" :value="mesa.id_mesa">
                                Mesa {{ mesa.numero_mesa }} - Capacidad: {{ mesa.capacidad }} personas ({{ mesa.ubicacion }})
                            </option>
                        </select>
                        <p v-if="errores.id_mesa" class="text-red-600 text-sm mt-1">{{ errores.id_mesa[0] }}</p>
                    </div>

                    <!-- Número de Personas -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Número de Personas *</label>
                        <input 
                            v-model.number="formulario.numero_personas" 
                            type="number"
                            min="1"
                            max="20"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            required
                        />
                        <p v-if="errores.numero_personas" class="text-red-600 text-sm mt-1">{{ errores.numero_personas[0] }}</p>
                    </div>

                    <!-- Estado -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Estado *</label>
                        <select 
                            v-model="formulario.estado"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            required
                        >
                            <option value="confirmada">Confirmada</option>
                            <option value="completada">Completada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                        <p v-if="errores.estado" class="text-red-600 text-sm mt-1">{{ errores.estado[0] }}</p>
                    </div>

                    <!-- Observaciones -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Observaciones (Opcional)</label>
                        <textarea 
                            v-model="formulario.observaciones"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            placeholder="Notas adicionales..."
                        ></textarea>
                        <p v-if="errores.observaciones" class="text-red-600 text-sm mt-1">{{ errores.observaciones[0] }}</p>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-4">
                        <button 
                            type="submit"
                            class="flex-1 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-semibold transition"
                        >
                            ✅ Actualizar Reserva
                        </button>
                        <Link 
                            href="/reservas"
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
import { reactive, ref, computed } from 'vue';

const props = defineProps({
    reserva: {
        type: Object,
        required: true,
    },
    mesas: {
        type: Array,
        required: true,
    },
});

const formulario = reactive({
    fecha_reserva: props.reserva.fecha_reserva,
    hora_inicio: extractTime(props.reserva.hora_inicio),
    hora_fin: extractTime(props.reserva.hora_fin),
    id_mesa: props.reserva.id_mesa,
    numero_personas: props.reserva.numero_personas,
    estado: props.reserva.estado,
    observaciones: props.reserva.observaciones,
});

const errores = ref({});

function extractTime(datetime) {
    if (!datetime) return '';
    const date = new Date(datetime);
    return date.toTimeString().substring(0, 5);
}

const hoyFormato = computed(() => {
    const hoy = new Date();
    return hoy.toISOString().split('T')[0];
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-BO', {
        year: 'numeric',
        month: 'short',
        day: '2-digit'
    });
};

const actualizarReserva = () => {
    errores.value = {};
    
    router.put(`/reservas/${props.reserva.id_reserva}`, {
        fecha_reserva: formulario.fecha_reserva,
        hora_inicio: formulario.hora_inicio,
        hora_fin: formulario.hora_fin,
        id_mesa: parseInt(formulario.id_mesa),
        numero_personas: formulario.numero_personas,
        estado: formulario.estado,
        observaciones: formulario.observaciones,
    }, {
        onError: (erroresApi) => {
            errores.value = erroresApi;
        }
    });
};
</script>

<style scoped>
</style>
