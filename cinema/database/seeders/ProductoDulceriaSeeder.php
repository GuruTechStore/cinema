<?php

namespace Database\Seeders;


use App\Models\ProductoDulceria;
use Illuminate\Database\Seeder; 
class ProductoDulceriaSeeder extends Seeder
{
    public function run()
    {
        $productos = [
            // Canchita
            ['nombre' => 'Canchita Pequeña', 'descripcion' => 'Porción individual de canchita dulce', 'precio' => 8.50, 'categoria_dulceria_id' => 1],
            ['nombre' => 'Canchita Mediana', 'descripcion' => 'Porción mediana perfecta para compartir', 'precio' => 12.50, 'categoria_dulceria_id' => 1],
            ['nombre' => 'Canchita Grande', 'descripcion' => 'Porción familiar de canchita dulce', 'precio' => 15.50, 'categoria_dulceria_id' => 1],
            ['nombre' => 'Canchita Salada Mediana', 'descripcion' => 'Canchita salada porción mediana', 'precio' => 12.50, 'categoria_dulceria_id' => 1],
            
            // Bebidas
            ['nombre' => 'Coca Cola 16oz', 'descripcion' => 'Bebida gaseosa Coca Cola tamaño personal', 'precio' => 6.50, 'categoria_dulceria_id' => 2],
            ['nombre' => 'Coca Cola 22oz', 'descripcion' => 'Bebida gaseosa Coca Cola tamaño mediano', 'precio' => 8.50, 'categoria_dulceria_id' => 2],
            ['nombre' => 'Coca Cola 32oz', 'descripcion' => 'Bebida gaseosa Coca Cola tamaño grande', 'precio' => 10.50, 'categoria_dulceria_id' => 2],
            ['nombre' => 'Agua San Luis', 'descripcion' => 'Agua mineral sin gas 500ml', 'precio' => 4.50, 'categoria_dulceria_id' => 2],
            ['nombre' => 'Jugo Frugos', 'descripcion' => 'Jugo de frutas naturales 300ml', 'precio' => 5.50, 'categoria_dulceria_id' => 2],
            ['nombre' => 'Inca Kola 16oz', 'descripcion' => 'Bebida gaseosa sabor único peruano', 'precio' => 6.50, 'categoria_dulceria_id' => 2],
            
            // Dulces
            ['nombre' => 'M&M Chocolate', 'descripcion' => 'Confites de chocolate con colores', 'precio' => 5.50, 'categoria_dulceria_id' => 3],
            ['nombre' => 'Skittles', 'descripcion' => 'Dulces masticables de frutas', 'precio' => 5.50, 'categoria_dulceria_id' => 3],
            ['nombre' => 'Kit Kat', 'descripcion' => 'Chocolate con galleta crujiente', 'precio' => 4.50, 'categoria_dulceria_id' => 3],
            ['nombre' => 'Snickers', 'descripcion' => 'Barra de chocolate con maní y caramelo', 'precio' => 4.50, 'categoria_dulceria_id' => 3],
            ['nombre' => 'Twix', 'descripcion' => 'Galleta con caramelo cubierta de chocolate', 'precio' => 4.50, 'categoria_dulceria_id' => 3],
            ['nombre' => 'Gomitas Haribo', 'descripcion' => 'Gomitas de frutas variadas', 'precio' => 6.50, 'categoria_dulceria_id' => 3],
            ['nombre' => 'Chicles Trident', 'descripcion' => 'Chicles sin azúcar sabor menta', 'precio' => 3.50, 'categoria_dulceria_id' => 3],
            
            // Combos
            ['nombre' => 'Combo Pareja', 'descripcion' => '2 bebidas medianas + canchita grande + 2 dulces', 'precio' => 25.50, 'categoria_dulceria_id' => 4, 'es_combo' => true],
            ['nombre' => 'Combo Familiar', 'descripcion' => '4 bebidas + 2 canchitas grandes + 4 dulces', 'precio' => 45.50, 'categoria_dulceria_id' => 4, 'es_combo' => true],
            ['nombre' => 'Combo Individual', 'descripcion' => '1 bebida mediana + canchita mediana + 1 dulce', 'precio' => 18.50, 'categoria_dulceria_id' => 4, 'es_combo' => true],
            ['nombre' => 'Combo Económico', 'descripcion' => '1 bebida pequeña + canchita pequeña', 'precio' => 12.50, 'categoria_dulceria_id' => 4, 'es_combo' => true],
            ['nombre' => 'Combo Mega', 'descripcion' => '2 bebidas grandes + 2 canchitas grandes + nachos + 4 dulces', 'precio' => 55.50, 'categoria_dulceria_id' => 4, 'es_combo' => true],
            
            // Nachos
            ['nombre' => 'Nachos con Queso', 'descripcion' => 'Tortillas de maíz con salsa de queso caliente', 'precio' => 12.50, 'categoria_dulceria_id' => 5],
            ['nombre' => 'Nachos Supremos', 'descripcion' => 'Nachos con queso, guacamole y jalapeños', 'precio' => 16.50, 'categoria_dulceria_id' => 5],
            ['nombre' => 'Nachos Mexicanos', 'descripcion' => 'Nachos con queso, pico de gallo y crema', 'precio' => 18.50, 'categoria_dulceria_id' => 5],
            
            // Hot Dogs
            ['nombre' => 'Hot Dog Clásico', 'descripcion' => 'Salchicha en pan con mostaza y kétchup', 'precio' => 9.50, 'categoria_dulceria_id' => 6],
            ['nombre' => 'Hot Dog Especial', 'descripcion' => 'Hot dog con queso, cebolla frita y salsas', 'precio' => 12.50, 'categoria_dulceria_id' => 6],
            ['nombre' => 'Hot Dog Americano', 'descripcion' => 'Hot dog grande con papas fritas', 'precio' => 15.50, 'categoria_dulceria_id' => 6],
            ['nombre' => 'Hot Dog Vegetariano', 'descripcion' => 'Salchicha vegetal con vegetales frescos', 'precio' => 11.50, 'categoria_dulceria_id' => 6],
            
            // Helados
            ['nombre' => 'Helado Paleta', 'descripcion' => 'Paleta de helado de agua sabores variados', 'precio' => 4.50, 'categoria_dulceria_id' => 7],
            ['nombre' => 'Helado Cremoso', 'descripcion' => 'Helado cremoso en vaso individual', 'precio' => 6.50, 'categoria_dulceria_id' => 7],
            ['nombre' => 'Helado Sandwich', 'descripcion' => 'Helado entre dos galletas de chocolate', 'precio' => 7.50, 'categoria_dulceria_id' => 7],
            ['nombre' => 'Sundae de Chocolate', 'descripcion' => 'Helado con salsa de chocolate y topping', 'precio' => 9.50, 'categoria_dulceria_id' => 7],
            ['nombre' => 'Milkshake Fresa', 'descripcion' => 'Batido cremoso sabor fresa', 'precio' => 12.50, 'categoria_dulceria_id' => 7],
            
            // Snacks
            ['nombre' => 'Papas Lays Original', 'descripcion' => 'Papas fritas clásicas bolsa individual', 'precio' => 4.50, 'categoria_dulceria_id' => 8],
            ['nombre' => 'Papas Pringles', 'descripcion' => 'Papas apiladas sabor original', 'precio' => 8.50, 'categoria_dulceria_id' => 8],
            ['nombre' => 'Doritos Nacho', 'descripcion' => 'Tortillas de maíz sabor nacho cheese', 'precio' => 5.50, 'categoria_dulceria_id' => 8],
            ['nombre' => 'Cheetos', 'descripcion' => 'Snack de maíz con sabor a queso', 'precio' => 4.50, 'categoria_dulceria_id' => 8],
            ['nombre' => 'Mix de Frutos Secos', 'descripcion' => 'Mezcla de nueces, almendras y pasas', 'precio' => 8.50, 'categoria_dulceria_id' => 8],
            ['nombre' => 'Galletas Oreo', 'descripcion' => 'Galletas de chocolate rellenas de crema', 'precio' => 5.50, 'categoria_dulceria_id' => 8],
            ['nombre' => 'Tostitos con Salsa', 'descripcion' => 'Tostadas de maíz con salsa picante', 'precio' => 7.50, 'categoria_dulceria_id' => 8],
        ];

        foreach ($productos as $producto) {
            ProductoDulceria::create($producto);
        }
    }
}