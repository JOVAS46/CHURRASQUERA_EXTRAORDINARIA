<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoleController extends Controller
{
    use AuthorizesRequests;

    /**
     * Mostrar todos los roles
     * Cualquier usuario autenticado puede ver los roles
     */
    public function index()
    {
        $roles = Role::with('usuarios')
            ->get();

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
            'isGerente' => auth()->user()->rol->nombre === 'Gerente',
        ]);
    }

    /**
     * Mostrar formulario para editar rol
     * Solo Gerente puede editar roles
     */
    public function edit(Role $role)
    {
        $this->authorize('editarRol');

        return Inertia::render('Admin/Roles/Edit', [
            'role' => $role,
        ]);
    }

    /**
     * Actualizar un rol
     * Solo Gerente puede actualizar roles
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('editarRol');

        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:roles,nombre,' . $role->id_rol . ',id_rol',
            'descripcion' => 'nullable|string|max:500',
            'activo' => 'required|boolean',
        ]);

        $role->update($validated);

        return redirect()->route('roles.index')
            ->with('success', 'Rol actualizado exitosamente');
    }

    /**
     * Eliminar un rol
     * Solo Gerente puede eliminar roles
     */
    public function destroy(Role $role)
    {
        $this->authorize('editarRol');

        // Verificar que no hay usuarios asignados a este rol
        if ($role->usuarios()->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar un rol que tiene usuarios asignados');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rol eliminado exitosamente');
    }

    /**
     * Verificar si el usuario actual es Gerente
     */
    private function isGerente(): bool
    {
        return auth()->user()->rol->nombre === 'Gerente';
    }
}
