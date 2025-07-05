<?php
// database/migrations/2024_01_01_000003_create_salas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('salas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cine_id')->constrained('cines')->onDelete('cascade');
            $table->string('nombre'); // Sala 1, Sala 2
            $table->integer('total_asientos')->default(80);
            $table->integer('filas')->default(8); // filas A-H
            $table->integer('asientos_por_fila')->default(10); // asientos 1-10
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('salas');
    }
};
