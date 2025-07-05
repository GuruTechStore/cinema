
<?php
// database/migrations/2024_01_01_000004_create_peliculas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('peliculas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('genero')->nullable();
            $table->integer('duracion'); // minutos
            $table->string('director')->nullable();
            $table->string('clasificacion'); // R, PG-13, etc.
            $table->string('poster')->nullable();
            $table->date('fecha_estreno');
            $table->boolean('activa')->default(true);
            $table->boolean('destacada')->default(false); // para destacar en home
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('peliculas');
    }
};

?>