<?php
// app/Console/Commands/TestApiDirectly.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pelicula;
use App\Models\Funcion;
use Carbon\Carbon;

class TestApiDirectly extends Command
{
    protected $signature = 'cinema:test-api {pelicula_id} {fecha} {ciudad_id}';
    protected $description = 'Probar la lógica de la API directamente';

    public function handle()
    {
        $peliculaId = $this->argument('pelicula_id');
        $fecha = $this->argument('fecha');
        $ciudadId = $this->argument('ciudad_id');

        $this->info("🧪 PROBANDO API DIRECTAMENTE");
        $this->info("Película ID: {$peliculaId}");
        $this->info("Fecha: {$fecha}");
        $this->info("Ciudad ID: {$ciudadId}");
        $this->newLine();

        try {
            // 1. Buscar película
            $pelicula = Pelicula::find($peliculaId);
            if (!$pelicula) {
                $this->error("❌ Película no encontrada");
                return 1;
            }

            $this->info("✅ Película encontrada: {$pelicula->titulo}");
            $this->info("📅 Fecha estreno: {$pelicula->fecha_estreno->format('Y-m-d')}");

            // 2. Validar fecha
            $fechaConsulta = Carbon::parse($fecha);
            if ($fechaConsulta->lt($pelicula->fecha_estreno)) {
                $this->warn("⚠️ Fecha anterior al estreno - retornando vacío");
                return 0;
            }

            // 3. Consultar funciones
            $funciones = Funcion::select([
                    'funciones.id',
                    'funciones.hora_funcion', 
                    'funciones.formato',
                    'funciones.tipo',
                    'funciones.precio',
                    'salas.id as sala_id',
                    'salas.nombre as sala_nombre',
                    'cines.id as cine_id',
                    'cines.nombre as cine_nombre',
                    'cines.direccion as cine_direccion',
                    'ciudades.id as ciudad_id',
                    'ciudades.nombre as ciudad_nombre'
                ])
                ->join('salas', 'funciones.sala_id', '=', 'salas.id')
                ->join('cines', 'salas.cine_id', '=', 'cines.id')
                ->join('ciudades', 'cines.ciudad_id', '=', 'ciudades.id')
                ->where('funciones.pelicula_id', $pelicula->id)
                ->where('funciones.fecha_funcion', $fecha)
                ->where('ciudades.id', $ciudadId)
                ->orderBy('funciones.hora_funcion')
                ->get();

            $this->info("🎬 Funciones encontradas: {$funciones->count()}");

            if ($funciones->count() > 0) {
                $this->newLine();
                $this->info("📋 DETALLES DE FUNCIONES:");
                
                foreach ($funciones as $funcion) {
                    $this->line("- {$funcion->hora_funcion} | {$funcion->formato} {$funcion->tipo} | {$funcion->cine_nombre} - Sala {$funcion->sala_nombre}");
                }
            } else {
                $this->warn("❌ No se encontraron funciones");
                
                // Diagnóstico adicional
                $this->newLine();
                $this->info("🔍 DIAGNÓSTICO:");
                
                $funcionesPelicula = Funcion::where('pelicula_id', $pelicula->id)->count();
                $this->line("- Funciones de esta película: {$funcionesPelicula}");
                
                $funcionesFecha = Funcion::where('fecha_funcion', $fecha)->count();
                $this->line("- Funciones en esta fecha: {$funcionesFecha}");
                
                $funcionesCiudad = Funcion::join('salas', 'funciones.sala_id', '=', 'salas.id')
                    ->join('cines', 'salas.cine_id', '=', 'cines.id')
                    ->where('cines.ciudad_id', $ciudadId)
                    ->count();
                $this->line("- Funciones en esta ciudad: {$funcionesCiudad}");
            }

            $this->newLine();
            $this->info("✅ Prueba completada");

        } catch (\Exception $e) {
            $this->error("❌ ERROR: {$e->getMessage()}");
            $this->error("Línea: {$e->getLine()}");
            return 1;
        }

        return 0;
    }
}

// Para crear y usar:
// php artisan make:command TestApiDirectly
// Copiar el código y ejecutar:
// php artisan cinema:test-api 1 2025-07-09 1
// dasdw as