<?php

namespace Tests\Unit;

use App\Models\Ticket;
use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que se puede crear un ticket
     */
    public function test_puede_crear_un_ticket(): void
    {
        $pedido = Pedido::factory()->create();
        
        $ticket = Ticket::create([
            'numero_ticket' => 10001,
            'tipo' => 'Normal',
            'fecha_emision' => now(),
            'id_pedido' => $pedido->id_pedido,
        ]);

        $this->assertDatabaseHas('tickets', [
            'numero_ticket' => 10001,
            'tipo' => 'Normal',
            'id_pedido' => $pedido->id_pedido,
        ]);
    }

    /**
     * Test que el ticket tiene relación con pedido
     */
    public function test_ticket_pertenece_a_un_pedido(): void
    {
        $pedido = Pedido::factory()->create();
        $ticket = Ticket::factory()->create(['id_pedido' => $pedido->id_pedido]);

        $this->assertInstanceOf(Pedido::class, $ticket->pedido);
        $this->assertEquals($pedido->id_pedido, $ticket->pedido->id_pedido);
    }

    /**
     * Test que el número de ticket es único
     */
    public function test_numero_ticket_es_unico(): void
    {
        $numero = 10002;
        Ticket::factory()->create(['numero_ticket' => $numero]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Ticket::factory()->create(['numero_ticket' => $numero]);
    }

    /**
     * Test que el tipo de ticket es válido
     */
    public function test_tipo_ticket_es_valido(): void
    {
        $tiposValidos = ['Normal', 'Especial', 'Cortesía'];

        foreach ($tiposValidos as $tipo) {
            $ticket = Ticket::factory()->create(['tipo' => $tipo]);
            $this->assertEquals($tipo, $ticket->tipo);
        }
    }

    /**
     * Test que la fecha de emisión se castea a datetime
     */
    public function test_fecha_emision_se_castea_a_datetime(): void
    {
        $ticket = Ticket::factory()->create();

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $ticket->fecha_emision);
    }

    /**
     * Test que se puede obtener un ticket por ID
     */
    public function test_se_puede_obtener_ticket_por_id(): void
    {
        $ticket = Ticket::factory()->create();
        $recuperado = Ticket::find($ticket->id_ticket);

        $this->assertNotNull($recuperado);
        $this->assertEquals($ticket->id_ticket, $recuperado->id_ticket);
    }

    /**
     * Test que se puede actualizar un ticket
     */
    public function test_se_puede_actualizar_un_ticket(): void
    {
        $ticket = Ticket::factory()->create(['tipo' => 'Normal']);
        
        $ticket->update(['tipo' => 'Especial']);

        $this->assertEquals('Especial', $ticket->fresh()->tipo);
    }

    /**
     * Test que se puede eliminar un ticket
     */
    public function test_se_puede_eliminar_un_ticket(): void
    {
        $ticket = Ticket::factory()->create();
        $id = $ticket->id_ticket;

        $ticket->delete();

        $this->assertNull(Ticket::find($id));
    }

    /**
     * Test que los campos fillable están configurados
     */
    public function test_campos_fillable_estan_configurados(): void
    {
        $expectedFillable = [
            'numero_ticket',
            'tipo',
            'fecha_emision',
            'id_pedido',
        ];

        $ticket = new Ticket();
        $this->assertEquals($expectedFillable, $ticket->getFillable());
    }

    /**
     * Test que se pueden usar factories con métodos personalizados
     */
    public function test_factory_normal_crea_ticket_normal(): void
    {
        $ticket = Ticket::factory()->normal()->create();
        $this->assertEquals('Normal', $ticket->tipo);
    }

    /**
     * Test que se pueden usar factories especial
     */
    public function test_factory_especial_crea_ticket_especial(): void
    {
        $ticket = Ticket::factory()->especial()->create();
        $this->assertEquals('Especial', $ticket->tipo);
    }

    /**
     * Test que se pueden usar factories cortesia
     */
    public function test_factory_cortesia_crea_ticket_cortesia(): void
    {
        $ticket = Ticket::factory()->cortesia()->create();
        $this->assertEquals('Cortesía', $ticket->tipo);
    }

    /**
     * Test que cuando se elimina un pedido, se elimina el ticket asociado (si existe relación)
     */
    public function test_ticket_se_asocia_correctamente_con_pedido(): void
    {
        $pedido = Pedido::factory()->create();
        $ticket = Ticket::factory()->create(['id_pedido' => $pedido->id_pedido]);

        $this->assertTrue($pedido->ticket()->exists());
        $this->assertEquals($ticket->id_ticket, $pedido->ticket->id_ticket);
    }

    /**
     * Test que la primary key está configurada correctamente
     */
    public function test_primary_key_es_id_ticket(): void
    {
        $ticket = new Ticket();
        $this->assertEquals('id_ticket', $ticket->getKeyName());
    }

    /**
     * Test que el nombre de tabla es correcto
     */
    public function test_nombre_tabla_es_tickets(): void
    {
        $ticket = new Ticket();
        $this->assertEquals('tickets', $ticket->getTable());
    }
}
