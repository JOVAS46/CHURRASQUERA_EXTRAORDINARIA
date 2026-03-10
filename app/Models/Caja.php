<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Caja extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_caja';
    protected $table = 'cajas';

    protected $fillable = [
        'fecha_apertura',
        'fecha_cierre',
        'monto_inicial',
        'monto_final',
        'total_ingresos',
        'total_egresos',
        'estado',
        'id_usuario',
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'monto_inicial' => 'decimal:2',
        'monto_final' => 'decimal:2',
        'total_ingresos' => 'decimal:2',
        'total_egresos' => 'decimal:2',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_caja');
    }
}
