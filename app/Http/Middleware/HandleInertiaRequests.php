<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\MenuItem;
use App\Models\PageVisit;
use App\Models\UserPreference;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // Obtener menú dinámico SOLO si usuario está autenticado
        $menu = [];
        if ($request->user()) {
            // Obtener menús permitidos para el rol del usuario
            $userRolId = $request->user()->id_rol;
            
            // Usar join directo para mejor ordenamiento
            $menu = MenuItem::join('rol_menu', 'menu_items.id_menu', '=', 'rol_menu.id_menu')
                ->where('rol_menu.id_rol', $userRolId)
                ->where('menu_items.activo', true)
                ->whereNull('menu_items.parent_id')
                ->select('menu_items.*')
                ->orderBy('menu_items.orden', 'asc')
                ->orderBy('menu_items.id_menu', 'asc')
                ->with(['hijos' => function ($query) {
                    $query->where('activo', true)
                        ->orderBy('orden', 'asc')
                        ->orderBy('id_menu', 'asc');
                }])
                ->get();
        }

        // Obtener contador de visitas de la página actual
        $paginaActual = basename($request->path()) ?: 'inicio';
        $visitasActuales = PageVisit::contarVisitasPorPagina($paginaActual);
        $totalVisitas = PageVisit::obtenerTotalVisitas();

        // Obtener preferencias del usuario
        $preferencias = null;
        if ($request->user() && $request->user()->id) {
            $preferencias = UserPreference::obtenerPreferencias($request->user()->id);
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? $request->user()->load('rol') : null,
            ],
            'menu' => $menu,
            'visitas' => [
                'pagina_actual' => $visitasActuales,
                'total' => $totalVisitas,
            ],
            'preferencias' => $preferencias ? [
                'tema' => $preferencias->tema,
                'tamano_letra' => $preferencias->tamano_letra,
                'alto_contraste' => $preferencias->alto_contraste,
            ] : null,
        ];
    }
}

