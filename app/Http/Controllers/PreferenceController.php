<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;

class PreferenceController extends Controller
{
    /**
     * Actualizar preferencia de tema
     * POST /preferences/tema
     */
    public function actualizarTema(Request $request)
    {
        $validated = $request->validate([
            'tema' => 'required|in:ninos,adultos,alto_contraste',
        ]);

        $preferencia = UserPreference::obtenerPreferencias(Auth::id());
        $preferencia->update(['tema' => $validated['tema']]);

        return back()->with('success', '✅ Tema actualizado exitosamente');
    }

    /**
     * Actualizar tamaño de letra
     * POST /preferences/tamano-letra
     */
    public function actualizarTamanoLetra(Request $request)
    {
        $validated = $request->validate([
            'tamano_letra' => 'required|in:pequeno,mediano,grande',
        ]);

        $preferencia = UserPreference::obtenerPreferencias(Auth::id());
        $preferencia->update(['tamano_letra' => $validated['tamano_letra']]);

        return back()->with('success', '✅ Tamaño de letra actualizado');
    }

    /**
     * Actualizar contraste alto
     * POST /preferences/alto-contraste
     */
    public function actualizarAltoContraste(Request $request)
    {
        $validated = $request->validate([
            'alto_contraste' => 'required|boolean',
        ]);

        $preferencia = UserPreference::obtenerPreferencias(Auth::id());
        $preferencia->update(['alto_contraste' => $validated['alto_contraste']]);

        return back()->with('success', '✅ Contraste actualizado');
    }
}

