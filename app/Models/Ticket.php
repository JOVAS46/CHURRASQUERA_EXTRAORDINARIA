<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_ticket';
    protected $table = 'tickets';

    protected $fillable = [
        'numero_ticket',
        'tipo',
        'fecha_emision',
        'id_pedido',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }
}
