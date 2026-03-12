<?php
// Verificar tablas en la base de datos

use Illuminate\Support\Facades\DB;

// Obtener todas las tablas
$tables = DB::select("
    SELECT tablename FROM pg_tables 
    WHERE schemaname = 'public'
    ORDER BY tablename
");

echo "=== TODAS LAS TABLAS EN LA BD ===\n";
foreach ($tables as $table) {
    echo $table->tablename . "\n";
}

echo "\n=== TABLA RECETAS (si existe) ===\n";
try {
    $recetas = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'recetas' ORDER BY ordinal_position");
    if (count($recetas) > 0) {
        foreach ($recetas as $col) {
            echo $col->column_name . ' (' . $col->data_type . ")\n";
        }
    } else {
        echo "TABLA NO EXISTE\n";
    }
} catch (\Exception $e) {
    echo "TABLA NO EXISTE\n";
}

echo "\n=== TABLA ROL_MENU (si existe) ===\n";
try {
    $rol_menu = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'rol_menu' ORDER BY ordinal_position");
    if (count($rol_menu) > 0) {
        foreach ($rol_menu as $col) {
            echo $col->column_name . ' (' . $col->data_type . ")\n";
        }
    } else {
        echo "TABLA NO EXISTE\n";
    }
} catch (\Exception $e) {
    echo "TABLA NO EXISTE\n";
}

echo "\n=== REGISTROS EN ROL_MENU (muestra de permisos) ===\n";
try {
    $rol_menu_data = DB::table('rol_menu')->limit(10)->get();
    foreach ($rol_menu_data as $row) {
        echo "id_rol: " . $row->id_rol . " | id_menu: " . $row->id_menu . "\n";
    }
} catch (\Exception $e) {
    echo "Error al consultar rol_menu\n";
}
