<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cines', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('direccion');
            $table->foreignId('ciudad_id')->constrained('ciudades')->onDelete('cascade');
            $table->string('imagen')->nullable();
            $table->string('formatos')->default('2D, REGULAR'); // "2D, REGULAR, 3D"
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cines');
    }
};