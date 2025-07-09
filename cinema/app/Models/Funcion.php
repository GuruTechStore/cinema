<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'tarifa_servicio'
    ];

    protected $casts = [
        'fecha_funcion' => 'date',
        'hora_funcion' => 'datetime:H:i',
        'precio' => 'decimal:2',
        'tarifa_servicio' => 'decimal:2'
    ];

    // ALIASES TEMPORALES PARA COMPATIBILIDAD
    protected $appends = ['fecha', 'hora_inicio'];

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

    // ACCESSORS PARA COMPATIBILIDAD TEMPORAL
    public function getFechaAttribute()
    {
        return $this->fecha_funcion;
    }

    public function getHoraInicioAttribute()
    {
        return $this->hora_funcion;
    }

    // Accessors principales
    public function getFechaHoraAttribute()
    {
        return Carbon::parse($this->fecha_funcion->format('Y-m-d') . ' ' . $this->hora_funcion->format('H:i'));
    }

    public function getHoraFinAttribute()
    {
        $duracion = $this->pelicula ? $this->pelicula->duracion : 120;
        return $this->fecha_hora->copy()->addMinutes($duracion);
    }

    // Scopes actualizados
    public function scopeHoy($query)
    {
        return $query->where('fecha_funcion', Carbon::today());
    }

    public function scopeFuturas($query)
    {
        return $query->where('fecha_funcion', '>=', Carbon::today());
    }

    public function scopePasadas($query)
    {
        return $query->where('fecha_funcion', '<', Carbon::today());
    }

// Método corregido para obtener asientos ocupados
public function getAsientosOcupados()
{
    return $this->reservas()
        ->where('estado', 'confirmada')
        ->get()
        ->flatMap(function($reserva) {
            // Ya no necesitas json_decode porque el cast lo hace automáticamente
            return $reserva->asientos ?? [];
        })
        ->toArray();
}}