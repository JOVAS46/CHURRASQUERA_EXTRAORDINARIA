<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_menu';
    protected $table = 'menu_items';

    protected $fillable = [
        'nombre',
        'ruta',
        'icono',
        'orden',
        'activo',
        'parent_id',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function hijos()
    {
        return $this->hasMany(MenuItem::class, 'parent_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'rol_menu', 'id_menu', 'id_rol');
    }
}
