<?php
// database/seeders/FuncionSeeder.php - CORREGIDO

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Funcion;
use App\Models\Pelicula;
use Carbon\Carbon;

class FuncionSeeder extends Seeder
{
    public function run()
    {
        $horarios = ['11:00', '13:45', '14:40', '15:40', '17:15', '18:15', '20:00', '21:00', '22:30'];
        $precios = [12.00, 18.00, 25.00];
        $tipos = ['REGULAR', 'GOLD CLASS', 'VELVET'];
        
        // Obtener todas las películas
        $peliculas = Pelicula::all();
        
        foreach ($peliculas as $pelicula) {
            // Calcular la fecha de inicio de las funciones
            $fechaEstreno = $pelicula->fecha_estreno; // Ya es Carbon
            $hoy = Carbon::today();
            
            // Las funciones empiezan desde la fecha de estreno o desde hoy (lo que sea mayor)
            $fechaInicio = $fechaEstreno->gt($hoy) ? $fechaEstreno : $hoy;
            
            // Crear funciones para los próximos 14 días desde la fecha de inicio
            for ($dia = 0; $dia < 14; $dia++) {
                $fechaFuncion = $fechaInicio->copy()->addDays($dia);
                
                // Para salas aleatorias de diferentes cines
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
                                'pelicula_id' => $pelicula->id,
                                'sala_id' => $salaId,
                                'fecha_funcion' => $fechaFuncion->format('Y-m-d'),
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
            
            echo "Funciones creadas para: {$pelicula->titulo} (Estreno: {$fechaEstreno->format('d/m/Y')})\n";
        }
        
        echo "\n✅ Seeder de funciones completado. Se respetaron las fechas de estreno.\n";
    }
}

