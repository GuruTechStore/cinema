<?php
// app/Models/PedidoDulceria.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoDulceria extends Model
{
    use HasFactory;

    protected $table = 'pedidos_dulceria';

    protected $fillable = [
        'user_id',
        'codigo_pedido',
        'monto_total',
        'metodo_pago',
        'estado',
    ];

    protected $casts = [
        'monto_total' => 'decimal:2',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ItemPedidoDulceria::class);
    }

    // Métodos auxiliares
    public static function generarCodigoPedido()
    {
        return 'DUL-' . strtoupper(uniqid());
    }

    public function calcularTotal()
    {
        return $this->items->sum('subtotal');
    }
}

?>