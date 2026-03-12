<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_producto';
    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'id_categoria',
        'imagen',
        'tiempo_preparacion',
        'disponible',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'disponible' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function detalles_pedidos()
    {
        return $this->hasMany(DetallePedido::class, 'id_producto');
    }

    public function detalles_ventas()
    {
        return $this->hasMany(DetalleVenta::class, 'id_producto');
    }

    public function recetas()
    {
        return $this->hasMany(Receta::class, 'id_producto');
    }
}
