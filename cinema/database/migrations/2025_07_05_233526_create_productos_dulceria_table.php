<?php
// database/migrations/2024_01_01_000008_create_productos_dulceria_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('productos_dulceria', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 8, 2);
            $table->string('imagen')->nullable();
            $table->foreignId('categoria_dulceria_id')->constrained('categorias_dulceria')->onDelete('cascade');
            $table->boolean('activo')->default(true);
            $table->boolean('es_combo')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('productos_dulceria');
    }
};