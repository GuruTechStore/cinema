<?php
// app/Models/Cine.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cine extends Model
{
    use HasFactory;

    protected $table = 'cines';

    protected $fillable = [
        'nombre',
        'direccion',
        'ciudad_id',
        'imagen',
        'formatos',
    ];

    // Relaciones
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }

    public function salas()
    {
        return $this->hasMany(Sala::class);
    }

    public function funciones()
    {
        return $this->hasManyThrough(Funcion::class, Sala::class);
    }

    // Métodos auxiliares
    public function getFormatosArray()
    {
        return explode(', ', $this->formatos);
    }
}

?>