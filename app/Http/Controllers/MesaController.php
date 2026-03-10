<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MesaController extends Controller
{
    public function index()
    {
        $mesas = Mesa::paginate(12);

        return Inertia::render('Mesas/Index', [
            'mesas' => $mesas,
        ]);
    }

    public function create()
    {
        return Inertia::render('Mesas/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_mesa' => 'required|integer|unique:mesas|min:1',
            'capacidad' => 'required|integer|min:1|max:20',
            'ubicacion' => 'required|string|max:255',
            'estado' => 'required|in:disponible,ocupada,mantenimiento',
        ]);

        Mesa::create($validated);

        return redirect()->route('mesas.index')
            ->with('success', 'Mesa creada exitosamente');
    }

    public function edit(Mesa $mesa)
    {
        return Inertia::render('Mesas/Edit', [
            'mesa' => $mesa,
        ]);
    }

    public function update(Request $request, Mesa $mesa)
    {
        $validated = $request->validate([
            'numero_mesa' => 'required|integer|unique:mesas,numero_mesa,' . $mesa->id_mesa . ',id_mesa|min:1',
            'capacidad' => 'required|integer|min:1|max:20',
            'ubicacion' => 'required|string|max:255',
            'estado' => 'required|in:disponible,ocupada,mantenimiento',
        ]);

        $mesa->update($validated);

        return redirect()->route('mesas.index')
            ->with('success', 'Mesa actualizada exitosamente');
    }

    public function destroy(Mesa $mesa)
    {
        $mesa->delete();

        return redirect()->route('mesas.index')
            ->with('success', 'Mesa eliminada exitosamente');
    }
}
