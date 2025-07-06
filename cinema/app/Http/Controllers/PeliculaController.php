<?php
// app/Http/Controllers/PeliculaController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelicula;
use App\Models\Funcion;
use App\Models\Ciudad;
use App\Models\Cine;
use Carbon\Carbon;

class PeliculaController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelicula::where('activa', true);

        // Filtros
        if ($request->filled('buscar')) {
            $query->where('titulo', 'like', '%' . $request->buscar . '%');
        }

        $peliculas = $query->paginate(12);

        return view('peliculas.index', compact('peliculas'));
    }

    public function show(Pelicula $pelicula)
    {
        $ciudades = Ciudad::whereHas('cines.salas.funciones', function($query) use ($pelicula) {
            $query->where('pelicula_id', $pelicula->id)
                  ->where('fecha_funcion', '>=', Carbon::today());
        })->get();

        return view('peliculas.show', compact('pelicula', 'ciudades'));
    }

    public function calendario(Request $request, Pelicula $pelicula)
    {
        $ciudadId = $request->get('ciudad_id');
        $fecha = $request->get('fecha', Carbon::today()->format('Y-m-d'));

        $cines = Cine::whereHas('salas.funciones', function($query) use ($pelicula, $fecha) {
            $query->where('pelicula_id', $pelicula->id)
                  ->where('fecha_funcion', $fecha);
        });

        if ($ciudadId) {
            $cines->where('ciudad_id', $ciudadId);
        }

        $cines = $cines->with(['salas.funciones' => function($query) use ($pelicula, $fecha) {
            $query->where('pelicula_id', $pelicula->id)
                  ->where('fecha_funcion', $fecha)
                  ->orderBy('hora_funcion');
        }])->get();

        $ciudades = Ciudad::all();

        return view('peliculas.calendario', compact('pelicula', 'cines', 'ciudades', 'fecha'));
    }
}

?>
