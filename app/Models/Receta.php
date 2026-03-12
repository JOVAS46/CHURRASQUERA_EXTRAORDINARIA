<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receta extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_receta';
    protected $table = 'recetas';

    protected $fillable = [
        'id_producto',
        'id_insumo',
        'cantidad_requerida',
    ];

    protected $casts = [
        'cantidad_requerida' => 'decimal:2',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'id_insumo');
    }
}
