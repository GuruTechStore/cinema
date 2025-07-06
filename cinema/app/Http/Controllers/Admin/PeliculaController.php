<?php
// app/Http/Controllers/Admin/PeliculaController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelicula;
use App\Models\Cine;
use App\Models\Sala;
use App\Models\Funcion;
use Carbon\Carbon;

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

    public function index()
    {
        $peliculas = Pelicula::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.peliculas.index', compact('peliculas'));
    }

    public function create()
    {
        return view('admin.peliculas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'genero' => 'required|string|max:255',
            'duracion' => 'required|integer|min:1',
            'director' => 'required|string|max:255',
            'clasificacion' => 'required|string|max:10',
            'poster' => 'nullable|image|max:2048',
            'fecha_estreno' => 'required|date',
        ]);

        $datos = $request->all();

        if ($request->hasFile('poster')) {
            $datos['poster'] = $request->file('poster')->store('posters', 'public');
        }

        Pelicula::create($datos);

        return redirect()->route('admin.peliculas.index')
            ->with('success', 'Película creada exitosamente');
    }

    public function show(Pelicula $pelicula)
    {
        return view('admin.peliculas.show', compact('pelicula'));
    }

    public function edit(Pelicula $pelicula)
    {
        return view('admin.peliculas.edit', compact('pelicula'));
    }

    public function update(Request $request, Pelicula $pelicula)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'genero' => 'required|string|max:255',
            'duracion' => 'required|integer|min:1',
            'director' => 'required|string|max:255',
            'clasificacion' => 'required|string|max:10',
            'poster' => 'nullable|image|max:2048',
            'fecha_estreno' => 'required|date',
        ]);

        $datos = $request->all();

        if ($request->hasFile('poster')) {
            $datos['poster'] = $request->file('poster')->store('posters', 'public');
        }

        $pelicula->update($datos);

        return redirect()->route('admin.peliculas.index')
            ->with('success', 'Película actualizada exitosamente');
    }

    public function destroy(Pelicula $pelicula)
    {
        $pelicula->delete();
        return redirect()->route('admin.peliculas.index')
            ->with('success', 'Película eliminada exitosamente');
    }

    public function programarFunciones(Request $request, Pelicula $pelicula)
    {
        $cines = Cine::with('salas')->get();
        
        if ($request->isMethod('post')) {
            $request->validate([
                'cine_id' => 'required|exists:cines,id',
                'sala_id' => 'required|exists:salas,id',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'horarios' => 'required|array',
                'horarios.*' => 'required|date_format:H:i',
                'formato' => 'required|in:2D,3D',
                'tipo' => 'required|in:REGULAR,GOLD CLASS,VELVET',
                'precio' => 'required|numeric|min:0',
            ]);

            $fechaInicio = Carbon::parse($request->fecha_inicio);
            $fechaFin = Carbon::parse($request->fecha_fin);

            // Crear funciones para cada día y horario
            for ($fecha = $fechaInicio; $fecha->lte($fechaFin); $fecha->addDay()) {
                foreach ($request->horarios as $horario) {
                    try {
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
                    } catch (\Exception $e) {
                        // Continuar si hay conflicto de horario
                        continue;
                    }
                }
            }

            return redirect()->back()->with('success', 'Funciones programadas exitosamente');
        }

        return view('admin.peliculas.programar-funciones', compact('pelicula', 'cines'));
    }
}

?>
