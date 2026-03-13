<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPreference extends Model
{
    use HasFactory;

    protected $table = 'user_preferences';
    protected $primaryKey = 'id_preferencia';
    protected $fillable = [
        'id_usuario',
        'tema', // 'ninos', 'jovenes', 'adultos'
        'tamano_letra', // 'pequeño', 'normal', 'grande', 'muy_grande'
        'alto_contraste', // true/false
    ];

    protected $casts = [
        'alto_contraste' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    // Valores por defecto
    public static function obtenerPreferencias($id_usuario)
    {
        // Validar que id_usuario no sea nulo
        if (!$id_usuario) {
            return null;
        }
        
        return self::where('id_usuario', $id_usuario)->first() ?? self::crearPorDefecto($id_usuario);
    }

    public static function crearPorDefecto($id_usuario)
    {
        // Validar que id_usuario no sea nulo
        if (!$id_usuario) {
            return null;
        }
        
        return self::create([
            'id_usuario' => $id_usuario,
            'tema' => 'ninos',
            'tamano_letra' => 'mediano',
            'alto_contraste' => false,
        ]);
    }

    // Temas disponibles
    public static function temasDisponibles()
    {
        return [
            'ninos' => ['nombre' => '👧 Niños', 'colores' => 'colorfull'],
            'jovenes' => ['nombre' => '👦 Jóvenes', 'colores' => 'moderno'],
            'adultos' => ['nombre' => '👨 Adultos', 'colores' => 'profesional'],
        ];
    }
}
