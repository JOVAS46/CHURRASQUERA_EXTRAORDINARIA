<template>
    <Layout>
        <Head title="Reportes y Estadísticas" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">📊 Reportes y Estadísticas del Gerente</h1>

                <!-- Filtros por Fecha -->
                <div class="bg-white rounded-lg shadow p-6 mb-6 border-l-4 border-blue-500">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-search mr-2"></i> Filtros por Fecha</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                            <input
                                type="date"
                                id="fecha_inicio"
                                :value="filtros.fecha_inicio"
                                @change="cambiarFecha('inicio', $event)"
                                class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                            <input
                                type="date"
                                id="fecha_fin"
                                :value="filtros.fecha_fin"
                                @change="cambiarFecha('fin', $event)"
                                class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
                            <button
                                @click="descargarPDF"
                                :disabled="descargandoPDF"
                                :class="descargandoPDF ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-700'"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 transition"
                            >
                                <span v-if="!descargandoPDF">📥 Descargar PDF</span>
                                <span v-else class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Generando PDF...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Container para PDF -->
                <div class="pdf-container">

                <!-- KPIs Principales -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Total Ventas -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Total Ventas</p>
                                <p class="text-3xl font-bold text-blue-600 mt-2">{{ formatCurrency(estadisticas.totalVentas) }}</p>
                                <p class="text-xs text-gray-500 mt-2">{{ estadisticas.ventasCount }} ventas</p>
                            </div>
                            <span class="text-4xl">💰</span>
                        </div>
                    </div>

                    <!-- Total Pagos Recibidos -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-md p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Pagos Recibidos</p>
                                <p class="text-3xl font-bold text-green-600 mt-2">{{ formatCurrency(estadisticas.totalPagos) }}</p>
                                <p class="text-xs text-gray-500 mt-2">{{ estadisticas.pagosCount }} pagos</p>
                            </div>
                            <span class="text-4xl">✅</span>
                        </div>
                    </div>

                    <!-- Pendiente de Cobro -->
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg shadow-md p-6 border-l-4 border-orange-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Por Cobrar</p>
                                <p class="text-3xl font-bold text-orange-600 mt-2">{{ formatCurrency(estadisticas.pendienteCobro) }}</p>
                                <p class="text-xs text-gray-500 mt-2">Pendiente</p>
                            </div>
                            <span class="text-4xl"><i class="fas fa-hourglass-half text-yellow-500"></i></span>
                        </div>
                    </div>

                    <!-- Promedio por Venta -->
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Promedio/Venta</p>
                                <p class="text-3xl font-bold text-purple-600 mt-2">{{ formatCurrency(estadisticas.promedioPorVenta) }}</p>
                                <p class="text-xs text-gray-500 mt-2">Venta promedio</p>
                            </div>
                            <span class="text-4xl">📈</span>
                        </div>
                    </div>
                </div>

                <!-- Estado de Pedidos -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Pedidos por Estado -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-indigo-500">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Pedidos por Estado</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center pb-3 border-b">
                                <div class="flex items-center">
                                    <span class="inline-block w-3 h-3 rounded-full bg-yellow-500 mr-3"></span>
                                    <span class="text-gray-700">Listos</span>
                                </div>
                                <span class="text-2xl font-bold text-yellow-600">{{ pedidosPorEstado.listo }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b">
                                <div class="flex items-center">
                                    <span class="inline-block w-3 h-3 rounded-full bg-blue-500 mr-3"></span>
                                    <span class="text-gray-700">Cocinando</span>
                                </div>
                                <span class="text-2xl font-bold text-blue-600">{{ pedidosPorEstado.cocinando }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b">
                                <div class="flex items-center">
                                    <span class="inline-block w-3 h-3 rounded-full bg-green-500 mr-3"></span>
                                    <span class="text-gray-700">Entregados</span>
                                </div>
                                <span class="text-2xl font-bold text-green-600">{{ pedidosPorEstado.entregado }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <span class="inline-block w-3 h-3 rounded-full bg-red-500 mr-3"></span>
                                    <span class="text-gray-700">Cancelados</span>
                                </div>
                                <span class="text-2xl font-bold text-red-600">{{ pedidosPorEstado.cancelado }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Métodos de Pago -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-500">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">💳 Métodos de Pago Utilizados</h2>
                        <div class="space-y-4">
                            <div v-for="metodo in metodosPago" :key="metodo.nombre" class="pb-3 border-b last:border-0">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-gray-900">{{ metodo.nombre }}</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ metodo.cantidad }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" :style="{ width: (metodo.monto / (metodosPago[0]?.monto || 1) * 100) + '%' }"></div>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ formatCurrency(metodo.monto) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Productos y Usuarios -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Top Productos -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">🍽️ Top 10 Productos Vendidos</h2>
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            <div v-for="(producto, index) in topProductos" :key="index" class="flex items-center justify-between pb-3 border-b last:border-0">
                                <div class="flex items-center flex-grow">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-orange-100 text-orange-700 text-sm font-semibold mr-3">
                                        {{ index + 1 }}
                                    </span>
                                    <span class="text-gray-700 font-medium">{{ producto.nombre }}</span>
                                </div>
                                <span class="text-orange-600 font-bold">{{ producto.cantidad }}x</span>
                            </div>
                            <div v-if="topProductos.length === 0" class="text-center py-4 text-gray-500">
                                No hay datos
                            </div>
                        </div>
                    </div>

                    <!-- Top Usuarios (Mozos/Meseros) -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">👥 Empleados - Ventas Totales</h2>
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            <div v-for="(usuario, index) in topUsuarios" :key="index" class="pb-3 border-b last:border-0">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-purple-100 text-purple-700 text-sm font-semibold mr-3">
                                            {{ index + 1 }}
                                        </span>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ usuario.nombre }}</p>
                                            <p class="text-xs text-gray-500">{{ usuario.pedidos }} pedidos</p>
                                        </div>
                                    </div>
                                    <span class="text-purple-600 font-bold">{{ formatCurrency(usuario.monto) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" :style="{ width: (usuario.monto / (topUsuarios[0]?.monto || 1) * 100) + '%' }"></div>
                                </div>
                            </div>
                            <div v-if="topUsuarios.length === 0" class="text-center py-4 text-gray-500">
                                No hay datos
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Ingresos Diarios -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">📅 Ingresos Diarios</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b-2 border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Monto</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Visualización</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="dia in ingresoDiario" :key="dia.fecha" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ formatDate(dia.fecha) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">{{ formatCurrency(dia.monto) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-40 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full" :style="{ width: (dia.monto / (maxIngreso || 1) * 100) + '%' }"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="ingresoDiario.length === 0">
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">No hay datos para mostrar</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                </div> <!-- Cierre del pdf-container -->
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    estadisticas: { type: Object, default: () => ({}) },
    pedidosPorEstado: { type: Object, default: () => ({}) },
    metodosPago: { type: Array, default: () => [] },
    topProductos: { type: Array, default: () => [] },
    topUsuarios: { type: Array, default: () => [] },
    ingresoDiario: { type: Array, default: () => [] },
    filtros: { type: Object, default: () => ({}) },
});

const filtros = ref({
    fecha_inicio: props.filtros.fecha_inicio || new Date(new Date().setDate(new Date().getDate() - 30)).toISOString().split('T')[0],
    fecha_fin: props.filtros.fecha_fin || new Date().toISOString().split('T')[0],
});

const descargandoPDF = ref(false);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
    }).format(value || 0);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-BO', { weekday: 'short', day: '2-digit', month: 'short' });
};

const maxIngreso = computed(() => {
    return Math.max(...(props.ingresoDiario?.map(d => d.monto) || [1]));
});

const cambiarFecha = (tipo, event) => {
    if (tipo === 'inicio') {
        filtros.value.fecha_inicio = event.target.value;
    } else {
        filtros.value.fecha_fin = event.target.value;
    }
    aplicarFiltros();
};

const aplicarFiltros = () => {
    router.get(route('reportes.index'), {
        fecha_inicio: filtros.value.fecha_inicio,
        fecha_fin: filtros.value.fecha_fin,
    }, {
        preserveState: false,
        preserveScroll: true,
    });
};

const descargarPDF = async () => {
    descargandoPDF.value = true;
    
    try {
        console.log('Iniciando descarga de PDF...');
        
        // Cargar las librerías necesarias
        const html2canvasScript = document.createElement('script');
        html2canvasScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
        
        const jsPDFScript = document.createElement('script');
        jsPDFScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
        
        await Promise.all([
            new Promise((resolve, reject) => {
                html2canvasScript.onload = resolve;
                html2canvasScript.onerror = reject;
                document.head.appendChild(html2canvasScript);
            }),
            new Promise((resolve, reject) => {
                jsPDFScript.onload = resolve;
                jsPDFScript.onerror = reject;
                document.head.appendChild(jsPDFScript);
            })
        ]);

        console.log('Librerías cargadas exitosamente');

        // Esperar a que las librerías estén disponibles
        setTimeout(async () => {
            try {
                const element = document.querySelector('.pdf-container');
                console.log('Elemento encontrado:', !!element);
                
                if (!element) {
                    alert('Error: No se encontró el contenedor PDF');
                    descargandoPDF.value = false;
                    return;
                }

                // Clonar el elemento para no afectar el original
                const clone = element.cloneNode(true);
                
                // Aplicar estilos profesionales al PDF
                const applyPDFStyles = (el, isRoot = false) => {
                    el.removeAttribute('class');
                    
                    if (isRoot) {
                        el.setAttribute('style', `
                            background: white !important;
                            color: #2c3e50 !important;
                            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
                            padding: 50px !important;
                            line-height: 1.8 !important;
                        `);
                    } else {
                        // Detectar elemento por contenido anterior
                        const text = el.textContent || '';
                        
                        // Títulos principales
                        if (el.tagName === 'H1') {
                            el.setAttribute('style', `
                                font-size: 32px !important;
                                font-weight: 800 !important;
                                color: #1a1a1a !important;
                                text-align: center !important;
                                margin: 0 0 10px 0 !important;
                                letter-spacing: 1px !important;
                                border-bottom: 4px solid #2c3e50 !important;
                                padding-bottom: 20px !important;
                            `);
                        }
                        // Subtítulos de sección
                        else if (el.tagName === 'H2') {
                            el.setAttribute('style', `
                                font-size: 18px !important;
                                font-weight: 700 !important;
                                color: #fff !important;
                                background: #2c3e50 !important;
                                padding: 14px 18px !important;
                                margin: 25px 0 12px 0 !important;
                                border-radius: 4px !important;
                                letter-spacing: 0.5px !important;
                            `);
                        }
                        // Tablas
                        else if (el.tagName === 'TABLE') {
                            el.setAttribute('style', `
                                width: 100% !important;
                                border-collapse: collapse !important;
                                margin: 20px 0 !important;
                                box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
                            `);
                        }
                        else if (el.tagName === 'TH') {
                            el.setAttribute('style', `
                                background: #34495e !important;
                                color: white !important;
                                border: 1px solid #2c3e50 !important;
                                padding: 15px !important;
                                font-weight: 700 !important;
                                text-align: left !important;
                                font-size: 12px !important;
                                text-transform: uppercase !important;
                                letter-spacing: 0.5px !important;
                            `);
                        }
                        else if (el.tagName === 'TD') {
                            el.setAttribute('style', `
                                border: 1px solid #ecf0f1 !important;
                                padding: 12px 14px !important;
                                color: #2c3e50 !important;
                                font-size: 11px !important;
                                background: ${el.parentElement?.rowIndex % 2 === 0 ? '#fff' : '#f8f9fa'} !important;
                            `);
                        }
                        // Párrafos
                        else if (el.tagName === 'P') {
                            el.setAttribute('style', `
                                margin: 8px 0 !important;
                                color: #2c3e50 !important;
                                font-size: 12px !important;
                            `);
                        }
                        // Divs con clase específica
                        else if (el.className && typeof el.className === 'string') {
                            if (el.className.includes('estado')) {
                                el.setAttribute('style', `
                                    display: inline-block !important;
                                    width: 22% !important;
                                    margin-right: 2% !important;
                                    border: 2px solid #34495e !important;
                                    padding: 20px !important;
                                    text-align: center !important;
                                    margin-bottom: 15px !important;
                                    background: #ecf0f1 !important;
                                    border-radius: 6px !important;
                                    box-shadow: 0 2px 4px rgba(0,0,0,0.08) !important;
                                `);
                            } else {
                                el.setAttribute('style', `background: white !important; color: #2c3e50 !important;`);
                            }
                        } else {
                            el.setAttribute('style', `background: white !important; color: #2c3e50 !important;`);
                        }
                    }
                    
                    Array.from(el.children).forEach(child => applyPDFStyles(child, false));
                };
                
                applyPDFStyles(clone, true);
                
                // Agregar el clon al DOM temporalmente
                clone.style.position = 'absolute';
                clone.style.left = '-9999px';
                clone.style.top = '-9999px';
                document.body.appendChild(clone);

                console.log('Clon creado y colocado en DOM');

                // Suprimir warnings
                const originalWarn = console.warn;
                const originalError = console.error;
                console.warn = () => {};
                console.error = () => {};

                console.log('Generando canvas...');
                
                // Generar canvas del clon
                const canvas = await window.html2canvas(clone, {
                    scale: 1,
                    logging: false,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff'
                });

                console.log('Canvas generado:', {
                    width: canvas.width,
                    height: canvas.height
                });

                console.warn = originalWarn;
                console.error = originalError;

                // Remover el clon del DOM
                document.body.removeChild(clone);

                // Convertir a imagen
                const imgData = canvas.toDataURL('image/jpeg', 0.95);
                console.log('Imagen creada, tamaño:', imgData.length);

                // Crear PDF
                const pdf = new window.jspdf.jsPDF({
                    orientation: 'landscape',
                    unit: 'mm',
                    format: 'a4'
                });

                const imgWidth = pdf.internal.pageSize.getWidth();
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                
                console.log('Agregando imagen al PDF:', {
                    width: imgWidth,
                    height: imgHeight
                });

                pdf.addImage(imgData, 'JPEG', 0, 0, imgWidth, imgHeight);
                
                const filename = `reporte_${filtros.value.fecha_inicio}_${filtros.value.fecha_fin}.pdf`;
                console.log('Descargando:', filename);
                
                pdf.save(filename);
                
                console.log('PDF descargado exitosamente');
                descargandoPDF.value = false;
                
            } catch (error) {
                console.error('Error completo:', error);
                alert('Error: ' + error.message);
                descargandoPDF.value = false;
            }
        }, 1000);
    } catch (error) {
        console.error('Error al cargar librerías:', error);
        alert('Error al cargar las librerías: ' + error.message);
        descargandoPDF.value = false;
    }
};
</script>

<style scoped>
/* Estilos profesionales para PDF - Blanco y Negro */
:deep(.pdf-container) {
    background: white !important;
    color: #1a1a1a !important;
    font-family: 'Segoe UI', Arial, sans-serif !important;
    padding: 40px !important;
    line-height: 1.6 !important;
}

:deep(.pdf-container *) {
    background: transparent !important;
    color: inherit !important;
}

/* HEADER */
:deep(.pdf-container h1) {
    font-size: 28px !important;
    font-weight: 700 !important;
    text-align: center !important;
    margin: 0 0 5px 0 !important;
    color: #000 !important;
    letter-spacing: 0.5px !important;
}

:deep(.pdf-container .header) {
    text-align: center !important;
    border-bottom: 3px solid #000 !important;
    padding-bottom: 20px !important;
    margin-bottom: 30px !important;
}

:deep(.pdf-container .header p) {
    margin: 5px 0 !important;
    font-size: 12px !important;
    color: #555 !important;
}

:deep(.pdf-container .fecha-rango) {
    text-align: center !important;
    margin: 20px 0 30px 0 !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    background: #f8f8f8 !important;
    padding: 12px !important;
    border-left: 4px solid #000 !important;
}

/* KPIs */
:deep(.pdf-container .kpis) {
    display: table !important;
    width: 100% !important;
    margin-bottom: 35px !important;
    border-collapse: separate !important;
    border-spacing: 10px !important;
}

:deep(.pdf-container .kpi) {
    display: table-cell !important;
    border: 2px solid #333 !important;
    padding: 20px !important;
    text-align: center !important;
    width: 25% !important;
    background: #fafafa !important;
}

:deep(.pdf-container .kpi-label) {
    font-size: 11px !important;
    color: #666 !important;
    margin-bottom: 10px !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
}

:deep(.pdf-container .kpi-value) {
    font-size: 24px !important;
    font-weight: 700 !important;
    color: #000 !important;
    margin: 8px 0 !important;
}

/* SECCIONES */
:deep(.pdf-container .section) {
    margin: 30px 0 !important;
    page-break-inside: avoid !important;
}

:deep(.pdf-container .section-title) {
    background: #333 !important;
    color: white !important;
    padding: 12px 16px !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    margin: 0 0 15px 0 !important;
    border-radius: 3px !important;
    letter-spacing: 0.5px !important;
}

/* TABLAS */
:deep(.pdf-container table) {
    width: 100% !important;
    border-collapse: collapse !important;
    margin: 15px 0 !important;
    border: 1px solid #ccc !important;
}

:deep(.pdf-container table thead) {
    background: #f0f0f0 !important;
}

:deep(.pdf-container table th) {
    background: #e8e8e8 !important;
    border: 1px solid #bbb !important;
    padding: 14px 12px !important;
    font-weight: 700 !important;
    text-align: left !important;
    color: #000 !important;
    font-size: 12px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
}

:deep(.pdf-container table td) {
    border: 1px solid #ddd !important;
    padding: 11px 12px !important;
    color: #333 !important;
    font-size: 11px !important;
}

:deep(.pdf-container table tr:nth-child(even)) {
    background: #f9f9f9 !important;
}

:deep(.pdf-container table tr:nth-child(odd)) {
    background: #fff !important;
}

:deep(.pdf-container table tr:hover) {
    background: #f5f5f5 !important;
}

/* ESTADO BOXES */
:deep(.pdf-container .estado-box) {
    display: inline-block !important;
    width: 22% !important;
    margin-right: 2.5% !important;
    border: 2px solid #333 !important;
    padding: 18px 12px !important;
    text-align: center !important;
    margin-bottom: 15px !important;
    page-break-inside: avoid !important;
    background: #fafafa !important;
    border-radius: 4px !important;
}

:deep(.pdf-container .estado-box:last-child) {
    margin-right: 0 !important;
}

:deep(.pdf-container .estado-label) {
    font-size: 10px !important;
    font-weight: 700 !important;
    margin-bottom: 10px !important;
    color: #555 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
}

:deep(.pdf-container .estado-numero) {
    font-size: 32px !important;
    font-weight: 700 !important;
    color: #000 !important;
    margin: 5px 0 !important;
}

/* TEXTOS */
:deep(.pdf-container .text-right) {
    text-align: right !important;
}

:deep(.pdf-container .text-center) {
    text-align: center !important;
}

:deep(.pdf-container .text-bold) {
    font-weight: 700 !important;
}

/* LISTAS */
:deep(.pdf-container .space-y-3) {
    margin: 10px 0 !important;
}

:deep(.pdf-container .pb-3) {
    padding-bottom: 10px !important;
    margin-bottom: 10px !important;
    border-bottom: 1px solid #e0e0e0 !important;
}

:deep(.pdf-container .pb-3:last-child) {
    border-bottom: none !important;
}

:deep(.pdf-container .flex) {
    display: flex !important;
    align-items: center !important;
}

:deep(.pdf-container .items-center) {
    align-items: center !important;
}

:deep(.pdf-container .justify-between) {
    justify-content: space-between !important;
}

:deep(.pdf-container .w-full) {
    width: 100% !important;
}

/* FOOTER */
:deep(.pdf-container .footer) {
    text-align: center !important;
    font-size: 10px !important;
    color: #888 !important;
    margin-top: 40px !important;
    padding-top: 20px !important;
    border-top: 2px solid #ddd !important;
}

/* ELIMINAR ELEMENTOS NO NECESARIOS */
:deep(.pdf-container svg) {
    display: none !important;
}

:deep(.pdf-container .animate-spin) {
    display: none !important;
}

:deep(.pdf-container [class*="bg-gradient"]) {
    background: white !important;
}

/* MÁRGENES Y ESPACIADO */
:deep(.pdf-container .mb-6) {
    margin-bottom: 25px !important;
}

:deep(.pdf-container .mb-4) {
    margin-bottom: 15px !important;
}

:deep(.pdf-container .p-6) {
    padding: 20px !important;
}

/* ÍNDICES Y NÚMEROS */
:deep(.pdf-container p) {
    margin: 8px 0 !important;
}

:deep(.pdf-container strong) {
    font-weight: 700 !important;
}
</style>

