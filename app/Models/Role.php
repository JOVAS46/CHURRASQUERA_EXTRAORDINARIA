<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_rol';
    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_rol');
    }

    public function menu_items()
    {
        return $this->belongsToMany(MenuItem::class, 'rol_menu', 'id_rol', 'id_menu');
    }
}
