<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MetodoPago extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_metodo_pago';
    protected $table = 'metodo_pagos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_metodo_pago');
    }
}
