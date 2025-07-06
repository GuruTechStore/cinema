<?php
// app/Console/Commands/FixCinemaData.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pelicula;
use App\Models\Funcion;
use Carbon\Carbon;

class FixCinemaData extends Command
{
    protected $signature = 'cinema:fix-data';
    protected $description = 'Arreglar datos del cinema: fechas y funciones';

    public function handle()
    {
        $this->info('🎬 Iniciando corrección de datos del cinema...');
        
        // 1. Verificar y mostrar películas
        $this->info('📽️ Verificando películas...');
        $peliculas = Pelicula::all();
        
        foreach ($peliculas as $pelicula) {
            $this->line("- {$pelicula->titulo}: Estreno {$pelicula->fecha_estreno->format('d/m/Y')}");
        }
        
        // 2. Eliminar funciones inválidas (antes del estreno)
        $this->info('🧹 Limpiando funciones inválidas...');
        $funcionesEliminadas = 0;
        
        foreach ($peliculas as $pelicula) {
            $eliminadas = Funcion::where('pelicula_id', $pelicula->id)
                ->where('fecha_funcion', '<', $pelicula->fecha_estreno)
                ->delete();
            
            if ($eliminadas > 0) {
                $funcionesEliminadas += $eliminadas;
                $this->warn("  Eliminadas {$eliminadas} funciones inválidas de '{$pelicula->titulo}'");
            }
        }
        
        if ($funcionesEliminadas > 0) {
            $this->info("✅ Total funciones inválidas eliminadas: {$funcionesEliminadas}");
        } else {
            $this->info("✅ No se encontraron funciones inválidas");
        }
        
        // 3. Verificar funciones existentes
        $this->info('🔍 Verificando funciones existentes...');
        
        foreach ($peliculas as $pelicula) {
            $fechaMinima = max(Carbon::today(), $pelicula->fecha_estreno);
            $funcionesValidas = Funcion::where('pelicula_id', $pelicula->id)
                ->where('fecha_funcion', '>=', $fechaMinima)
                ->count();
            
            $this->line("- {$pelicula->titulo}: {$funcionesValidas} funciones válidas desde {$fechaMinima->format('d/m/Y')}");
            
            // Si no tiene funciones, crear algunas
            if ($funcionesValidas == 0) {
                $this->warn("  ⚠️ Sin funciones válidas, creando funciones...");
                $this->crearFuncionesPelicula($pelicula);
            }
        }
        
        $this->info('✅ Corrección completada!');
        $this->info('🎉 El sistema de cinema está listo para usar.');
        
        return 0;
    }
    
    private function crearFuncionesPelicula(Pelicula $pelicula)
    {
        $horarios = ['11:00', '14:00', '17:00', '20:00'];
        $tipos = ['REGULAR', 'GOLD CLASS'];
        $precios = [15.00, 25.00];
        
        $fechaInicio = max(Carbon::today(), $pelicula->fecha_estreno);
        
        // Crear funciones para los próximos 7 días
        for ($dia = 0; $dia < 7; $dia++) {
            $fechaFuncion = $fechaInicio->copy()->addDays($dia);
            
            // Crear algunas funciones en salas aleatorias
            for ($i = 0; $i < 3; $i++) {
                $salaId = rand(1, 40); // Asumiendo que hay 40 salas
                $horario = $horarios[array_rand($horarios)];
                $tipoIndex = array_rand($tipos);
                
                try {
                    Funcion::create([
                        'pelicula_id' => $pelicula->id,
                        'sala_id' => $salaId,
                        'fecha_funcion' => $fechaFuncion->format('Y-m-d'),
                        'hora_funcion' => $horario,
                        'formato' => rand(0, 1) ? '2D' : '3D',
                        'tipo' => $tipos[$tipoIndex],
                        'precio' => $precios[$tipoIndex],
                        'tarifa_servicio' => 3.00,
                    ]);
                } catch (\Exception $e) {
                    // Continuar si hay conflicto
                    continue;
                }
            }
        }
        
        $this->line("    ✅ Funciones creadas para '{$pelicula->titulo}'");
    }
}

// Para ejecutar: php artisan cinema:fix-data