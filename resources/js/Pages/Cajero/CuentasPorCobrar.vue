<template>
    <Layout>
        <Head title="Cuentas por Cobrar" />

        <div class="min-h-screen bg-gradient-to-br from-orange-50 to-gray-100 p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Encabezado -->
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-gray-900">💳 Cuentas por Cobrar</h1>
                    <p class="text-gray-600 mt-2">Registra pagos y completa las ventas</p>
                </div>

                <!-- Mensaje de éxito/error -->
                <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg animate-bounce">
                    ✅ {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    ❌ {{ $page.props.flash.error }}
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                        <p class="text-gray-600 text-sm font-semibold">PENDIENTES</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ pedidos.data?.length || 0 }}</p>
                        <p class="text-gray-500 text-xs mt-1">Pedidos listos</p>
                    </div>
                </div>

                <!-- Tabla de Pedidos -->
                <div v-if="pedidos.data && pedidos.data.length > 0" class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-orange-500 to-red-600 text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Pedido</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Mesa</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Cliente</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Hora</th>
                                    <th class="px-6 py-4 text-right text-sm font-semibold">Total</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold">Estado Pago</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="pedido in pedidos.data" :key="pedido.id_pedido" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-mono font-bold text-orange-600">PED-{{ pedido.id_pedido }}</td>
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-gray-900">Mesa {{ pedido.mesa?.numero_mesa }}</span>
                                        <p class="text-xs text-gray-500">{{ pedido.mesa?.ubicacion }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">
                                        {{ pedido.usuario?.nombre || 'Sin asignar' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        🕐 {{ formatTime(pedido.fecha_pedido) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-orange-600">
                                        Bs. {{ parseFloat(pedido.total).toFixed(2) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span v-if="obtenerEstadoPago(pedido)" :class="[
                                            'px-3 py-1 rounded-full text-xs font-bold',
                                            obtenerEstadoPago(pedido).estado === 'pagado'
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-yellow-100 text-yellow-800'
                                        ]">
                                            {{ obtenerEstadoPago(pedido).texto }}
                                        </span>
                                        <span v-else class="text-gray-500 text-xs">⏳ Sin pago</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex gap-2 flex-wrap justify-center">
                                            <button 
                                                @click="abrirModalPago(pedido)"
                                                class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-3 rounded text-sm transition"
                                            >
                                                💰 Efectivo
                                            </button>
                                            <button 
                                                @click="generarQR(pedido)"
                                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded text-sm transition"
                                            >
                                                📱 QR
                                            </button>
                                            <button 
                                                v-if="obtenerEstadoPago(pedido)?.nro_transaccion"
                                                @click="verificarEstadoDesdeTabla(pedido)"
                                                :disabled="pagoEnVerificacion[pedido.id_pedido]"
                                                class="bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-bold py-2 px-3 rounded text-sm transition"
                                            >
                                                {{ pagoEnVerificacion[pedido.id_pedido] ? '✓ Verificando...' : '✓ Revisar' }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Sin pedidos -->
                <div v-else class="bg-white rounded-lg shadow-lg p-12 text-center">
                    <p class="text-gray-500 text-lg mb-4">✓ No hay pedidos pendientes de cobro</p>
                    <p class="text-gray-400">Todos los pedidos han sido entregados</p>
                </div>

                <!-- Paginación -->
                <div v-if="pedidos.links" class="mt-6 flex justify-center gap-2">
                    <template v-for="link in pedidos.links" :key="link.label">
                        <Link 
                            v-if="link.url"
                            :href="link.url"
                            :class="[
                                'px-3 py-2 rounded-lg text-sm border transition',
                                link.active
                                    ? 'bg-orange-600 text-white border-orange-600'
                                    : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                            ]"
                        >
                            <span v-html="link.label"></span>
                        </Link>
                        <span 
                            v-else
                            :class="[
                                'px-3 py-2 text-sm border cursor-not-allowed',
                                link.active 
                                    ? 'bg-orange-600 text-white border-orange-600'
                                    : 'border-gray-300 text-gray-500'
                            ]"
                        >
                            <span v-html="link.label"></span>
                        </span>
                    </template>
                </div>
            </div>
            <!-- Modal de Pago QR -->
            <div v-if="mostrarModalQR" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-lg shadow-2xl max-w-md w-full p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">📱 Código QR</h2>

                    <div v-if="pedidoSeleccionado" class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-2">
                            <span class="font-semibold">Pedido:</span> PED-{{ pedidoSeleccionado.id_pedido }}
                        </p>
                        <p class="text-xl font-bold text-blue-600">
                            Total: Bs. {{ parseFloat(pedidoSeleccionado.total).toFixed(2) }}
                        </p>
                    </div>

                    <!-- Estado: Esperando Pago -->
                    <div v-if="estadoPagoActual === 'esperando'" class="flex flex-col items-center">
                        <!-- QR Image -->
                        <div v-if="qrGenerado" class="flex justify-center mb-6">
                            <img :src="'data:image/png;base64,' + qrGenerado" alt="QR" class="w-64 h-64 border-4 border-blue-500 rounded-lg" />
                        </div>

                        <div v-else class="flex justify-center mb-6">
                            <div class="w-64 h-64 bg-gray-200 rounded-lg flex items-center justify-center animate-pulse">
                                <span class="text-gray-500">Generando QR...</span>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-sm text-blue-800">
                            <p class="font-semibold mb-2">📋 Instrucciones:</p>
                            <p class="mb-2">1. El cliente escanea el código QR con su billetera digital</p>
                            <p class="mb-2">2. Completa el pago en su aplicación</p>
                            <p>3. El sistema verifica automáticamente en 2 segundos</p>
                        </div>

                        <!-- Estado de Verificación -->
                        <div class="bg-gray-100 rounded-lg p-4 mb-6">
                            <p class="text-xs text-gray-600 mb-3">
                                <span class="font-semibold">Estado:</span>
                                <span v-if="pollingPausado" class="ml-2 text-yellow-600 font-semibold">⏸ PAUSADO</span>
                                <span v-else class="ml-2 text-blue-600 font-semibold">▶ VERIFICANDO</span>
                            </p>
                            <p class="text-xs text-gray-600">
                                <span class="font-semibold">Verificaciones:</span> {{ nroVerificaciones }}
                            </p>
                        </div>
                    </div>

                    <!-- Estado: Pago Completado -->
                    <div v-else-if="estadoPagoActual === 'pagado'" class="flex flex-col items-center py-8">
                        <div class="text-6xl mb-4 animate-bounce">✅</div>
                        <p class="text-2xl font-bold text-green-600 mb-2">¡Pago Confirmado!</p>
                        <p class="text-gray-600 text-center mb-4">PagoFácil ha confirmado la transacción</p>
                        <p class="text-sm text-gray-500">Cerrando en breve...</p>
                    </div>

                    <!-- Estado: Error -->
                    <div v-else-if="estadoPagoActual === 'error'" class="flex flex-col items-center py-8">
                        <div class="text-6xl mb-4">❌</div>
                        <p class="text-xl font-bold text-red-600 mb-2">Error en la Verificación</p>
                        <p class="text-gray-600 text-center mb-4">No se pudo verificar el estado del pago</p>
                    </div>

                    <!-- Botones -->
                    <div v-if="estadoPagoActual === 'esperando'" class="flex gap-2 flex-col">
                        <!-- Fila 1: Verificar Ahora + Pausar/Reanudar -->
                        <div class="flex gap-2">
                            <button 
                                @click="verificarAhora"
                                :disabled="verificandoPago"
                                type="button"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-3 rounded transition disabled:opacity-50"
                            >
                                🔍 {{ verificandoPago ? 'Verificando...' : 'Verificar Ahora' }}
                            </button>
                            <button 
                                v-if="!pollingPausado"
                                @click="pausarVerificacion"
                                type="button"
                                class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-3 rounded transition"
                            >
                                ⏸ Pausar
                            </button>
                            <button 
                                v-else
                                @click="reanudarVerificacion"
                                type="button"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded transition"
                            >
                                ▶ Reanudar
                            </button>
                        </div>

                        <!-- Fila 2: Descargar + Cerrar -->
                        <div class="flex gap-2">
                            <button 
                                @click="descargarQR"
                                type="button"
                                :disabled="!qrGenerado"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded transition disabled:opacity-50"
                            >
                                ⬇️ Descargar
                            </button>
                            <button 
                                @click="cerrarModalQR"
                                type="button"
                                class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-3 rounded transition"
                            >
                                Cerrar
                            </button>
                        </div>
                    </div>

                    <div v-else class="flex gap-3">
                        <button 
                            @click="cerrarModalQR"
                            type="button"
                            class="w-full bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded transition"
                        >
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal de Pago Efectivo -->
            <div v-if="mostrarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-lg shadow-2xl max-w-md w-full p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">💳 Registrar Pago</h2>

                    <div v-if="pedidoSeleccionado" class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-2">
                            <span class="font-semibold">Pedido:</span> PED-{{ pedidoSeleccionado.id_pedido }}
                        </p>
                        <p class="text-sm text-gray-600 mb-2">
                            <span class="font-semibold">Mesa:</span> {{ pedidoSeleccionado.mesa?.numero_mesa }}
                        </p>
                        <p class="text-xl font-bold text-orange-600">
                            Total: Bs. {{ parseFloat(pedidoSeleccionado.total).toFixed(2) }}
                        </p>
                    </div>

                    <form @submit.prevent="enviarPago">
                        <!-- Método de Pago -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Método de Pago *</label>
                            <select 
                                v-model="formularioPago.id_metodo_pago"
                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500"
                            >
                                <option value="">Selecciona método</option>
                                <option v-for="metodo in metodosPago" :key="metodo.id_metodo_pago" :value="metodo.id_metodo_pago">
                                    {{ metodo.nombre }}
                                </option>
                            </select>
                            <p v-if="erroresPago.id_metodo_pago" class="text-red-600 text-sm mt-1">{{ erroresPago.id_metodo_pago[0] }}</p>
                        </div>

                        <!-- Monto -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Monto *</label>
                            <input 
                                v-model.number="formularioPago.monto"
                                type="number"
                                step="0.01"
                                min="0"
                                :placeholder="`Bs. ${parseFloat(pedidoSeleccionado?.total || 0).toFixed(2)}`"
                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500"
                            />
                            <p v-if="erroresPago.monto" class="text-red-600 text-sm mt-1">{{ erroresPago.monto[0] }}</p>
                        </div>

                        <!-- Número de Transacción -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Nro. Transacción (Opcional)</label>
                            <input 
                                v-model="formularioPago.nro_transaccion"
                                type="text"
                                placeholder="Ej: TRX123456"
                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500"
                            />
                        </div>

                        <!-- Botones -->
                        <div class="flex gap-3">
                            <button 
                                @click="cerrarModal"
                                type="button"
                                class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded transition"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit"
                                :disabled="cargandoPago"
                                class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded transition disabled:opacity-50"
                            >
                                {{ cargandoPago ? 'Procesando...' : '✅ Aceptar Pago' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { ref, reactive, onBeforeUnmount } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';

const props = defineProps({
    pedidos: Object,
    metodosPago: Array,
});

const mostrarModal = ref(false);
const mostrarModalQR = ref(false);
const pedidoSeleccionado = ref(null);
const cargandoPago = ref(false);
const cargandoQR = ref(false);
const qrGenerado = ref(null);
const nroTransaccionActual = ref(null); // Guardar el número de transacción
const verificandoPago = ref(false);
const estadoPagoActual = ref('esperando'); // 'esperando', 'pagado', 'error'
const pollingPausado = ref(false);
const nroVerificaciones = ref(0);
const pagoEnVerificacion = ref({}); // Para tracking de verificaciones en la tabla
let intervalVerificacion = null;

const formularioPago = reactive({
    id_metodo_pago: '',
    monto: 0,
    nro_transaccion: '',
});

const erroresPago = ref({});

const abrirModalPago = (pedido) => {
    pedidoSeleccionado.value = pedido;
    formularioPago.id_metodo_pago = '';
    formularioPago.monto = parseFloat(pedido.total);
    formularioPago.nro_transaccion = '';
    erroresPago.value = {};
    mostrarModal.value = true;
};

const cerrarModal = () => {
    mostrarModal.value = false;
    pedidoSeleccionado.value = null;
};

const cerrarModalQR = () => {
    // Limpiar intervalo si existe
    if (intervalVerificacion) {
        clearInterval(intervalVerificacion);
        intervalVerificacion = null;
    }
    mostrarModalQR.value = false;
    qrGenerado.value = null;
    nroTransaccionActual.value = null;
    estadoPagoActual.value = 'esperando';
    pollingPausado.value = false;
    nroVerificaciones.value = 0;
};

const enviarPago = () => {
    if (!pedidoSeleccionado.value) return;

    cargandoPago.value = true;

    router.post(`/cajero/registrar-pago/${pedidoSeleccionado.value.id_pedido}`, {
        id_metodo_pago: parseInt(formularioPago.id_metodo_pago),
        monto: formularioPago.monto,
        nro_transaccion: formularioPago.nro_transaccion,
    }, {
        onSuccess: () => {
            cerrarModal();
            cargandoPago.value = false;
            router.reload();
        },
        onError: (errors) => {
            erroresPago.value = errors;
            cargandoPago.value = false;
        }
    });
};

const verificarPago = async () => {
    console.log('🔍 verificarPago iniciada');
    console.log('  - nroTransaccionActual:', nroTransaccionActual.value);
    console.log('  - pollingPausado:', pollingPausado.value);
    
    if (!nroTransaccionActual.value || pollingPausado.value) {
        console.log('❌ BLOQUEADO: No hay transacción o polling pausado');
        return;
    }

    verificandoPago.value = true;
    nroVerificaciones.value++;

    try {
        const url = `/cajero/verificar-transaccion/${nroTransaccionActual.value}`;
        console.log('📡 Fetch a:', url);
        
        // Consultar directamente por número de transacción
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        console.log('📦 Response status:', response.status);
        
        const data = await response.json();
        
        console.log('✅ Respuesta JSON:', data);

        if (data.completado) {
            // ✅ Pago completado - PagoFácil confirmó
            console.log('🎉 PAGO COMPLETADO');
            estadoPagoActual.value = 'pagado';

            // Limpiar intervalo
            if (intervalVerificacion) {
                clearInterval(intervalVerificacion);
                intervalVerificacion = null;
            }

            // Esperar 2 segundos antes de cerrar y recargar
            setTimeout(() => {
                cerrarModalQR();
                router.reload();
            }, 2000);
        } else {
            console.log('⏳ Pago aun pendiente - paymentStatus:', data.paymentStatus);
        }
    } catch (error) {
        console.error('❌ Error verificando pago:', error);
        estadoPagoActual.value = 'error';
    } finally {
        verificandoPago.value = false;
    }
};

const verificarAhora = () => {
    console.log('👆 Click en "Verificar Ahora"');
    console.log('  - pollingPausado antes:', pollingPausado.value);
    
    if (pollingPausado.value) {
        console.log('  ➡️ Reanudando (estaba pausado)');
        pollingPausado.value = false;
    }
    
    console.log('  ➡️ Llamando a verificarPago()');
    verificarPago();
};

const pausarVerificacion = () => {
    pollingPausado.value = true;
    if (intervalVerificacion) {
        clearInterval(intervalVerificacion);
        intervalVerificacion = null;
    }
};

const reanudarVerificacion = () => {
    pollingPausado.value = false;
    if (intervalVerificacion) {
        clearInterval(intervalVerificacion);
    }
    intervalVerificacion = setInterval(() => {
        if (!pollingPausado.value) {
            verificarPago();
        }
    }, 2000);
};

const generarQR = async (pedido) => {
    pedidoSeleccionado.value = pedido;
    cargandoQR.value = true;
    estadoPagoActual.value = 'esperando';
    pollingPausado.value = false;
    nroVerificaciones.value = 0;
    nroTransaccionActual.value = null;

    try {
        const response = await fetch(`/cajero/generar-qr/${pedido.id_pedido}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
            body: JSON.stringify({
                cliente_email: 'cajero@restaurante.com'
            })
        });

        // ✅ Verificar si la respuesta es JSON válida
        if (!response.ok && response.status >= 500) {
            throw new Error(`Error del servidor (${response.status})`);
        }

        let data;
        try {
            data = await response.json();
        } catch (parseError) {
            console.error('Error al parsear JSON:', parseError);
            console.error('Respuesta del servidor:', await response.text());
            throw new Error('Respuesta inválida del servidor');
        }

        if (data.success) {
            qrGenerado.value = data.qr_image;
            nroTransaccionActual.value = data.nro_transaccion;
            console.log('✅ QR Generado - Transacción:', data.nro_transaccion);
            mostrarModalQR.value = true;
        } else {
            alert('❌ Error: ' + (data.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error en generarQR:', error);
        alert('❌ Error al generar QR: ' + error.message);
    } finally {
        cargandoQR.value = false;
    }
};

const descargarQR = () => {
    if (!qrGenerado.value) return;

    const link = document.createElement('a');
    link.href = 'data:image/png;base64,' + qrGenerado.value;
    link.download = `QR-Pedido-${pedidoSeleccionado.value.id_pedido}.png`;
    link.click();
};

const obtenerEstadoPago = (pedido) => {
    // Obtener el primer pago del pedido (a través de venta)
    if (!pedido.ventas || pedido.ventas.length === 0) return null;
    
    const venta = pedido.ventas[0];
    if (!venta.pagos || venta.pagos.length === 0) return null;
    
    const pago = venta.pagos[0];
    
    return {
        nro_transaccion: pago.nro_transaccion,
        estado: pago.estado === 'completado' ? 'pagado' : 'pendiente',
        texto: pago.estado === 'completado' ? '✅ Pagado' : '⏳ Pendiente',
        pago: pago
    };
};

const verificarEstadoDesdeTabla = async (pedido) => {
    const estadoPago = obtenerEstadoPago(pedido);
    if (!estadoPago?.nro_transaccion) {
        alert('No hay transacción para verificar');
        return;
    }
    
    console.log('🔍 Verificando estado desde tabla:', pedido.id_pedido, 'Transacción:', estadoPago.nro_transaccion);
    
    pagoEnVerificacion.value[pedido.id_pedido] = true;

    try {
        const response = await fetch(`/cajero/verificar-transaccion/${estadoPago.nro_transaccion}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        const data = await response.json();
        console.log('✅ Respuesta:', data);

        if (data.completado) {
            console.log('🎉 PAGO CONFIRMADO');
            // Recarga la página para actualizar la tabla
            router.reload();
        } else {
            console.log('⏳ Pago pendiente - Status:', data.paymentStatus);
            alert(`⏳ Pago aún pendiente (Estado: ${data.statusDescription || 'En proceso'})`);
        }
    } catch (error) {
        console.error('❌ Error verificando:', error);
        alert('Error al verificar pago');
    } finally {
        pagoEnVerificacion.value[pedido.id_pedido] = false;
    }
};

const formatTime = (date) => {
    return new Date(date).toLocaleString('es-BO', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
};

// Limpiar intervalo cuando el componente se desmonta
onBeforeUnmount(() => {
    if (intervalVerificacion) {
        clearInterval(intervalVerificacion);
    }
});
</script>
