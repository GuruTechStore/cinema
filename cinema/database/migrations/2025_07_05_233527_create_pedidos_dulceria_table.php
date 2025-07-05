<?php
// database/migrations/2024_01_01_000009_create_pedidos_dulceria_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pedidos_dulceria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('codigo_pedido')->unique();
            $table->decimal('monto_total', 8, 2);
            $table->enum('metodo_pago', ['yape', 'visa', 'mastercard']);
            $table->enum('estado', ['pendiente', 'confirmado', 'listo', 'entregado'])->default('confirmado');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pedidos_dulceria');
    }
};