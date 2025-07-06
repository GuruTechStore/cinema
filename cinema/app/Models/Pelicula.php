<?php
// app/Models/Pelicula.php - CORREGIDO

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
        'fecha_estreno' => 'datetime', // Cambiado de 'date' a 'datetime' para que use Carbon
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
        
        return $minutos . ' minutos';
    }

    /**
     * Verificar si la película ya se estrenó
     */
    public function yaSeEstreno()
    {
        return $this->fecha_estreno->lte(Carbon::now());
    }

    /**
     * Verificar si es un próximo estreno
     */
    public function esProximoEstreno()
    {
        return $this->fecha_estreno->gt(Carbon::now());
    }

    /**
     * Obtener la fecha mínima para mostrar funciones
     */
    public function getFechaMinimaFunciones()
    {
        return $this->fecha_estreno->max(Carbon::today());
    }

    /**
     * Verificar si puede tener funciones en una fecha específica
     */
    public function puedeProyectarseEn($fecha)
    {
        $fechaConsulta = Carbon::parse($fecha);
        return $fechaConsulta->gte($this->fecha_estreno) && $fechaConsulta->gte(Carbon::today());
    }

    /**
     * Obtener estado de la película
     */
    public function getEstado()
    {
        if ($this->esProximoEstreno()) {
            $diasRestantes = $this->fecha_estreno->diffInDays(Carbon::now());
            if ($diasRestantes == 0) {
                return 'Se estrena hoy';
            } elseif ($diasRestantes == 1) {
                return 'Se estrena mañana';
            } else {
                return "Se estrena en {$diasRestantes} días";
            }
        }
        
        $diasEstreno = Carbon::now()->diffInDays($this->fecha_estreno);
        if ($diasEstreno < 7) {
            return 'Estreno reciente';
        } elseif ($diasEstreno < 30) {
            return 'En cartelera';
        } else {
            return 'En cartelera extendida';
        }
    }
}

?>