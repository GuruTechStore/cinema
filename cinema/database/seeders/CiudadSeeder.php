<?php
// database/seeders/CiudadSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ciudad;

class CiudadSeeder extends Seeder
{
    public function run()
    {
        $ciudades = [
            'Lima',
            'Arequipa',
            'Trujillo',
            'Chiclayo',
            'Piura',
            'Iquitos',
            'Cusco',
            'Huancayo',
            'Tacna',
            'Ica',
        ];

        foreach ($ciudades as $ciudad) {
            Ciudad::create(['nombre' => $ciudad]);
        }
    }
}