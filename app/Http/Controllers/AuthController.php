<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Buscar usuario por email (la columna 'estado' no existe en la tabla actual)
        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return back()->withErrors(['email' => 'Las credenciales no coinciden con nuestros registros.']);
        }

        Auth::login($user, $request->boolean('remember'));

        return redirect()->route('dashboard')->with('success', '¡Bienvenido ' . $user->nombre . '!');
    }

    public function showRegister()
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|min:8|confirmed',
            'telefono' => 'nullable|string|max:20',
        ]);

        try {
            $user = User::create([
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'telefono' => $validated['telefono'],
                'id_rol' => 3, // Cliente role (default)
                'estado' => true, // Activado por defecto
            ]);

            Auth::login($user);

            return redirect()->route('dashboard')->with('success', '¡Registro exitoso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al registrar: ' . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Sesión cerrada exitosamente');
    }
}
