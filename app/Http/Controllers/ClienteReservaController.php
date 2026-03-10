<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClienteReservaController extends Controller
{
    /**
     * Mostrar reservas del cliente actual
     */
    public function index()
    {
        $user = Auth::user();
        
        $reservas = Reserva::where('id_cliente', $user->id_usuario)
            ->with(['mesa'])
            ->orderBy('fecha_reserva', 'desc')
            ->paginate(15);

        return Inertia::render('Reservas/MisReservas', [
            'reservas' => $reservas,
        ]);
    }
}
