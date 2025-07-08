<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductoDulceria;
use App\Models\CategoriaDulceria;
use App\Models\PedidoDulceria;
use App\Models\ItemPedidoDulceria;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DulceriaController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductoDulceria::with('categoria');

        // Filtros
        if ($request->filled('categoria')) {
            $query->where('categoria_dulceria_id', $request->categoria);
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->estado === 'activo');
        }

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        $productos = $query->orderBy('created_at', 'desc')->paginate(12);
        $categorias = CategoriaDulceria::all();

        // Estadísticas
        $totalProductos = ProductoDulceria::count();
        $productosActivos = ProductoDulceria::where('activo', true)->count();
        $ventasHoy = PedidoDulceria::whereDate('created_at', Carbon::today())->count();
        $ingresosTotales = PedidoDulceria::where('estado', '!=', 'cancelado')->sum('monto_total');
        $ventasEsteMes = PedidoDulceria::whereMonth('created_at', Carbon::now()->month)->count();
        $ultimosPedidos = PedidoDulceria::with('user')->latest()->take(5)->get();

        return view('admin.dulceria.index', compact(
            'productos', 
            'categorias', 
            'totalProductos', 
            'productosActivos', 
            'ingresosTotales', 
            'ventasEsteMes', 
            'ventasHoy',
            'ultimosPedidos'
        ));
    }

    public function create()
    {
        $categorias = CategoriaDulceria::all();
        return view('admin.dulceria.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:productos_dulceria',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'categoria_dulceria_id' => 'required|exists:categorias_dulceria,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'es_combo' => 'boolean',
            'activo' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $datos = $request->only(['nombre', 'descripcion', 'precio', 'categoria_dulceria_id']);
            $datos['es_combo'] = $request->has('es_combo');
            $datos['activo'] = $request->has('activo');

            // Manejar imagen
            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
                $rutaImagen = $imagen->storeAs('dulceria', $nombreImagen, 'public');
                $datos['imagen'] = $rutaImagen;
                
                Log::info('Imagen subida correctamente', ['ruta' => $rutaImagen]);
            }

            $producto = ProductoDulceria::create($datos);
            
            DB::commit();

            return redirect()->route('admin.dulceria.show', $producto)
                ->with('success', 'Producto creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear producto', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el producto. Inténtalo de nuevo.');
        }
    }

    public function show($id)
    {
        $dulceria = ProductoDulceria::with(['categoria', 'items.pedido.user'])->findOrFail($id);
        
        // Estadísticas del producto
        $ventasTotal = ItemPedidoDulceria::where('producto_dulceria_id', $dulceria->id)
            ->whereHas('pedido', function($q) {
                $q->where('estado', '!=', 'cancelado');
            })->sum('cantidad');
            
        $ingresosTotal = ItemPedidoDulceria::where('producto_dulceria_id', $dulceria->id)
            ->whereHas('pedido', function($q) {
                $q->where('estado', '!=', 'cancelado');
            })->sum('subtotal');

        return view('admin.dulceria.show', compact('dulceria', 'ventasTotal', 'ingresosTotal'));
    }

    public function edit($id)
    {
        $dulceria = ProductoDulceria::findOrFail($id);
        $categorias = CategoriaDulceria::all();
        return view('admin.dulceria.edit', compact('dulceria', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        $dulceria = ProductoDulceria::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:255|unique:productos_dulceria,nombre,' . $id,
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'categoria_dulceria_id' => 'required|exists:categorias_dulceria,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'es_combo' => 'boolean',
            'activo' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $datos = $request->only(['nombre', 'descripcion', 'precio', 'categoria_dulceria_id']);
            $datos['es_combo'] = $request->has('es_combo');
            $datos['activo'] = $request->has('activo');

            // Manejar imagen nueva
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior si existe
                if ($dulceria->imagen && Storage::disk('public')->exists($dulceria->imagen)) {
                    Storage::disk('public')->delete($dulceria->imagen);
                    Log::info('Imagen anterior eliminada', ['ruta' => $dulceria->imagen]);
                }
                
                // Subir nueva imagen
                $imagen = $request->file('imagen');
                $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
                $rutaImagen = $imagen->storeAs('dulceria', $nombreImagen, 'public');
                $datos['imagen'] = $rutaImagen;
                
                Log::info('Nueva imagen subida', ['ruta' => $rutaImagen]);
            }

            $dulceria->update($datos);
            
            DB::commit();
            
            Log::info('Producto actualizado exitosamente', ['producto_id' => $dulceria->id]);

            return redirect()->route('admin.dulceria.show', $dulceria)
                ->with('success', 'Producto actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar producto', [
                'producto_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el producto. Inténtalo de nuevo.');
        }
    }

    public function destroy($id)
    {
        $dulceria = ProductoDulceria::findOrFail($id);
        
        try {
            DB::beginTransaction();
            
            // Verificar si el producto tiene pedidos
            $tienePedidos = ItemPedidoDulceria::where('producto_dulceria_id', $dulceria->id)->exists();
            
            if ($tienePedidos) {
                return redirect()->back()
                    ->with('warning', 'No se puede eliminar el producto porque tiene pedidos asociados. Puedes desactivarlo en su lugar.');
            }

            // Eliminar imagen si existe
            if ($dulceria->imagen && Storage::disk('public')->exists($dulceria->imagen)) {
                Storage::disk('public')->delete($dulceria->imagen);
                Log::info('Imagen eliminada', ['ruta' => $dulceria->imagen]);
            }

            $dulceria->delete();
            
            DB::commit();

            return redirect()->route('admin.dulceria.index')
                ->with('success', 'Producto eliminado exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar producto', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'Error al eliminar el producto.');
        }
    }

    public function toggleStatus($id)
    {
        try {
            $dulceria = ProductoDulceria::findOrFail($id);
            $dulceria->update(['activo' => !$dulceria->activo]);
            
            $estado = $dulceria->activo ? 'activado' : 'desactivado';
            
            Log::info('Estado de producto cambiado', [
                'producto_id' => $id,
                'nuevo_estado' => $dulceria->activo
            ]);
            
            return redirect()->back()
                ->with('success', "Producto {$estado} exitosamente");
                
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado de producto', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'Error al cambiar el estado del producto.');
        }
    }

    public function pedidos(Request $request)
    {
        $query = PedidoDulceria::with(['user', 'items.producto']);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        if ($request->filled('usuario')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->usuario . '%')
                  ->orWhere('email', 'like', '%' . $request->usuario . '%');
            });
        }

        $pedidos = $query->orderBy('created_at', 'desc')->paginate(20);

        // Estadísticas
        $totalPedidos = PedidoDulceria::count();
        $pedidosHoy = PedidoDulceria::whereDate('created_at', Carbon::today())->count();
        $pedidosPendientes = PedidoDulceria::where('estado', 'confirmado')->count();
        $pedidosListos = PedidoDulceria::where('estado', 'listo')->count();
        $ingresosTotales = PedidoDulceria::where('estado', '!=', 'cancelado')->sum('monto_total');
        $ingresosHoy = PedidoDulceria::where('estado', '!=', 'cancelado')
            ->whereDate('created_at', Carbon::today())
            ->sum('monto_total');

        return view('admin.dulceria.pedidos', compact(
            'pedidos', 
            'totalPedidos', 
            'pedidosHoy', 
            'pedidosPendientes',
            'pedidosListos',
            'ingresosTotales', 
            'ingresosHoy'
        ));
    }

    public function cambiarEstadoPedido(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,confirmado,listo,entregado,cancelado'
        ]);

        try {
            DB::beginTransaction();
            
            $pedido = PedidoDulceria::findOrFail($id);
            $estadoAnterior = $pedido->estado;
            
            $pedido->update(['estado' => $request->estado]);
            
            DB::commit();
            
            Log::info('Estado de pedido cambiado', [
                'pedido_id' => $id,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $request->estado
            ]);

            // Respuesta para AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Estado del pedido #{$pedido->codigo_pedido} cambiado de '{$estadoAnterior}' a '{$request->estado}'"
                ]);
            }

            return redirect()->back()->with('success', 
                "Estado del pedido #{$pedido->codigo_pedido} cambiado de '{$estadoAnterior}' a '{$request->estado}'"
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al cambiar estado de pedido', [
                'pedido_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cambiar el estado del pedido'
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error al cambiar el estado del pedido.');
        }
    }
}