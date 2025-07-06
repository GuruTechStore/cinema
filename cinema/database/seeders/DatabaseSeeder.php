<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UsuarioSeeder::class,
            CiudadSeeder::class,
            CineSeeder::class,
            SalaSeeder::class,
            PeliculaSeeder::class,
            FuncionSeeder::class,
            CategoriaDulceriaSeeder::class,
            ProductoDulceriaSeeder::class,
        ]);
    }
}

