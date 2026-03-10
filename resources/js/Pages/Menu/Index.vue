<template>
    <Layout>
        <Head title="Menú de Productos" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Menú de Productos</h1>
                
                <!-- Search and Filter -->
                <div class="mb-6 flex gap-4">
                    <input
                        v-model="busqueda"
                        type="text"
                        placeholder="Buscar producto..."
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    />
                    <select
                        v-model="categoriaFiltro"
                        class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">Todas las categorías</option>
                        <option value="carnes">Carnes</option>
                        <option value="bebidas">Bebidas</option>
                        <option value="acompañamientos">Acompañamientos</option>
                        <option value="postres">Postres</option>
                    </select>
                </div>

                <!-- Productos Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="producto in productosFiltrados" :key="producto.id_producto" class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden">
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400">📷 Imagen</span>
                        </div>
                        
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ producto.nombre }}</h3>
                            
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                {{ producto.descripcion || 'Sin descripción' }}
                            </p>
                            
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-2xl font-bold text-orange-600">{{ formatCurrency(producto.precio) }}</span>
                                <span v-if="producto.disponible" class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                    Disponible
                                </span>
                                <span v-else class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">
                                    No disponible
                                </span>
                            </div>
                            
                            <button
                                @click="agregarAlPedido(producto)"
                                :disabled="!producto.disponible"
                                :class="[
                                    'w-full py-2 px-4 rounded-lg font-semibold transition',
                                    producto.disponible
                                        ? 'bg-orange-600 text-white hover:bg-orange-700'
                                        : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                ]"
                            >
                                Agregar al Pedido
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="productosFiltrados.length === 0" class="text-center py-12">
                    <p class="text-gray-500 text-lg">No hay productos que coincidan con tu búsqueda</p>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    productos: {
        type: Array,
        default: () => [],
    },
});

const busqueda = ref('');
const categoriaFiltro = ref('');

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
    }).format(value || 0);
};

const productosFiltrados = computed(() => {
    return props.productos.filter(p => {
        const coincidenBusqueda = p.nombre.toLowerCase().includes(busqueda.value.toLowerCase());
        const coincidenCategoria = !categoriaFiltro.value || p.categoria === categoriaFiltro.value;
        return coincidenBusqueda && coincidenCategoria;
    });
});

const agregarAlPedido = (producto) => {
    // Future: Add to cart/order system
    console.log('Agregar a pedido:', producto);
    alert(`${producto.nombre} agregado al pedido`);
};
</script>
