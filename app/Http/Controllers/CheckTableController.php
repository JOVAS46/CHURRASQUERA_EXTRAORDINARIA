<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class CheckTableController extends Controller
{
    public function recetas()
    {
        try {
            $columns = DB::select("
                SELECT column_name, data_type, is_nullable
                FROM information_schema.columns 
                WHERE table_name = 'recetas'
                ORDER BY ordinal_position
            ");
            
            return response()->json([
                'status' => 'success',
                'tabla' => 'recetas',
                'estructura' => $columns,
                'total_campos' => count($columns),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    public function insumos()
    {
        try {
            $columns = DB::select("
                SELECT column_name, data_type, is_nullable
                FROM information_schema.columns 
                WHERE table_name = 'insumos'
                ORDER BY ordinal_position
            ");
            
            return response()->json([
                'status' => 'success',
                'tabla' => 'insumos',
                'estructura' => $columns,
                'total_campos' => count($columns),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    public function proveedores()
    {
        try {
            $columns = DB::select("
                SELECT column_name, data_type, is_nullable
                FROM information_schema.columns 
                WHERE table_name = 'proveedores'
                ORDER BY ordinal_position
            ");
            
            return response()->json([
                'status' => 'success',
                'tabla' => 'proveedores',
                'estructura' => $columns,
                'total_campos' => count($columns),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    public function rolMenu()
    {
        try {
            $columns = DB::select("
                SELECT column_name, data_type, is_nullable
                FROM information_schema.columns 
                WHERE table_name = 'rol_menu'
                ORDER BY ordinal_position
            ");
            
            $data = DB::select("SELECT * FROM rol_menu LIMIT 5");
            
            return response()->json([
                'status' => 'success',
                'tabla' => 'rol_menu',
                'estructura' => $columns,
                'total_campos' => count($columns),
                'ejemplos' => $data,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    public function menuItems()
    {
        try {
            $columns = DB::select("
                SELECT column_name, data_type, is_nullable
                FROM information_schema.columns 
                WHERE table_name = 'menu_items'
                ORDER BY ordinal_position
            ");
            
            $data = DB::select("SELECT id_menu, nombre, ruta, icono FROM menu_items LIMIT 10");
            
            return response()->json([
                'status' => 'success',
                'tabla' => 'menu_items',
                'estructura' => $columns,
                'total_campos' => count($columns),
                'ejemplos' => $data,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }
}
