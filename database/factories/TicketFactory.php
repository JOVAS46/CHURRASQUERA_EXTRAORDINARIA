<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'numero_ticket' => $this->faker->unique()->numberBetween(10000, 99999),
            'tipo' => $this->faker->randomElement(['cocina', 'cliente']),
            'fecha_emision' => $this->faker->dateTime(),
            'id_pedido' => Pedido::factory(),
            'estado' => $this->faker->randomElement(['pendiente', 'impreso', 'anulado']),
        ];
    }

    public function normal(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'cocina',
        ]);
    }

    public function especial(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'cliente',
        ]);
    }

    public function cortesia(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'cocina',
            'estado' => 'impreso',
        ]);
    }
}
