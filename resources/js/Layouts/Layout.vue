<template>
    <div 
        class="min-h-screen flex"
        :data-tema="preferencias?.tema || 'ninos'"
        :data-modo="modoVisual"
        :data-tamano="preferencias?.tamano_letra || 'mediano'"
        :data-contraste="preferencias?.alto_contraste ? 'si' : 'no'"
        :class="[
            `tema-${preferencias?.tema || 'ninos'}`,
            `modo-${modoVisual}`,
            `tamaño-${preferencias?.tamano_letra || 'mediano'}`,
            { 'contraste-alto': preferencias?.alto_contraste }
        ]"
    >
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 border-r-2 border-orange-600 text-white">
            <div class="p-6 border-b border-orange-600">
                <div class="flex items-center gap-3">
                    <div class="text-3xl">🔥</div>
                    <div>
                        <h1 class="text-xl font-bold text-orange-500">ROBERTO</h1>
                        <p class="text-xs text-orange-300">CHURRASQUERIA</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu - Dinámico desde BD -->
            <nav class="mt-8 space-y-2 px-4 flex-1 overflow-y-auto">
                <template v-for="item in menu" :key="item.id_menu">
                    <!-- Item sin hijos -->
                    <template v-if="!item.hijos || item.hijos.length === 0">
                        <Link :href="item.ruta" 
                            class="flex items-center gap-3 px-4 py-3 rounded-lg transition"
                            :class="isActive(item.ruta) ? 'bg-orange-600 text-white' : 'text-gray-300 hover:bg-gray-800'">
                            <component :is="getIconComponent(item.icono)" class="w-5 h-5" />
                            <span>{{ item.nombre }}</span>
                        </Link>
                    </template>

                    <!-- Item con hijos (submenu) -->
                    <template v-else>
                        <button @click="toggleSubmenu(item.id_menu)"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 transition">
                            <component :is="getIconComponent(item.icono)" class="w-5 h-5" />
                            <span class="flex-1 text-left">{{ item.nombre }}</span>
                            <svg :class="{ 'rotate-180': expandedMenus.includes(item.id_menu) }" 
                                class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </button>

                        <!-- Submenu items -->
                        <div v-if="expandedMenus.includes(item.id_menu)" class="pl-4 space-y-1">
                            <Link v-for="subitem in item.hijos" :key="subitem.id_menu"
                                :href="subitem.ruta"
                                class="flex items-center gap-3 px-3 py-2 rounded text-gray-400 hover:text-orange-400 hover:bg-gray-800 transition text-sm">
                                <span class="w-1 h-1 bg-orange-500 rounded-full"></span>
                                {{ subitem.nombre }}
                            </Link>
                        </div>
                    </template>
                </template>
            </nav>

            <!-- Footer -->
            <div class="border-t border-gray-700 p-4">
                <div class="text-xs text-gray-500 text-center">
                    <p class="text-orange-600 font-semibold">ROBERTO</p>
                    <p>CHURRASQUERIA 2026</p>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white border-b-2 border-orange-600 sticky top-0 z-10 shadow">
                <div class="px-8 py-4 flex items-center justify-between">
                    <!-- Search Bar -->
                    <div class="flex-1 max-w-md">
                        <SearchBar />
                    </div>

                    <!-- Right Side Actions -->
                    <div class="flex items-center gap-4 ml-8">
                        <!-- Theme Switcher -->
                        <ThemeSwitcher />

                        <!-- Notifications -->
                        <button class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-600 rounded-full"></span>
                        </button>

                        <!-- User Profile with Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center gap-3 pl-4 border-l-2 border-gray-300 hover:text-orange-600 transition">
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ auth.user?.nombre ?? 'Usuario' }}</p>
                                    <p class="text-xs text-gray-500">{{ auth.user?.rol?.nombre ?? 'Guest' }}</p>
                                </div>
                                <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-600 rounded-full"></div>
                            </button>

                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-20 border border-orange-600">
                                <div class="px-4 py-3 border-b border-gray-200">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth.user?.nombre }}</p>
                                    <p class="text-xs text-gray-500">{{ auth.user?.email }}</p>
                                </div>
                                <Link href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50">
                                    👤 Mi Perfil
                                </Link>
                                <Link href="/profile/edit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50">
                                    ⚙️ Editar Perfil
                                </Link>
                                <div class="border-t border-gray-200">
                                    <Link href="/logout" method="post" as="button" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                        🚪 Cerrar Sesión
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto">
                <div class="p-8">
                    <slot />
                </div>
            </main>

            <!-- Footer with page counter -->
            <footer class="bg-gray-900 border-t-2 border-orange-600 text-center py-4">
                <p class="text-sm text-orange-300">© 2026 ROBERTO CHURRASQUERIA | Visitas: <span class="font-semibold">{{ visitas.total }}</span></p>
            </footer>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import SearchBar from '@/Components/SearchBar.vue';
import ThemeSwitcher from '@/Components/ThemeSwitcher.vue';

const page = usePage();
const modoVisual = ref('claro');
const auth = computed(() => page.props.auth || {});
const menu = computed(() => page.props.menu || []);
const visitas = computed(() => page.props.visitas || { pagina_actual: 0, total: 0 });
const preferencias = computed(() => page.props.preferencias || { tema: 'ninos', tamano_letra: 'mediano', alto_contraste: false });
const expandedMenus = ref([]);

onMounted(() => {
    // Cargar modo visual desde localStorage
    const modo = localStorage.getItem('modoVisual') || 'claro';
    modoVisual.value = modo;
    document.documentElement.setAttribute('data-modo', modo);
    
    // Escuchar cambios en storage (cambios en otras pestañas)
    window.addEventListener('storage', (e) => {
        if (e.key === 'modoVisual') {
            modoVisual.value = e.newValue || 'claro';
            document.documentElement.setAttribute('data-modo', modoVisual.value);
            forceRerender();
        }
    });
    
    // Escuchar evento custom cuando cambie el modo visual
    window.addEventListener('modoVisualChanged', (e) => {
        modoVisual.value = e.detail.modo;
        document.documentElement.setAttribute('data-modo', e.detail.modo);
        forceRerender();
    });
});

// Forzar re-render cuando cambie el modo
const forceRerender = () => {
    // Pequeño truco para forzar que Vue recompile
    const oldModo = modoVisual.value;
    modoVisual.value = 'temporal';
    setTimeout(() => {
        modoVisual.value = oldModo;
    }, 0);
};

const isActive = (path) => {
    return page.url === path || page.url.startsWith(path + '/');
};

const toggleSubmenu = (menuId) => {
    const index = expandedMenus.value.indexOf(menuId);
    if (index > -1) {
        expandedMenus.value.splice(index, 1);
    } else {
        expandedMenus.value.push(menuId);
    }
};

const getIconComponent = (iconName) => {
    // Aquí puedes retornar componentes de iconos específicos
    // Por ahora retornamos un icono genérico
    return 'div';
};

// Exponer modoVisual para que ThemeSwitcher pueda modificarlo
defineExpose({ modoVisual });
</script>

<style scoped>
/* Estilos personalizados si es necesario */
</style>
