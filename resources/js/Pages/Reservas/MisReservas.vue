<template>
    <Layout>
        <Head title="Mis Reservas" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">📅 Mis Reservas</h1>
                    <Link href="/mesas" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                        + Nueva Reserva
                    </Link>
                </div>

                <!-- Mensaje de éxito -->
                <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    ✅ {{ $page.props.flash.success }}
                </div>

                <!-- Filtros -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Filtro por Tipo -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Filtrar por:</label>
                            <select 
                                v-model="filtroTipo"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            >
                                <option value="">Todas</option>
                                <option value="hoy">Hoy</option>
                                <option value="proximo">Próximas</option>
                                <option value="pasadas">Pasadas</option>
                            </select>
                        </div>

                        <!-- Filtro por Estado -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Estado:</label>
                            <select 
                                v-model="filtroEstado"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            >
                                <option value="">Todos los estados</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="confirmada">Confirmada</option>
                                <option value="completada">Completada</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>

                        <!-- Botón Limpiar -->
                        <div class="flex items-end">
                            <button @click="limpiarFiltros" class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                🔄 Limpiar Filtros
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Reservas -->
                <div v-if="reservasFiltradas.length > 0" class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mesa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Personas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="reserva in reservasFiltradas" :key="reserva.id_reserva" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm font-semibold text-gray-900">
                                            Mesa {{ reserva.mesa?.numero_mesa }}
                                        </p>
                                        <p class="text-xs text-gray-500">Capacidad: {{ reserva.mesa?.capacidad }} personas</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ formatDate(reserva.fecha_reserva) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ formatTime(reserva.hora_inicio) }} - {{ formatTime(reserva.hora_fin) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        👥 {{ reserva.numero_personas }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="['px-3 py-1 rounded-full text-xs font-semibold', estadoBadge(reserva.estado)]">
                                            {{ reserva.estado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                        <Link :href="`/reservas/${reserva.id_reserva}/edit`" class="text-orange-600 hover:text-orange-700 font-semibold">
                                            ✏️ Editar
                                        </Link>
                                        <button @click="cancelarReserva(reserva)" 
                                            v-if="!['completada', 'cancelada'].includes(reserva.estado)"
                                            class="text-red-600 hover:text-red-700 font-semibold">
                                            ❌ Cancelar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mensaje Vacío -->
                <div v-else class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <p class="text-gray-500 text-lg mb-6">No tienes reservas registradas</p>
                    <Link href="/mesas" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                        📅 Hacer una Reserva
                    </Link>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';

const props = defineProps({
    reservas: {
        type: Object,
        required: true,
    },
});

const filtroTipo = ref('');
const filtroEstado = ref('');

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-BO', {
        year: 'numeric',
        month: 'short',
        day: '2-digit'
    });
};

const formatTime = (time) => {
    if (!time) return '';
    // Las horas vienen como "HH:MM" directamente
    return time.substring(0, 5);
};

const estadoBadge = (estado) => {
    const colores = {
        'pendiente': 'bg-yellow-100 text-yellow-800',
        'confirmada': 'bg-blue-100 text-blue-800',
        'completada': 'bg-green-100 text-green-800',
        'cancelada': 'bg-red-100 text-red-800',
    };
    return colores[estado] || 'bg-gray-100 text-gray-800';
};

const reservasFiltradas = computed(() => {
    let resultado = props.reservas.data || [];

    // Filtro por tipo
    if (filtroTipo.value) {
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);

        resultado = resultado.filter(r => {
            const fechaReserva = new Date(r.fecha_reserva);
            fechaReserva.setHours(0, 0, 0, 0);

            switch (filtroTipo.value) {
                case 'hoy':
                    return fechaReserva.getTime() === hoy.getTime();
                case 'proximo':
                    return fechaReserva.getTime() >= hoy.getTime();
                case 'pasadas':
                    return fechaReserva.getTime() < hoy.getTime();
                default:
                    return true;
            }
        });
    }

    // Filtro por estado
    if (filtroEstado.value) {
        resultado = resultado.filter(r => r.estado === filtroEstado.value);
    }

    return resultado;
});

const limpiarFiltros = () => {
    filtroTipo.value = '';
    filtroEstado.value = '';
};

const cancelarReserva = (reserva) => {
    if (confirm(`¿Estás seguro de que deseas cancelar la reserva de la Mesa ${reserva.mesa.numero_mesa}?`)) {
        router.patch(`/reservas/${reserva.id_reserva}`, {
            estado: 'cancelada'
        });
    }
};
</script>

<style scoped>
</style>
