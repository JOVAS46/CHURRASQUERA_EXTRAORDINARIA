<template>
  <div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <!-- Encabezado -->
      <div class="mb-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Tickets</h1>
            <p class="mt-2 text-gray-600">Gestiona los tickets de los pedidos</p>
          </div>
          <Link 
            href="route('tickets.create')" 
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            + Nuevo Ticket
          </Link>
        </div>
      </div>

      <!-- Filtros -->
      <div class="bg-white rounded-lg shadow mb-6 p-6">
        <form method="get" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Número de Ticket -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Número de Ticket
              </label>
              <input
                type="text"
                name="numero_ticket"
                :value="filtros.numero_ticket"
                placeholder="Ej: 10100"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500"
              />
            </div>

            <!-- Tipo -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Tipo
              </label>
              <select
                name="tipo"
                :value="filtros.tipo"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500"
              >
                <option value="">Todos</option>
                <option value="Normal">Normal</option>
                <option value="Especial">Especial</option>
                <option value="Cortesía">Cortesía</option>
              </select>
            </div>

            <!-- Fecha Desde -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Desde
              </label>
              <input
                type="date"
                name="fecha_desde"
                :value="filtros.fecha_desde"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500"
              />
            </div>

            <!-- Fecha Hasta -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Hasta
              </label>
              <input
                type="date"
                name="fecha_hasta"
                :value="filtros.fecha_hasta"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500"
              />
            </div>
          </div>

          <div class="flex gap-2">
            <button
              type="submit"
              class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded"
            >
              Filtrar
            </button>
            <Link
              href="route('tickets.index')"
              class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded"
            >
              Limpiar
            </Link>
          </div>
        </form>
      </div>

      <!-- Tabla de Tickets -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Número
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Tipo
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Fecha
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Mesa
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Total
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Acciones
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="ticket in tickets.data" :key="ticket.id_ticket" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                #{{ ticket.numero_ticket }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <span
                  :class="getTipoBadgeClass(ticket.tipo)"
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                >
                  {{ ticket.tipo }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatearFecha(ticket.fecha_emision) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ ticket.pedido?.mesa?.numero_mesa || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                ${{ formatearMoneda(ticket.pedido?.total) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                <Link
                  :href="route('tickets.show', ticket.id_ticket)"
                  class="text-blue-600 hover:text-blue-900"
                >
                  Ver
                </Link>
                <Link
                  :href="route('tickets.edit', ticket.id_ticket)"
                  class="text-yellow-600 hover:text-yellow-900"
                >
                  Editar
                </Link>
                <button
                  @click="eliminar(ticket.id_ticket)"
                  class="text-red-600 hover:text-red-900"
                >
                  Eliminar
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Sin resultados -->
        <div v-if="!tickets.data.length" class="text-center py-12">
          <p class="text-gray-500">No hay tickets para mostrar</p>
        </div>
      </div>

      <!-- Paginación -->
      <div v-if="tickets.links" class="mt-6 flex justify-center">
        <nav class="bg-white px-4 py-3 rounded-lg shadow">
          <div class="flex space-x-1">
            <Link
              v-for="link in tickets.links"
              :key="link.url"
              :href="link.url || ''"
              :class="[
                'px-3 py-2 rounded text-sm font-medium',
                link.active
                  ? 'bg-blue-600 text-white'
                  : 'text-gray-700 hover:bg-gray-100'
              ]"
              v-html="link.label"
            />
          </div>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
  tickets: Object,
  filtros: Object,
})

const page = usePage()

function formatearFecha(fecha) {
  return new Date(fecha).toLocaleDateString('es-MX', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
  })
}

function formatearMoneda(valor) {
  return (valor || 0).toFixed(2)
}

function getTipoBadgeClass(tipo) {
  const clases = {
    Normal: 'bg-green-100 text-green-800',
    Especial: 'bg-blue-100 text-blue-800',
    Cortesía: 'bg-purple-100 text-purple-800',
  }
  return clases[tipo] || 'bg-gray-100 text-gray-800'
}

function eliminar(id) {
  if (confirm('¿Está seguro de que desea eliminar este ticket?')) {
    // Enviar petición DELETE
    const form = document.createElement('form')
    form.method = 'POST'
    form.action = route('tickets.destroy', id)
    form.innerHTML = `
      <input type="hidden" name="_method" value="DELETE">
      <input type="hidden" name="_token" value="${page.props.csrf_token}">
    `
    document.body.appendChild(form)
    form.submit()
  }
}
</script>
