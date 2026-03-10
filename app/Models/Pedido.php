<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pedido extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_pedido';
    protected $table = 'pedidos';

    protected $fillable = [
        'fecha_pedido',
        'estado',
        'total',
        'observaciones',
        'id_mesa',
        'id_cliente',
        'id_mesero',
    ];

    protected $casts = [
        'fecha_pedido' => 'datetime',
        'total' => 'decimal:2',
    ];

    public function mesa()
    {
        return $this->belongsTo(Mesa::class, 'id_mesa');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_mesero', 'id_usuario');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'id_cliente', 'id_usuario');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'id_pedido');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_pedido');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_venta', 'id_pedido');
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class, 'id_pedido');
    }
}
