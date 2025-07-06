<?php
// database/seeders/PeliculaSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelicula;

class PeliculaSeeder extends Seeder
{
    public function run()
    {
        $peliculas = [
            [
                'titulo' => 'Los 4 Fantásticos',
                'descripcion' => 'Del estudio que nos trajo X-Men: Días del Futuro Pasado. Cuatro jóvenes adquieren poderes sobrenaturales tras un experimento que sale mal.',
                'genero' => 'Acción, Ciencia Ficción',
                'duracion' => 120,
                'director' => 'Josh Trank',
                'clasificacion' => 'PG-13',
                'fecha_estreno' => '2025-08-21',
                'poster' => 'posters/los-4-fantasticos.jpg', // Poster específico
                'activa' => true,
                'destacada' => true,
            ],
            [
                'titulo' => 'Deadpool',
                'descripcion' => 'Un ex operativo de las fuerzas especiales convertido en mercenario es sometido a un experimento ruin que lo deja con poderes curativos acelerados.',
                'genero' => 'Acción, Comedia',
                'duracion' => 108,
                'director' => 'Tim Miller',
                'clasificacion' => 'R',
                'fecha_estreno' => '2025-02-12',
                'poster' => 'posters/deadpool.jpg',
                'activa' => true,
                'destacada' => false,
            ],
            [
                'titulo' => 'Guardianes de la Galaxia',
                'descripcion' => 'Un grupo de inadaptados intergalácticos debe trabajar juntos para detener a un fanático de destruir la galaxia.',
                'genero' => 'Acción, Aventura',
                'duracion' => 121,
                'director' => 'James Gunn',
                'clasificacion' => 'PG-13',
                'fecha_estreno' => '2025-07-01',
                'poster' => 'posters/guardianes-galaxia.jpg',
                'activa' => true,
                'destacada' => false,
            ],
            [
                'titulo' => 'IT',
                'descripcion' => 'En el pueblo de Derry, Maine, siete niños conocidos como The Losers Club se enfrentan a sus peores miedos.',
                'genero' => 'Terror, Thriller',
                'duracion' => 135,
                'director' => 'Andy Muschietti',
                'clasificacion' => 'R',
                'fecha_estreno' => '2025-09-08',
                'poster' => 'posters/it.jpg',
                'activa' => true,
                'destacada' => false,
            ],
            [
                'titulo' => 'The Dark Knight',
                'descripcion' => 'Batman se enfrenta al Joker, un criminal psicópata que desea sumir Ciudad Gótica en la anarquía.',
                'genero' => 'Acción, Drama',
                'duracion' => 152,
                'director' => 'Christopher Nolan',
                'clasificacion' => 'PG-13',
                'fecha_estreno' => '2025-07-18',
                'poster' => 'posters/dark-knight.jpg',
                'activa' => true,
                'destacada' => false,
            ],
            [
                'titulo' => 'Jumanji: Bienvenidos a la Jungla',
                'descripcion' => 'Cuatro adolescentes son transportados a un videojuego y deben completar una aventura peligrosa.',
                'genero' => 'Aventura, Comedia',
                'duracion' => 119,
                'director' => 'Jake Kasdan',
                'clasificacion' => 'PG-13',
                'fecha_estreno' => '2025-12-20',
                'poster' => 'posters/jumanji.jpg',
                'activa' => true,
                'destacada' => false,
            ],
            [
                'titulo' => 'Lilo y Stitch',
                'descripcion' => 'Una niña hawaiana adopta lo que piensa que es un perro, pero resulta ser un alienígena genéticamente modificado.',
                'genero' => 'Animación, Familia',
                'duracion' => 85,
                'director' => 'Dean Fleischer Camp',
                'clasificacion' => 'G',
                'fecha_estreno' => '2025-05-24',
                'poster' => 'posters/lilo-stitch.jpg',
                'activa' => true,
                'destacada' => false,
            ],
            [
                'titulo' => 'Avatar: El Camino del Agua',
                'descripcion' => 'Jake Sully vive con su nueva familia en el planeta Pandora. Cuando una amenaza familiar regresa.',
                'genero' => 'Acción, Aventura, Ciencia Ficción',
                'duracion' => 192,
                'director' => 'James Cameron',
                'clasificacion' => 'PG-13',
                'fecha_estreno' => '2025-06-15',
                'poster' => 'posters/avatar.jpg',
                'activa' => true,
                'destacada' => false,
            ],
        ];

        foreach ($peliculas as $pelicula) {
            Pelicula::create($pelicula);
        }
    }
}