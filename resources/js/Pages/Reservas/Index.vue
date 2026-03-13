<template>
    <Layout>
        <Head title="Reservas" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">
                        {{ esCliente ? 'Mis Reservas' : 'Todas las Reservas' }}
                    </h1>
                    <Link href="/mesas" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                        + Reservar Mesa
                    </Link>
                </div>

                <!-- Filtros -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <!-- Filtro de Fecha -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Filtrar por:</label>
                            <select 
                                v-model="filtroTipo"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            >
                                <option value="">Todas las reservas</option>
                                <option value="hoy">Hoy</option>
                                <option value="mes">Este Mes</option>
                                <option value="fecha">Fecha personalizada</option>
                            </select>
                        </div>

                        <!-- Fecha Personalizada -->
                        <div v-if="filtroTipo === 'fecha'">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Fecha:</label>
                            <input 
                                v-model="filtroFecha"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            />
                        </div>

                        <!-- Filtro por Hora (solo para staff) -->
                        <div v-if="esGerente">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Rango Horario:</label>
                            <select 
                                v-model="filtroHora"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            >
                                <option value="">Todo el día</option>
                                <option value="morning">Mañana (6:00 - 12:00)</option>
                                <option value="afternoon">Tarde (12:00 - 18:00)</option>
                                <option value="evening">Noche (18:00 - 23:59)</option>
                            </select>
                        </div>

                        <!-- Filtro por Estado (solo para staff) -->
                        <div v-if="esGerente">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Estado:</label>
                            <select 
                                v-model="filtroEstado"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                            >
                                <option value="">Todos</option>
                                <option value="confirmada">Confirmada</option>
                                <option value="completada">Completada</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>

                        <!-- Botón Limpiar -->
                        <div class="flex items-end">
                            <button 
                                @click="limpiarFiltros"
                                class="w-full px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-semibold transition"
                            >
                                Limpiar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Reservas -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mesa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Personas</th>
                                    <th v-if="esGerente" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="reserva in reservasFiltradas" :key="reserva.id_reserva" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        Mesa {{ reserva.mesa?.numero_mesa || 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ formatDate(reserva.fecha_reserva) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ formatTime(reserva.hora_inicio) }} - {{ formatTime(reserva.hora_fin) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <i class="fas fa-users mr-2"></i> {{ reserva.numero_personas }}
                                    </td>
                                    <td v-if="esGerente" class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ nombreCliente(reserva.usuario) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span :class="['px-3 py-1 rounded-full text-xs font-semibold', estadoBadge(reserva.estado)]">
                                            {{ reserva.estado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button 
                                            @click="imprimirReserva(reserva)"
                                            class="text-blue-600 hover:text-blue-900 tooltip"
                                            title="Imprimir reserva"
                                        >
                                            <i class="fas fa-print"></i>
                                        </button>
                                        <button v-if="esGerente"
                                            @click="verDetalles(reserva)"
                                            class="text-orange-600 hover:text-orange-900"
                                        >
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mensaje vacío -->
                    <div v-if="reservasFiltradas.length === 0" class="text-center py-12">
                        <p class="text-gray-500 text-lg">No hay reservas para mostrar</p>
                    </div>
                </div>

                <!-- Paginación -->
                <div v-if="reservas.links && reservas.links.length > 0" class="mt-6 flex justify-center gap-2">
                    <template v-for="link in reservas.links" :key="link.label">
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
import { Head, Link } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    reservas: {
        type: Object,
        required: true,
    },
    esGerente: {
        type: Boolean,
        default: false,
    },
    esCliente: {
        type: Boolean,
        default: false,
    },
});

const filtroTipo = ref('');
const filtroFecha = ref('');
const filtroHora = ref('');
const filtroEstado = ref('');

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-BO', { 
        weekday: 'short', 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
};

const formatTime = (time) => {
    if (!time) return '';
    // Las horas vienen como "HH:MM"
    return time.substring(0, 5);
};

const nombreCliente = (usuario) => {
    if (usuario) {
        return `${usuario.nombre} ${usuario.apellido}`;
    }
    return 'Cliente Anónimo';
};

const estadoBadge = (estado) => {
    return {
        'confirmada': 'bg-green-100 text-green-800',
        'completada': 'bg-blue-100 text-blue-800',
        'cancelada': 'bg-red-100 text-red-800',
    }[estado] || 'bg-gray-100 text-gray-800';
};

const reservasFiltradas = computed(() => {
    let resultado = props.reservas.data || [];

    // Filtro por tipo de fecha
    if (filtroTipo.value === 'hoy') {
        const hoy = new Date().toISOString().split('T')[0];
        resultado = resultado.filter(r => r.fecha_reserva === hoy);
    } else if (filtroTipo.value === 'mes') {
        const ahora = new Date();
        const mesActual = ahora.getMonth();
        const anoActual = ahora.getFullYear();
        resultado = resultado.filter(r => {
            const fecha = new Date(r.fecha_reserva);
            return fecha.getMonth() === mesActual && fecha.getFullYear() === anoActual;
        });
    } else if (filtroTipo.value === 'fecha' && filtroFecha.value) {
        resultado = resultado.filter(r => r.fecha_reserva === filtroFecha.value);
    }

    // Filtro por hora
    if (filtroHora.value) {
        resultado = resultado.filter(r => {
            const horaStr = r.hora_inicio || '';
            const hora = parseInt(horaStr.substring(0, 2) || 0);
            if (filtroHora.value === 'morning') return hora >= 6 && hora < 12;
            if (filtroHora.value === 'afternoon') return hora >= 12 && hora < 18;
            if (filtroHora.value === 'evening') return hora >= 18 && hora < 24;
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
    filtroFecha.value = '';
    filtroHora.value = '';
    filtroEstado.value = '';
};

const imprimirReserva = (reserva) => {
    const ventanaImpresion = window.open('', '', 'width=800,height=600');
    const fecha = new Date(reserva.fecha_reserva).toLocaleDateString('es-BO', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    const horaInicio = formatTime(reserva.hora_inicio);
    const horaFin = formatTime(reserva.hora_fin);
    
    const contenidoHTML = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Comprobante de Reserva</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
                .contenedor { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { text-align: center; margin-bottom: 40px; border-bottom: 3px solid #ea580c; padding-bottom: 20px; }
                .logo { font-size: 28px; font-weight: bold; color: #ea580c; margin-bottom: 10px; }
                .subtitulo { font-size: 14px; color: #666; }
                .titulo { font-size: 24px; font-weight: bold; color: #333; margin: 30px 0 20px 0; text-align: center; }
                .seccion { margin-bottom: 30px; }
                .fila { display: flex; justify-content: space-between; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
                .fila:last-child { border-bottom: none; }
                .etiqueta { font-weight: bold; color: #666; font-size: 14px; }
                .valor { color: #333; font-size: 16px; font-weight: 500; }
                .mesa-grande { font-size: 32px; color: #ea580c; text-align: center; font-weight: bold; }
                .pie { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 2px solid #ea580c; color: #666; font-size: 12px; }
                @media print { body { background: white; } .contenedor { box-shadow: none; } }
            </style>
        </head>
        <body>
            <div class="contenedor">
                <div class="header">
                    <div class="logo">🍗 CHURRASQUERA</div>
                    <div class="subtitulo">Comprobante de Reserva</div>
                </div>
                
                <div class="titulo">Detalles de tu Reserva</div>
                
                <div class="seccion">
                    <div class="fila">
                        <span class="etiqueta">Mesa Reservada:</span>
                        <span class="valor">Mesa #${reserva.mesa?.numero_mesa || 'N/A'}</span>
                    </div>
                    
                    <div class="fila">
                        <span class="etiqueta">Fecha:</span>
                        <span class="valor">${fecha}</span>
                    </div>
                    
                    <div class="fila">
                        <span class="etiqueta">Hora de Inicio:</span>
                        <span class="valor">${horaInicio}</span>
                    </div>
                    
                    <div class="fila">
                        <span class="etiqueta">Hora de Fin:</span>
                        <span class="valor">${horaFin}</span>
                    </div>
                    
                    <div class="fila">
                        <span class="etiqueta">Cantidad de Personas:</span>
                        <span class="valor">${reserva.numero_personas}</span>
                    </div>
                    
                    <div class="fila">
                        <span class="etiqueta">Estado:</span>
                        <span class="valor" style="color: ${reserva.estado === 'confirmada' ? '#16a34a' : reserva.estado === 'completada' ? '#2563eb' : '#dc2626'}">
                            ${reserva.estado.toUpperCase()}
                        </span>
                    </div>
                </div>
                
                ${reserva.observaciones ? `
                <div class="seccion">
                    <div class="etiqueta">Observaciones:</div>
                    <div class="valor" style="margin-top: 10px;">${reserva.observaciones}</div>
                </div>
                ` : ''}
                
                <div class="pie">
                    <p>Gracias por tu confianza 🙏</p>
                    <p>Por favor, presenta este comprobante en el restaurante</p>
                    <p style="margin-top: 10px; font-size: 11px;">Generado: ${new Date().toLocaleString('es-BO')}</p>
                </div>
            </div>
            <script>
                window.print();
            <\/script>
        </body>
        </html>
    `;
    ventanaImpresion.document.write(contenidoHTML);
    ventanaImpresion.document.close();
};

const verDetalles = (reserva) => {
    console.log('Ver detalles de reserva:', reserva);
    // TODO: Implementar modal de detalles
};
</script>

<style scoped>
</style>
