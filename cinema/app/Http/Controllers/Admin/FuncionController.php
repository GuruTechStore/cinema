<?php
// app/Http/Controllers/Admin/FuncionController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Funcion;
use App\Models\Cine;
use Carbon\Carbon;

class FuncionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pelicula_id' => 'required|exists:peliculas,id',
            'sala_id' => 'required|exists:salas,id',
            'fecha_funcion' => 'required|date',
            'hora_funcion' => 'required',
            'precio' => 'required|numeric|min:0'
        ]);

        // Verificar que no existe conflicto
        $existeConflicto = Funcion::where('sala_id', $request->sala_id)
            ->where('fecha_funcion', $request->fecha_funcion)
            ->where('hora_funcion', $request->hora_funcion)
            ->exists();

        if ($existeConflicto) {
            return redirect()->back()
                ->withErrors(['hora_funcion' => 'Ya existe una función en esa sala a esa hora'])
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
            $mensaje = "Función eliminada. Se han cancelado {$funcion->reservas()->count()} reservas.";
        } else {
            $mensaje = "Función eliminada exitosamente";
        }

        $funcion->delete();

        return redirect()->back()->with('success', $mensaje);
    }

    public function storeMultiple(Request $request)
    {
        // Debug inicial
        \Log::info('=== PROGRAMACIÓN MASIVA INICIADA ===', [
            'request_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url()
        ]);

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

        \Log::info('Validación pasada', [
            'pelicula_id' => $request->pelicula_id,
            'cine_id_masivo' => $request->cine_id_masivo,
            'horarios' => $request->horarios,
            'dias' => $request->dias
        ]);

        $cine = Cine::findOrFail($request->cine_id_masivo);
        $salas = $cine->salas;

        \Log::info('Cine y salas obtenidas', [
            'cine_nombre' => $cine->nombre,
            'total_salas' => $salas->count(),
            'salas_ids' => $salas->pluck('id')->toArray()
        ]);

        if ($salas->isEmpty()) {
            return redirect()->back()
                ->withErrors(['cine_id_masivo' => 'El cine seleccionado no tiene salas disponibles']);
        }

        $fechaInicio = Carbon::parse($request->fecha_inicio);
        $fechaFin = Carbon::parse($request->fecha_fin);
        $funcionesCreadas = 0;
        $conflictosEncontrados = 0;

        \Log::info('Iniciando creación de funciones', [
            'fecha_inicio' => $fechaInicio->format('Y-m-d'),
            'fecha_fin' => $fechaFin->format('Y-m-d'),
            'total_dias' => $fechaInicio->diffInDays($fechaFin) + 1
        ]);

        // Iterar por cada día en el rango
        for ($fecha = $fechaInicio->copy(); $fecha->lte($fechaFin); $fecha->addDay()) {
            // Verificar si el día de la semana está seleccionado
            $diaSemana = $fecha->dayOfWeek; // 0=domingo, 1=lunes, etc.
            
            \Log::info('Procesando fecha', [
                'fecha' => $fecha->format('Y-m-d'),
                'dia_semana' => $diaSemana,
                'dias_seleccionados' => $request->dias,
                'incluir_dia' => in_array($diaSemana, $request->dias)
            ]);
            
            if (in_array($diaSemana, $request->dias)) {
                foreach ($request->horarios as $horario) {
                    // Usar la primera sala disponible (o todas si quieres)
                    foreach ($salas as $sala) {
                        // Verificar que no exista conflicto
                        $existeFuncion = Funcion::where('sala_id', $sala->id)
                            ->where('fecha_funcion', $fecha->format('Y-m-d'))
                            ->where('hora_funcion', $horario)
                            ->exists();

                        if (!$existeFuncion) {
                            try {
                                $funcionCreada = Funcion::create([
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
                                
                                \Log::info('Función creada', [
                                    'id' => $funcionCreada->id,
                                    'sala' => $sala->nombre,
                                    'fecha' => $fecha->format('Y-m-d'),
                                    'hora' => $horario
                                ]);
                                
                            } catch (\Exception $e) {
                                \Log::error('Error creando función', [
                                    'sala_id' => $sala->id,
                                    'fecha' => $fecha->format('Y-m-d'),
                                    'hora' => $horario,
                                    'error' => $e->getMessage()
                                ]);
                            }
                        } else {
                            $conflictosEncontrados++;
                            \Log::info('Conflicto encontrado', [
                                'sala' => $sala->nombre,
                                'fecha' => $fecha->format('Y-m-d'),
                                'hora' => $horario
                            ]);
                        }
                        
                        // Solo crear una función por horario (en la primera sala disponible)
                        // Si quieres crear en todas las salas, comenta este break
                        if (!$existeFuncion) {
                            break;
                        }
                    }
                }
            }
        }

        \Log::info('Programación masiva completada', [
            'funciones_creadas' => $funcionesCreadas,
            'conflictos' => $conflictosEncontrados
        ]);

        $mensaje = "Se crearon {$funcionesCreadas} funciones exitosamente";
        if ($conflictosEncontrados > 0) {
            $mensaje .= " ({$conflictosEncontrados} conflictos omitidos)";
        }

        return redirect()->back()
            ->with('success', $mensaje);
    }
}