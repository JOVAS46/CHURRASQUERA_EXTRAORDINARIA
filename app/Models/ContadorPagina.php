<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContadorPagina extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_contador';
    protected $table = 'contador_paginas';

    protected $fillable = [
        'pagina',
        'total_visitas',
    ];

    protected $casts = [
        'total_visitas' => 'integer',
    ];
}
