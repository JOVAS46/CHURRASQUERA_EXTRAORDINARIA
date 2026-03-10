<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Insumo extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_insumo';
    protected $table = 'insumos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'unidad_medida',
        'stock_actual',
        'stock_minimo',
        'precio_unitario',
        'id_proveedor',
    ];

    protected $casts = [
        'stock_actual' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }
}
