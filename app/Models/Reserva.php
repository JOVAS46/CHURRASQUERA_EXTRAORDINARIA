<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reserva extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_reserva';
    protected $table = 'reservas';

    protected $fillable = [
        'fecha_reserva',
        'hora_inicio',
        'hora_fin',
        'numero_personas',
        'estado',
        'observaciones',
        'id_mesa',
        'id_cliente',
    ];

    protected $casts = [
        'fecha_reserva' => 'date',
        'hora_inicio' => 'string',
        'hora_fin' => 'string',
    ];

    public function mesa()
    {
        return $this->belongsTo(Mesa::class, 'id_mesa');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
