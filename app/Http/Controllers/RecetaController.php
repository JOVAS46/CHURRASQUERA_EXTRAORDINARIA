<?php

namespace App\Http\Controllers;

use App\Models\Receta;
use App\Models\Producto;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RecetaController extends Controller
{
    public function index()
    {
        // Agrupar recetas por producto
        $productos = Producto::with(['recetas' => function ($query) {
            $query->with('insumo');
        }])->paginate(10);

        return Inertia::render('Admin/Recetas/Index', [
            'productos' => $productos,
        ]);
    }

    public function show(Producto $producto)
    {
        $recetas = $producto->recetas()->with('insumo')->get();

        return Inertia::render('Admin/Recetas/Show', [
            'producto' => $producto,
            'recetas' => $recetas,
        ]);
    }

    public function create(Producto $producto)
    {
        $insumos = Insumo::all();

        return Inertia::render('Admin/Recetas/Create', [
            'producto' => $producto,
            'insumos' => $insumos,
        ]);
    }

    public function store(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'id_insumo' => 'required|exists:insumos,id_insumo',
            'cantidad_requerida' => 'required|numeric|min:0.01',
        ]);

        // Verificar si ya existe esta receta
        $existe = Receta::where('id_producto', $producto->id_producto)
            ->where('id_insumo', $validated['id_insumo'])
            ->exists();

        if ($existe) {
            return redirect()->back()
                ->with('error', 'Este insumo ya está en la receta del producto');
        }

        Receta::create([
            'id_producto' => $producto->id_producto,
            'id_insumo' => $validated['id_insumo'],
            'cantidad_requerida' => $validated['cantidad_requerida'],
        ]);

        return redirect()->route('recetas.show', $producto->id_producto)
            ->with('success', 'Ingrediente agregado a la receta');
    }

    public function edit(Producto $producto, Receta $receta)
    {
        if ($receta->id_producto !== $producto->id_producto) {
            abort(404);
        }

        $insumos = Insumo::all();

        return Inertia::render('Admin/Recetas/Edit', [
            'producto' => $producto,
            'receta' => $receta->load('insumo'),
            'insumos' => $insumos,
        ]);
    }

    public function update(Request $request, Producto $producto, Receta $receta)
    {
        if ($receta->id_producto !== $producto->id_producto) {
            abort(404);
        }

        $validated = $request->validate([
            'cantidad_requerida' => 'required|numeric|min:0.01',
        ]);

        $receta->update($validated);

        return redirect()->route('recetas.show', $producto->id_producto)
            ->with('success', 'Cantidad de ingrediente actualizada');
    }

    public function destroy(Producto $producto, Receta $receta)
    {
        if ($receta->id_producto !== $producto->id_producto) {
            abort(404);
        }

        $receta->delete();

        return redirect()->route('recetas.show', $producto->id_producto)
            ->with('success', 'Ingrediente eliminado de la receta');
    }
}
