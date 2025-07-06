<?php
// database/seeders/UsuarioSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        // Usuario admin
        User::create([
            'name' => 'Administrador Sistema',
            'email' => 'admin@salchichon.com',
            'password' => Hash::make('admin123'),
            'rol' => 'admin',
        ]);

        // Usuario cliente
        User::create([
            'name' => 'Cliente Prueba',
            'email' => 'cliente@test.com',
            'password' => Hash::make('cliente123'),
            'rol' => 'usuario',
        ]);

        // Más usuarios de prueba
        User::create([
            'name' => 'María García',
            'email' => 'maria@test.com',
            'password' => Hash::make('maria123'),
            'rol' => 'usuario',
        ]);

        User::create([
            'name' => 'Carlos López',
            'email' => 'carlos@test.com',
            'password' => Hash::make('carlos123'),
            'rol' => 'usuario',
        ]);
    }
}