<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Mesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReservaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Si es cliente, mostrar solo sus reservas
        if ($user->rol && $user->rol->nombre_rol === 'cliente') {
            $reservas = Reserva::where('id_cliente', $user->id_usuario)
                ->with(['mesa', 'usuario' => function($q) { $q->select('id_usuario', 'nombre', 'apellido'); }])
                ->orderBy('fecha_reserva', 'desc')
                ->paginate(15);
        } else {
            // Para gerente, mesero, cajero, cocinero: mostrar todas las reservas
            $reservas = Reserva::with(['mesa', 'usuario' => function($q) { $q->select('id_usuario', 'nombre', 'apellido'); }])
                ->orderBy('fecha_reserva', 'desc')
                ->paginate(15);
        }

        return Inertia::render('Reservas/Index', [
            'reservas' => $reservas,
            'esGerente' => $user->rol && in_array($user->rol->nombre_rol, ['gerente', 'mesero', 'cajero', 'cocinero']),
            'esCliente' => $user->rol && $user->rol->nombre_rol === 'cliente',
        ]);
    }

    public function create(Request $request)
    {
        $mesas = Mesa::orderBy('numero_mesa')->get();
        
        // Si viene con parámetro mesa_id desde la vista de Mesas
        $mesaPreseleccionada = null;
        if ($request->input('mesa')) {
            $mesaPreseleccionada = Mesa::find($request->input('mesa'));
        }

        return Inertia::render('Reservas/Create', [
            'mesas' => $mesas,
            'mesaPreseleccionada' => $mesaPreseleccionada,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_mesa' => 'required|integer|exists:mesas,id_mesa',
            'fecha_reserva' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'numero_personas' => 'required|integer|min:1|max:20',
            'observaciones' => 'nullable|string',
        ]);

        try {
            // Crear fecha-hora completa para validación
            $fechaHoraInicio = $validated['fecha_reserva'] . ' ' . $validated['hora_inicio'];
            $fechaHoraFin = $validated['fecha_reserva'] . ' ' . $validated['hora_fin'];

            // Validar disponibilidad de mesa en esa fecha/hora
            $reservaExistente = Reserva::where('id_mesa', $validated['id_mesa'])
                ->where('fecha_reserva', $validated['fecha_reserva'])
                ->where('estado', '!=', 'cancelada')
                ->where(function ($query) use ($fechaHoraInicio, $fechaHoraFin) {
                    // Revisar si hay solapamiento de horarios
                    $query->whereBetween('hora_inicio', [$fechaHoraInicio, $fechaHoraFin])
                        ->orWhereBetween('hora_fin', [$fechaHoraInicio, $fechaHoraFin]);
                })
                ->exists();

            if ($reservaExistente) {
                return back()->withErrors(['id_mesa' => 'La mesa no está disponible en ese horario']);
            }

            // Para clientes autenticados, usar su id
            $idCliente = Auth::check() ? Auth::id() : null;

            Reserva::create([
                'id_mesa' => $validated['id_mesa'],
                'fecha_reserva' => $validated['fecha_reserva'],
                'hora_inicio' => $validated['hora_inicio'],
                'hora_fin' => $validated['hora_fin'],
                'numero_personas' => $validated['numero_personas'],
                'estado' => 'confirmada',
                'observaciones' => $validated['observaciones'],
                'id_cliente' => $idCliente,
            ]);

            return redirect('/mesas')
                ->with('success', '✅ Reserva creada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear la reserva: ' . $e->getMessage());
        }
    }

    public function show(Reserva $reserva)
    {
        $reserva->load(['mesa', 'usuario']);

        return Inertia::render('Reservas/Show', [
            'reserva' => $reserva,
        ]);
    }

    public function edit(Reserva $reserva)
    {
        $mesas = Mesa::orderBy('numero')->get();

        return Inertia::render('Reservas/Edit', [
            'reserva' => $reserva,
            'mesas' => $mesas,
        ]);
    }

    public function update(Request $request, Reserva $reserva)
    {
        $validated = $request->validate([
            'id_mesa' => 'required|integer|exists:mesas,id_mesa',
            'fecha_reserva' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'numero_personas' => 'required|integer|min:1|max:20',
            'estado' => 'required|string|in:confirmada,completada,cancelada',
            'observaciones' => 'nullable|string',
        ]);

        try {
            $fechaHoraInicio = $validated['fecha_reserva'] . ' ' . $validated['hora_inicio'];
            $fechaHoraFin = $validated['fecha_reserva'] . ' ' . $validated['hora_fin'];

            // Validar disponibilidad si cambió mesa u hora
            if ($reserva->id_mesa !== $validated['id_mesa'] || 
                $reserva->fecha_reserva !== $validated['fecha_reserva'] ||
                $reserva->hora_inicio !== $fechaHoraInicio) {
                
                $reservaExistente = Reserva::where('id_mesa', $validated['id_mesa'])
                    ->where('fecha_reserva', $validated['fecha_reserva'])
                    ->where('id_reserva', '!=', $reserva->id_reserva)
                    ->where('estado', '!=', 'cancelada')
                    ->where(function ($query) use ($fechaHoraInicio, $fechaHoraFin) {
                        $query->whereBetween('hora_inicio', [$fechaHoraInicio, $fechaHoraFin])
                            ->orWhereBetween('hora_fin', [$fechaHoraInicio, $fechaHoraFin]);
                    })
                    ->exists();

                if ($reservaExistente) {
                    return back()->withErrors(['id_mesa' => 'La mesa no está disponible en ese horario']);
                }
            }

            $reserva->update([
                'id_mesa' => $validated['id_mesa'],
                'fecha_reserva' => $validated['fecha_reserva'],
                'hora_inicio' => $fechaHoraInicio,
                'hora_fin' => $fechaHoraFin,
                'numero_personas' => $validated['numero_personas'],
                'estado' => $validated['estado'],
                'observaciones' => $validated['observaciones'],
            ]);

            return redirect()->route('reservas.index')
                ->with('success', 'Reserva actualizada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar la reserva: ' . $e->getMessage());
        }
    }

    public function destroy(Reserva $reserva)
    {
        try {
            $reserva->update(['estado' => 'cancelada']);

            return redirect()->route('reservas.index')
                ->with('success', 'Reserva cancelada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cancelar la reserva');
        }
    }

    public function obtenerMesasDisponibles(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        $fechaHoraInicio = $validated['fecha'] . ' ' . $validated['hora_inicio'];
        $fechaHoraFin = $validated['fecha'] . ' ' . $validated['hora_fin'];

        $mesasOcupadas = Reserva::where('fecha_reserva', $validated['fecha'])
            ->where('estado', '!=', 'cancelada')
            ->where(function ($query) use ($fechaHoraInicio, $fechaHoraFin) {
                $query->whereBetween('hora_inicio', [$fechaHoraInicio, $fechaHoraFin])
                    ->orWhereBetween('hora_fin', [$fechaHoraInicio, $fechaHoraFin]);
            })
            ->pluck('id_mesa')
            ->toArray();

        $mesasDisponibles = Mesa::whereNotIn('id_mesa', $mesasOcupadas)
            ->where('estado', 'disponible')
            ->orderBy('numero_mesa')
            ->get();

        return response()->json($mesasDisponibles);
    }
}

