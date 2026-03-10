<template>
    <div class="search-container flex-1 max-w-md">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input 
                v-model="searchQuery"
                @input="realizarBusqueda"
                @focus="showResults = true"
                @blur="ocultarResultados"
                type="text" 
                placeholder="🔍 Buscar productos, precios..." 
                class="w-full pl-10 pr-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-300"
            />
            <div v-if="cargando" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                <svg class="animate-spin h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>

        <!-- Resultados desplegable -->
        <div v-if="showResults && (productos.length > 0 || categorias.length > 0 || mesas.length > 0 || pedidos.length > 0)" class="absolute top-full left-0 right-0 mt-2 bg-white border-2 border-orange-300 rounded-lg shadow-2xl z-50 max-h-96 overflow-y-auto">
            
            <!-- Productos -->
            <div v-if="productos.length > 0">
                <div class="px-4 py-3 bg-gradient-to-r from-orange-500 to-orange-600 font-bold text-white sticky top-0 z-10 flex items-center gap-2"><span class="text-xl">🍴</span><span>PRODUCTOS DISPONIBLES</span><span class="ml-auto text-sm font-normal bg-orange-700 px-2 py-1 rounded">{{ productos.length }}</span></div>
                <button 
                    v-for="producto in productos" 
                    :key="'prod-' + producto.id" 
                    @click="navigarProducto(producto)"
                    class="w-full text-left px-4 py-4 hover:bg-orange-50 cursor-pointer border-b border-gray-200 transition-all duration-200 group hover:pl-6"
                >
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <p class="font-bold text-gray-900 text-base group-hover:text-orange-700">{{ producto.nombre }}</p>
                            <p class="text-xs text-gray-500 mt-1">📁 {{ producto.categoria }}</p>
                            <div class="flex gap-2 mt-2">
                                <span v-if="producto.disponible" class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded font-semibold">✅ Disponible</span>
                                <span v-else class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded font-semibold">❌ Agotado</span>
                            </div>
                        </div>
                        <div class="text-right whitespace-nowrap">
                            <div class="text-3xl font-black text-orange-600">Bs</div>
                            <div class="text-2xl font-black text-orange-600">{{ producto.precio.toFixed(2) }}</div>
                        </div>
                    </div>
                </button>
            </div>

            <!-- Categorías -->
            <div v-if="categorias.length > 0">
                <div class="px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 font-bold text-white sticky top-0 z-10 flex items-center gap-2"><span class="text-xl">📂</span><span>CATEGORÍAS</span><span class="ml-auto text-sm font-normal bg-blue-700 px-2 py-1 rounded">{{ categorias.length }}</span></div>
                <button 
                    v-for="categoria in categorias" 
                    :key="'cat-' + categoria.id" 
                    @click="navigarCategoria(categoria)"
                    class="w-full text-left px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-200 transition-all duration-200 group hover:pl-6"
                >
                    <p class="font-semibold text-gray-900 group-hover:text-blue-700">{{ categoria.nombre }}</p>
                    <p v-if="categoria.descripcion" class="text-xs text-gray-600 mt-1">{{ categoria.descripcion }}</p>
                </button>
            </div>

            <!-- Mesas -->
            <div v-if="mesas.length > 0">
                <div class="px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 font-bold text-white sticky top-0 z-10 flex items-center gap-2"><span class="text-xl">🪑</span><span>MESAS DISPONIBLES</span><span class="ml-auto text-sm font-normal bg-green-700 px-2 py-1 rounded">{{ mesas.length }}</span></div>
                <button 
                    v-for="mesa in mesas" 
                    :key="'mesa-' + mesa.id" 
                    @click="navigarMesa(mesa)"
                    class="w-full text-left px-4 py-3 hover:bg-green-50 cursor-pointer border-b border-gray-200 transition-all duration-200 group hover:pl-6"
                >
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 group-hover:text-green-700">{{ mesa.nombre }}</p>
                            <p class="text-xs text-gray-600 mt-1">📍 {{ mesa.ubicacion }} • Capacidad: {{ mesa.capacidad }} 👥</p>
                            <span :class="mesaEstadoClase(mesa.estado)" class="text-xs font-bold inline-block mt-2 px-3 py-1 rounded">{{ mesa.estado.toUpperCase() }}</span>
                        </div>
                    </div>
                </button>
            </div>

            <!-- Pedidos -->
            <div v-if="pedidos.length > 0">
                <div class="px-4 py-3 bg-gradient-to-r from-purple-500 to-purple-600 font-bold text-white sticky top-0 z-10 flex items-center gap-2"><span class="text-xl">🧾</span><span>PEDIDOS</span><span class="ml-auto text-sm font-normal bg-purple-700 px-2 py-1 rounded">{{ pedidos.length }}</span></div>
                <button 
                    v-for="pedido in pedidos" 
                    :key="'pedido-' + pedido.id" 
                    @click="navigarPedido(pedido)"
                    class="w-full text-left px-4 py-4 hover:bg-purple-50 cursor-pointer border-b border-gray-200 transition-all duration-200 group hover:pl-6"
                >
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <p class="font-bold text-gray-900 group-hover:text-purple-700">{{ pedido.nombre }}</p>
                            <p class="text-xs text-gray-600 mt-1">👤 {{ pedido.usuario }} • {{ pedido.mesa }}</p>
                            <span :class="pedidoEstadoClase(pedido.estado)" class="text-xs font-bold inline-block mt-2 px-3 py-1 rounded">{{ pedido.estado.toUpperCase() }}</span>
                        </div>
                        <div class="text-right whitespace-nowrap">
                            <div class="text-2xl font-black text-purple-600">Bs. {{ pedido.total.toFixed(2) }}</div>
                        </div>
                    </div>
                </button>
            </div>
        </div>

        <!-- Sin resultados -->
        <div v-if="showResults && searchQuery.length > 1 && total === 0 && !cargando" class="absolute top-full left-0 right-0 mt-2 bg-white border-2 border-gray-300 rounded-lg shadow-lg z-50 p-6 text-center">
            <p class="text-4xl mb-2">🔍</p>
            <p class="text-gray-600 font-semibold">No se encontraron resultados para "{{ searchQuery }}"</p>
            <p class="text-xs text-gray-500 mt-2">Intenta buscar productos, categorías, mesas o pedidos</p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const searchQuery = ref('');
const showResults = ref(false);
const cargando = ref(false);

const productos = ref([]);
const categorias = ref([]);
const mesas = ref([]);
const pedidos = ref([]);

const total = computed(() => productos.value.length + categorias.value.length + mesas.value.length + pedidos.value.length);

// Realizar búsqueda en tiempo real
const realizarBusqueda = async () => {
    showResults.value = searchQuery.value.length > 0;
    
    if (searchQuery.value.length < 2) {
        productos.value = [];
        categorias.value = [];
        mesas.value = [];
        pedidos.value = [];
        return;
    }

    cargando.value = true;
    
    try {
        const response = await axios.get('/api/search', {
            params: { q: searchQuery.value }
        });
        
        productos.value = response.data.productos || [];
        categorias.value = response.data.categorias || [];
        mesas.value = response.data.mesas || [];
        pedidos.value = response.data.pedidos || [];
    } catch (error) {
        console.error('Error en búsqueda:', error);
        productos.value = [];
        categorias.value = [];
        mesas.value = [];
        pedidos.value = [];
    } finally {
        cargando.value = false;
    }
};

// Cerrar resultados con delay para evitar parpadeo
const ocultarResultados = () => {
    setTimeout(() => { showResults.value = false; }, 150);
};

// Navegación a producto
const navigarProducto = (producto) => {
    router.visit(`/productos/${producto.id}/edit`);
    searchQuery.value = '';
    showResults.value = false;
};

// Navegación a categoría
const navigarCategoria = (categoria) => {
    router.visit(`/productos?categoria=${categoria.id}`);
    searchQuery.value = '';
    showResults.value = false;
};

// Navegación a mesa
const navigarMesa = (mesa) => {
    router.visit(`/mesas/${mesa.id}/edit`);
    searchQuery.value = '';
    showResults.value = false;
};

// Navegación a pedido
const navigarPedido = (pedido) => {
    router.visit(`/pedidos/${pedido.id}`);
    searchQuery.value = '';
    showResults.value = false;
};

// Clases dinámicas para estado de mesa
const mesaEstadoClase = (estado) => {
    const baseClass = 'bg-opacity-100';
    return {
        [baseClass]: true,
        'bg-green-100 text-green-800': estado === 'disponible',
        'bg-red-100 text-red-800': estado === 'ocupada',
        'bg-yellow-100 text-yellow-800': estado === 'reservada',
    };
};

// Clases dinámicas para estado de pedido
const pedidoEstadoClase = (estado) => {
    const baseClass = 'bg-opacity-100';
    return {
        [baseClass]: true,
        'bg-yellow-100 text-yellow-800': estado === 'pendiente',
        'bg-blue-100 text-blue-800': estado === 'cocinando',
        'bg-green-100 text-green-800': estado === 'listo' || estado === 'entregado',
        'bg-red-100 text-red-800': estado === 'cancelado',
    };
};
</script>

<style scoped>
.search-container {
    position: relative;
}
</style>
