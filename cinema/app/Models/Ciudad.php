<?php
// app/Models/Ciudad.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    use HasFactory;

    protected $table = 'ciudades';

    protected $fillable = [
        'nombre',
    ];

    // Relaciones
    public function cines()
    {
        return $this->hasMany(Cine::class);
    }
}

?>