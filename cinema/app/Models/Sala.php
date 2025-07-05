<?php
// app/Models/Sala.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sala extends Model
{
    use HasFactory;

    protected $table = 'salas';

    protected $fillable = [
        'cine_id',
        'nombre',
        'total_asientos',
        'filas',
        'asientos_por_fila',
    ];

    // Relaciones
    public function cine()
    {
        return $this->belongsTo(Cine::class);
    }

    public function funciones()
    {
        return $this->hasMany(Funcion::class);
    }

    // Métodos auxiliares
    public function generarAsientos()
    {
        $asientos = [];
        $letras = range('A', chr(65 + $this->filas - 1));
        
        foreach ($letras as $fila) {
            for ($numero = 1; $numero <= $this->asientos_por_fila; $numero++) {
                $asientos[] = $fila . $numero;
            }
        }
        
        return $asientos;
    }
}

?>