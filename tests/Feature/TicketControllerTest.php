<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $pedido;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear un usuario autenticado
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        
        // Crear un pedido para usar en los tests
        $this->pedido = Pedido::factory()->create();
    }

    /**
     * Test que se puede listar todos los tickets
     */
    public function test_puede_listar_todos_los_tickets(): void
    {
        Ticket::factory()->count(5)->create();

        $response = $this->get('/tickets');

        $response->assertStatus(200);
    }

    /**
     * Test que se puede crear un nuevo ticket
     */
    public function test_puede_crear_un_nuevo_ticket(): void
    {
        $datos = [
            'numero_ticket' => 10001,
            'tipo' => 'Normal',
            'fecha_emision' => now(),
            'id_pedido' => $this->pedido->id_pedido,
        ];

        $response = $this->post('/tickets', $datos);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tickets', $datos);
    }

    /**
     * Test que la validación falla sin número de ticket
     */
    public function test_validacion_falla_sin_numero_ticket(): void
    {
        $datos = [
            'tipo' => 'Normal',
            'fecha_emision' => now(),
            'id_pedido' => $this->pedido->id_pedido,
        ];

        $response = $this->post('/tickets', $datos);

        $response->assertStatus(422);
    }

    /**
     * Test que la validación falla sin tipo de ticket
     */
    public function test_validacion_falla_sin_tipo_ticket(): void
    {
        $datos = [
            'numero_ticket' => 10002,
            'fecha_emision' => now(),
            'id_pedido' => $this->pedido->id_pedido,
        ];

        $response = $this->post('/tickets', $datos);

        $response->assertStatus(422);
    }

    /**
     * Test que la validación falla sin ID de pedido
     */
    public function test_validacion_falla_sin_id_pedido(): void
    {
        $datos = [
            'numero_ticket' => 10003,
            'tipo' => 'Normal',
            'fecha_emision' => now(),
        ];

        $response = $this->post('/tickets', $datos);

        $response->assertStatus(422);
    }

    /**
     * Test que se puede obtener un ticket específico
     */
    public function test_puede_obtener_un_ticket_especifico(): void
    {
        $ticket = Ticket::factory()->create();

        $response = $this->get("/tickets/{$ticket->id_ticket}");

        $response->assertStatus(200);
    }

    /**
     * Test que retorna 404 si el ticket no existe
     */
    public function test_retorna_404_si_ticket_no_existe(): void
    {
        $response = $this->get("/tickets/99999");

        $response->assertStatus(404);
    }

    /**
     * Test que se puede actualizar un ticket
     */
    public function test_puede_actualizar_un_ticket(): void
    {
        $ticket = Ticket::factory()->create(['tipo' => 'Normal']);

        $datos = ['tipo' => 'Especial'];

        $response = $this->put("/tickets/{$ticket->id_ticket}", $datos);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tickets', [
            'id_ticket' => $ticket->id_ticket,
            'tipo' => 'Especial',
        ]);
    }

    /**
     * Test que se puede eliminar un ticket
     */
    public function test_puede_eliminar_un_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $response = $this->delete("/tickets/{$ticket->id_ticket}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tickets', [
            'id_ticket' => $ticket->id_ticket,
        ]);
    }

    /**
     * Test que se pueden filtrar tickets por tipo
     */
    public function test_puede_filtrar_tickets_por_tipo(): void
    {
        Ticket::factory()->create(['tipo' => 'Normal']);
        Ticket::factory()->create(['tipo' => 'Especial']);
        Ticket::factory()->create(['tipo' => 'Cortesía']);

        $response = $this->get('/tickets?tipo=Normal');

        $response->assertStatus(200);
    }

    /**
     * Test que se pueden filtrar tickets por ID de pedido
     */
    public function test_puede_filtrar_tickets_por_pedido(): void
    {
        $pedido1 = Pedido::factory()->create();
        $pedido2 = Pedido::factory()->create();

        Ticket::factory()->create(['id_pedido' => $pedido1->id_pedido]);
        Ticket::factory()->create(['id_pedido' => $pedido2->id_pedido]);

        $response = $this->get("/tickets?id_pedido={$pedido1->id_pedido}");

        $response->assertStatus(200);
    }

    /**
     * Test que se puede obtener el ticket con sus relaciones
     */
    public function test_puede_obtener_ticket_con_relaciones(): void
    {
        $ticket = Ticket::factory()->create();

        $response = $this->getJson("/tickets/{$ticket->id_ticket}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id_ticket',
                'numero_ticket',
                'tipo',
                'fecha_emision',
                'id_pedido',
                'pedido' => [
                    'id_pedido',
                    'numero_pedido',
                    'estado',
                ]
            ]
        ]);
    }

    /**
     * Test que no se puede crear un ticket sin autenticación
     */
    public function test_no_puede_crear_ticket_sin_autenticacion(): void
    {
        $this->signOut();

        $datos = [
            'numero_ticket' => 10004,
            'tipo' => 'Normal',
            'fecha_emision' => now(),
            'id_pedido' => $this->pedido->id_pedido,
        ];

        $response = $this->post('/tickets', $datos);

        $response->assertStatus(401);
    }

    /**
     * Test que se puede exportar tickets
     */
    public function test_puede_exportar_tickets(): void
    {
        Ticket::factory()->count(10)->create();

        $response = $this->get('/tickets/export?format=pdf');

        $response->assertStatus(200);
    }

    /**
     * Test que el número de ticket debe ser único
     */
    public function test_numero_ticket_debe_ser_unico(): void
    {
        $ticket = Ticket::factory()->create(['numero_ticket' => 10005]);

        $datos = [
            'numero_ticket' => 10005,
            'tipo' => 'Normal',
            'fecha_emision' => now(),
            'id_pedido' => $this->pedido->id_pedido,
        ];

        $response = $this->post('/tickets', $datos);

        $response->assertStatus(422);
    }

    /**
     * Ayudante para desautenticarse
     */
    protected function signOut(): void
    {
        auth()->logout();
    }
}
