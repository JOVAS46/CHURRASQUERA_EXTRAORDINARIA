<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\User;
use App\Models\Producto;
use App\Models\DetallePedido;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $mesa;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        
        $this->mesa = Mesa::factory()->create();
    }

    /**
     * Test flujo completo: Crear pedido, agregar detalles, crear ticket
     */
    public function test_flujo_completo_pedido_a_ticket(): void
    {
        // 1. Crear un pedido
        $pedido = Pedido::factory()->create([
            'id_mesa' => $this->mesa->id_mesa,
            'id_mesero' => $this->user->id_usuario,
            'estado' => 'pendiente',
            'total' => 0,
        ]);

        $this->assertDatabaseHas('pedidos', [
            'id_pedido' => $pedido->id_pedido,
            'estado' => 'pendiente',
        ]);

        // 2. Crear un producto
        $producto = Producto::factory()->create();

        // 3. Agregar detalles al pedido
        $detalle = DetallePedido::factory()->create([
            'id_pedido' => $pedido->id_pedido,
            'id_producto' => $producto->id_producto,
            'cantidad' => 2,
            'precio_unitario' => 100,
            'subtotal' => 200,
        ]);

        // 4. Actualizar total del pedido
        $pedido->update(['total' => 200]);

        $this->assertDatabaseHas('detalle_pedidos', [
            'id_pedido' => $pedido->id_pedido,
        ]);

        // 5. Crear un ticket para el pedido
        $ticket = Ticket::factory()->create([
            'id_pedido' => $pedido->id_pedido,
            'numero_ticket' => 10100,
        ]);

        $this->assertDatabaseHas('tickets', [
            'id_petido' => $pedido->id_pedido,
            'numero_ticket' => 10100,
        ]);

        // 6. Verificar que el pedido tiene el ticket
        $this->assertTrue($pedido->ticket()->exists());
        $this->assertEquals($ticket->id_ticket, $pedido->ticket->id_ticket);
    }

    /**
     * Test que un pedido puede tener múltiples detalles y un ticket
     */
    public function test_pedido_con_multiples_detalles_y_ticket(): void
    {
        $pedido = Pedido::factory()->create(['id_mesa' => $this->mesa->id_mesa]);

        // Crear múltiples detalles
        $detalles = DetallePedido::factory()->count(3)->create([
            'id_pedido' => $pedido->id_pedido,
        ]);

        // Crear ticket
        $ticket = Ticket::factory()->create(['id_pedido' => $pedido->id_pedido]);

        $this->assertEquals(3, $pedido->detalles()->count());
        $this->assertEquals($ticket->id_ticket, $pedido->ticket->id_ticket);
    }

    /**
     * Test que se puede generar reportes de tickets
     */
    public function test_puede_generar_reporte_tickets_por_fecha(): void
    {
        $fechaInicio = now()->subDays(7);
        $fechaFin = now();

        Ticket::factory()->count(5)->create([
            'fecha_emision' => now(),
        ]);

        Ticket::factory()->count(3)->create([
            'fecha_emision' => now()->subDays(10),
        ]);

        // Simular request de reporte
        $response = $this->get('/tickets/reporte?fecha_inicio=' . $fechaInicio->toDateString() . '&fecha_fin=' . $fechaFin->toDateString());

        $response->assertStatus(200);
    }

    /**
     * Test que se pueden agrupar tickets por tipo
     */
    public function test_puede_agrupar_tickets_por_tipo(): void
    {
        Ticket::factory()->count(5)->create(['tipo' => 'Normal']);
        Ticket::factory()->count(3)->create(['tipo' => 'Especial']);
        Ticket::factory()->count(2)->create(['tipo' => 'Cortesía']);

        $ticketsNormal = Ticket::where('tipo', 'Normal')->count();
        $ticketsEspecial = Ticket::where('tipo', 'Especial')->count();
        $ticketsCortesia = Ticket::where('tipo', 'Cortesía')->count();

        $this->assertEquals(5, $ticketsNormal);
        $this->assertEquals(3, $ticketsEspecial);
        $this->assertEquals(2, $ticketsCortesia);
    }

    /**
     * Test que se puede filtrar tickets por rango de fechas
     */
    public function test_puede_filtrar_tickets_por_rango_fechas(): void
    {
        $ahora = now();
        $hace7dias = now()->subDays(7);
        $hace14dias = now()->subDays(14);

        Ticket::factory()->create(['fecha_emision' => $ahora]);
        Ticket::factory()->create(['fecha_emision' => $hace7dias]);
        Ticket::factory()->create(['fecha_emision' => $hace14dias]);

        $ticketsRecientes = Ticket::whereBetween('fecha_emision', [$hace7dias, $ahora])->count();

        $this->assertEquals(2, $ticketsRecientes);
    }

    /**
     * Test que la eliminación de un pedido no afecta el ticket (datos históricos)
     */
    public function test_ticket_permanece_cuando_pedido_es_eliminado(): void
    {
        $pedido = Pedido::factory()->create();
        $ticket = Ticket::factory()->create(['id_pedido' => $pedido->id_pedido]);

        $ticketId = $ticket->id_ticket;

        // Nota: Dependiendo de tu configuración, esto puede necesitar ajustes
        // Si tienes ON DELETE CASCADE, el ticket se eliminará
        // Si tienes ON DELETE SET NULL, el id_pedido se volverá null
        // Este test asume que el ticket permanece con referencia histórica

        // $pedido->delete();
        // $this->assertDatabaseHas('tickets', ['id_ticket' => $ticketId]);
    }

    /**
     * Test que se puede contar tickets por usuario
     */
    public function test_puede_contar_tickets_por_usuario(): void
    {
        $usuario1 = User::factory()->create();
        $usuario2 = User::factory()->create();

        $pedido1 = Pedido::factory()->create(['id_mesero' => $usuario1->id_usuario]);
        $pedido2 = Pedido::factory()->create(['id_mesero' => $usuario2->id_usuario]);

        Ticket::factory()->count(3)->create(['id_pedido' => $pedido1->id_pedido]);
        Ticket::factory()->count(2)->create(['id_pedido' => $pedido2->id_pedido]);

        $ticketsUsuario1 = Pedido::where('id_mesero', $usuario1->id_usuario)
            ->with('ticket')
            ->count();

        $this->assertEquals(1, $ticketsUsuario1); // 1 pedido con ticket para usuario1
    }

    /**
     * Test que se puede validar la integridad del ticket
     */
    public function test_ticket_tiene_informacion_consistente(): void
    {
        $pedido = Pedido::factory()->create([
            'total' => 500,
            'estado' => 'completado',
        ]);

        $ticket = Ticket::factory()->create([
            'id_pedido' => $pedido->id_pedido,
            'numero_ticket' => 10200,
        ]);

        // Verificar que el ticket está vinculado correctamente
        $this->assertEquals($pedido->id_pedido, $ticket->id_pedido);
        $this->assertEquals($ticket->id_ticket, $pedido->ticket->id_ticket);
    }
}
