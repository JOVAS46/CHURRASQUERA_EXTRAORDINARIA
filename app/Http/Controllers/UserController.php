<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with('rol')
            ->orderBy('nombre')
            ->paginate(15);

        return Inertia::render('Usuarios/Index', [
            'usuarios' => $usuarios,
        ]);
    }

    public function create()
    {
        $roles = Role::orderBy('nombre_rol')->get();

        return Inertia::render('Usuarios/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:8|confirmed',
            'id_rol' => 'required|integer|exists:roles,id_rol',
            'telefono' => 'nullable|string|max:20',
            'estado' => 'required|boolean',
        ]);

        try {
            $validated['password'] = Hash::make($validated['password']);
            
            User::create($validated);

            return redirect()->route('admin.usuarios.index')
                ->with('success', 'Usuario creado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }

    public function edit(User $usuario)
    {
        $roles = Role::orderBy('nombre_rol')->get();

        return Inertia::render('Usuarios/Edit', [
            'usuario' => $usuario->load('rol'),
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email,' . $usuario->id_usuario . ',id_usuario',
            'id_rol' => 'required|integer|exists:roles,id_rol',
            'telefono' => 'nullable|string|max:20',
            'estado' => 'required|boolean',
        ]);

        try {
            $usuario->update($validated);

            return redirect()->route('admin.usuarios.index')
                ->with('success', 'Usuario actualizado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function destroy(User $usuario)
    {
        try {
            // Evitar eliminar el usuario actual
            if ($usuario->id_usuario === auth()->id()) {
                return back()->with('error', 'No puedes eliminar tu propio usuario');
            }

            $usuario->delete();

            return redirect()->route('admin.usuarios.index')
                ->with('success', 'Usuario eliminado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }

    public function cambiarPassword(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $usuario->update([
                'password' => Hash::make($validated['password']),
            ]);

            return redirect()->route('admin.usuarios.index')
                ->with('success', 'Contraseña actualizada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar la contraseña: ' . $e->getMessage());
        }
    }
}
