# Copilot Configuration for Laravel Inertia Vue.js Project

Este es un proyecto de Laravel 11 con Inertia.js y Vue.js 3 integrados.

## Tecnologías Utilizadas

- **Backend**: Laravel 11
- **Frontend Framework**: Vue 3
- **Backend-Frontend Bridge**: Inertia.js
- **Build Tool**: Vite
- **CSS Framework**: Tailwind CSS
- **Database**: SQLite (configurable)

## Estructura del Proyecto

```
.
├── app/                      # Código de aplicación Laravel
│   ├── Http/
│   │   ├── Controllers/      # Controladores
│   │   └── Middleware/       # Middlewares (incluye HandleInertiaRequests)
├── resources/
│   ├── js/
│   │   ├── Pages/            # Componentes de páginas Inertia
│   │   ├── Layouts/          # Layouts de la aplicación
│   │   ├── Components/       # Componentes reutilizables
│   │   └── app.js            # Punto de entrada de Vue.js
│   ├── views/
│   │   └── app.blade.php     # Vista raíz de Inertia
│   └── css/
│       └── app.css           # Estilos principales
├── routes/
│   └── web.php               # Rutas web
├── public/
│   └── build/                # Assets compilados
├── config/                   # Archivos de configuración
├── bootstrap/
│   └── app.php               # Configuración de aplicación (includes middleware)
├── vite.config.js            # Configuración de Vite con plugin de Vue
├── tailwind.config.js        # Configuración de Tailwind
├── composer.json             # Dependencias PHP
└── package.json              # Dependencias JavaScript
```

## Configuración de Desarrollo

### Requisitos Previos

- PHP 8.2 o superior
- Node.js 20.19+ o 22.12+
- Composer
- npm

### Instalación Inicial (si es necesaria)

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### Ejecución en Desarrollo

1. **Terminal 1 - Servidor Laravel**:
```bash
php artisan serve
```
El servidor estará disponible en http://localhost:8000

2. **Terminal 2 - Compilación de Assets (Vite)**:
```bash
npm run dev
```
Vite estará disponible en http://localhost:5173

### Compilación para Producción

```bash
npm run build
```

## Funcionalidades de Inertia.js

- **Router Automático**: No necesita crear una API REST separada
- **Shared Data**: Datos compartidos entre frontend y backend
- **Lazy Props**: Carga perezosa de datos
- **Validation**: Validación integrada con Laravel
- **File Uploads**: Manejo de carga de archivos

## Componentes Vue.js

Se encuentran en `resources/js/`:
- **Pages**: Componentes que representan páginas completas
- **Layouts**: Layouts que envuelven las páginas
- **Components**: Componentes reutilizables

## Rutas Disponibles

Definidas en `routes/web.php`:
- `GET /` → Home page
- `GET /about` → About page

Ejemplo de cómo crear una nueva ruta:
```php
Route::get('/nueva-pagina', function () {
    return Inertia::render('NuevaPagina');
});
```

## Crear Nuevos Componentes

### Componente de Página

Crear archivo en `resources/js/Pages/MiPagina.vue`:
```vue
<template>
    <div>Contenido de la página</div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
</script>
```

### Componente Reutilizable

Crear archivo en `resources/js/Components/MiComponente.vue`:
```vue
<template>
    <div>Componente reutilizable</div>
</template>

<script setup>
defineProps({
    mensaje: String,
});
</script>
```

## Configuración Importante

- **HandleInertiaRequests Middleware**: Registrado en `bootstrap/app.php`, gestiona compartir datos
- **Vite Plugin**: Configurado en `vite.config.js` con soporte para Vue y Tailwind
- **Tailwind CSS**: Disponible para estilos

## Documentación Útil

- [Inertia.js Docs](https://inertiajs.com)
- [Laravel Docs](https://laravel.com/docs)
- [Vue 3 Docs](https://vuejs.org)
- [Vite Docs](https://vitejs.dev)
- [Tailwind Docs](https://tailwindcss.com)

## Notas de Desarrollo

- Los cambios en componentes Vue se recargan automáticamente con HMR
- Blade views (`*.blade.php`) requieren reinicio del servidor
- Los assets compilados se encuentran en `public/build/`
