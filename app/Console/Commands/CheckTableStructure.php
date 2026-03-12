<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckTableStructure extends Command
{
    protected $signature = 'check:tables';
    protected $description = 'Verificar estructura de tablas recetas y rol_menu';

    public function handle()
    {
        $this->info('=== LISTADO DE TODAS LAS TABLAS ===');
        try {
            $tables = DB::select("
                SELECT tablename FROM pg_tables 
                WHERE schemaname = 'public'
                ORDER BY tablename
            ");
            
            foreach ($tables as $table) {
                $this->line($table->tablename);
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }

        $this->info("\n=== TABLA RECETAS ===");
        try {
            $recetas = DB::select("
                SELECT column_name, data_type 
                FROM information_schema.columns 
                WHERE table_name = 'recetas' 
                ORDER BY ordinal_position
            ");
            
            if (count($recetas) > 0) {
                $this->table(['Campo', 'Tipo'], collect($recetas)->map(fn($col) => [
                    $col->column_name,
                    $col->data_type
                ])->toArray());
                
                // Contar registros
                $count = DB::selectOne("SELECT COUNT(*) as cnt FROM recetas");
                $this->info("Total registros: " . $count->cnt);
            } else {
                $this->warn('TABLA NO EXISTE');
            }
        } catch (\Exception $e) {
            $this->warn('TABLA NO EXISTE - ' . $e->getMessage());
        }

        $this->info("\n=== TABLA ROL_MENU ===");
        try {
            $rol_menu = DB::select("
                SELECT column_name, data_type 
                FROM information_schema.columns 
                WHERE table_name = 'rol_menu' 
                ORDER BY ordinal_position
            ");
            
            if (count($rol_menu) > 0) {
                $this->table(['Campo', 'Tipo'], collect($rol_menu)->map(fn($col) => [
                    $col->column_name,
                    $col->data_type
                ])->toArray());
                
                // Contar registros
                $count = DB::selectOne("SELECT COUNT(*) as cnt FROM rol_menu");
                $this->info("Total registros de permisos: " . $count->cnt);
                
                // Mostrar ejemplo
                $this->info("\nEjemplos de permisos asignados:");
                $samples = DB::select("SELECT id_rol, id_menu FROM rol_menu LIMIT 5");
                foreach ($samples as $sample) {
                    $this->line("  id_rol: {$sample->id_rol} | id_menu: {$sample->id_menu}");
                }
            } else {
                $this->warn('TABLA NO EXISTE');
            }
        } catch (\Exception $e) {
            $this->warn('TABLA NO EXISTE - ' . $e->getMessage());
        }
    }
}
