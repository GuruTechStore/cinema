<?php
// app/Console/Commands/VerifySystemData.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pelicula;
use App\Models\Funcion;
use App\Models\Ciudad;
use App\Models\Cine;
use App\Models\Sala;
use Carbon\Carbon;

class VerifySystemData extends Command
{
    protected $signature = 'cinema:verify-data';
    protected $description = 'Verificar integridad completa de datos del sistema';

    public function handle()
    {
        $this->info('🔍 VERIFICACIÓN COMPLETA DEL SISTEMA');
        $this->newLine();

        // 1. Verificar películas
        $this->verifyMovies();
        $this->newLine();

        // 2. Verificar ciudades y cines
        $this->verifyCinemas();
        $this->newLine();

        // 3. Verificar funciones
        $this->verifyFunctions();
        $this->newLine();

        // 4. Verificar relaciones
        $this->verifyRelations();
        $this->newLine();

        // 5. Crear datos de prueba si faltan
        $this->createTestData();

        $this->info('✅ Verificación completada');
        return 0;
    }

    private function verifyMovies()
    {
        $this->info('📽️ VERIFICANDO PELÍCULAS...');
        
        $peliculas = Pelicula::all();
        $this->line("Total películas: {$peliculas->count()}");
        
        foreach ($peliculas as $pelicula) {
            $estreno = $pelicula->fecha_estreno;
            $hoy = Carbon::today();
            $status = $estreno->gt($hoy) ? '🔜 Próximo' : '✅ Disponible';
            
            $this->line("- {$pelicula->titulo} | {$estreno->format('d/m/Y')} | {$status}");
        }

        if ($peliculas->count() == 0) {
            $this->error('❌ No hay películas en el sistema');
        }
    }

    private function verifyCinemas()
    {
        $this->info('🏢 VERIFICANDO CINES Y CIUDADES...');
        
        $ciudades = Ciudad::with('cines.salas')->get();
        $this->line("Total ciudades: {$ciudades->count()}");
        
        foreach ($ciudades as $ciudad) {
            $this->line("- {$ciudad->nombre}: {$ciudad->cines->count()} cines");
            
            foreach ($ciudad->cines as $cine) {
                $this->line("  * {$cine->nombre}: {$cine->salas->count()} salas");
            }
        }

        if ($ciudades->count() == 0) {
            $this->error('❌ No hay ciudades en el sistema');
        }
    }

    private function verifyFunctions()
    {
        $this->info('🎬 VERIFICANDO FUNCIONES...');
        
        $funcionesHoy = Funcion::whereDate('fecha_funcion', Carbon::today())->count();
        $funcionesTotales = Funcion::count();
        
        $this->line("Funciones hoy: {$funcionesHoy}");
        $this->line("Funciones totales: {$funcionesTotales}");

        // Verificar funciones por película
        $peliculas = Pelicula::withCount('funciones')->get();
        
        foreach ($peliculas as $pelicula) {
            $fechaMinima = $pelicula->fecha_estreno->max(Carbon::today());
            $funcionesValidas = Funcion::where('pelicula_id', $pelicula->id)
                ->where('fecha_funcion', '>=', $fechaMinima)
                ->count();
                
            $status = $funcionesValidas > 0 ? '✅' : '❌';
            $this->line("{$status} {$pelicula->titulo}: {$funcionesValidas} funciones válidas");
        }
    }

    private function verifyRelations()
    {
        $this->info('🔗 VERIFICANDO RELACIONES...');
        
        // Verificar funciones sin película
        $funcionesSinPelicula = Funcion::whereDoesntHave('pelicula')->count();
        $this->line("Funciones sin película: {$funcionesSinPelicula}");
        
        // Verificar funciones sin sala
        $funcionesSinSala = Funcion::whereDoesntHave('sala')->count();
        $this->line("Funciones sin sala: {$funcionesSinSala}");
        
        // Verificar salas sin cine
        $salasSinCine = Sala::whereDoesntHave('cine')->count();
        $this->line("Salas sin cine: {$salasSinCine}");
        
        // Verificar cines sin ciudad
        $cinesSinCiudad = Cine::whereDoesntHave('ciudad')->count();
        $this->line("Cines sin ciudad: {$cinesSinCiudad}");

        if ($funcionesSinPelicula > 0 || $funcionesSinSala > 0 || $salasSinCine > 0 || $cinesSinCiudad > 0) {
            $this->error('❌ Hay relaciones rotas en el sistema');
        } else {
            $this->info('✅ Todas las relaciones están correctas');
        }
    }

    private function createTestData()
    {
        $this->info('🛠️ CREANDO DATOS DE PRUEBA SI ES NECESARIO...');
        
        // Verificar si hay funciones para hoy
        $funcionesHoy = Funcion::whereDate('fecha_funcion', Carbon::today())->count();
        
        if ($funcionesHoy == 0) {
            $this->warn('No hay funciones para hoy, creando datos de prueba...');
            
            $peliculas = Pelicula::where('fecha_estreno', '<=', Carbon::today())->get();
            $salas = Sala::all();
            
            if ($peliculas->count() > 0 && $salas->count() > 0) {
                $horarios = ['14:00', '17:00', '20:00'];
                
                foreach ($peliculas->take(3) as $pelicula) {
                    foreach ($salas->take(5) as $sala) {
                        foreach ($horarios as $horario) {
                            try {
                                Funcion::create([
                                    'pelicula_id' => $pelicula->id,
                                    'sala_id' => $sala->id,
                                    'fecha_funcion' => Carbon::today()->format('Y-m-d'),
                                    'hora_funcion' => $horario,
                                    'formato' => '2D',
                                    'tipo' => 'REGULAR',
                                    'precio' => 15.00,
                                    'tarifa_servicio' => 3.00,
                                ]);
                            } catch (\Exception $e) {
                                // Continuar si hay conflicto
                                continue;
                            }
                        }
                    }
                }
                
                $this->info('✅ Datos de prueba creados');
            } else {
                $this->error('❌ No hay películas o salas para crear funciones');
            }
        }
    }
}

// Para crear y ejecutar:
// php artisan make:command VerifySystemData
// Luego copiar este código y ejecutar:
// php artisan cinema:verify-data