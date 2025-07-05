<?php
// database/seeders/CategoriaDulceriaSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoriaDulceria;

class CategoriaDulceriaSeeder extends Seeder
{
    public function run()
    {
        $categorias = [
            'Canchita',
            'Bebidas',
            'Dulces',
            'Combos',
            'Nachos',
            'Hot Dogs',
            'Helados',
            'Snacks',
        ];

        foreach ($categorias as $categoria) {
            CategoriaDulceria::create(['nombre' => $categoria]);
        }
    }
}

?>
