<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetallePedido extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_detalle';
    protected $table = 'detalle_pedidos';

    protected $fillable = [
        'cantidad',
        'precio_unitario',
        'subtotal',
        'observaciones',
        'id_pedido',
        'id_producto',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
