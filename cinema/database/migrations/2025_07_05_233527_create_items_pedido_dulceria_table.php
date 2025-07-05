<?php
// database/migrations/2024_01_01_000010_create_items_pedido_dulceria_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('items_pedido_dulceria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_dulceria_id')->constrained('pedidos_dulceria')->onDelete('cascade');
            $table->foreignId('producto_dulceria_id')->constrained('productos_dulceria')->onDelete('cascade');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 8, 2);
            $table->decimal('subtotal', 8, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('items_pedido_dulceria');
    }
};
