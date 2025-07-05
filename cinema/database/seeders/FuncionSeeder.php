<?php
// database/seeders/FuncionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Funcion;
use Carbon\Carbon;

class FuncionSeeder extends Seeder
{
    public function run()
    {
        $horarios = ['11:00', '13:45', '14:40', '15:40', '17:15', '18:15', '20:00', '21:00'];
        $precios = [12.00, 18.00, 25.00];
        $tipos = ['REGULAR', 'GOLD CLASS', 'VELVET'];
        
        // Crear horarios para los próximos 14 días
        for ($dia = 0; $dia < 14; $dia++) {
            $fechaFuncion = Carbon::now()->addDays($dia)->format('Y-m-d');
            
            // Para cada película
            for ($peliculaId = 1; $peliculaId <= 8; $peliculaId++) {
                // Para algunas salas aleatorias de diferentes cines
                $salaIds = range(1, 40); // 8 cines x 5 salas = 40 salas
                shuffle($salaIds);
                $salasSeleccionadas = array_slice($salaIds, 0, rand(5, 10));
                
                foreach ($salasSeleccionadas as $salaId) {
                    // Crear algunos horarios aleatorios
                    $horariosSeleccionados = array_rand(array_flip($horarios), rand(2, 4));
                    if (!is_array($horariosSeleccionados)) {
                        $horariosSeleccionados = [$horariosSeleccionados];
                    }
                    
                    foreach ($horariosSeleccionados as $horario) {
                        $indiceTipo = array_rand($tipos);
                        
                        try {
                            Funcion::create([
                                'pelicula_id' => $peliculaId,
                                'sala_id' => $salaId,
                                'fecha_funcion' => $fechaFuncion,
                                'hora_funcion' => $horario,
                                'formato' => rand(0, 1) ? '2D' : '3D',
                                'tipo' => $tipos[$indiceTipo],
                                'precio' => $precios[$indiceTipo],
                                'tarifa_servicio' => 3.00,
                            ]);
                        } catch (\Exception $e) {
                            // Continuar si hay conflicto de horario
                            continue;
                        }
                    }
                }
            }
        }
    }
}