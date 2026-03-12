<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Support\Facades\DB;

class SetupMenuController extends Controller
{
    /**
     * Agregar items de menú para Insumos, Proveedores y Recetas
     * Acceso: GET /setup/menu
     */
    public function setupMenu()
    {
        try {
            // Obtener el orden máximo actual en menu_items
            $maxOrden = DB::table('menu_items')->max('orden') ?? 0;

            // Verificar si los items ya existen
            $insumo_existe = DB::table('menu_items')->where('ruta', '/insumos')->exists();
            $proveedor_existe = DB::table('menu_items')->where('ruta', '/proveedores')->exists();
            $receta_existe = DB::table('menu_items')->where('ruta', '/recetas')->exists();

            $creados = [];
            $ids_creados = [];

            // Crear Insumos si no existe
            if (!$insumo_existe) {
                $id_insumo = DB::table('menu_items')->insertGetId([
                    'nombre' => 'Insumos',
                    'ruta' => '/insumos',
                    'icono' => '📦',
                    'orden' => $maxOrden + 1,
                    'activo' => true,
                    'parent_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $creados[] = 'Insumos';
                $ids_creados['insumo'] = $id_insumo;
                $maxOrden++;
            } else {
                $ids_creados['insumo'] = DB::table('menu_items')->where('ruta', '/insumos')->value('id_menu');
            }

            // Crear Proveedores si no existe
            if (!$proveedor_existe) {
                $id_proveedor = DB::table('menu_items')->insertGetId([
                    'nombre' => 'Proveedores',
                    'ruta' => '/proveedores',
                    'icono' => '🚚',
                    'orden' => $maxOrden + 1,
                    'activo' => true,
                    'parent_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $creados[] = 'Proveedores';
                $ids_creados['proveedor'] = $id_proveedor;
                $maxOrden++;
            } else {
                $ids_creados['proveedor'] = DB::table('menu_items')->where('ruta', '/proveedores')->value('id_menu');
            }

            // Crear Recetas si no existe
            if (!$receta_existe) {
                $id_receta = DB::table('menu_items')->insertGetId([
                    'nombre' => 'Recetas',
                    'ruta' => '/recetas',
                    'icono' => '👨‍🍳',
                    'orden' => $maxOrden + 1,
                    'activo' => true,
                    'parent_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $creados[] = 'Recetas';
                $ids_creados['receta'] = $id_receta;
            } else {
                $ids_creados['receta'] = DB::table('menu_items')->where('ruta', '/recetas')->value('id_menu');
            }

            return response()->json([
                'status' => 'success',
                'mensaje' => 'Configuración de menú completada',
                'items_creados' => $creados,
                'ids' => $ids_creados,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Agregar permisos en rol_menu para Admin y Cocinero
     * Admin (1) ve: Insumos, Proveedores, Recetas
     * Cocinero (4) ve: Recetas (ajustar número según tu BD)
     * Acceso: GET /setup/permisos
     */
    public function setupPermisos()
    {
        try {
            // Primero obtener los IDs de los menú items
            $id_insumo = DB::table('menu_items')->where('ruta', '/insumos')->value('id_menu');
            $id_proveedor = DB::table('menu_items')->where('ruta', '/proveedores')->value('id_menu');
            $id_receta = DB::table('menu_items')->where('ruta', '/recetas')->value('id_menu');

            if (!$id_insumo || !$id_proveedor || !$id_receta) {
                return response()->json([
                    'status' => 'error',
                    'mensaje' => 'Primero ejecuta GET /setup/menu para crear los items',
                ], 400);
            }

            // Obtener ID de roles
            $id_admin = DB::table('roles')->where('nombre', 'Admin')->value('id_rol') ?? 1;
            $id_cocinero = DB::table('roles')->where('nombre', 'Cocinero')->value('id_rol') ?? 4;

            $creados = [];

            // Verificar y crear permisos para Admin (puede ver todo)
            if (!DB::table('rol_menu')->where('id_rol', $id_admin)->where('id_menu', $id_insumo)->exists()) {
                DB::table('rol_menu')->insert(['id_rol' => $id_admin, 'id_menu' => $id_insumo]);
                $creados[] = "Admin + Insumos";
            }

            if (!DB::table('rol_menu')->where('id_rol', $id_admin)->where('id_menu', $id_proveedor)->exists()) {
                DB::table('rol_menu')->insert(['id_rol' => $id_admin, 'id_menu' => $id_proveedor]);
                $creados[] = "Admin + Proveedores";
            }

            if (!DB::table('rol_menu')->where('id_rol', $id_admin)->where('id_menu', $id_receta)->exists()) {
                DB::table('rol_menu')->insert(['id_rol' => $id_admin, 'id_menu' => $id_receta]);
                $creados[] = "Admin + Recetas";
            }

            // Permisos para Cocinero (solo Recetas)
            if (!DB::table('rol_menu')->where('id_rol', $id_cocinero)->where('id_menu', $id_receta)->exists()) {
                DB::table('rol_menu')->insert(['id_rol' => $id_cocinero, 'id_menu' => $id_receta]);
                $creados[] = "Cocinero + Recetas";
            }

            return response()->json([
                'status' => 'success',
                'mensaje' => 'Permisos configurados exitosamente',
                'permisos_creados' => $creados,
                'info' => [
                    'Admin ID' => $id_admin,
                    'Cocinero ID' => $id_cocinero,
                    'id_insumo' => $id_insumo,
                    'id_proveedor' => $id_proveedor,
                    'id_receta' => $id_receta,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }
}
