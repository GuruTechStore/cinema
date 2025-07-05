<?php
// app/Models/Reserva.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';

    protected $fillable = [
        'user_id',
        'funcion_id',
        'codigo_reserva',
        'clave_seguridad',
        'asientos',
        'total_boletos',
        'precio_boleto',
        'tarifa_servicio',
        'monto_total',
        'metodo_pago',
        'estado',
    ];

    protected $casts = [
        'asientos' => 'array',
        'precio_boleto' => 'decimal:2',
        'tarifa_servicio' => 'decimal:2',
        'monto_total' => 'decimal:2',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function funcion()
    {
        return $this->belongsTo(Funcion::class);
    }

    // Métodos auxiliares
    public static function generarCodigoReserva()
    {
        return strtoupper(uniqid());
    }

    public static function generarClaveSeguridad()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function getAsientosFormateados()
    {
        return implode(', ', $this->asientos);
    }
}

?>
