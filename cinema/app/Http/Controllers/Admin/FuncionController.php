<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Funcion;
use App\Models\Pelicula;
use App\Models\Sala;
use App\Models\Cine;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FuncionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pelicula_id' => 'required|exists:peliculas,id',
            'sala_id' => 'required|exists:salas,id',
            'fecha_funcion' => 'required|date|after_or_equal:today',
            'hora_funcion' => 'required',
            'precio' => 'required|numeric|min:0',
            'formato' => 'sometimes|in:2D,3D',
            'tipo' => 'sometimes|in:REGULAR,GOLD CLASS,VELVET'
        ]);

        // Verificar que no haya conflicto de horarios
        $fechaHora = Carbon::parse($request->fecha_funcion . ' ' . $request->hora_funcion);
        $pelicula = Pelicula::findOrFail($request->pelicula_id);
        $sala = Sala::findOrFail($request->sala_id);
        
        // Calcular hora de fin (duración de película + 30 min de limpieza)
        $horaFin = $fechaHora->copy()->addMinutes($pelicula->duracion + 30);
        
        // Verificar conflictos
        $conflicto = Funcion::where('sala_id', $request->sala_id)
            ->where('fecha_funcion', $request->fecha_funcion)
            ->where(function($query) use ($fechaHora, $horaFin) {
                $query->whereBetween('hora_funcion', [$fechaHora->format('H:i'), $horaFin->format('H:i')])
                      ->orWhere(function($q) use ($fechaHora, $horaFin) {
                          // Verificar si alguna función existente se solapa
                          $q->where('hora_funcion', '<=', $fechaHora->format('H:i'));
                          // Aquí deberíamos calcular la hora de fin de la función existente
                      });
            })
            ->exists();

        if ($conflicto) {
            return redirect()->back()
                ->withErrors(['hora_funcion' => 'Ya existe una función en este horario que genera conflicto.'])
                ->withInput();
        }

        Funcion::create([
            'pelicula_id' => $request->pelicula_id,
            'sala_id' => $request->sala_id,
            'fecha_funcion' => $request->fecha_funcion,
            'hora_funcion' => $request->hora_funcion,
            'formato' => $request->formato ?? '2D',
            'tipo' => $request->tipo ?? 'REGULAR',
            'precio' => $request->precio,
            'tarifa_servicio' => $request->tarifa_servicio ?? 3.00
        ]);

        return redirect()->back()
            ->with('success', 'Función programada exitosamente');
    }

    public function update(Request $request, Funcion $funcion)
    {
        $request->validate([
            'fecha_funcion' => 'required|date',
            'hora_funcion' => 'required',
            'precio' => 'required|numeric|min:0'
        ]);

        // Solo permitir editar si no es en el pasado
        if (Carbon::parse($funcion->fecha_funcion)->isPast()) {
            return redirect()->back()
                ->withErrors(['fecha_funcion' => 'No se puede editar una función pasada']);
        }

        $funcion->update([
            'fecha_funcion' => $request->fecha_funcion,
            'hora_funcion' => $request->hora_funcion,
            'precio' => $request->precio
        ]);

        return redirect()->back()
            ->with('success', 'Función actualizada exitosamente');
    }

    public function destroy(Funcion $funcion)
    {
        // Verificar si tiene reservas
        if ($funcion->reservas()->count() > 0) {
            // Aquí podrías enviar notificaciones a los usuarios
            // Por ahora solo mostramos un mensaje
            $mensaje = "Función eliminada. Se han cancelado {$funcion->reservas()->count()} reservas.";
        } else {
            $mensaje = "Función eliminada exitosamente";
        }

        $funcion->delete();

        return redirect()->back()->with('success', $mensaje);
    }

    public function storeMultiple(Request $request)
    {
        $request->validate([
            'pelicula_id' => 'required|exists:peliculas,id',
            'cine_id_masivo' => 'required|exists:cines,id',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'horarios' => 'required|array|min:1',
            'horarios.*' => 'string',
            'dias' => 'required|array|min:1',
            'dias.*' => 'integer|between:0,6'
        ]);

        $cine = Cine::findOrFail($request->cine_id_masivo);
        $salas = $cine->salas; // Asumiendo que usaremos todas las salas disponibles

        if ($salas->isEmpty()) {
            return redirect()->back()
                ->withErrors(['cine_id_masivo' => 'El cine seleccionado no tiene salas disponibles']);
        }

        $fechaInicio = Carbon::parse($request->fecha_inicio);
        $fechaFin = Carbon::parse($request->fecha_fin);
        $funcionesCreadas = 0;

        // Iterar por cada día en el rango
        for ($fecha = $fechaInicio->copy(); $fecha->lte($fechaFin); $fecha->addDay()) {
            // Verificar si el día de la semana está seleccionado
            if (in_array($fecha->dayOfWeek, $request->dias)) {
                
                foreach ($request->horarios as $horario) {
                    // Usar la primera sala disponible (puedes modificar esta lógica)
                    $sala = $salas->first();
                    
                    // Verificar que no exista conflicto
                    $existeFuncion = Funcion::where('sala_id', $sala->id)
                        ->where('fecha_funcion', $fecha->format('Y-m-d'))
                        ->where('hora_funcion', $horario)
                        ->exists();

                    if (!$existeFuncion) {
                        Funcion::create([
                            'pelicula_id' => $request->pelicula_id,
                            'sala_id' => $sala->id,
                            'fecha_funcion' => $fecha->format('Y-m-d'),
                            'hora_funcion' => $horario,
                            'formato' => '2D',
                            'tipo' => 'REGULAR',
                            'precio' => 15.00, // Precio por defecto
                            'tarifa_servicio' => 3.00
                        ]);
                        $funcionesCreadas++;
                    }
                }
            }
        }

        return redirect()->back()
            ->with('success', "Se crearon {$funcionesCreadas} funciones exitosamente");
    }
}