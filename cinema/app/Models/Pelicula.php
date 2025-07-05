<?php
// app/Models/Pelicula.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelicula extends Model
{
    use HasFactory;

    protected $table = 'peliculas';

    protected $fillable = [
        'titulo',
        'descripcion',
        'genero',
        'duracion',
        'director',
        'clasificacion',
        'poster',
        'fecha_estreno',
        'activa',
        'destacada',
    ];

    protected $casts = [
        'fecha_estreno' => 'date',
        'activa' => 'boolean',
        'destacada' => 'boolean',
    ];

    // Relaciones
    public function funciones()
    {
        return $this->hasMany(Funcion::class);
    }

    public function reservas()
    {
        return $this->hasManyThrough(Reserva::class, Funcion::class);
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeDestacadas($query)
    {
        return $query->where('destacada', true);
    }

    // Métodos auxiliares
    public function getDuracionFormateada()
    {
        $horas = floor($this->duracion / 60);
        $minutos = $this->duracion % 60;
        
        if ($horas > 0) {
            return $horas . 'h ' . $minutos . 'min';
        }
        
        return $minutos . ' minutos';
    }
}

?>