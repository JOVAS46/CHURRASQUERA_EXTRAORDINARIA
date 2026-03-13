<template>
    <Layout>
        <Head title="Crear Pedido" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6"><i class="fas fa-receipt mr-2"></i> Crear Nuevo Pedido</h1>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Panel Izquierdo - Formulario y Productos -->
                    <div class="lg:col-span-2">
                        <!-- Selección de Mesa -->
                        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-3"><i class="fas fa-chair mr-1"></i> Seleccionar Mesa *</label>
                            <select 
                                v-model="formulario.id_mesa"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500"
                            >
                                <option value="">Elige una mesa</option>
                                <option v-for="mesa in mesas" :key="mesa.id_mesa" :value="mesa.id_mesa">
                                    Mesa {{ mesa.numero_mesa }} - Capacidad: {{ mesa.capacidad }} personas ({{ mesa.ubicacion }}) {{ mesa.estado !== 'disponible' ? `[${mesa.estado.toUpperCase()}]` : '' }}
                                </option>
                            </select>
                            <p v-if="errors.id_mesa" class="text-red-600 text-sm mt-2"><i class="fas fa-exclamation-triangle mr-1 text-yellow-600"></i> {{ errors.id_mesa[0] }}</p>
                        </div>

                        <!-- Grid de Productos -->
                        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4"><i class="fas fa-drumstick-chicken mr-2"></i> Productos Disponibles</h2>
                            
                            <!-- Buscador de Productos -->
                            <input 
                                v-model="buscarProducto"
                                type="text"
                                placeholder="<i class='fas fa-search'></i> Buscar producto..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-4 focus:outline-none focus:border-orange-500"
                            />

                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div 
                                    v-for="producto in productosFiltrados"
                                    :key="producto.id_producto"
                                    @click="agregarAlCarrito(producto)"
                                    class="p-4 border-2 border-gray-200 rounded-lg hover:border-orange-500 hover:shadow-lg transition cursor-pointer transform hover:scale-105"
                                >
                                    <p class="font-semibold text-gray-900 text-sm truncate">{{ producto.nombre }}</p>
                                    <p class="text-xs text-gray-500 mb-2">{{ producto.categoria?.nombre || 'General' }}</p>
                                    <p class="text-lg font-bold text-orange-600">Bs. {{ parseFloat(producto.precio).toFixed(2) }}</p>
                                    <button class="mt-2 w-full bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold py-1 px-2 rounded transition">
                                        + Agregar
                                    </button>
                                </div>
                            </div>

                            <p v-if="productosFiltrados.length === 0" class="text-center text-gray-500 py-8">
                                No hay productos que coincidan con tu búsqueda
                            </p>
                        </div>
                    </div>

                    <!-- Panel Derecho - Carrito -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4"><i class="fas fa-shopping-cart mr-2"></i> Carrito</h2>

                            <!-- Items del Carrito -->
                            <div v-if="formulario.detalles.length > 0" class="space-y-3 max-h-96 overflow-y-auto mb-4">
                                <div 
                                    v-for="(item, idx) in formulario.detalles"
                                    :key="idx"
                                    class="bg-gray-50 p-3 rounded-lg border border-gray-200"
                                >
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="font-semibold text-sm text-gray-900">{{ item.nombreProducto }}</p>
                                            <p class="text-xs text-gray-500">Bs. {{ parseFloat(item.precioUnitario).toFixed(2) }} c/u</p>
                                        </div>
                                        <button @click="eliminarDelCarrito(idx)" class="text-red-600 hover:text-red-800 text-lg">×</button>
                                    </div>

                                    <!-- Cantidad -->
                                    <div class="flex items-center gap-2 mt-2">
                                        <button @click="decrementarCantidad(idx)" class="bg-gray-300 hover:bg-gray-400 w-6 h-6 rounded text-xs font-bold">−</button>
                                        <input 
                                            v-model.number="item.cantidad"
                                            type="number"
                                            min="1"
                                            class="w-12 text-center px-2 py-1 border border-gray-300 rounded"
                                            @input="actualizarCarrito"
                                        />
                                        <button @click="incrementarCantidad(idx)" class="bg-gray-300 hover:bg-gray-400 w-6 h-6 rounded text-xs font-bold">+</button>
                                        <span class="ml-auto font-bold text-orange-600">Bs. {{ item.subtotal.toFixed(2) }}</span>
                                    </div>

                                    <!-- Observaciones -->
                                    <input 
                                        v-model="item.observaciones"
                                        type="text"
                                        placeholder="Obs (sem sal...)"
                                        class="w-full text-xs px-2 py-1 border border-gray-300 rounded mt-2"
                                    />
                                </div>
                            </div>

                            <div v-else class="bg-gray-50 p-6 rounded-lg text-center text-gray-500 mb-4">
                                <p class="text-sm"><i class="fas fa-arrow-down mr-2"></i> Selecciona productos para agregar</p>
                            </div>

                            <!-- Resumen -->
                            <div class="border-t-2 border-gray-200 pt-4 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Subtotal:</span>
                                    <span class="font-semibold">Bs. {{ subtotal.toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold bg-gradient-to-r from-orange-100 to-orange-50 p-3 rounded-lg">
                                    <span class="text-gray-900">TOTAL:</span>
                                    <span class="text-orange-600">Bs. {{ subtotal.toFixed(2) }}</span>
                                </div>
                            </div>

                            <!-- Botón Enviar -->
                            <button 
                                @click="enviarFormulario"
                                :disabled="!formulario.id_mesa || formulario.detalles.length === 0"
                                :class="[
                                    'w-full mt-4 py-3 rounded-lg font-bold transition text-white',
                                    !formulario.id_mesa || formulario.detalles.length === 0
                                        ? 'bg-gray-400 cursor-not-allowed'
                                        : 'bg-orange-600 hover:bg-orange-700 shadow-lg'
                                ]"
                            >
                                 Crear Pedido
                            </button>
                            <Link href="/pedidos" class="block w-full mt-2 py-3 rounded-lg font-bold text-center border-2 border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                                Cancelar
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { reactive, computed, ref } from 'vue';

const props = defineProps({
    mesas: {
        type: Array,
        required: true,
    },
    productos: {
        type: Array,
        required: true,
    },
});

const formulario = reactive({
    id_mesa: '',
    detalles: [],
    observaciones: '',
});

const errors = ref({});
const buscarProducto = ref('');

const productosFiltrados = computed(() => {
    return props.productos.filter(p => 
        p.nombre.toLowerCase().includes(buscarProducto.value.toLowerCase()) ||
        p.categoria?.nombre.toLowerCase().includes(buscarProducto.value.toLowerCase())
    );
});

const subtotal = computed(() => {
    return formulario.detalles.reduce((sum, item) => sum + item.subtotal, 0);
});

const agregarAlCarrito = (producto) => {
    const existe = formulario.detalles.find(d => d.id_producto === producto.id_producto);
    
    if (existe) {
        existe.cantidad++;
        existe.subtotal = existe.cantidad * existe.precioUnitario;
    } else {
        formulario.detalles.push({
            id_producto: producto.id_producto,
            nombreProducto: producto.nombre,
            precioUnitario: parseFloat(producto.precio),
            cantidad: 1,
            subtotal: parseFloat(producto.precio),
            observaciones: '',
        });
    }
};

const incrementarCantidad = (index) => {
    formulario.detalles[index].cantidad++;
    actualizarCarrito();
};

const decrementarCantidad = (index) => {
    if (formulario.detalles[index].cantidad > 1) {
        formulario.detalles[index].cantidad--;
        actualizarCarrito();
    }
};

const actualizarCarrito = () => {
    formulario.detalles.forEach(item => {
        item.subtotal = item.cantidad * item.precioUnitario;
    });
};

const eliminarDelCarrito = (index) => {
    formulario.detalles.splice(index, 1);
};

const enviarFormulario = () => {
    if (!formulario.id_mesa || formulario.detalles.length === 0) {
        errors.value = { general: 'Selecciona mesa y al menos un producto' };
        return;
    }

    router.post('/pedidos', {
        id_mesa: parseInt(formulario.id_mesa),
        observaciones: formulario.observaciones,
        detalles: formulario.detalles.map(d => ({
            id_producto: parseInt(d.id_producto),
            cantidad: parseInt(d.cantidad),
            observaciones: d.observaciones,
        })),
    }, {
        onSuccess: () => {
            // Limpiar formulario
            formulario.detalles = [];
            formulario.id_mesa = '';
            formulario.observaciones = '';
        },
        onError: (erroresApi) => {
            errors.value = erroresApi;
        }
    });
};
</script>

<style scoped>
</style>
