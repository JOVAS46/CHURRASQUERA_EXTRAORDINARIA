<template>
  <div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
      <!-- Botón volver -->
      <div class="mb-6">
        <Link
          href="route('tickets.show', ticket.id_ticket)"
          class="text-blue-600 hover:text-blue-900 flex items-center"
        >
          ← Volver
        </Link>
      </div>

      <!-- Formulario -->
      <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">
          Editar Ticket #{{ ticket.numero_ticket }}
        </h1>

        <form @submit.prevent="enviar" class="space-y-6">
          <!-- Token CSRF -->
          <input type="hidden" name="_token" :value="page.props.csrf_token" />
          <input type="hidden" name="_method" value="PUT" />

          <!-- Número de Ticket (solo lectura) -->
          <div>
            <label for="numero_ticket" class="block text-sm font-medium text-gray-700 mb-2">
              Número de Ticket
            </label>
            <input
              id="numero_ticket"
              :value="ticket.numero_ticket"
              type="text"
              disabled
              class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed"
            />
            <p class="text-xs text-gray-500 mt-1">No se puede cambiar</p>
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
              <option v-for="tipoOption in tipos" :key="tipoOption" :value="tipoOption">
                {{ tipoOption }}
              </option>
            </select>
            <p v-if="errors.tipo" class="text-red-500 text-sm mt-1">
              {{ errors.tipo }}
            </p>
          </div>

          <!-- Información del Pedido (solo lectura) -->
          <div class="bg-gray-50 p-4 rounded-lg border border-gray-300">
            <h3 class="font-bold text-gray-900 mb-3">Información del Pedido</h3>
            <div class="space-y-2 text-sm">
              <p><strong>ID Pedido:</strong> {{ ticket.pedido?.id_pedido }}</p>
              <p><strong>Mesa:</strong> {{ ticket.pedido?.mesa?.numero_mesa }}</p>
              <p><strong>Mesero:</strong> {{ ticket.pedido?.usuario?.name }}</p>
              <p><strong>Total:</strong> <span class="text-green-600 font-bold">${{ formatearMoneda(ticket.pedido?.total) }}</span></p>
              <p><strong>Estado:</strong> {{ ticket.pedido?.estado }}</p>
            </div>
          </div>

          <!-- Botones -->
          <div class="flex justify-between pt-6">
            <Link
              :href="route('tickets.show', ticket.id_ticket)"
              class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium"
            >
              Cancelar
            </Link>
            <button
              type="submit"
              :disabled="cargando"
              class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium disabled:opacity-50"
            >
              {{ cargando ? 'Guardando...' : 'Guardar Cambios' }}
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
  ticket: Object,
  tipos: Array,
})

const page = usePage()
const cargando = ref(false)
const errors = reactive({})

const formulario = reactive({
  tipo: props.ticket.tipo,
})

function formatearMoneda(valor) {
  return (valor || 0).toFixed(2)
}

function enviar() {
  cargando.value = true
  
  const form = document.createElement('form')
  form.method = 'POST'
  form.action = route('tickets.update', props.ticket.id_ticket)
  form.innerHTML = `
    <input type="hidden" name="_token" value="${page.props.csrf_token}">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="tipo" value="${formulario.tipo}">
  `
  document.body.appendChild(form)
  form.submit()
}
</script>
