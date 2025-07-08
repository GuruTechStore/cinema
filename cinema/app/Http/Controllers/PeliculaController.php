<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelicula;
use App\Models\Ciudad;
use App\Models\Cine;
use App\Models\Funcion;
use Carbon\Carbon;

class PeliculaController extends Controller
{
    public function show(Pelicula $pelicula)
    {
        $pelicula->load('funciones.sala.cine.ciudad');
        
        // Obtener ciudades que tienen funciones de esta película
        $ciudades = Ciudad::whereHas('cines.salas.funciones', function($query) use ($pelicula) {
            $query->where('pelicula_id', $pelicula->id)
                  ->where('fecha_funcion', '>=', Carbon::today());
        })->get();

        return view('peliculas.show', compact('pelicula', 'ciudades'));
    }

    public function calendario(Pelicula $pelicula)
    {
        $pelicula->load('funciones.sala.cine.ciudad');
        
        // Obtener fechas disponibles para esta película
        $fechasDisponibles = $pelicula->funciones()
            ->where('fecha_funcion', '>=', Carbon::today())
            ->orderBy('fecha_funcion')
            ->pluck('fecha_funcion')
            ->unique()
            ->values();

        return view('peliculas.calendario', compact('pelicula', 'fechasDisponibles'));
    }

    /**
     * API para obtener funciones de una película
     */
    public function getFunciones(Request $request, Pelicula $pelicula)
    {
        try {
            $fecha = $request->get('fecha', Carbon::today()->format('Y-m-d'));
            $ciudadId = $request->get('ciudad_id');
            $cineId = $request->get('cine_id');
            
            \Log::info('=== API FUNCIONES INICIADA ===', [
                'pelicula_id' => $pelicula->id,
                'pelicula_titulo' => $pelicula->titulo,
                'fecha' => $fecha,
                'ciudad_id' => $ciudadId,
                'cine_id' => $cineId,
                'request_all' => $request->all()
            ]);
            
            // Verificar que la película existe y está activa
            if (!$pelicula->activa) {
                return response()->json([
                    'error' => 'Película no disponible',
                    'message' => 'La película no está activa'
                ], 404);
            }
            
            // Construir query con relaciones
            $query = $pelicula->funciones()
                ->with([
                    'sala.cine.ciudad',
                    'sala.cine'
                ])
                ->where('fecha_funcion', $fecha);
            
            // Filtrar por ciudad si se especifica
            if ($ciudadId) {
                $query->whereHas('sala.cine', function($q) use ($ciudadId) {
                    $q->where('ciudad_id', $ciudadId);
                });
            }
            
            // Filtrar por cine si se especifica
            if ($cineId) {
                $query->whereHas('sala', function($q) use ($cineId) {
                    $q->where('cine_id', $cineId);
                });
            }
            
            // Obtener funciones ordenadas por hora
            $funciones = $query->orderBy('hora_funcion')->get();
            
            \Log::info('Funciones encontradas', [
                'count' => $funciones->count(),
                'primera_funcion' => $funciones->first() ? [
                    'id' => $funciones->first()->id,
                    'hora' => $funciones->first()->hora_funcion,
                    'sala' => $funciones->first()->sala->nombre ?? 'sin sala',
                    'cine' => $funciones->first()->sala->cine->nombre ?? 'sin cine'
                ] : null
            ]);
            
            // Verificar que las relaciones están cargadas correctamente
            foreach ($funciones as $funcion) {
                if (!$funcion->sala || !$funcion->sala->cine) {
                    \Log::warning('Función con relación incompleta', [
                        'funcion_id' => $funcion->id,
                        'tiene_sala' => !is_null($funcion->sala),
                        'tiene_cine' => !is_null($funcion->sala->cine ?? null)
                    ]);
                }
            }
            
            return response()->json($funciones);
            
        } catch (\Exception $e) {
            \Log::error('ERROR CRÍTICO EN API FUNCIONES', [
                'pelicula_id' => $pelicula->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Error interno del servidor',
                'message' => app()->environment('local') ? $e->getMessage() : 'Error al cargar funciones',
                'debug_info' => app()->environment('local') ? [
                    'line' => $e->getLine(),
                    'file' => basename($e->getFile())
                ] : null
            ], 500);
        }
    }

    /**
     * Método alternativo usando Query Builder en caso de problemas con Eloquent
     */
    public function getFuncionesRaw(Request $request, $peliculaId)
    {
        try {
            $fecha = $request->get('fecha', Carbon::today()->format('Y-m-d'));
            $ciudadId = $request->get('ciudad_id');
            
            \Log::info('=== API FUNCIONES RAW INICIADA ===', [
                'pelicula_id' => $peliculaId,
                'fecha' => $fecha,
                'ciudad_id' => $ciudadId
            ]);
            
            $query = \DB::table('funciones')
                ->join('salas', 'funciones.sala_id', '=', 'salas.id')
                ->join('cines', 'salas.cine_id', '=', 'cines.id')
                ->join('ciudades', 'cines.ciudad_id', '=', 'ciudades.id')
                ->select(
                    'funciones.id',
                    'funciones.hora_funcion',
                    'funciones.formato',
                    'funciones.tipo',
                    'funciones.precio',
                    'funciones.tarifa_servicio',
                    'salas.id as sala_id',
                    'salas.nombre as sala_nombre',
                    'cines.id as cine_id',
                    'cines.nombre as cine_nombre',
                    'cines.direccion as cine_direccion',
                    'ciudades.id as ciudad_id',
                    'ciudades.nombre as ciudad_nombre'
                )
                ->where('funciones.pelicula_id', $peliculaId)
                ->where('funciones.fecha_funcion', $fecha);
            
            if ($ciudadId) {
                $query->where('ciudades.id', $ciudadId);
            }
            
            $funciones = $query->orderBy('funciones.hora_funcion')->get();
            
            // Formatear para el frontend
            $funcionesFormatted = $funciones->map(function($funcion) {
                return [
                    'id' => $funcion->id,
                    'hora_funcion' => $funcion->hora_funcion,
                    'formato' => $funcion->formato,
                    'tipo' => $funcion->tipo,
                    'precio' => $funcion->precio,
                    'tarifa_servicio' => $funcion->tarifa_servicio,
                    'sala' => [
                        'id' => $funcion->sala_id,
                        'nombre' => $funcion->sala_nombre,
                        'cine' => [
                            'id' => $funcion->cine_id,
                            'nombre' => $funcion->cine_nombre,
                            'direccion' => $funcion->cine_direccion,
                            'ciudad' => [
                                'id' => $funcion->ciudad_id,
                                'nombre' => $funcion->ciudad_nombre
                            ]
                        ]
                    ]
                ];
            });
            
            \Log::info('Funciones raw encontradas', [
                'count' => $funcionesFormatted->count()
            ]);
            
            return response()->json($funcionesFormatted);
            
        } catch (\Exception $e) {
            \Log::error('Error en API funciones raw', [
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'error' => 'Error interno del servidor',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
?>