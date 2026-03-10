<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venta extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_venta';
    protected $table = 'ventas';

    protected $fillable = [
        'numero_venta',
        'fecha_venta',
        'total',
        'estado',
        'id_usuario',
        'id_cliente',
        'id_pedido',
        'id_reserva',
    ];

    protected $casts = [
        'fecha_venta' => 'datetime',
        'total' => 'decimal:2',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'id_cliente', 'id_usuario');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_venta');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta');
    }
}
