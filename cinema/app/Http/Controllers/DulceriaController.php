<?php

namespace App\Http\Controllers;

use App\Models\ProductoDulceria;
use App\Models\CategoriaDulceria;
use App\Models\PedidoDulceria;
use App\Models\ItemPedidoDulceria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DulceriaController extends Controller
{
    /**
     * Mostrar la página principal de dulcería
     */
    public function index()
    {
        try {
            $categorias = CategoriaDulceria::with(['productos' => function($query) {
                $query->where('activo', true)->orderBy('nombre');
            }])->orderBy('nombre')->get();

            // Filtrar categorías que tienen productos activos
            $categorias = $categorias->filter(function($categoria) {
                return $categoria->productos->count() > 0;
            });

            return view('dulceria.index', compact('categorias'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar dulcería', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al cargar los productos');
        }
    }

    /**
     * Agregar producto al carrito
     */
    public function agregarCarrito(Request $request)
    {
        try {
            $request->validate([
                'producto_id' => 'required|exists:productos_dulceria,id',
                'cantidad' => 'required|integer|min:1|max:10'
            ]);

            $producto = ProductoDulceria::findOrFail($request->producto_id);
            
            if (!$producto->activo) {
                return response()->json([
                    'success' => false,
                    'message' => 'El producto no está disponible'
                ], 400);
            }

            $carrito = Session::get('carrito_dulceria', []);
            $productoId = $producto->id;

            if (isset($carrito[$productoId])) {
                $carrito[$productoId]['cantidad'] += $request->cantidad;
                // Limitar cantidad máxima
                if ($carrito[$productoId]['cantidad'] > 10) {
                    $carrito[$productoId]['cantidad'] = 10;
                }
            } else {
                $carrito[$productoId] = [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'precio' => $producto->precio,
                    'imagen' => $producto->imagen,
                    'cantidad' => $request->cantidad
                ];
            }

            Session::put('carrito_dulceria', $carrito);

            // Calcular totales
            $cantidadTotal = array_sum(array_column($carrito, 'cantidad'));
            $montoTotal = array_sum(array_map(function($item) {
                return $item['precio'] * $item['cantidad'];
            }, $carrito));

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Producto agregado al carrito',
                    'carrito_count' => $cantidadTotal,
                    'monto_total' => $montoTotal
                ]);
            }

            return redirect()->back()->with('success', 'Producto agregado al carrito');
            
        } catch (\Exception $e) {
            Log::error('Error al agregar al carrito', [
                'error' => $e->getMessage(),
                'producto_id' => $request->producto_id ?? null
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al agregar producto al carrito'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al agregar producto al carrito');
        }
    }

    /**
     * Ver carrito de compras
     */
    public function carrito()
    {
        try {
            $carrito = Session::get('carrito_dulceria', []);
            
            if (empty($carrito)) {
                return view('dulceria.carrito', [
                    'carrito' => [],
                    'total' => 0
                ]);
            }

            // Verificar que los productos siguen existiendo y están activos
            $productosIds = array_keys($carrito);
            $productosDB = ProductoDulceria::whereIn('id', $productosIds)
                ->where('activo', true)
                ->get()
                ->keyBy('id');

            // Limpiar carrito de productos que ya no existen o están inactivos
            $carritoLimpio = [];
            foreach ($carrito as $productoId => $item) {
                if ($productosDB->has($productoId)) {
                    $producto = $productosDB->get($productoId);
                    $carritoLimpio[$productoId] = [
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'precio' => $producto->precio,
                        'imagen' => $producto->imagen,
                        'cantidad' => $item['cantidad']
                    ];
                }
            }

            Session::put('carrito_dulceria', $carritoLimpio);

            $total = array_sum(array_map(function($item) {
                return $item['precio'] * $item['cantidad'];
            }, $carritoLimpio));

            return view('dulceria.carrito', [
                'carrito' => $carritoLimpio,
                'total' => $total
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al mostrar carrito', ['error' => $e->getMessage()]);
            return view('dulceria.carrito', [
                'carrito' => [],
                'total' => 0
            ])->with('error', 'Error al cargar el carrito');
        }
    }

    /**
     * Actualizar cantidad en el carrito
     */
    public function actualizarCarrito(Request $request)
    {
        try {
            $request->validate([
                'producto_id' => 'required|integer',
                'cantidad' => 'required|integer|min:1|max:10'
            ]);

            $carrito = Session::get('carrito_dulceria', []);
            $productoId = $request->producto_id;

            if (!isset($carrito[$productoId])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado en el carrito'
                ], 404);
            }

            $carrito[$productoId]['cantidad'] = $request->cantidad;
            Session::put('carrito_dulceria', $carrito);

            $cantidadTotal = array_sum(array_column($carrito, 'cantidad'));
            $montoTotal = array_sum(array_map(function($item) {
                return $item['precio'] * $item['cantidad'];
            }, $carrito));

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Carrito actualizado',
                    'carrito_count' => $cantidadTotal,
                    'monto_total' => $montoTotal,
                    'subtotal' => $carrito[$productoId]['precio'] * $carrito[$productoId]['cantidad']
                ]);
            }

            return redirect()->back()->with('success', 'Carrito actualizado');
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar carrito', ['error' => $e->getMessage()]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el carrito'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al actualizar el carrito');
        }
    }

    /**
     * Eliminar producto del carrito
     */
    public function eliminarCarrito($productoId)
    {
        try {
            $carrito = Session::get('carrito_dulceria', []);
            
            if (isset($carrito[$productoId])) {
                unset($carrito[$productoId]);
                Session::put('carrito_dulceria', $carrito);
                
                return redirect()->back()->with('success', 'Producto eliminado del carrito');
            }
            
            return redirect()->back()->with('warning', 'Producto no encontrado en el carrito');
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar del carrito', [
                'error' => $e->getMessage(),
                'producto_id' => $productoId
            ]);
            
            return redirect()->back()->with('error', 'Error al eliminar producto del carrito');
        }
    }

    /**
     * Página de checkout (requiere autenticación)
     */
    public function checkout()
    {
        try {
            $carrito = Session::get('carrito_dulceria', []);
            
            if (empty($carrito)) {
                return redirect()->route('dulceria.index')
                    ->with('warning', 'Tu carrito está vacío');
            }

            // Verificar productos nuevamente
            $productosIds = array_keys($carrito);
            $productosDB = ProductoDulceria::whereIn('id', $productosIds)
                ->where('activo', true)
                ->get()
                ->keyBy('id');

            $carritoValidado = [];
            $total = 0;

            foreach ($carrito as $productoId => $item) {
                if ($productosDB->has($productoId)) {
                    $producto = $productosDB->get($productoId);
                    $subtotal = $producto->precio * $item['cantidad'];
                    
                    $carritoValidado[$productoId] = [
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'precio' => $producto->precio,
                        'imagen' => $producto->imagen,
                        'cantidad' => $item['cantidad'],
                        'subtotal' => $subtotal
                    ];
                    
                    $total += $subtotal;
                }
            }

            if (empty($carritoValidado)) {
                Session::forget('carrito_dulceria');
                return redirect()->route('dulceria.index')
                    ->with('warning', 'Los productos en tu carrito ya no están disponibles');
            }

            Session::put('carrito_dulceria', $carritoValidado);

            return view('dulceria.checkout', [
                'carrito' => $carritoValidado,
                'total' => $total,
                'user' => Auth::user()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en checkout', ['error' => $e->getMessage()]);
            return redirect()->route('dulceria.carrito')
                ->with('error', 'Error al procesar el checkout');
        }
    }

    /**
     * Procesar pedido (requiere autenticación)
     */
    public function procesarPedido(Request $request)
    {
        try {
            $request->validate([
                'metodo_pago' => 'required|in:efectivo,tarjeta,yape,plin,visa,mastercard',
                'notas' => 'nullable|string|max:500'
            ]);

            $carrito = Session::get('carrito_dulceria', []);
            
            if (empty($carrito)) {
                return redirect()->route('dulceria.index')
                    ->with('error', 'Tu carrito está vacío');
            }

            DB::beginTransaction();

            // Crear el pedido
            $codigoPedido = 'DUL-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            $total = array_sum(array_map(function($item) {
                return $item['precio'] * $item['cantidad'];
            }, $carrito));

            $pedido = PedidoDulceria::create([
                'user_id' => Auth::id(),
                'codigo_pedido' => $codigoPedido,
                'estado' => 'confirmado',
                'monto_total' => $total,
                'metodo_pago' => $request->metodo_pago,
                'notas' => $request->notas
            ]);

            // Crear los items del pedido
            foreach ($carrito as $item) {
                $producto = ProductoDulceria::find($item['id']);
                
                if (!$producto || !$producto->activo) {
                    throw new \Exception("Producto {$item['nombre']} ya no está disponible");
                }

                ItemPedidoDulceria::create([
                    'pedido_dulceria_id' => $pedido->id,
                    'producto_dulceria_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $producto->precio * $item['cantidad']
                ]);
            }

            DB::commit();

            // Limpiar carrito
            Session::forget('carrito_dulceria');

            return redirect()->route('dulceria.boleta', $pedido)
                ->with('success', 'Pedido realizado exitosamente. Código: ' . $codigoPedido);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar pedido', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'carrito' => $carrito ?? []
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al procesar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar boleta del pedido
     */
    public function boleta(PedidoDulceria $pedido)
    {
        try {
            // Verificar que el usuario puede ver esta boleta
            if ($pedido->user_id !== Auth::id() && !Auth::user()->is_admin) {
                abort(403, 'No tienes permiso para ver esta boleta');
            }

            $pedido->load(['items.producto', 'user']);

            return view('dulceria.boleta', compact('pedido'));
            
        } catch (\Exception $e) {
            Log::error('Error al mostrar boleta', [
                'error' => $e->getMessage(),
                'pedido_id' => $pedido->id ?? null
            ]);
            
            return redirect()->route('dulceria.mis-pedidos')
                ->with('error', 'Error al cargar la boleta');
        }
    }

    /**
     * Mostrar mis pedidos
     */
    public function misPedidos()
    {
        try {
            $pedidos = PedidoDulceria::where('user_id', Auth::id())
                ->with(['items.producto'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('dulceria.mis-pedidos', compact('pedidos'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar mis pedidos', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return view('dulceria.mis-pedidos', ['pedidos' => collect()])
                ->with('error', 'Error al cargar tus pedidos');
        }
    }

    /**
     * Obtener cantidad de items en carrito (para badge en navbar)
     */
    public function carritoCount()
    {
        try {
            $carrito = Session::get('carrito_dulceria', []);
            $count = array_sum(array_column($carrito, 'cantidad'));
            
            return response()->json(['count' => $count]);
            
        } catch (\Exception $e) {
            return response()->json(['count' => 0]);
        }
    }

    /**
     * Limpiar carrito completamente
     */
    public function limpiarCarrito()
    {
        try {
            Session::forget('carrito_dulceria');
            
            return redirect()->route('dulceria.index')
                ->with('success', 'Carrito limpiado');
                
        } catch (\Exception $e) {
            Log::error('Error al limpiar carrito', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'Error al limpiar el carrito');
        }
    }
public function carritoInfo()
{
    try {
        $carrito = Session::get('carrito_dulceria', []);
        
        $cantidadTotal = 0;
        $montoTotal = 0;
        
        if (!empty($carrito)) {
            $cantidadTotal = array_sum(array_column($carrito, 'cantidad'));
            $montoTotal = array_sum(array_map(function($item) {
                return $item['precio'] * $item['cantidad'];
            }, $carrito));
        }
        
        return response()->json([
            'carrito_count' => $cantidadTotal,
            'monto_total' => $montoTotal,
            'items_count' => count($carrito)
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error al obtener info del carrito', ['error' => $e->getMessage()]);
        return response()->json([
            'carrito_count' => 0,
            'monto_total' => 0,
            'items_count' => 0
        ]);
    }
}
}