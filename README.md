# Laravel Inertia Vue.js Project

Este es un proyecto de Laravel con Inertia.js y Vue 3 integrados.

## Características

- **Laravel 11** - Framework PHP robusto
- **Inertia.js** - Construye aplicaciones de una sola página sin necesidad de crear una API
- **Vue 3** - Framework JavaScript moderno y reactivo
- **Tailwind CSS** - Sistema de utilidades CSS
- **Vite** - Herramienta de compilación rápida

## Requisitos

- PHP 8.2+
- Node.js 20.19+ o 22.12+
- Composer
- npm

## Instalación

1. Instalar dependencias PHP:
```bash
composer install
```

2. Instalar dependencias JavaScript:
```bash
npm install
```

3. Copiar archivo de configuración:
```bash
cp .env.example .env
```

4. Generar clave de aplicación:
```bash
php artisan key:generate
```

5. Compilar assets (desarrollo):
```bash
npm run dev
```

## Uso en Desarrollo

### Terminal 1 - Servidor Laravel:
```bash
php artisan serve
```

### Terminal 2 - Compilación de Assets:
```bash
npm run dev
```

## Compilación para Producción

```bash
npm run build
```

## Estructura del Proyecto

```
resources/
├── js/
│   ├── Pages/          # Componentes de páginas
│   ├── Layouts/        # Layouts principales
│   ├── Components/     # Componentes reutilizables
│   └── app.js          # Archivo principal de la aplicación
├── views/
│   └── app.blade.php   # Vista raíz de Inertia
└── css/
    └── app.css         # Estilos principales
```

## Rutas Disponibles

- `/` - Página de inicio
- `/about` - Página acerca de

## Documentación

- [Documentación de Laravel](https://laravel.com/docs)
- [Documentación de Inertia.js](https://inertiajs.com)
- [Documentación de Vue 3](https://vuejs.org/)
- [Documentación de Tailwind CSS](https://tailwindcss.com)

## Licencia

Este proyecto está bajo la licencia MIT.

