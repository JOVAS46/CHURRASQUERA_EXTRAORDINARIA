<template>
    <Layout>
        <Head title="Mis Reservas" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Mis Reservas</h1>
                    <Link href="/cliente/reservas/crear" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                        + Nueva Reserva
                    </Link>
                </div>

                <!-- Filtros -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <div class="flex gap-4 flex-wrap">
                        <button
                            v-for="filtro in filtros"
                            :key="filtro"
                            @click="filtroActual = filtro"
                            :class="[
                                'px-4 py-2 rounded-lg font-semibold transition',
                                filtroActual === filtro
                                    ? 'bg-orange-600 text-white'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                            ]"
                        >
                            {{ filtro === 'todas' ? 'Todas' : filtro === 'proximas' ? 'Próximas' : filtro === 'pasadas' ? 'Pasadas' : 'Canceladas' }}
                        </button>
                    </div>
                </div>

                <!-- Reservas List -->
                <div v-if="reservasFiltradas.length > 0" class="space-y-4">
                    <div v-for="reserva in reservasFiltradas" :key="reserva.id_reserva" class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
                        <div :class="['h-1', borderColorByEstado(reserva.estado)]"></div>
                        
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">Mesa para {{ reserva.numero_personas }} {{ reserva.numero_personas === 1 ? 'persona' : 'personas' }}</h3>
                                    <p class="text-sm text-gray-500">Reserva ID: {{ reserva.id_reserva }}</p>
                                </div>
                                <span :class="badgeColorByEstado(reserva.estado)" class="px-3 py-1 rounded-full text-sm font-semibold">
                                    {{ reserva.estado }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-xs text-gray-600 uppercase tracking-wide mb-1">Fecha</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ formatDate(reserva.fecha_reserva) }}</p>
                                </div>
                                
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-xs text-gray-600 uppercase tracking-wide mb-1">Hora</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ formatTime(reserva.hora_reserva) }}</p>
                                </div>

                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-xs text-gray-600 uppercase tracking-wide mb-1">Mesa Asignada</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ reserva.numero_mesa || 'Pendiente' }}</p>
                                </div>
                            </div>

                            <!-- Observaciones -->
                            <div v-if="reserva.observaciones" class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                                <p class="text-sm text-gray-700">
                                    <span class="font-semibold text-blue-900">📝 Observaciones:</span> {{ reserva.observaciones }}
                                </p>
                            </div>

                            <!-- Acciones -->
                            <div class="flex gap-3 justify-end">
                                <Link
                                    v-if="canEdit(reserva)"
                                    :href="`/cliente/reservas/${reserva.id_reserva}/edit`"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    <i class="fas fa-edit mr-1"></i> Editar
                                </Link>
                                
                                <button
                                    v-if="canCancel(reserva)"
                                    @click="cancelarReserva(reserva)"
                                    class="inline-flex items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50"
                                >
                                    ✕ Cancelar
                                </button>

                                <Link
                                    :href="`/cliente/reservas/${reserva.id_reserva}`"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700"
                                >
                                    👁️ Ver Detalles
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="bg-white rounded-lg shadow p-12 text-center">
                    <p class="text-gray-500 text-lg mb-4">No tienes {{ filtroActual === 'todas' ? 'reservas' : 'reservas ' + filtroActual }}</p>
                    <Link href="/cliente/reservas/crear" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                        Crear tu primera reserva
                    </Link>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    reservas: {
        type: Array,
        default: () => [],
    },
});

const filtroActual = ref('todas');
const filtros = ['todas', 'proximas', 'pasadas', 'canceladas'];

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-BO', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatTime = (time) => {
    return new Date(`2024-01-01 ${time}`).toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit' });
};

const borderColorByEstado = (estado) => {
    return {
        'confirmada': 'bg-green-500',
        'pendiente': 'bg-yellow-500',
        'cancelada': 'bg-red-500',
        'completada': 'bg-blue-500',
    }[estado] || 'bg-gray-500';
};

const badgeColorByEstado = (estado) => {
    return {
        'confirmada': 'bg-green-100 text-green-800',
        'pendiente': 'bg-yellow-100 text-yellow-800',
        'cancelada': 'bg-red-100 text-red-800',
        'completada': 'bg-blue-100 text-blue-800',
    }[estado] || 'bg-gray-100 text-gray-800';
};

const canEdit = (reserva) => {
    const fecha = new Date(reserva.fecha_reserva);
    const hoy = new Date();
    return ['confirmada', 'pendiente'].includes(reserva.estado) && fecha > hoy;
};

const canCancel = (reserva) => {
    const fecha = new Date(reserva.fecha_reserva);
    const hoy = new Date();
    return ['confirmada', 'pendiente'].includes(reserva.estado) && fecha > hoy;
};

const reservasFiltradas = computed(() => {
    const hoy = new Date();
    
    switch (filtroActual.value) {
        case 'proximas':
            return props.reservas.filter(r => {
                const fechaReserva = new Date(r.fecha_reserva);
                return ['confirmada', 'pendiente'].includes(r.estado) && fechaReserva >= hoy;
            }).sort((a, b) => new Date(a.fecha_reserva) - new Date(b.fecha_reserva));
        
        case 'pasadas':
            return props.reservas.filter(r => {
                const fechaReserva = new Date(r.fecha_reserva);
                return r.estado === 'completada' || (fechaReserva < hoy && r.estado !== 'cancelada');
            }).sort((a, b) => new Date(b.fecha_reserva) - new Date(a.fecha_reserva));
        
        case 'canceladas':
            return props.reservas.filter(r => r.estado === 'cancelada')
                .sort((a, b) => new Date(b.fecha_reserva) - new Date(a.fecha_reserva));
        
        default: // 'todas'
            return props.reservas.sort((a, b) => new Date(b.fecha_reserva) - new Date(a.fecha_reserva));
    }
});

const cancelarReserva = (reserva) => {
    if (confirm('¿Estás seguro de que deseas cancelar esta reserva?')) {
        router.patch(`/cliente/reservas/${reserva.id_reserva}`, {
            estado: 'cancelada'
        });
    }
};
</script>
