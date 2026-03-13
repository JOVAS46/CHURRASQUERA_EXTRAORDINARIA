<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pago extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_pago';
    protected $table = 'pagos';

    protected $fillable = [
        'monto',
        'fecha_pago',
        'estado',
        'nro_transaccion',
        'id_metodo_pago',
        'id_venta',
        'qr_image',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'datetime',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'id_metodo_pago');
    }

    public function usuario()
    {
        return $this->venta->usuario();
    }
}
