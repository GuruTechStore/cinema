<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('funcion_id')->constrained('funciones')->onDelete('cascade');
            $table->string('codigo_reserva')->unique();
            $table->string('clave_seguridad', 10);
            $table->json('asientos'); // ["C8", "C9", "C10"]
            $table->integer('total_boletos');
            $table->decimal('precio_boleto', 8, 2);
            $table->decimal('tarifa_servicio', 8, 2);
            $table->decimal('monto_total', 8, 2);
            $table->enum('metodo_pago', ['yape', 'visa', 'mastercard']);
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada'])->default('confirmada');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservas');
    }
};