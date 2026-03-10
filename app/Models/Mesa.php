<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mesa extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_mesa';
    protected $table = 'mesas';

    protected $fillable = [
        'numero_mesa',
        'capacidad',
        'ubicacion',
        'estado',
    ];

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_mesa');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_mesa');
    }
}
