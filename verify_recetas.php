<?php
// Script simple para verificar estructura de tabla recetas
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

try {
    echo "=== ESTRUCTURA TABLA RECETAS ===\n\n";
    
    $columns = DB::select("
        SELECT column_name, data_type, is_nullable
        FROM information_schema.columns 
        WHERE table_name = 'recetas'
        ORDER BY ordinal_position
    ");
    
    if (count($columns) > 0) {
        echo "Campo | Tipo | Nullable\n";
        echo "------|------|----------\n";
        foreach ($columns as $col) {
            $nullable = $col->is_nullable === 'YES' ? 'SÍ' : 'NO';
            echo $col->column_name . " | " . $col->data_type . " | " . $nullable . "\n";
        }
        
        // Contar registros
        $count = DB::selectOne("SELECT COUNT(*) as total FROM recetas");
        echo "\n\nTotal de registros: " . $count->total . "\n";
        
        // Mostrar un ejemplo
        $example = DB::selectOne("SELECT * FROM recetas LIMIT 1");
        if ($example) {
            echo "\n=== EJEMPLO DE UN REGISTRO ===\n";
            foreach ((array)$example as $field => $value) {
                echo $field . ": " . ($value ?? 'NULL') . "\n";
            }
        }
        
    } else {
        echo "ERROR: Tabla recetas no encontrada\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
