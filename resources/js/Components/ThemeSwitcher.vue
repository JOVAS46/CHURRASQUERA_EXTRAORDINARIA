<template>
    <!-- Botón Theme Switcher -->
    <div class="relative group">
        <button class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition" title="Cambiar Tema">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-20 border border-orange-600 p-4 max-h-96 overflow-y-auto">
            <h3 class="text-sm font-bold text-gray-900 mb-4">🎨 Personaliza tu Interfaz</h3>

            <!-- SELECTOR 1: TEMA TEMÁTICO -->
            <div class="mb-4 pb-4 border-b border-gray-200">
                <p class="text-xs font-bold text-gray-700 mb-3">📚 Tema Temático</p>
                <div class="space-y-2">
                    <!-- Tema Niños -->
                    <button
                        @click="cambiarTema('ninos')"
                        :class="temaActual === 'ninos' ? 'ring-2 ring-offset-2 ring-red-400' : ''"
                        class="w-full text-left px-4 py-3 rounded-lg border-2 border-pink-300 bg-gradient-to-r from-pink-50 to-orange-50 hover:from-pink-100 hover:to-orange-100 transition"
                    >
                        <span class="text-xl">🎠</span> <span class="font-semibold text-pink-900">Tema Niños</span>
                        <p class="text-xs text-pink-700 mt-1">Colorido y divertido</p>
                    </button>

                    <!-- Tema Adultos -->
                    <button
                        @click="cambiarTema('adultos')"
                        :class="temaActual === 'adultos' ? 'ring-2 ring-offset-2 ring-blue-600' : ''"
                        class="w-full text-left px-4 py-3 rounded-lg border-2 border-blue-400 bg-gradient-to-r from-blue-50 to-slate-50 hover:from-blue-100 hover:to-slate-100 transition"
                    >
                        <span class="text-xl">💼</span> <span class="font-semibold text-blue-900">Tema Adultos</span>
                        <p class="text-xs text-blue-700 mt-1">Profesional y elegante</p>
                    </button>
                </div>
            </div>

            <!-- SELECTOR 2: MODO VISUAL -->
            <div class="mb-4 pb-4 border-b border-gray-200">
                <p class="text-xs font-bold text-gray-700 mb-3">☀️ Modo Visual</p>
                <div class="space-y-2">
                    <!-- Modo Claro -->
                    <button
                        @click="cambiarModoVisual('claro')"
                        :class="modoVisualActual === 'claro' ? 'ring-2 ring-offset-2 ring-yellow-500' : ''"
                        class="w-full text-left px-4 py-3 rounded-lg border-2 border-yellow-300 bg-yellow-50 hover:bg-yellow-100 transition"
                    >
                        <span class="text-xl">☀️</span> <span class="font-semibold text-yellow-900">Modo Claro</span>
                    </button>

                    <!-- Modo Oscuro -->
                    <button
                        @click="cambiarModoVisual('oscuro')"
                        :class="modoVisualActual === 'oscuro' ? 'ring-2 ring-offset-2 ring-gray-500' : ''"
                        class="w-full text-left px-4 py-3 rounded-lg border-2 border-gray-500 bg-gray-800 hover:bg-gray-700 transition"
                    >
                        <span class="text-xl">🌙</span> <span class="font-semibold text-gray-100">Modo Oscuro</span>
                    </button>
                </div>
            </div>

            <!-- SELECTOR 3: CONTRASTE ALTO -->
            <div class="mb-4 pb-4 border-b border-gray-200">
                <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 px-2 py-2 rounded">
                    <input 
                        type="checkbox" 
                        v-model="altoContraste"
                        @change="cambiarContraste"
                        class="w-4 h-4 rounded border-2 border-gray-400"
                    />
                    <span class="text-sm font-semibold text-gray-700">⚫ Alto Contraste</span>
                    <span class="text-xs text-gray-500">(Accesibilidad)</span>
                </label>
            </div>

            <!-- SELECTOR 4: TAMAÑO DE LETRA -->
            <div>
                <p class="text-xs font-bold text-gray-700 mb-2">📝 Tamaño de Letra</p>
                <div class="flex gap-2">
                    <button
                        v-for="size in ['pequeno', 'mediano', 'grande']"
                        :key="size"
                        @click="cambiarTamanoLetra(size)"
                        :class="tamanoActual === size ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700'"
                        class="flex-1 py-2 rounded text-sm font-semibold transition hover:opacity-90"
                    >
                        {{ size === 'pequeno' ? 'S' : size === 'mediano' ? 'M' : 'L' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();
const temaActual = computed(() => page.props.preferencias?.tema || 'ninos');
const altoContraste = ref(false);
const tamanoActual = computed(() => page.props.preferencias?.tamano_letra || 'mediano');
const modoVisualActual = ref('claro');

onMounted(() => {
    altoContraste.value = page.props.preferencias?.alto_contraste || false;
    modoVisualActual.value = localStorage.getItem('modoVisual') || 'claro';
});

const cambiarTema = async (tema) => {
    try {
        // Cambiar inmediatamente en el DOM
        const rootElement = document.querySelector('[data-tema]');
        if (rootElement) {
            rootElement.setAttribute('data-tema', tema);
            // Cambiar también la clase
            rootElement.className = rootElement.className.replace(/tema-\w+/g, `tema-${tema}`);
        }
        
        // Guardar en la BD
        await axios.post('/preferences/tema', { tema });
        
        // Recargar después de 800ms para sincronizar todo
        setTimeout(() => {
            window.location.reload();
        }, 800);
    } catch (error) {
        console.error('Error al cambiar tema:', error);
        // Si hay error, recargar de todas formas
        window.location.reload();
    }
};

const cambiarModoVisual = (modo) => {
    modoVisualActual.value = modo;
    localStorage.setItem('modoVisual', modo);
    // Actualizar el atributo data-modo en el HTML
    document.documentElement.setAttribute('data-modo', modo);
    // Emitir evento para que Layout se entere
    window.dispatchEvent(new CustomEvent('modoVisualChanged', { detail: { modo } }));
};

const cambiarContraste = async () => {
    try {
        // Actualizar inmediatamente en el DOM
        const rootElement = document.querySelector('[data-contraste]');
        if (rootElement) {
            if (altoContraste.value) {
                rootElement.setAttribute('data-contraste', 'si');
                rootElement.classList.add('contraste-alto');
            } else {
                rootElement.setAttribute('data-contraste', 'no');
                rootElement.classList.remove('contraste-alto');
            }
        }
        
        // Guardar en la BD
        await axios.post('/preferences/alto-contraste', { alto_contraste: altoContraste.value });
        
        // Recargar después de 500ms para sincronizar
        setTimeout(() => {
            window.location.reload();
        }, 500);
    } catch (error) {
        console.error('Error al cambiar contraste:', error);
        window.location.reload();
    }
};

const cambiarTamanoLetra = async (size) => {
    try {
        // Actualizar inmediatamente en el DOM
        const rootElement = document.querySelector('[data-tamano]');
        if (rootElement) {
            rootElement.setAttribute('data-tamano', size);
            // Cambiar clase tamaño
            const currentClasses = rootElement.className;
            rootElement.className = currentClasses.replace(/tamaño-\w+/g, `tamaño-${size}`);
        }
        
        // Guardar en la BD
        await axios.post('/preferences/tamano-letra', { tamano_letra: size });
        
        // Recargar después de 500ms para sincronizar
        setTimeout(() => {
            window.location.reload();
        }, 500);
    } catch (error) {
        console.error('Error al cambiar tamaño de letra:', error);
        window.location.reload();
    }
};
</script>

<style scoped>
/* Los estilos se aplicarán globalmente según el tema seleccionado */
</style>
