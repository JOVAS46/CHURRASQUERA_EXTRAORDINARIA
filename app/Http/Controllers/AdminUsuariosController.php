<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminUsuariosController extends Controller
{
    public function index()
    {
        $usuarios = User::with('rol')
            ->paginate(15);

        return Inertia::render('Admin/Usuarios/Index', [
            'usuarios' => $usuarios,
        ]);
    }

    public function create()
    {
        $roles = Role::all();

        return Inertia::render('Admin/Usuarios/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'password' => 'required|string|min:8',
            'telefono' => 'nullable|string',
            'id_rol' => 'required|integer|exists:roles,id_rol',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['estado'] = true;

        User::create($validated);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario creado exitosamente');
    }

    public function edit(User $usuario)
    {
        $roles = Role::all();

        return Inertia::render('Admin/Usuarios/Edit', [
            'usuario' => $usuario,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios,email,' . $usuario->id_usuario . ',id_usuario',
            'telefono' => 'nullable|string',
            'id_rol' => 'required|integer|exists:roles,id_rol',
            'estado' => 'required|boolean',
        ]);

        $usuario->update($validated);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente');
    }

    public function destroy(User $usuario)
    {
        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente');
    }
}
