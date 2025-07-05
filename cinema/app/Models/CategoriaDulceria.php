<?php
// app/Models/CategoriaDulceria.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaDulceria extends Model
{
    use HasFactory;

    protected $table = 'categorias_dulceria';

    protected $fillable = [
        'nombre',
    ];

    // Relaciones
    public function productos()
    {
        return $this->hasMany(ProductoDulceria::class);
    }

    public function productosActivos()
    {
        return $this->hasMany(ProductoDulceria::class)->where('activo', true);
    }
}

?>