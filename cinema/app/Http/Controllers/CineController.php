<?php
// app/Http/Controllers/CineController.php - COMPLETO

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cine;
use App\Models\Ciudad;
use App\Models\Pelicula;
use App\Models\Funcion;
use Carbon\Carbon;

class CineController extends Controller
{
    public function index(Request $request)
    {
        $query = Cine::with('ciudad');

        // Filtro por ciudad
        if ($request->filled('ciudad_id')) {
            $query->where('ciudad_id', $request->ciudad_id);
        }

        // Búsqueda por nombre
        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        $cines = $query->paginate(12);
        $ciudades = Ciudad::all();

        return view('cines.index', compact('cines', 'ciudades'));
    }

    public function show(Cine $cine)
    {
        $cine->load('salas', 'ciudad');
        
        // Obtener películas que se proyectan en este cine
        $peliculas = Pelicula::whereHas('funciones.sala', function($query) use ($cine) {
            $query->where('cine_id', $cine->id);
        })->where('activa', true)->get();

        return view('cines.show', compact('cine', 'peliculas'));
    }

    public function programacion(Request $request, Cine $cine)
    {
        $fecha = $request->get('fecha', Carbon::today()->format('Y-m-d'));
        
        // Obtener funciones de este cine para la fecha seleccionada
        $funciones = Funcion::whereHas('sala', function($query) use ($cine) {
            $query->where('cine_id', $cine->id);
        })
        ->with(['pelicula', 'sala'])
        ->where('fecha_funcion', $fecha)
        ->orderBy('hora_funcion')
        ->get()
        ->groupBy('pelicula_id');

        return view('cines.programacion', compact('cine', 'funciones', 'fecha'));
    }

    // MÉTODOS AJAX PARA OBTENER DATOS DINÁMICOS

    public function funcionesAjax(Request $request, Cine $cine)
    {
        $fecha = $request->get('fecha', Carbon::today()->format('Y-m-d'));
        $peliculaId = $request->get('pelicula_id');

        $query = Funcion::whereHas('sala', function($q) use ($cine) {
            $q->where('cine_id', $cine->id);
        })
        ->with(['pelicula', 'sala'])
        ->where('fecha_funcion', $fecha);

        if ($peliculaId) {
            $query->where('pelicula_id', $peliculaId);
        }

        $funciones = $query->orderBy('hora_funcion')->get();

        return response()->json($funciones);
    }

    public function salasAjax(Cine $cine)
    {
        $salas = $cine->salas()->orderBy('nombre')->get();
        return response()->json($salas);
    }

    public function peliculasAjax(Request $request, Cine $cine)
    {
        $fecha = $request->get('fecha');
        
        $query = Pelicula::whereHas('funciones.sala', function($q) use ($cine, $fecha) {
            $q->where('cine_id', $cine->id);
            if ($fecha) {
                $q->where('fecha_funcion', $fecha);
            }
        })->where('activa', true);

        $peliculas = $query->orderBy('titulo')->get();

        return response()->json($peliculas);
    }

    public function horariosDisponibles(Request $request, Cine $cine)
    {
        $fecha = $request->get('fecha');
        $peliculaId = $request->get('pelicula_id');
        $salaId = $request->get('sala_id');

        if (!$fecha || !$peliculaId) {
            return response()->json([]);
        }

        $query = Funcion::whereHas('sala', function($q) use ($cine) {
            $q->where('cine_id', $cine->id);
        })
        ->where('fecha_funcion', $fecha)
        ->where('pelicula_id', $peliculaId);

        if ($salaId) {
            $query->where('sala_id', $salaId);
        }

        $funciones = $query->with('sala')
            ->orderBy('hora_funcion')
            ->get();

        return response()->json($funciones);
    }

    // Para obtener estadísticas del cine (uso admin)
    public function estadisticas(Cine $cine)
    {
        $totalReservas = $cine->funciones()
            ->whereHas('reservas')
            ->count();

        $ingresosTotales = $cine->funciones()
            ->whereHas('reservas', function($query) {
                $query->where('estado', 'confirmada');
            })
            ->with('reservas')
            ->get()
            ->sum(function($funcion) {
                return $funcion->reservas->where('estado', 'confirmada')->sum('monto_total');
            });

        $peliculasMasVistas = Pelicula::whereHas('funciones.sala', function($query) use ($cine) {
            $query->where('cine_id', $cine->id);
        })
        ->withCount(['reservas' => function($query) use ($cine) {
            $query->whereHas('funcion.sala', function($q) use ($cine) {
                $q->where('cine_id', $cine->id);
            });
        }])
        ->orderBy('reservas_count', 'desc')
        ->limit(5)
        ->get();

        return response()->json([
            'total_reservas' => $totalReservas,
            'ingresos_totales' => $ingresosTotales,
            'peliculas_mas_vistas' => $peliculasMasVistas
        ]);
    }

    // Buscar cines por ubicación (para app móvil o mapa)
    public function buscarPorUbicacion(Request $request)
    {
        $lat = $request->get('lat');
        $lng = $request->get('lng');
        $radio = $request->get('radio', 10); // km

        // Aquí podrías implementar búsqueda por coordenadas
        // Por ahora devolvemos todos los cines con sus coordenadas
        $cines = Cine::with('ciudad')->get();

        return response()->json($cines);
    }

    // Método para obtener información completa de un cine
    public function informacion(Cine $cine)
    {
        $cine->load([
            'ciudad',
            'salas',
            'funciones' => function($query) {
                $query->where('fecha_funcion', '>=', Carbon::today())
                      ->with('pelicula')
                      ->orderBy('fecha_funcion')
                      ->orderBy('hora_funcion');
            }
        ]);

        $proximasFunciones = $cine->funciones->take(10);
        $peliculasEnCartelera = $cine->funciones
            ->pluck('pelicula')
            ->unique('id')
            ->values();

        return response()->json([
            'cine' => $cine,
            'proximas_funciones' => $proximasFunciones,
            'peliculas_cartelera' => $peliculasEnCartelera
        ]);
    }
}

?>