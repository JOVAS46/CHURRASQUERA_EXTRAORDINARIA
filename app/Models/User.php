<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'id_rol',
        'telefono',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    // Relaciones
    public function rol()
    {
        return $this->belongsTo(Role::class, 'id_rol');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_usuario');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_usuario');
    }

    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class, 'id_usuario');
    }
}
