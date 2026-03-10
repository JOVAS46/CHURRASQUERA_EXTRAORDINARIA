<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function getMenu()
    {
        $menus = MenuItem::where('activo', true)
            ->whereNull('parent_id')
            ->with('hijos')
            ->orderBy('orden')
            ->get();

        return response()->json($menus);
    }
}

