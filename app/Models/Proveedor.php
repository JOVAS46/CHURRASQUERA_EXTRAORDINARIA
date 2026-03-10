<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proveedor extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_proveedor';
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'contacto',
        'telefono',
        'email',
        'direccion',
        'estado',
    ];

    public function insumos()
    {
        return $this->hasMany(Insumo::class, 'id_proveedor');
    }
}
