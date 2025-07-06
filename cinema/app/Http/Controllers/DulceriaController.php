<?php
// app/Http/Controllers/DulceriaController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductoDulceria;
use App\Models\CategoriaDulceria;
use App\Models\PedidoDulceria;
use App\Models\ItemPedidoDulceria;
use Illuminate\Support\Facades\Auth;

class DulceriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only([
            'checkout', 
            'procesarPedido', 
            'boleta', 
            'misPedidos'
        ]);
    }
    
    public function index()
    {
        $categorias = CategoriaDulceria::with(['productosActivos' => function($query) {
            $query->orderBy('nombre');
        }])->get();

        return view('dulceria.index', compact('categorias'));
    }

    public function agregarAlCarrito(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos_dulceria,id',
            'cantidad' => 'required|integer|min:1|max:10',
        ]);

        $producto = ProductoDulceria::findOrFail($request->producto_id);
        
        $carrito = session()->get('carrito_dulceria', []);
        $productoId = $request->producto_id;

        if (isset($carrito[$productoId])) {
            $carrito[$productoId]['cantidad'] += $request->cantidad;
        } else {
            $carrito[$productoId] = [
                'nombre' => $producto->nombre,
                'precio' => $producto->precio,
                'cantidad' => $request->cantidad,
                'imagen' => $producto->imagen,
            ];
        }

        session()->put('carrito_dulceria', $carrito);

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'carrito_count' => array_sum(array_column($carrito, 'cantidad'))
        ]);
    }

    public function verCarrito()
    {
        $carrito = session()->get('carrito_dulceria', []);
        $total = 0;

        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        return view('dulceria.carrito', compact('carrito', 'total'));
    }

    public function actualizarCarrito(Request $request)
    {
        $carrito = session()->get('carrito_dulceria', []);
        
        if (isset($carrito[$request->producto_id])) {
            if ($request->cantidad > 0) {
                $carrito[$request->producto_id]['cantidad'] = $request->cantidad;
            } else {
                unset($carrito[$request->producto_id]);
            }
        }

        session()->put('carrito_dulceria', $carrito);

        return redirect()->back()->with('success', 'Carrito actualizado');
    }

    public function eliminarDelCarrito($productoId)
    {
        $carrito = session()->get('carrito_dulceria', []);
        unset($carrito[$productoId]);
        session()->put('carrito_dulceria', $carrito);

        return redirect()->back()->with('success', 'Producto eliminado del carrito');
    }

    public function checkout()
    {
        $carrito = session()->get('carrito_dulceria', []);
        
        if (empty($carrito)) {
            return redirect()->route('dulceria.index')->with('error', 'El carrito está vacío');
        }

        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        return view('dulceria.checkout', compact('carrito', 'total'));
    }

    public function procesarPedido(Request $request)
    {
        $request->validate([
            'metodo_pago' => 'required|in:yape,visa,mastercard',
        ]);

        $carrito = session()->get('carrito_dulceria', []);
        
        if (empty($carrito)) {
            return redirect()->route('dulceria.index')->with('error', 'El carrito está vacío');
        }

        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        // Crear el pedido
        $pedido = PedidoDulceria::create([
            'user_id' => Auth::id(),
            'codigo_pedido' => PedidoDulceria::generarCodigoPedido(),
            'monto_total' => $total,
            'metodo_pago' => $request->metodo_pago,
            'estado' => 'confirmado',
        ]);

        // Crear los items del pedido
        foreach ($carrito as $productoId => $item) {
            ItemPedidoDulceria::create([
                'pedido_dulceria_id' => $pedido->id,
                'producto_dulceria_id' => $productoId,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio'],
                'subtotal' => $item['precio'] * $item['cantidad'],
            ]);
        }

        // Limpiar el carrito
        session()->forget('carrito_dulceria');

        return redirect()->route('dulceria.boleta', $pedido)
            ->with('success', '¡Pedido realizado exitosamente!');
    }

    public function boleta(PedidoDulceria $pedido)
    {
        // Verificar que el pedido pertenece al usuario autenticado
        if ($pedido->user_id !== Auth::id()) {
            abort(403);
        }

        return view('dulceria.boleta', compact('pedido'));
    }

    public function misPedidos()
    {
        $pedidos = Auth::user()->pedidosDulceria()
            ->with('items.producto')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dulceria.mis-pedidos', compact('pedidos'));
    }
}