<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InsumoController extends Controller
{
    public function index()
    {
        $insumos = Insumo::with('proveedor')
            ->paginate(15);

        return Inertia::render('Admin/Insumos/Index', [
            'insumos' => $insumos,
        ]);
    }

    public function create()
    {
        $proveedores = Proveedor::all();

        return Inertia::render('Admin/Insumos/Create', [
            'proveedores' => $proveedores,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string|max:50',
            'stock_actual' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'precio_unitario' => 'required|numeric|min:0',
            'id_proveedor' => 'required|exists:proveedores,id_proveedor',
        ]);

        Insumo::create($validated);

        return redirect()->route('insumos.index')
            ->with('success', 'Insumo creado exitosamente');
    }

    public function edit(Insumo $insumo)
    {
        $proveedores = Proveedor::all();

        return Inertia::render('Admin/Insumos/Edit', [
            'insumo' => $insumo->load('proveedor'),
            'proveedores' => $proveedores,
        ]);
    }

    public function update(Request $request, Insumo $insumo)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string|max:50',
            'stock_actual' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'precio_unitario' => 'required|numeric|min:0',
            'id_proveedor' => 'required|exists:proveedores,id_proveedor',
        ]);

        $insumo->update($validated);

        return redirect()->route('insumos.index')
            ->with('success', 'Insumo actualizado exitosamente');
    }

    public function destroy(Insumo $insumo)
    {
        $insumo->delete();

        return redirect()->route('insumos.index')
            ->with('success', 'Insumo eliminado exitosamente');
    }
}
