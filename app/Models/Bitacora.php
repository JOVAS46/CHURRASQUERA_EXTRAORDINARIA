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
}
