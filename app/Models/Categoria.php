<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categoria extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_categoria';
    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_categoria');
    }
}
