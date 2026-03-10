<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Mostrar el perfil del usuario autenticado
     */
    public function show(Request $request)
    {
        $user = $request->user()->load('rol');
        
        return Inertia::render('Profile/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Mostrar formulario para editar perfil
     */
    public function edit(Request $request)
    {
        $user = $request->user()->load('rol');
        
        return Inertia::render('Profile/Edit', [
            'user' => $user,
        ]);
    }

    /**
     * Actualizar el perfil del usuario
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email,' . $request->user()->id_usuario . ',id_usuario',
            'telefono' => 'nullable|string|max:20',
        ]);

        try {
            $request->user()->update($validated);

            return redirect()->route('profile.show')
                ->with('success', 'Perfil actualizado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar perfil: ' . $e->getMessage());
        }
    }
}
