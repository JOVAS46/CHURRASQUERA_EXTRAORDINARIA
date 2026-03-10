<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Mesa;
use App\Models\Pedido;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * TEST SIMPLE - Obtener todos los productos
     * GET /api/test/productos
     */
    public function testProductos()
    {
        $productos = Producto::with('categoria')
            ->select('id_producto', 'nombre', 'descripcion', 'precio', 'disponible', 'id_categoria')
            ->get();

        return response()->json([
            'message' => '🧪 TEST - Todos los productos en BD',
            'total' => $productos->count(),
            'productos' => $productos,
        ]);
    }

    /**
     * TEST BUSQUEDA - Probar búsqueda con término
     * GET /api/test/buscar?q=termino
     */
    public function testBuscar(Request $request)
    {
        $query = $request->query('q', '');
        $searchTerm = '%' . $query . '%';

        $productos = Producto::with('categoria')
            ->where('nombre', 'like', $searchTerm)
            ->orWhere('descripcion', 'like', $searchTerm)
            ->get();

        return response()->json([
            'message' => "🧪 TEST - Búsqueda por término: '{$query}'",
            'termino' => $query,
            'total_encontrados' => $productos->count(),
            'sql_query' => "WHERE nombre LIKE '{$searchTerm}' OR descripcion LIKE '{$searchTerm}'",
            'productos' => $productos,
        ]);
    }

    /**
     * ENDPOINT DEBUG - Ver estructura de bases de datos
     * GET /api/search/debug/info
     */
    public function debugInfo()
    {
        return response()->json([
            'message' => '🔍 DEBUG - Información de bases de datos',
            'productos' => [
                'tabla' => 'productos',
                'total_registros' => Producto::count(),
                'campos' => ['id_producto', 'nombre', 'descripcion', 'precio', 'id_categoria', 'imagen', 'tiempo_preparacion', 'disponible'],
                'primeros_5' => Producto::with('categoria')->limit(5)->get(['id_producto', 'nombre', 'precio', 'disponible', 'id_categoria']),
            ],
            'categorias' => [
                'tabla' => 'categorias',
                'total_registros' => Categoria::count(),
                'primeras_5' => Categoria::limit(5)->get(['id_categoria', 'nombre', 'tipo']),
            ],
            'mesas' => [
                'tabla' => 'mesas',
                'total_registros' => Mesa::count(),
                'primeras_5' => Mesa::limit(5)->get(['id_mesa', 'numero_mesa', 'ubicacion', 'estado']),
            ],
            'pedidos' => [
                'tabla' => 'pedidos',
                'total_registros' => Pedido::count(),
                'primeros_5' => Pedido::limit(5)->get(['id_pedido', 'estado']),
            ],
        ]);
    }

    /**
     * Búsqueda global en toda la aplicación
     * GET /api/search?q=término
     * 
     * Busca en:
     * - Productos (nombre, descripcion)
     * - Categorías (nombre)
     * - Mesas (numero, ubicacion)
     * - Pedidos (por número)
     */
    public function search(Request $request)
    {
        $query = $request->query('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'productos' => [],
                'categorias' => [],
                'mesas' => [],
                'pedidos' => [],
                'total' => 0,
            ]);
        }

        $searchTerm = '%' . $query . '%';
        
        // Buscar Productos
        $productos = Producto::with('categoria')
            ->where('nombre', 'like', $searchTerm)
            ->orWhere('descripcion', 'like', $searchTerm)
            ->limit(5)
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id_producto,
                    'nombre' => $p->nombre,
                    'tipo' => 'producto',
                    'icono' => '🍴',
                    'categoria' => $p->categoria?->nombre ?? 'N/A',
                    'precio' => (float) $p->precio,
                    'disponible' => (bool) $p->disponible,
                ];
            });

        // Buscar Categorías
        $categorias = Categoria::where('nombre', 'like', $searchTerm)
            ->limit(3)
            ->get()
            ->map(function($c) {
                return [
                    'id' => $c->id_categoria,
                    'nombre' => $c->nombre,
                    'tipo' => 'categoria',
                    'icono' => '📂',
                    'tipo_categoria' => $c->tipo,
                    'descripcion' => $c->descripcion ?? '',
                ];
            });

        // Buscar Mesas (solo mostrar disponibles)
        $mesas = Mesa::where('numero_mesa', 'like', $searchTerm)
            ->orWhere('ubicacion', 'like', $searchTerm)
            ->limit(3)
            ->get()
            ->map(function($m) {
                return [
                    'id' => $m->id_mesa,
                    'nombre' => "Mesa {$m->numero_mesa}",
                    'tipo' => 'mesa',
                    'icono' => '🪑',
                    'numero' => $m->numero_mesa,
                    'ubicacion' => $m->ubicacion ?? 'N/A',
                    'capacidad' => $m->capacidad ?? 4,
                    'estado' => $m->estado,
                ];
            });

        // Buscar Pedidos (por número o ID) - solo si es número
        $pedidos = collect([]);
        if (is_numeric($query)) {
            $pedidos = Pedido::where('id_pedido', '=', (int)$query)
                ->with(['mesa', 'usuario'])
                ->limit(5)
                ->get()
                ->map(function($p) {
                    return [
                        'id' => $p->id_pedido,
                        'nombre' => "Pedido #{$p->id_pedido}",
                        'tipo' => 'pedido',
                        'icono' => '🧾',
                        'mesa' => "Mesa " . ($p->mesa?->numero_mesa ?? 'N/A'),
                        'estado' => $p->estado,
                        'total' => (float) $p->total,
                        'usuario' => $p->usuario?->nombre ?? 'N/A',
                    ];
                });
        }

        $total = $productos->count() + $categorias->count() + $mesas->count() + $pedidos->count();

        return response()->json([
            'productos' => $productos,
            'categorias' => $categorias,
            'mesas' => $mesas,
            'pedidos' => $pedidos,
            'total' => $total,
        ]);
    }
}
