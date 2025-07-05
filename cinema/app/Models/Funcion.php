<?php
// app/Models/Funcion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcion extends Model
{
    use HasFactory;

    protected $table = 'funciones';

    protected $fillable = [
        'pelicula_id',
        'sala_id',
        'fecha_funcion',
        'hora_funcion',
        'formato',
        'tipo',
        'precio',
        'tarifa_servicio',
    ];

    protected $casts = [
        'fecha_funcion' => 'date',
        'hora_funcion' => 'datetime:H:i',
        'precio' => 'decimal:2',
        'tarifa_servicio' => 'decimal:2',
    ];

    // Relaciones
    public function pelicula()
    {
        return $this->belongsTo(Pelicula::class);
    }

    public function sala()
    {
        return $this->belongsTo(Sala::class);
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    // Métodos auxiliares
    public function getAsientosOcupados()
    {
        $asientosOcupados = [];
        
        foreach ($this->reservas as $reserva) {
            $asientosReserva = json_decode($reserva->asientos, true);
            $asientosOcupados = array_merge($asientosOcupados, $asientosReserva);
        }
        
        return $asientosOcupados;
    }

    public function getAsientosDisponibles()
    {
        $todosLosAsientos = $this->sala->generarAsientos();
        $asientosOcupados = $this->getAsientosOcupados();
        
        return array_diff($todosLosAsientos, $asientosOcupados);
    }

    public function getPrecioTotal()
    {
        return $this->precio + $this->tarifa_servicio;
    }
}

?>
