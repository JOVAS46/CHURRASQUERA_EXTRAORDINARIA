<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_ticket';
    protected $table = 'tickets';

    protected $fillable = [
        'numero_ticket',
        'tipo',
        'fecha_emision',
        'id_pedido',
        'estado',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
    ];

    /**
     * Relación: un ticket pertenece a un pedido
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    /**
     * Generar número de ticket único automáticamente
     */
    public static function generarNumeroTicket(): int
    {
        $ultimoTicket = self::max('numero_ticket') ?? 10000;
        return $ultimoTicket + 1;
    }

    /**
     * Crear ticket automáticamente para un pedido
     */
    public static function crearParaPedido(Pedido $pedido, string $tipo = 'cocina'): ?self
    {
        // Verificar que el pedido no tenga ticket
        if ($pedido->ticket()->exists()) {
            return null;
        }

        $numeroTicket = self::generarNumeroTicket();

        return self::create([
            'numero_ticket' => $numeroTicket,
            'tipo' => $tipo,
            'fecha_emision' => now(),
            'id_pedido' => $pedido->id_pedido,
            'estado' => 'pendiente',  // Esperando impresión
        ]);
    }

    /**
     * Obtener el total del ticket
     */
    public function getTotal(): float
    {
        return $this->pedido?->total ?? 0;
    }

    /**
     * Obtener detalles del ticket (detalles del pedido)
     */
    public function getDetalles()
    {
        return $this->pedido?->detalles ?? collect([]);
    }

    /**
     * Obtener número de mesa
     */
    public function getNumeroMesa(): ?int
    {
        return $this->pedido?->mesa?->numero_mesa;
    }

    /**
     * Obtener información del mesero
     */
    public function getMesero()
    {
        return $this->pedido?->usuario;
    }

    /**
     * Verificar si el ticket es exportable
     */
    public function esExportable(): bool
    {
        return $this->pedido && $this->pedido->total > 0;
    }

    /**
     * Obtener tipos de ticket disponibles
     */
    public static function getTiposDisponibles(): array
    {
        return ['cocina', 'cliente'];
    }

    /**
     * Scope: filtrar por tipo
     */
    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope: filtrar por rango de fechas
     */
    public function scopeEntreFechas($query, $fechaDesde, $fechaHasta)
    {
        return $query->whereDate('fecha_emision', '>=', $fechaDesde)
                     ->whereDate('fecha_emision', '<=', $fechaHasta);
    }

    /**
     * Scope: tickets recientes
     */
    public function scopeRecientes($query, $dias = 7)
    {
        return $query->where('fecha_emision', '>=', now()->subDays($dias))
                     ->orderBy('fecha_emision', 'desc');
    }

    /**
     * Verificar si el ticket está impreso (procesado)
     */
    public function estaImpreso(): bool
    {
        return $this->estado === 'impreso';
    }

    /**
     * Verificar si el ticket está pendiente
     */
    public function estaPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    /**
     * Verificar si el ticket está anulado
     */
    public function estaAnulado(): bool
    {
        return $this->estado === 'anulado';
    }

    /**
     * Obtener estados disponibles
     */
    public static function getEstadosDisponibles(): array
    {
        return ['pendiente', 'impreso', 'anulado'];
    }

    /**
     * Scope: tickets sin pagar
     */
    public function scopeSinPagar($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope: tickets impresos
     */
    public function scopeImpresos($query)
    {
        return $query->where('estado', 'impreso');
    }
}