<template>
    <Layout>
        <Head title="Nueva Reserva" />
        
        <div class="py-6">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 md:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Crear Nueva Reserva</h1>

                <!-- Mensaje de Éxito -->
                <div v-if="mensajeExito" class="mb-6 p-6 bg-gradient-to-r from-green-400 to-green-600 border-4 border-green-700 text-white rounded-lg shadow-lg animate-bounce">
                    <div class="flex items-center gap-4">
                        <span class="text-5xl">✅</span>
                        <div>
                            <p class="font-bold text-2xl">¡RESERVA CREADA!</p>
                            <p class="text-lg mt-2">Tu reserva ha sido confirmada correctamente</p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="crearReserva" class="bg-white rounded-lg shadow-sm p-6">
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
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Mesa Seleccionada *</label>
                        <div v-if="mesaPreseleccionada" class="p-4 bg-gradient-to-r from-orange-50 to-orange-100 border-2 border-orange-500 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-2xl font-bold text-orange-900">Mesa {{ mesaPreseleccionada.numero_mesa }}</p>
                                    <p class="text-sm text-orange-700 mt-1">👥 Capacidad: {{ mesaPreseleccionada.capacidad }} personas</p>
                                    <p class="text-sm text-orange-700 mt-1">📍 {{ mesaPreseleccionada.ubicacion }}</p>
                                </div>
                                <span :class="['px-3 py-1 rounded-full text-xs font-semibold', 
                                    mesaPreseleccionada.estado === 'disponible' ? 'bg-green-100 text-green-800' :
                                    mesaPreseleccionada.estado === 'ocupada' ? 'bg-yellow-100 text-yellow-800' :
                                    'bg-red-100 text-red-800'
                                ]">
                                    {{ mesaPreseleccionada.estado }}
                                </span>
                            </div>
                        </div>
                        <select 
                            v-else
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

                    <!-- Observaciones -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Observaciones (Opcional)</label>
                        <textarea 
                            v-model="formulario.observaciones"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            placeholder="Alergias, preferencias, información adicional..."
                        ></textarea>
                        <p v-if="errores.observaciones" class="text-red-600 text-sm mt-1">{{ errores.observaciones[0] }}</p>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-4">
                        <button 
                            type="submit"
                            class="flex-1 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-semibold transition"
                        >
                             Crear Reserva
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
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { reactive, ref, computed, watch, onMounted } from 'vue';

const page = usePage();

const props = defineProps({
    mesas: {
        type: Array,
        required: true,
    },
    mesaPreseleccionada: {
        type: Object,
        default: null,
    },
});

const formulario = reactive({
    fecha_reserva: '',
    hora_inicio: '20:00',
    hora_fin: '21:30',
    id_mesa: props.mesaPreseleccionada?.id_mesa || '',
    numero_personas: 2,
    observaciones: '',
});

const errores = ref({});
const mensajeExito = ref(false);

const hoyFormato = computed(() => {
    const hoy = new Date();
    return hoy.toISOString().split('T')[0];
});

const crearReserva = () => {
    errores.value = {};
    
    router.post('/reservas', {
        fecha_reserva: formulario.fecha_reserva,
        hora_inicio: formulario.hora_inicio,
        hora_fin: formulario.hora_fin,
        id_mesa: parseInt(formulario.id_mesa),
        numero_personas: formulario.numero_personas,
        observaciones: formulario.observaciones,
    }, {
        onError: (erroresApi) => {
            errores.value = erroresApi;
        }
    });
};

// Monitorear cambios en el mensaje flash
watch(() => page.props.flash?.success, (newVal) => {
    if (newVal) {
        mensajeExito.value = true;
        // Desaparecer después de 15 segundos
        setTimeout(() => {
            mensajeExito.value = false;
        }, 15000);
    }
}, { deep: true });

// Verificar si hay mensaje flash al cargar
onMounted(() => {
    if (page.props.flash?.success) {
        mensajeExito.value = true;
        // Desaparecer después de 15 segundos
        setTimeout(() => {
            mensajeExito.value = false;
        }, 15000);
    }
});
</script>

<style scoped>
</style>
