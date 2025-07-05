<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('funciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelicula_id')->constrained('peliculas')->onDelete('cascade');
            $table->foreignId('sala_id')->constrained('salas')->onDelete('cascade');
            $table->date('fecha_funcion');
            $table->time('hora_funcion');
            $table->enum('formato', ['2D', '3D'])->default('2D');
            $table->enum('tipo', ['REGULAR', 'GOLD CLASS', 'VELVET'])->default('REGULAR');
            $table->decimal('precio', 8, 2);
            $table->decimal('tarifa_servicio', 8, 2)->default(3.00);
            $table->timestamps();
            
            $table->unique(['sala_id', 'fecha_funcion', 'hora_funcion']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('funciones');
    }
};