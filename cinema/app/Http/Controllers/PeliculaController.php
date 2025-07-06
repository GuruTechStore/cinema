<?php
// app/Http/Controllers/PeliculaController.php - CORREGIDO

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

        if ($request->filled('genero')) {
            $query->where('genero', 'like', '%' . $request->genero . '%');
        }

        $peliculas = $query->orderBy('fecha_estreno', 'desc')->paginate(12);

        return view('peliculas.index', compact('peliculas'));
    }

    public function show(Pelicula $pelicula)
    {
        // Obtener ciudades que tienen funciones de esta película
        // SOLO desde la fecha de estreno en adelante
        $fechaMinima = max(Carbon::today(), $pelicula->fecha_estreno);
        
        $ciudades = Ciudad::whereHas('cines.salas.funciones', function($query) use ($pelicula, $fechaMinima) {
            $query->where('pelicula_id', $pelicula->id)
                  ->where('fecha_funcion', '>=', $fechaMinima);
        })->orderBy('nombre')->get();

        return view('peliculas.show', compact('pelicula', 'ciudades'));
    }

    public function calendario(Request $request, Pelicula $pelicula)
    {
        $ciudadId = $request->get('ciudad_id');
        $fecha = $request->get('fecha');
        
        // Validar que la fecha no sea anterior al estreno
        if ($fecha) {
            $fechaConsulta = Carbon::parse($fecha);
            $fechaEstreno = $pelicula->fecha_estreno; // Ya es Carbon
            
            if ($fechaConsulta->lt($fechaEstreno)) {
                return redirect()->back()
                    ->with('error', 'No hay funciones disponibles antes de la fecha de estreno: ' . $fechaEstreno->format('d/m/Y'));
            }
        } else {
            // Si no hay fecha, usar hoy o fecha de estreno
            $fecha = max(Carbon::today(), $pelicula->fecha_estreno)->format('Y-m-d');
        }

        $query = Cine::whereHas('salas.funciones', function($query) use ($pelicula, $fecha) {
            $query->where('pelicula_id', $pelicula->id)
                  ->where('fecha_funcion', $fecha);
        });

        if ($ciudadId) {
            $query->where('ciudad_id', $ciudadId);
        }

        $cines = $query->with(['salas.funciones' => function($query) use ($pelicula, $fecha) {
            $query->where('pelicula_id', $pelicula->id)
                  ->where('fecha_funcion', $fecha)
                  ->orderBy('hora_funcion');
        }])->get();

        $ciudades = Ciudad::orderBy('nombre')->get();
        
        // Calcular fechas disponibles respetando el estreno
        $fechaMinima = max(Carbon::today(), $pelicula->fecha_estreno);
        $fechasDisponibles = [];
        
        for ($i = 0; $i < 14; $i++) {
            $fechaItem = $fechaMinima->copy()->addDays($i);
            $fechasDisponibles[] = [
                'value' => $fechaItem->format('Y-m-d'),
                'label' => $fechaItem->format('l, d M Y'),
                'is_today' => $fechaItem->isToday(),
                'is_tomorrow' => $fechaItem->isTomorrow(),
                'is_estreno' => $fechaItem->isSameDay($pelicula->fecha_estreno)
            ];
        }

        return view('peliculas.calendario', compact(
            'pelicula', 
            'cines', 
            'ciudades', 
            'fecha',
            'fechasDisponibles'
        ));
    }

    /**
     * Verificar si una película puede mostrar funciones en una fecha
     */
    public function verificarDisponibilidadFecha(Pelicula $pelicula, $fecha)
    {
        $fechaConsulta = Carbon::parse($fecha);
        $fechaEstreno = $pelicula->fecha_estreno; // Ya es Carbon
        $hoy = Carbon::today();
        
        // No se pueden mostrar funciones antes del estreno
        if ($fechaConsulta->lt($fechaEstreno)) {
            return [
                'disponible' => false,
                'mensaje' => 'La película se estrena el ' . $fechaEstreno->format('d/m/Y')
            ];
        }
        
        // No se pueden mostrar funciones de fechas pasadas
        if ($fechaConsulta->lt($hoy)) {
            return [
                'disponible' => false,
                'mensaje' => 'No se pueden mostrar funciones de fechas pasadas'
            ];
        }
        
        return [
            'disponible' => true,
            'mensaje' => 'Fecha disponible'
        ];
    }

    /**
     * Obtener las fechas válidas para una película
     */
    public function fechasValidas(Pelicula $pelicula)
    {
        $fechaMinima = max(Carbon::today(), $pelicula->fecha_estreno);
        $fechaMaxima = $fechaMinima->copy()->addDays(14);
        
        $fechas = [];
        for ($fecha = $fechaMinima->copy(); $fecha->lte($fechaMaxima); $fecha->addDay()) {
            $fechas[] = [
                'value' => $fecha->format('Y-m-d'),
                'label' => $fecha->format('l, d M Y'),
                'is_today' => $fecha->isToday(),
                'is_tomorrow' => $fecha->isTomorrow(),
                'is_estreno' => $fecha->isSameDay($pelicula->fecha_estreno)
            ];
        }
        
        return $fechas;
    }
}