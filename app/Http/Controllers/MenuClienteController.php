<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MenuController extends Controller
{
    public function index()
    {
        $productos = Producto::where('disponible', true)
            ->get();

        return Inertia::render('Menu/Index', [
            'productos' => $productos,
        ]);
    }
}
