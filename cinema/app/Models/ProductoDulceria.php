<?php
// app/Models/ProductoDulceria.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoDulceria extends Model
{
    use HasFactory;

    protected $table = 'productos_dulceria';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'imagen',
        'categoria_dulceria_id',
        'activo',
        'es_combo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean',
        'es_combo' => 'boolean',
    ];

    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(CategoriaDulceria::class, 'categoria_dulceria_id');
    }

    public function itemsPedido()
    {
        return $this->hasMany(ItemPedidoDulceria::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeCombos($query)
    {
        return $query->where('es_combo', true);
    }

    public function scopeProductosIndividuales($query)
    {
        return $query->where('es_combo', false);
    }
}

?>
