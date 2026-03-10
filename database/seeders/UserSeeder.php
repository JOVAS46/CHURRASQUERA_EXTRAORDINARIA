<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nombre' => 'Admin',
                'apellido' => 'Sistema',
                'password' => Hash::make('password123'),
                'id_rol' => 1,
                'estado' => true,
                'telefono' => '+591 7123456'
            ]
        );

        // Mesero user
        User::updateOrCreate(
            ['email' => 'mesero@example.com'],
            [
                'nombre' => 'Juan',
                'apellido' => 'Mesero',
                'password' => Hash::make('password123'),
                'id_rol' => 2,
                'estado' => true,
                'telefono' => '+591 7654321'
            ]
        );

        // Cliente user
        User::updateOrCreate(
            ['email' => 'cliente@example.com'],
            [
                'nombre' => 'Pedro',
                'apellido' => 'Cliente',
                'password' => Hash::make('password123'),
                'id_rol' => 3,
                'estado' => true,
                'telefono' => '+591 7789012'
            ]
        );
    }
}
