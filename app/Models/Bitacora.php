<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bitacora extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_bitacora';
    protected $table = 'bitacoras';

    protected $fillable = [
        'accion',
        'tabla_afectada',
        'fecha_hora',
        'ip_address',
        'detalles',
        'id_usuario',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Registra una acción en la bitácora
     */
    public static function registrarAccion($accion, $tabla_afectada, $detalles = null)
    {
        try {
            return self::create([
                'accion' => $accion,
                'tabla_afectada' => $tabla_afectada,
                'detalles' => $detalles,
                'fecha_hora' => now(),
                'ip_address' => request()->ip(),
                'id_usuario' => auth()->id() ?? 1, // Default to 1 if not authenticated
            ]);
        } catch (\Exception $e) {
            // Log but don't fail if bitacora fails
            \Log::error('Error registering action in bitacora: ' . $e->getMessage());
        }
    }
}
