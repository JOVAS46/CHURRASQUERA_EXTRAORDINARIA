<template>
    <Layout>
        <div class="container mx-auto px-4 py-8 max-w-3xl">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Detalle de Reserva</h1>
                <Link href="/reservas" class="text-orange-600 hover:text-orange-800 font-bold">← Volver</Link>
            </div>

            <div v-if="$page.props.flash.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ $page.props.flash.success }}
            </div>

            <!-- Información General -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Información de la Reserva</h2>
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-600">N° Reserva:</span>
                            <p class="font-mono font-semibold">{{ props.reserva.numero_reserva }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Fecha:</span>
                            <p class="font-semibold">{{ formatDate(props.reserva.fecha_reserva) }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Hora:</span>
                            <p class="font-mono font-semibold">{{ props.reserva.hora_reserva }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Mesa:</span>
                            <p class="font-semibold">Mesa {{ props.reserva.mesa?.numero }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Cantidad de Personas:</span>
                            <p class="font-semibold text-lg">{{ props.reserva.cantidad_personas }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Datos del Cliente</h2>
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-600">Nombre:</span>
                            <p class="font-semibold">{{ props.reserva.nombre_cliente }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Teléfono:</span>
                            <p class="font-mono">{{ props.reserva.telefono }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Email:</span>
                            <p class="text-orange-600 font-semibold">{{ props.reserva.email || '-' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Estado:</span>
                            <p class="mt-1">
                                <span :class="getStatusBadge(props.reserva.estado)" class="px-3 py-1 rounded font-semibold">
                                    {{ props.reserva.estado }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <span class="text-gray-600">Registrado por:</span>
                            <p class="font-semibold">{{ props.reserva.usuario?.nombre }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Observaciones -->
            <div v-if="props.reserva.observaciones" class="bg-blue-50 rounded-lg border border-blue-200 p-4 mb-6">
                <h3 class="font-semibold text-blue-900 mb-2">Observaciones</h3>
                <p class="text-blue-800">{{ props.reserva.observaciones }}</p>
            </div>

            <!-- Acciones -->
            <div class="flex gap-4">
                <Link v-if="props.reserva.estado === 'confirmada'" :href="`/reservas/${props.reserva.id_reserva}/edit`" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Editar Reserva
                </Link>
                <button v-if="props.reserva.estado === 'confirmada'" @click="confirmarCancelacion" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Cancelar Reserva
                </button>
                <Link href="/reservas" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Volver
                </Link>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';

const props = defineProps({
    reserva: Object,
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-BO', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
};

const getStatusBadge = (status) => {
    const colors = {
        'confirmada': 'bg-green-100 text-green-700',
        'cancelada': 'bg-red-100 text-red-700'
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
};

const confirmarCancelacion = () => {
    if (confirm('¿Estás seguro de que deseas cancelar esta reserva?')) {
        router.delete(`/reservas/${props.reserva.id_reserva}`);
    }
};
</script>
