<?php
// database/seeders/SalaSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sala;

class SalaSeeder extends Seeder
{
    public function run()
    {
        // Crear salas para cada cine
        for ($cineId = 1; $cineId <= 8; $cineId++) {
            for ($numeroSala = 1; $numeroSala <= 5; $numeroSala++) {
                Sala::create([
                    'cine_id' => $cineId,
                    'nombre' => "Sala {$numeroSala}",
                    'total_asientos' => 80,
                    'filas' => 8,
                    'asientos_por_fila' => 10,
                ]);
            }
        }
    }
}

?>