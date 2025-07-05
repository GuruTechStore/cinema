<?php
// app/Models/ItemPedidoDulceria.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPedidoDulceria extends Model
{
    use HasFactory;

    protected $table = 'items_pedido_dulceria';

    protected $fillable = [
        'pedido_dulceria_id',
        'producto_dulceria_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relaciones
    public function pedido()
    {
        return $this->belongsTo(PedidoDulceria::class, 'pedido_dulceria_id');
    }

    public function producto()
    {
        return $this->belongsTo(ProductoDulceria::class, 'producto_dulceria_id');
    }

    // Métodos auxiliares
    public function calcularSubtotal()
    {
        return $this->cantidad * $this->precio_unitario;
    }
}

?>
