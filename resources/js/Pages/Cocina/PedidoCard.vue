<template>
    <div 
        draggable="true"
        @dragstart="iniciarArrastre"
        @dragend="finalizarArrastre"
        class="bg-white rounded-lg shadow-md p-4 border-l-4 cursor-grab active:cursor-grabbing hover:shadow-lg transition-all"
        :class="[borderColor, { 'opacity-75 scale-95': estaDragging, 'cursor-grabbing shadow-2xl ring-2': estaDragging }]"
    >
        <!-- Encabezado -->
        <div class="flex justify-between items-start mb-3">
            <h3 class="font-bold text-lg text-gray-900">PED-{{ pedido.id_pedido }}</h3>
            <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">Mesa {{ pedido.mesa?.numero_mesa }}</span>
        </div>

        <!-- Información de la mesa -->
        <div class="text-sm text-gray-600 mb-3">
            <p class="font-semibold">📍 {{ pedido.mesa?.ubicacion || 'Sin ubicación' }}</p>
            <p class="text-xs text-gray-500">👨‍🍳 Mesero: {{ pedido.usuario?.nombre || 'N/A' }}</p>
        </div>

        <!-- Detalles de productos -->
        <div class="bg-gray-50 rounded p-3 mb-4 max-h-40 overflow-y-auto">
            <h4 class="font-semibold text-sm text-gray-700 mb-2">Productos:</h4>
            <div v-for="detalle in pedido.detalles" :key="detalle.id_detalle" class="text-sm text-gray-700 mb-1">
                <p class="font-medium">{{ detalle.producto?.nombre }}</p>
                <p class="text-xs text-gray-600">
                    Cantidad: <span class="font-bold">{{ detalle.cantidad }}x</span>
                    <span v-if="detalle.observaciones" class="ml-2 text-orange-600">🔔 {{ detalle.observaciones }}</span>
                </p>
            </div>
        </div>

        <!-- Hora del pedido -->
        <p class="text-xs text-gray-400 text-center mt-3">
            🕐 {{ formatDate(pedido.fecha_pedido) }}
        </p>

        <!-- Indicador de arrastre -->
        <div v-if="estaDragging" class="text-center text-sm font-bold text-orange-600 mt-2">
            ✋ Soltando...
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    pedido: Object,
    estadoActual: String,
});

const estaDragging = ref(false);

const borderColor = computed(() => {
    const colors = {
        'pendiente': 'border-yellow-400',
        'en_preparacion': 'border-blue-400',
        'listo': 'border-green-400',
    };
    return colors[props.estadoActual] || 'border-gray-400';
});

const iniciarArrastre = (event) => {
    estaDragging.value = true;
    event.dataTransfer.effectAllowed = 'move';
    // Usar strings para asegurar que se envíen correctamente
    event.dataTransfer.setData('text/plain', props.pedido.id_pedido);
    event.dataTransfer.setData('application/pedidoId', String(props.pedido.id_pedido));
    event.dataTransfer.setData('application/estadoActual', String(props.estadoActual));
    
    // Crear imagen de arrastre personalizada
    const img = new Image();
    img.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    event.dataTransfer.setDragImage(img, 0, 0);
};

const finalizarArrastre = () => {
    estaDragging.value = false;
};

const formatDate = (date) => {
    return new Date(date).toLocaleString('es-BO', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
};
</script>
