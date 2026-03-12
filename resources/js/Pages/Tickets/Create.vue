<template>
  <div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
      <!-- Botón volver -->
      <div class="mb-6">
        <Link
          href="route('tickets.index')"
          class="text-blue-600 hover:text-blue-900 flex items-center"
        >
          ← Volver a Tickets
        </Link>
      </div>

      <!-- Formulario -->
      <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Crear Nuevo Ticket</h1>

        <form @submit.prevent="enviar" class="space-y-6">
          <!-- Token CSRF -->
          <input type="hidden" name="_token" :value="page.props.csrf_token" />

          <!-- Seleccionar Pedido -->
          <div>
            <label for="id_pedido" class="block text-sm font-medium text-gray-700 mb-2">
              Seleccionar Pedido *
            </label>
            <select
              id="id_pedido"
              v-model="formulario.id_pedido"
              required
              @change="seleccionarPedido"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">-- Seleccionar Pedido --</option>
              <option v-for="pedido in pedidos" :key="pedido.id_pedido" :value="pedido.id_pedido">
                Pedido #{{ pedido.id_pedido }} - Mesa {{ pedido.mesa?.numero_mesa }} - ${{ pedido.total }}
              </option>
            </select>
            <p v-if="errors.id_pedido" class="text-red-500 text-sm mt-1">
              {{ errors.id_pedido }}
            </p>
          </div>

          <!-- Número de Ticket -->
          <div>
            <label for="numero_ticket" class="block text-sm font-medium text-gray-700 mb-2">
              Número de Ticket *
            </label>
            <input
              id="numero_ticket"
              v-model="formulario.numero_ticket"
              type="number"
              required
              placeholder="Ej: 10100"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-transparent"
            />
            <p v-if="errors.numero_ticket" class="text-red-500 text-sm mt-1">
              {{ errors.numero_ticket }}
            </p>
          </div>

          <!-- Tipo de Ticket -->
          <div>
            <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">
              Tipo de Ticket *
            </label>
            <select
              id="tipo"
              v-model="formulario.tipo"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">-- Seleccionar Tipo --</option>
              <option value="Normal">Normal</option>
              <option value="Especial">Especial</option>
              <option value="Cortesía">Cortesía</option>
            </select>
            <p v-if="errors.tipo" class="text-red-500 text-sm mt-1">
              {{ errors.tipo }}
            </p>
          </div>

          <!-- Previsualización del Pedido -->
          <div v-if="pedidoSeleccionado" class="bg-gray-50 p-4 rounded-lg border border-gray-300">
            <h3 class="font-bold text-gray-900 mb-3">Resumen del Pedido</h3>
            <div class="space-y-2 text-sm">
              <p><strong>Mesa:</strong> {{ pedidoSeleccionado.mesa?.numero_mesa }}</p>
              <p><strong>Mesero:</strong> {{ pedidoSeleccionado.usuario?.name }}</p>
              <p><strong>Total:</strong> <span class="text-green-600 font-bold">${{ formatearMoneda(pedidoSeleccionado.total) }}</span></p>
              
              <div class="mt-3 border-t pt-3">
                <p class="font-semibold mb-2">Productos:</p>
                <ul class="space-y-1 ml-2">
                  <li v-for="detalle in pedidoSeleccionado.detalles" :key="detalle.id_detalle" class="text-gray-600">
                    • {{ detalle.producto?.nombre || 'Producto' }} x{{ detalle.cantidad }}
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Mensajes de error general -->
          <div v-if="errors.error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ errors.error }}
          </div>

          <!-- Botones -->
          <div class="flex justify-between pt-6">
            <Link
              href="route('tickets.index')"
              class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium"
            >
              Cancelar
            </Link>
            <button
              type="submit"
              :disabled="cargando"
              class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium disabled:opacity-50"
            >
              {{ cargando ? 'Generando...' : 'Generar Ticket' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { reactive, ref } from 'vue'

const props = defineProps({
  pedidos: Array,
})

const page = usePage()
const cargando = ref(false)
const errors = reactive({})
const pedidoSeleccionado = ref(null)

const formulario = reactive({
  numero_ticket: '',
  tipo: '',
  id_pedido: '',
})

function seleccionarPedido() {
  const pedido = props.pedidos.find(p => p.id_pedido == formulario.id_pedido)
  pedidoSeleccionado.value = pedido || null
}

function formatearMoneda(valor) {
  return (valor || 0).toFixed(2)
}

function enviar() {
  cargando.value = true
  
  const form = document.createElement('form')
  form.method = 'POST'
  form.action = route('tickets.store')
  form.innerHTML = `
    <input type="hidden" name="_token" value="${page.props.csrf_token}">
    <input type="hidden" name="numero_ticket" value="${formulario.numero_ticket}">
    <input type="hidden" name="tipo" value="${formulario.tipo}">
    <input type="hidden" name="id_pedido" value="${formulario.id_pedido}">
  `
  document.body.appendChild(form)
  form.submit()
}
</script>
