<?php
// app/Models/User.php (Actualizar el modelo existente)

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relaciones
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    public function pedidosDulceria()
    {
        return $this->hasMany(PedidoDulceria::class);
    }

    // Métodos auxiliares
    public function esAdmin()
    {
        return $this->rol === 'admin';
    }

    public function esUsuario()
    {
        return $this->rol === 'usuario';
    }
}

?>