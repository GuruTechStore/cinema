<?php
// database/seeders/CineSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cine;

class CineSeeder extends Seeder
{
    public function run()
    {
        $cines = [
            [
                'nombre' => 'CP Alcazar',
                'direccion' => 'Av. Santa Cruz 814-816',
                'ciudad_id' => 1, // Lima
                'formatos' => '2D, REGULAR, 3D'
            ],
            [
                'nombre' => 'CP Arequipa Mall Plaza',
                'direccion' => 'Av. Ejército 793 Cayma',
                'ciudad_id' => 2, // Arequipa
                'formatos' => '2D, 3D, REGULAR'
            ],
            [
                'nombre' => 'CP Arequipa Paseo Central',
                'direccion' => 'Av. Arturo Ibañez S/N.',
                'ciudad_id' => 2, // Arequipa
                'formatos' => '2D, REGULAR'
            ],
            [
                'nombre' => 'CP Arequipa Real Plaza',
                'direccion' => 'Av Ejército 1009 Cayma',
                'ciudad_id' => 2, // Arequipa
                'formatos' => '2D, REGULAR, 3D'
            ],
            [
                'nombre' => 'CP Brasil',
                'direccion' => 'Av. Brasil 714 - 792 Piso 3',
                'ciudad_id' => 1, // Lima
                'formatos' => '2D, REGULAR, 3D'
            ],
            [
                'nombre' => 'CP Cajamarca',
                'direccion' => 'Av. Vía de Evitamiento Norte',
                'ciudad_id' => 8, // Huancayo
                'formatos' => '2D, REGULAR, 3D'
            ],
            [
                'nombre' => 'CP Real Plaza Puruchuco',
                'direccion' => 'Av. Prolongación Javier Prado, 8680',
                'ciudad_id' => 1, // Lima
                'formatos' => '2D, REGULAR, 3D'
            ],
            [
                'nombre' => 'CP Mall Aventura',
                'direccion' => 'Av. Nicolás Ayllón, Santa Anita, Perú',
                'ciudad_id' => 1, // Lima
                'formatos' => '2D, REGULAR, 3D'
            ],
        ];

        foreach ($cines as $cine) {
            Cine::create($cine);
        }
    }
}
