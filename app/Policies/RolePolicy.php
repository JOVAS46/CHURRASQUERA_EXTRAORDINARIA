<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * Solo el usuario con rol "Gerente" puede editar roles
     */
    public function editarRol(User $user): bool
    {
        return $user->rol && $user->rol->nombre === 'Gerente';
    }

    /**
     * Solo el usuario con rol "Gerente" puede eliminar roles
     */
    public function destroy(User $user, Role $role): bool
    {
        return $user->rol && $user->rol->nombre === 'Gerente';
    }

    /**
     * Solo el usuario con rol "Gerente" puede crear roles
     */
    public function create(User $user): bool
    {
        return $user->rol && $user->rol->nombre === 'Gerente';
    }
}
