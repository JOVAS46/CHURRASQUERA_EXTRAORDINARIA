<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PageVisit extends Model
{
    use HasFactory;

    protected $table = 'visita_paginas';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'pagina',
        'url',
        'visitante_ip',
        'user_agent',
        'referrer',
        'fecha_hora',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public static function registrarVisita($pagina, $url, $ip, $userAgent, $referrer = null)
    {
        return self::create([
            'pagina' => $pagina,
            'url' => $url,
            'visitante_ip' => $ip,
            'user_agent' => $userAgent,
            'referrer' => $referrer,
            'fecha_hora' => now(),
        ]);
    }

    public static function contarVisitasPorPagina($pagina)
    {
        return self::where('pagina', $pagina)->count();
    }

    public static function obtenerTotalVisitas()
    {
        return self::count();
    }
}
