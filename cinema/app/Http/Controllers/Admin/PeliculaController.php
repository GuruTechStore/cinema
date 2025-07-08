<?php
// app/Http/Controllers/Admin/PeliculaController.php 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelicula;
use App\Models\Cine;
use App\Models\Sala;
use App\Models\Funcion;
use App\Models\Ciudad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PeliculaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->esAdmin()) {
                abort(403, 'Acceso denegado');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = Pelicula::query();

        // Filtros
        if ($request->filled('buscar')) {
            $query->where('titulo', 'like', '%' . $request->buscar . '%');
        }

        if ($request->filled('estado')) {
            if ($request->estado === 'activa') {
                $query->where('activa', true);
            } elseif ($request->estado === 'inactiva') {
                $query->where('activa', false);
            }
        }

        if ($request->filled('genero')) {
            $query->where('genero', 'like', '%' . $request->genero . '%');
        }

        if ($request->filled('fecha_desde')) {
            $query->where('fecha_estreno', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_estreno', '<=', $request->fecha_hasta);
        }

        // Ordenamiento
        $orden = $request->get('orden', 'created_at');
        $direccion = $request->get('direccion', 'desc');
        
        $query->orderBy($orden, $direccion);

        $peliculas = $query->paginate(12);

        // Obtener datos para filtros
        $generos = Pelicula::whereNotNull('genero')
            ->get()
            ->pluck('genero')
            ->flatMap(function($genero) {
                return explode(',', $genero);
            })
            ->map(function($genero) {
                return trim($genero);
            })
            ->unique()
            ->sort()
            ->values();

        return view('admin.peliculas.index', compact('peliculas', 'generos'));
    }

    public function create()
    {
        return view('admin.peliculas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255|unique:peliculas,titulo',
            'descripcion' => 'nullable|string|max:500',
            'sinopsis' => 'nullable|string|max:2000',
            'reparto' => 'nullable|string|max:500',
            'genero' => 'required|string|max:255',
            'duracion' => 'required|integer|min:1|max:300',
            'director' => 'required|string|max:255',
            'clasificacion' => 'required|string|max:10',
            'idioma' => 'nullable|string|max:50',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'trailer_url' => 'nullable|url|max:500',
            'fecha_estreno' => 'required|date|after_or_equal:today',
            'activa' => 'nullable|boolean',
            'destacada' => 'nullable|boolean',
        ], [
            'titulo.unique' => 'Ya existe una película con este título.',
            'duracion.max' => 'La duración no puede ser mayor a 300 minutos.',
            'poster.max' => 'El poster no puede ser mayor a 2MB.',
            'fecha_estreno.after_or_equal' => 'La fecha de estreno no puede ser en el pasado.',
        ]);

        $datos = $request->except(['programar_inmediatamente']);

        // Procesar poster
        if ($request->hasFile('poster')) {
            $datos['poster'] = $request->file('poster')->store('posters', 'public');
        }

        // Establecer valores por defecto
        $datos['activa'] = $request->has('activa');
        $datos['destacada'] = $request->has('destacada');

        $pelicula = Pelicula::create($datos);

        // Si se marcó programar inmediatamente, redirigir a programación
        if ($request->has('programar_inmediatamente')) {
            return redirect()
                ->route('admin.peliculas.programar-funciones', $pelicula)
                ->with('success', 'Película creada exitosamente. Ahora programa las funciones.')
                ->with('info', 'Recuerda asignar cines y horarios para que los usuarios puedan ver esta película.');
        }

        return redirect()
            ->route('admin.peliculas.index')
            ->with('success', 'Película "' . $pelicula->titulo . '" creada exitosamente.')
            ->with('info', 'No olvides programar funciones para que aparezca en la cartelera.');
    }

    public function show(Pelicula $pelicula)
    {
        $pelicula->load(['funciones.sala.cine.ciudad', 'funciones' => function($query) {
            $query->orderBy('fecha_funcion')->orderBy('hora_funcion');
        }]);

        // Estadísticas
        $estadisticas = [
            'total_funciones' => $pelicula->funciones()->count(),
            'funciones_futuras' => $pelicula->funciones()->where('fecha_funcion', '>=', Carbon::today())->count(),
            'cines_asignados' => $pelicula->funciones()->distinct('sala_id')->count(),
            'ciudades_disponibles' => $pelicula->funciones()
                ->join('salas', 'funciones.sala_id', '=', 'salas.id')
                ->join('cines', 'salas.cine_id', '=', 'cines.id')
                ->distinct('cines.ciudad_id')
                ->count(),
        ];

        return view('admin.peliculas.show', compact('pelicula', 'estadisticas'));
    }

    public function edit(Pelicula $pelicula)
    {
        return view('admin.peliculas.edit', compact('pelicula'));
    }

    public function update(Request $request, Pelicula $pelicula)
    {
        $request->validate([
            'titulo' => 'required|string|max:255|unique:peliculas,titulo,' . $pelicula->id,
            'descripcion' => 'nullable|string|max:500',
            'sinopsis' => 'nullable|string|max:2000',
            'reparto' => 'nullable|string|max:500',
            'genero' => 'required|string|max:255',
            'duracion' => 'required|integer|min:1|max:300',
            'director' => 'required|string|max:255',
            'clasificacion' => 'required|string|max:10',
            'idioma' => 'nullable|string|max:50',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'trailer_url' => 'nullable|url|max:500',
            'fecha_estreno' => 'required|date',
            'activa' => 'nullable|boolean',
            'destacada' => 'nullable|boolean',
        ]);

        $datos = $request->all();

        // Procesar poster
        if ($request->hasFile('poster')) {
            // Eliminar poster anterior si existe
            if ($pelicula->poster && Storage::disk('public')->exists($pelicula->poster)) {
                Storage::disk('public')->delete($pelicula->poster);
            }
            $datos['poster'] = $request->file('poster')->store('posters', 'public');
        }

        // Establecer valores booleanos
        $datos['activa'] = $request->has('activa');
        $datos['destacada'] = $request->has('destacada');

        $pelicula->update($datos);

        return redirect()
            ->route('admin.peliculas.show', $pelicula)
            ->with('success', 'Película actualizada exitosamente');
    }

    public function destroy(Pelicula $pelicula)
    {
        // Verificar si tiene funciones futuras
        $funcionesFuturas = $pelicula->funciones()->where('fecha_funcion', '>=', Carbon::today())->count();
        
        if ($funcionesFuturas > 0) {
            return redirect()
                ->back()
                ->with('error', 'No se puede eliminar la película porque tiene ' . $funcionesFuturas . ' funciones programadas en el futuro.')
                ->with('info', 'Cancela primero todas las funciones futuras o marca la película como inactiva.');
        }

        $titulo = $pelicula->titulo;

        // Eliminar poster si existe
        if ($pelicula->poster && Storage::disk('public')->exists($pelicula->poster)) {
            Storage::disk('public')->delete($pelicula->poster);
        }

        $pelicula->delete();

        return redirect()
            ->route('admin.peliculas.index')
            ->with('success', 'Película "' . $titulo . '" eliminada exitosamente');
    }

    public function toggleStatus(Pelicula $pelicula)
    {
        $pelicula->update(['activa' => !$pelicula->activa]);

        $estado = $pelicula->activa ? 'activada' : 'desactivada';
        
        return redirect()
            ->back()
            ->with('success', 'Película "' . $pelicula->titulo . '" ' . $estado . ' exitosamente');
    }

    public function programarFunciones(Request $request, Pelicula $pelicula)
    {
        $cines = Cine::with(['salas', 'ciudad'])->orderBy('nombre')->get();
        $ciudades = Ciudad::orderBy('nombre')->get();
        
        if ($request->isMethod('post')) {
            return $this->guardarFunciones($request, $pelicula);
        }

        return view('admin.peliculas.programar-funciones', compact('pelicula', 'cines', 'ciudades'));
    }

    public function guardarFunciones(Request $request, Pelicula $pelicula)
    {
        $request->validate([
            'cine_id' => 'required|exists:cines,id',
            'sala_id' => 'required|exists:salas,id',
            'fecha_inicio' => 'required|date|after_or_equal:' . $pelicula->fecha_estreno->format('Y-m-d'),
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'horarios' => 'required|array|min:1',
            'horarios.*' => 'required|date_format:H:i',
            'formato' => 'required|in:2D,3D',
            'tipo' => 'required|in:REGULAR,GOLD CLASS,VELVET',
            'precio' => 'required|numeric|min:0|max:200',
        ], [
            'fecha_inicio.after_or_equal' => 'Las funciones no pueden programarse antes de la fecha de estreno (' . $pelicula->fecha_estreno->format('d/m/Y') . ')',
        ]);

        $fechaInicio = Carbon::parse($request->fecha_inicio);
        $fechaFin = Carbon::parse($request->fecha_fin);
        $funcionesCreadas = 0;
        $conflictos = 0;

        // Crear funciones para cada día y horario
        for ($fecha = $fechaInicio->copy(); $fecha->lte($fechaFin); $fecha->addDay()) {
            foreach ($request->horarios as $horario) {
                // Verificar conflictos
                $existeConflicto = Funcion::where('sala_id', $request->sala_id)
                    ->where('fecha_funcion', $fecha->format('Y-m-d'))
                    ->where('hora_funcion', $horario)
                    ->exists();

                if (!$existeConflicto) {
                    Funcion::create([
                        'pelicula_id' => $pelicula->id,
                        'sala_id' => $request->sala_id,
                        'fecha_funcion' => $fecha->format('Y-m-d'),
                        'hora_funcion' => $horario,
                        'formato' => $request->formato,
                        'tipo' => $request->tipo,
                        'precio' => $request->precio,
                        'tarifa_servicio' => 3.00,
                    ]);
                    $funcionesCreadas++;
                } else {
                    $conflictos++;
                }
            }
        }

        $mensaje = "Se crearon {$funcionesCreadas} funciones exitosamente";
        if ($conflictos > 0) {
            $mensaje .= " ({$conflictos} conflictos omitidos)";
        }

        return redirect()
            ->back()
            ->with('success', $mensaje)
            ->with('info', 'Las funciones están disponibles para reserva desde ' . $fechaInicio->format('d/m/Y'));
    }

    /**
     * Obtener salas de un cine para AJAX
     */
    public function getSalas(Cine $cine)
    {
        $salas = $cine->salas()->orderBy('nombre')->get(['id', 'nombre', 'capacidad', 'tipo']);
        return response()->json($salas);
    }

    /**
     * Duplicar película (crear una copia)
     */
    public function duplicate(Pelicula $pelicula)
    {
        $nuevaPelicula = $pelicula->replicate();
        $nuevaPelicula->titulo = $pelicula->titulo . ' (Copia)';
        $nuevaPelicula->activa = false;
        $nuevaPelicula->destacada = false;
        $nuevaPelicula->fecha_estreno = Carbon::today()->addDays(7);
        $nuevaPelicula->save();

        return redirect()
            ->route('admin.peliculas.edit', $nuevaPelicula)
            ->with('success', 'Película duplicada exitosamente. Edita los detalles necesarios.')
            ->with('info', 'Recuerda cambiar el título y programar nuevas funciones.');
    }

    /**
     * Reporte de una película específica
     */
    public function reporte(Pelicula $pelicula)
    {
        $estadisticas = [
            'funciones_totales' => $pelicula->funciones()->count(),
            'funciones_pasadas' => $pelicula->funciones()->where('fecha_funcion', '<', Carbon::today())->count(),
            'funciones_futuras' => $pelicula->funciones()->where('fecha_funcion', '>=', Carbon::today())->count(),
            'reservas_totales' => $pelicula->reservas()->count(),
            'ingresos_totales' => $pelicula->reservas()->where('estado', 'confirmada')->sum('monto_total'),
            'cines_asignados' => $pelicula->funciones()
                ->join('salas', 'funciones.sala_id', '=', 'salas.id')
                ->distinct('salas.cine_id')
                ->count(),
        ];

        $funcionesPorCine = $pelicula->funciones()
            ->join('salas', 'funciones.sala_id', '=', 'salas.id')
            ->join('cines', 'salas.cine_id', '=', 'cines.id')
            ->selectRaw('cines.nombre as cine_nombre, COUNT(*) as total_funciones')
            ->groupBy('cines.id', 'cines.nombre')
            ->get();

        return view('admin.peliculas.reporte', compact('pelicula', 'estadisticas', 'funcionesPorCine'));
    }

    /**
     * Programación masiva por ciudad
     */
    public function programacionMasiva(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'pelicula_id' => 'required|exists:peliculas,id',
                'ciudad_id' => 'required|exists:ciudades,id',
                'fecha_inicio' => 'required|date|after_or_equal:today',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'horarios' => 'required|array|min:1',
                'horarios.*' => 'required|date_format:H:i',
                'formato' => 'required|in:2D,3D',
                'tipo' => 'required|in:REGULAR,GOLD CLASS,VELVET',
                'precio' => 'required|numeric|min:0',
            ]);

            $pelicula = Pelicula::findOrFail($request->pelicula_id);
            $ciudad = Ciudad::findOrFail($request->ciudad_id);
            
            $cinesCiudad = $ciudad->cines()->with('salas')->get();
            $funcionesCreadas = 0;

            foreach ($cinesCiudad as $cine) {
                foreach ($cine->salas as $sala) {
                    $fechaInicio = Carbon::parse($request->fecha_inicio);
                    $fechaFin = Carbon::parse($request->fecha_fin);

                    for ($fecha = $fechaInicio->copy(); $fecha->lte($fechaFin); $fecha->addDay()) {
                        foreach ($request->horarios as $horario) {
                            $existeConflicto = Funcion::where('sala_id', $sala->id)
                                ->where('fecha_funcion', $fecha->format('Y-m-d'))
                                ->where('hora_funcion', $horario)
                                ->exists();

                            if (!$existeConflicto) {
                                Funcion::create([
                                    'pelicula_id' => $pelicula->id,
                                    'sala_id' => $sala->id,
                                    'fecha_funcion' => $fecha->format('Y-m-d'),
                                    'hora_funcion' => $horario,
                                    'formato' => $request->formato,
                                    'tipo' => $request->tipo,
                                    'precio' => $request->precio,
                                    'tarifa_servicio' => 3.00,
                                ]);
                                $funcionesCreadas++;
                            }
                        }
                    }
                }
            }

            return redirect()
                ->back()
                ->with('success', "Programación masiva completada: {$funcionesCreadas} funciones creadas en {$ciudad->nombre}")
                ->with('info', 'La película está disponible en todos los cines de la ciudad seleccionada.');
        }

        $peliculas = Pelicula::where('activa', true)->orderBy('titulo')->get();
        $ciudades = Ciudad::orderBy('nombre')->get();

        return view('admin.peliculas.programacion-masiva', compact('peliculas', 'ciudades'));
    }
}