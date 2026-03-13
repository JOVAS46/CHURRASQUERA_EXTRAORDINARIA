<template>
    <Layout>
        <Head title="Gestión de Mesas" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Gestión de Mesas</h1>
                    <Link href="/mesas/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                        + Nueva Mesa
                    </Link>
                </div>

                <!-- Mensaje de Éxito -->
                <div v-if="mensajeExito" class="mb-6 p-6 bg-gradient-to-r from-green-400 to-green-600 border-4 border-green-700 text-white rounded-lg shadow-lg animate-bounce">
                    <div class="flex items-center gap-4">
                        <span class="text-5xl"><i class="fas fa-check-circle text-green-500"></i></span>
                        <div>
                            <p class="font-bold text-2xl">¡RESERVA CREADA!</p>
                            <p class="text-lg mt-2">Tu reserva ha sido confirmada correctamente</p>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                    <div class="flex gap-4 flex-wrap">
                        <button
                            @click="filtroEstado = ''"
                            :class="[
                                'px-4 py-2 rounded-lg font-semibold transition',
                                filtroEstado === ''
                                    ? 'bg-orange-600 text-white'
                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                            ]"
                        >
                            Todas
                        </button>
                        <button
                            @click="filtroEstado = 'disponible'"
                            :class="[
                                'px-4 py-2 rounded-lg font-semibold transition',
                                filtroEstado === 'disponible'
                                    ? 'bg-orange-600 text-white'
                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                            ]"
                        >
                            Disponibles
                        </button>
                        <button
                            @click="filtroEstado = 'ocupada'"
                            :class="[
                                'px-4 py-2 rounded-lg font-semibold transition',
                                filtroEstado === 'ocupada'
                                    ? 'bg-orange-600 text-white'
                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                            ]"
                        >
                            Ocupadas
                        </button>
                        <button
                            @click="filtroEstado = 'mantenimiento'"
                            :class="[
                                'px-4 py-2 rounded-lg font-semibold transition',
                                filtroEstado === 'mantenimiento'
                                    ? 'bg-orange-600 text-white'
                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                            ]"
                        >
                            Mantenimiento
                        </button>
                    </div>
                </div>

                <!-- Grilla de Mesas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <div v-for="mesa in mesasFiltradas" :key="mesa.id_mesa" 
                        :class="['p-4 rounded-lg shadow-sm cursor-pointer transition hover:shadow-lg', estadoClass(mesa.estado)]">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">Mesa {{ mesa.numero_mesa }}</h3>
                                <p class="text-sm text-gray-600 mt-1">👥 {{ mesa.capacidad }} personas</p>
                            </div>
                            <span :class="['px-3 py-1 rounded-full text-xs font-semibold', estadoBadge(mesa.estado)]">
                                {{ mesa.estado }}
                            </span>
                        </div>

                        <div class="bg-gray-50 rounded px-3 py-2 mb-4">
                            <p class="text-xs text-gray-600"><i class="fas fa-map-pin mr-1"></i> Ubicación</p>
                            <p class="text-sm font-semibold text-gray-900">{{ mesa.ubicacion }}</p>
                        </div>

                        <div class="flex gap-2">
                            <!-- Link to create reservation with mesa parameter -->
                            <Link :href="`/reservas/create?mesa=${mesa.id_mesa}`" 
                                class="flex-1 text-center text-sm font-medium px-3 py-2 rounded-md bg-orange-600 text-white hover:bg-orange-700 transition"
                                :disabled="mesa.estado === 'mantenimiento'"
                            >
                                📅 Reservar
                            </Link>
                            <Link :href="`/mesas/${mesa.id_mesa}/edit`" 
                                class="text-center text-sm font-medium px-3 py-2 rounded-md bg-orange-100 text-orange-700 hover:bg-orange-200 transition"
                            >
                                <i class="fas fa-edit"></i>
                            </Link>
                            <button @click="deleteMesa(mesa)" 
                                class="text-center text-sm font-medium px-3 py-2 rounded-md bg-red-100 text-red-700 hover:bg-red-200 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Paginación -->
                <div v-if="mesas.links" class="mt-8 flex justify-center gap-2">
                    <template v-for="link in mesas.links" :key="link.label">
                        <Link 
                            v-if="link.url"
                            :href="link.url"
                            :class="[
                                'px-4 py-2 rounded border text-sm',
                                link.active
                                    ? 'bg-orange-600 text-white border-orange-600'
                                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                            ]"
                            v-html="link.label"
                        ></Link>
                        <span 
                            v-else
                            :class="[
                                'px-4 py-2 rounded border text-sm',
                                'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                            ]"
                            v-html="link.label"
                        ></span>
                    </template>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { ref, computed, onMounted, watch } from 'vue';

const page = usePage();

const props = defineProps({
    mesas: {
        type: Object,
        required: true,
    },
});

const filtroEstado = ref('');
const mensajeExito = ref(false);

const mesasFiltradas = computed(() => {
    if (!filtroEstado.value) return props.mesas.data;
    return props.mesas.data.filter(mesa => mesa.estado === filtroEstado.value);
});

const estadoClass = (estado) => {
    return {
        'disponible': 'bg-green-50 border-2 border-green-200',
        'ocupada': 'bg-yellow-50 border-2 border-yellow-200',
        'mantenimiento': 'bg-red-50 border-2 border-red-200',
    }[estado] || 'bg-gray-50 border-2 border-gray-200';
};

const estadoBadge = (estado) => {
    return {
        'disponible': 'bg-green-100 text-green-800',
        'ocupada': 'bg-yellow-100 text-yellow-800',
        'mantenimiento': 'bg-red-100 text-red-800',
    }[estado] || 'bg-gray-100 text-gray-800';
};

const deleteMesa = (mesa) => {
    if (confirm(`¿Estás seguro de que deseas eliminar la Mesa ${mesa.numero_mesa}?`)) {
        router.delete(`/mesas/${mesa.id_mesa}`);
    }
};

// Monitorear mensajes flash
watch(() => page.props.flash?.success, (newVal) => {
    if (newVal) {
        mensajeExito.value = true;
        setTimeout(() => {
            mensajeExito.value = false;
        }, 10000);
    }
}, { deep: true });

// Verificar si hay mensaje flash al cargar
onMounted(() => {
    if (page.props.flash?.success) {
        mensajeExito.value = true;
        setTimeout(() => {
            mensajeExito.value = false;
        }, 10000);
    }
});
</script>

<style scoped>
</style>
