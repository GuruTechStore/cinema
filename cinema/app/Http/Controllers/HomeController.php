<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelicula;
use App\Models\Cine;
use App\Models\Ciudad;
use App\Models\Funcion;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // Películas destacadas para el banner principal
        $peliculaDestacada = Pelicula::where('destacada', true)
            ->where('activa', true)
            ->first();

        // Si no hay película destacada, tomar la primera activa
        if (!$peliculaDestacada) {
            $peliculaDestacada = Pelicula::where('activa', true)
                ->orderBy('fecha_estreno', 'desc')
                ->first();
        }

        // Películas en estreno
        $peliculasEstreno = Pelicula::where('activa', true)
            ->where('fecha_estreno', '<=', Carbon::now())
            ->orderBy('fecha_estreno', 'desc')
            ->limit(6)
            ->get();

        // Próximos estrenos
        $proximosEstrenos = Pelicula::where('activa', true)
            ->where('fecha_estreno', '>', Carbon::now())
            ->orderBy('fecha_estreno', 'asc')
            ->limit(4)
            ->get();

        // Ciudades para los filtros
        $ciudades = Ciudad::orderBy('nombre')->get();

        // Cines para el filtro de sedes
        $cines = Cine::with('ciudad')->orderBy('nombre')->get();

        return view('home.index', compact(
            'peliculaDestacada',
            'peliculasEstreno', 
            'proximosEstrenos',
            'ciudades',
            'cines'
        ));
    }

    public function peliculas(Request $request)
    {
        $query = Pelicula::where('activa', true);

        // Filtros
        if ($request->filled('buscar')) {
            $query->where('titulo', 'like', '%' . $request->buscar . '%');
        }

        if ($request->filled('genero')) {
            $query->where('genero', 'like', '%' . $request->genero . '%');
        }

        if ($request->filled('ciudad_id')) {
            // Filtrar por películas que se proyectan en esa ciudad
            $query->whereHas('funciones.sala.cine', function($q) use ($request) {
                $q->where('ciudad_id', $request->ciudad_id);
            });
        }

        if ($request->filled('cine_id')) {
            // Filtrar por películas que se proyectan en ese cine
            $query->whereHas('funciones.sala', function($q) use ($request) {
                $q->where('cine_id', $request->cine_id);
            });
        }

        if ($request->filled('fecha')) {
            // Filtrar por películas que tienen funciones en esa fecha
            $query->whereHas('funciones', function($q) use ($request) {
                $q->where('fecha_funcion', $request->fecha);
            });
        }

        // Ordenamiento
        $ordenamiento = $request->get('orden', 'fecha_estreno');
        switch ($ordenamiento) {
            case 'titulo':
                $query->orderBy('titulo', 'asc');
                break;
            case 'popularidad':
                $query->withCount('reservas')->orderBy('reservas_count', 'desc');
                break;
            default:
                $query->orderBy('fecha_estreno', 'desc');
                break;
        }

        $peliculas = $query->paginate(12);
        $ciudades = Ciudad::orderBy('nombre')->get();
        $cines = Cine::with('ciudad')->orderBy('nombre')->get();
        $generos = Pelicula::where('activa', true)
            ->select('genero')
            ->distinct()
            ->whereNotNull('genero')
            ->pluck('genero')
            ->map(function($genero) {
                return explode(',', $genero);
            })
            ->flatten()
            ->map(function($genero) {
                return trim($genero);
            })
            ->unique()
            ->sort()
            ->values();

        return view('home.peliculas', compact(
            'peliculas', 
            'ciudades', 
            'cines',
            'generos'
        ));
    }

    public function sedes(Request $request)
    {
        $query = Cine::with('ciudad');

        // Filtros
        if ($request->filled('ciudad_id')) {
            $query->where('ciudad_id', $request->ciudad_id);
        }

        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('direccion', 'like', '%' . $request->buscar . '%');
            });
        }

        // Ordenar por ciudad y luego por nombre
        $cines = $query->orderBy('nombre')->get()->groupBy('ciudad.nombre');
        $ciudades = Ciudad::orderBy('nombre')->get();

        // Estadísticas para mostrar
        $totalCines = Cine::count();
        $ciudadesConCines = Ciudad::whereHas('cines')->count();

        return view('home.sedes', compact(
            'cines', 
            'ciudades',
            'totalCines',
            'ciudadesConCines'
        ));
    }

    public function buscarPeliculas(Request $request)
    {
        $termino = $request->get('q', '');
        
        if (strlen($termino) < 2) {
            return response()->json([]);
        }

        $peliculas = Pelicula::where('activa', true)
            ->where('titulo', 'like', '%' . $termino . '%')
            ->select('id', 'titulo', 'poster', 'genero', 'clasificacion')
            ->limit(5)
            ->get();

        return response()->json($peliculas);
    }

    public function obtenerCinesPorCiudad(Request $request, $ciudadId)
    {
        $cines = Cine::where('ciudad_id', $ciudadId)
            ->select('id', 'nombre', 'direccion')
            ->orderBy('nombre')
            ->get();

        return response()->json($cines);
    }

    public function obtenerFuncionesPorFecha(Request $request)
    {
        $fecha = $request->get('fecha', Carbon::today()->format('Y-m-d'));
        $ciudadId = $request->get('ciudad_id');
        $cineId = $request->get('cine_id');
        $peliculaId = $request->get('pelicula_id');

        $query = Funcion::with(['pelicula', 'sala.cine.ciudad'])
            ->where('fecha_funcion', $fecha);

        if ($ciudadId) {
            $query->whereHas('sala.cine', function($q) use ($ciudadId) {
                $q->where('ciudad_id', $ciudadId);
            });
        }

        if ($cineId) {
            $query->whereHas('sala', function($q) use ($cineId) {
                $q->where('cine_id', $cineId);
            });
        }

        if ($peliculaId) {
            $query->where('pelicula_id', $peliculaId);
        }

        $funciones = $query->orderBy('hora_funcion')->get();

        return response()->json($funciones);
    }

    public function proximosEstrenos()
    {
        $proximosEstrenos = Pelicula::where('activa', true)
            ->where('fecha_estreno', '>', Carbon::now())
            ->orderBy('fecha_estreno', 'asc')
            ->limit(8)
            ->get();

        return response()->json($proximosEstrenos);
    }

    public function peliculasPopulares()
    {
        $peliculasPopulares = Pelicula::where('activa', true)
            ->withCount('reservas')
            ->orderBy('reservas_count', 'desc')
            ->limit(6)
            ->get();

        return response()->json($peliculasPopulares);
    }

    public function estadisticasGenerales()
    {
        $estadisticas = [
            'peliculas_activas' => Pelicula::where('activa', true)->count(),
            'cines_totales' => Cine::count(),
            'ciudades_con_cines' => Ciudad::whereHas('cines')->count(),
            'funciones_hoy' => Funcion::whereDate('fecha_funcion', Carbon::today())->count(),
            'funciones_esta_semana' => Funcion::whereBetween('fecha_funcion', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count(),
        ];

        return response()->json($estadisticas);
    }

    public function verificarDisponibilidad(Request $request)
    {
        $funcionId = $request->get('funcion_id');
        $asientos = $request->get('asientos', []);

        if (!$funcionId || empty($asientos)) {
            return response()->json(['disponible' => false, 'mensaje' => 'Datos incompletos']);
        }

        $funcion = Funcion::find($funcionId);
        if (!$funcion) {
            return response()->json(['disponible' => false, 'mensaje' => 'Función no encontrada']);
        }

        $asientosOcupados = $funcion->getAsientosOcupados();
        $asientosNoDisponibles = array_intersect($asientos, $asientosOcupados);

        if (!empty($asientosNoDisponibles)) {
            return response()->json([
                'disponible' => false, 
                'mensaje' => 'Algunos asientos ya no están disponibles',
                'asientos_ocupados' => $asientosNoDisponibles
            ]);
        }

        return response()->json(['disponible' => true, 'mensaje' => 'Asientos disponibles']);
    }
}
