<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the old constraint
        DB::statement('ALTER TABLE tickets DROP CONSTRAINT tickets_estado_check');
        
        // Add the new constraint with 'pagado' allowed
        DB::statement('ALTER TABLE tickets ADD CONSTRAINT tickets_estado_check CHECK (estado IN (\'pendiente\', \'impreso\', \'pagado\', \'anulado\'))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new constraint
        DB::statement('ALTER TABLE tickets DROP CONSTRAINT tickets_estado_check');
        
        // Restore the old constraint
        DB::statement('ALTER TABLE tickets ADD CONSTRAINT tickets_estado_check CHECK (estado IN (\'pendiente\', \'impreso\'))');
    }
};
