<template>
  <div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
      <!-- Botón volver -->
      <div class="mb-6">
        <Link
          href="route('tickets.index')"
          class="text-blue-600 hover:text-blue-900 flex items-center"
        >
          ← Volver a Tickets
        </Link>
      </div>

      <!-- Ticket Display -->
      <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Encabezado -->
        <div class="bg-gray-900 text-white p-6">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-4xl font-bold">#{{ ticket.numero_ticket }}</h1>
              <p class="text-gray-300 mt-2">{{ formatearFecha(ticket.fecha_emision) }}</p>
            </div>
            <div class="text-right">
              <p :class="getTipoBadgeClass(ticket.tipo)" class="text-lg font-semibold px-4 py-2 rounded inline-block">
                {{ ticket.tipo }}
              </p>
            </div>
          </div>
        </div>

        <!-- Información de la Mesa -->
        <div v-if="ticket.pedido" class="bg-gray-50 border-b border-gray-200 p-6">
          <div class="grid grid-cols-3 gap-4">
            <div>
              <p class="text-sm text-gray-500">Mesa</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ ticket.pedido.mesa?.numero_mesa || 'N/A' }}
              </p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Mesero</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ ticket.pedido.usuario?.name || 'N/A' }}
              </p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Total</p>
              <p class="text-2xl font-bold text-green-600">
                ${{ formatearMoneda(ticket.pedido.total) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Detalles del Pedido -->
        <div class="p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Detalles del Pedido</h2>
          
          <table class="w-full">
            <thead class="bg-gray-100 border-b-2 border-gray-300">
              <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Producto</th>
                <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700">Cantidad</th>
                <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Precio Unit.</th>
                <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="detalle in ticket.pedido?.detalles"
                :key="detalle.id_detalle"
                class="border-b border-gray-200 hover:bg-gray-50"
              >
                <td class="px-4 py-3 text-sm text-gray-900">
                  {{ detalle.producto?.nombre || 'Producto desconocido' }}
                  <p v-if="detalle.observaciones" class="text-xs text-gray-500 mt-1">
                    Nota: {{ detalle.observaciones }}
                  </p>
                </td>
                <td class="px-4 py-3 text-center text-sm text-gray-900">
                  {{ detalle.cantidad }}
                </td>
                <td class="px-4 py-3 text-right text-sm text-gray-900">
                  ${{ formatearMoneda(detalle.precio_unitario) }}
                </td>
                <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">
                  ${{ formatearMoneda(detalle.subtotal) }}
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Totales -->
          <div class="mt-6 border-t-2 border-gray-300 pt-4">
            <div class="flex justify-end">
              <div class="w-64">
                <div class="flex justify-between text-lg font-bold text-gray-900 bg-gray-100 px-4 py-3 rounded">
                  <span>TOTAL:</span>
                  <span class="text-green-600">
                    ${{ formatearMoneda(ticket.pedido?.total) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Acciones -->
        <div class="bg-gray-50 border-t border-gray-200 p-6 flex justify-between">
          <div class="space-x-3">
            <Link
              :href="route('tickets.edit', ticket.id_ticket)"
              class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded"
            >
              Editar
            </Link>
            <button
              @click="exportarPDF"
              class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
            >
              Descargar PDF
            </button>
            <button
              @click="imprimir"
              class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
            >
              Imprimir
            </button>
          </div>
          <button
            @click="eliminar"
            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
          >
            Eliminar
          </button>
        </div>
      </div>

      <!-- Footer -->
      <div class="mt-6 text-center">
        <p class="text-gray-500">¡Gracias por su compra!</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
  ticket: Object,
})

const page = usePage()

function formatearFecha(fecha) {
  return new Date(fecha).toLocaleDateString('es-MX', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
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

function exportarPDF() {
  // Llamar a la API para exportar
  window.open(route('tickets.exportarPDF', props.ticket.id_ticket))
}

function imprimir() {
  window.print()
}

function eliminar() {
  if (confirm('¿Está seguro de que desea eliminar este ticket?')) {
    const form = document.createElement('form')
    form.method = 'POST'
    form.action = route('tickets.destroy', props.ticket.id_ticket)
    form.innerHTML = `
      <input type="hidden" name="_method" value="DELETE">
      <input type="hidden" name="_token" value="${page.props.csrf_token}">
    `
    document.body.appendChild(form)
    form.submit()
  }
}
</script>

<style scoped>
@media print {
  * {
    background: transparent !important;
    color: black !important;
    box-shadow: none !important;
    text-shadow: none !important;
  }
  
  button, a {
    display: none !important;
  }
}
</style>
