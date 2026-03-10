<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categoria')
            ->paginate(15);

        return Inertia::render('Productos/Index', [
            'productos' => $productos,
        ]);
    }

    public function create()
    {
        $categorias = Categoria::whereIn('tipo', ['plato', 'bebida'])->get();

        return Inertia::render('Productos/Create', [
            'categorias' => $categorias,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'id_categoria' => 'required|integer|exists:categorias,id_categoria',
            'disponible' => 'required|boolean',
            'tiempo_preparacion' => 'nullable|integer|min:1',
        ]);

        Producto::create($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::whereIn('tipo', ['plato', 'bebida'])->get();

        return Inertia::render('Productos/Edit', [
            'producto' => $producto,
            'categorias' => $categorias,
        ]);
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'id_categoria' => 'required|integer|exists:categorias,id_categoria',
            'disponible' => 'required|boolean',
            'tiempo_preparacion' => 'nullable|integer|min:1',
        ]);

        $producto->update($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente');
    }
}

