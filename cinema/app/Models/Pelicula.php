<?php
// Reemplaza app/Models/Pelicula.php:

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pelicula extends Model
{
    use HasFactory;

    protected $table = 'peliculas';

    protected $fillable = [
        'titulo',
        'descripcion',
        'sinopsis',
        'reparto',
        'genero',
        'duracion',
        'director',
        'clasificacion',
        'idioma',
        'poster',
        'trailer_url',
        'fecha_estreno',
        'activa',
        'destacada',
    ];

    protected $casts = [
        'fecha_estreno' => 'date',
        'activa' => 'boolean',
        'destacada' => 'boolean',
    ];

    // RELACIÓN SIN ORDER BY (lo haremos en el controlador/vista cuando sea necesario)
    public function funciones()
    {
        return $this->hasMany(Funcion::class);
    }

    // Relación con ordenamiento específico cuando se necesite
    public function funcionesOrdenadas()
    {
        return $this->hasMany(Funcion::class)->orderBy('fecha_funcion')->orderBy('hora_funcion');
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

    public function scopeEnEstreno($query)
    {
        return $query->where('fecha_estreno', '<=', Carbon::now());
    }

    public function scopeProximosEstrenos($query)
    {
        return $query->where('fecha_estreno', '>', Carbon::now());
    }

    // Métodos auxiliares
    public function getDuracionFormateada()
    {
        $horas = floor($this->duracion / 60);
        $minutos = $this->duracion % 60;
        
        if ($horas > 0) {
            return $horas . 'h ' . $minutos . 'min';
        }
        
        return $minutos . 'min';
    }

    public function getSlugAttribute()
    {
        return \Str::slug($this->titulo);
    }

    public function getTieneTrailerAttribute()
    {
        return !empty($this->trailer_url);
    }

    public function esDestacada()
    {
        return $this->destacada;
    }

    public function estaActiva()
    {
        return $this->activa;
    }

    public function yaEstreno()
    {
        return $this->fecha_estreno <= Carbon::now();
    }
}

