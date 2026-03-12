<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables')->bootstrap($app);

use Illuminate\Support\Facades\DB;

$app->make('db');

try {
    echo "\n========================================\n";
    echo "TABLA: TICKETS\n";
    echo "========================================\n\n";

    // Obtener información de la tabla
    $columns = DB::select("
        SELECT column_name, data_type, is_nullable 
        FROM information_schema.columns 
        WHERE table_name = 'tickets' 
        ORDER BY ordinal_position
    ");
    
    if (!empty($columns)) {
        echo "✅ Campos encontrados:\n\n";
        foreach ($columns as $col) {
            $nullable = $col->is_nullable === 'YES' ? 'Nullable' : 'NOT NULL';
            echo "📌 {$col->column_name}\n";
            echo "   Tipo: {$col->data_type}\n";
            echo "   Restricción: {$nullable}\n\n";
        }
    } else {
        echo "⚠️ No se encontraron columnas.\n";
    }
    
    // Contar registros
    $count = DB::table('tickets')->count();
    echo "📊 Total de registros: " . $count . "\n";
    
    // Mostrar un ejemplo si existe
    $sample = DB::table('tickets')->first();
    if ($sample) {
        echo "\n📋 Ejemplo de registro:\n";
        echo json_encode($sample, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nDetalles: " . $e->getFile() . " (Línea: " . $e->getLine() . ")\n";
}

echo "\n========================================\n";
