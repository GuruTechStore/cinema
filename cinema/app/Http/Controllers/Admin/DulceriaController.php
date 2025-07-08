<?php
// app/Http/Controllers/Admin/DulceriaController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductoDulceria;
use App\Models\CategoriaDulceria;
use App\Models\PedidoDulceria;
use App\Models\ItemPedidoDulceria;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DulceriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->esAdmin()) {
                abort(403, 'Acceso denegado');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $productos = ProductoDulceria::with('categoria')->paginate(15);
        $categorias = CategoriaDulceria::all();
        return view('admin.dulceria.index', compact('productos', 'categorias'));
    }

    public function create()
    {
        $categorias = CategoriaDulceria::all();
        return view('admin.dulceria.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'categoria_dulceria_id' => 'required|exists:categorias_dulceria,id',
            'imagen' => 'nullable|image|max:2048',
            'es_combo' => 'boolean',
            'activo' => 'boolean',
        ]);

        $datos = $request->all();
        $datos['es_combo'] = $request->has('es_combo');
        $datos['activo'] = $request->has('activo');

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('dulceria', 'public');
        }

        ProductoDulceria::create($datos);

        return redirect()->route('admin.dulceria.index')
            ->with('success', 'Producto creado exitosamente');
    }

    public function show($id)
    {
        $dulceria = ProductoDulceria::with('categoria')->findOrFail($id);
        
        // Cargar estadísticas del producto
        $totalVendido = ItemPedidoDulceria::where('producto_dulceria_id', $dulceria->id)
            ->whereHas('pedido', function($query) {
                $query->where('estado', 'confirmado');
            })
            ->sum('cantidad');

        $ingresosTotales = ItemPedidoDulceria::where('producto_dulceria_id', $dulceria->id)
            ->whereHas('pedido', function($query) {
                $query->where('estado', 'confirmado');
            })
            ->sum('subtotal');

        $ventasEsteMes = ItemPedidoDulceria::where('producto_dulceria_id', $dulceria->id)
            ->whereHas('pedido', function($query) {
                $query->where('estado', 'confirmado')
                      ->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
            })
            ->sum('cantidad');

        $ventasHoy = ItemPedidoDulceria::where('producto_dulceria_id', $dulceria->id)
            ->whereHas('pedido', function($query) {
                $query->where('estado', 'confirmado')
                      ->whereDate('created_at', Carbon::today());
            })
            ->sum('cantidad');

        // Últimos pedidos
        $ultimosPedidos = ItemPedidoDulceria::where('producto_dulceria_id', $dulceria->id)
            ->with(['pedido' => function($query) {
                $query->with('user');
            }])
            ->whereHas('pedido', function($query) {
                $query->where('estado', 'confirmado');
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dulceria.show', compact(
            'dulceria', 
            'totalVendido', 
            'ingresosTotales', 
            'ventasEsteMes', 
            'ventasHoy',
            'ultimosPedidos'
        ));
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
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'categoria_dulceria_id' => 'required|exists:categorias_dulceria,id',
            'imagen' => 'nullable|image|max:2048',
            'es_combo' => 'boolean',
            'activo' => 'boolean',
        ]);

        $datos = $request->all();
        $datos['es_combo'] = $request->has('es_combo');
        $datos['activo'] = $request->has('activo');

        // Manejar imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($dulceria->imagen) {
                Storage::disk('public')->delete($dulceria->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('dulceria', 'public');
        }

        $dulceria->update($datos);

        return redirect()->route('admin.dulceria.show', $dulceria)
            ->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy($id)
    {
        $dulceria = ProductoDulceria::findOrFail($id);
        
        // Verificar si el producto tiene pedidos
        $tienePedidos = ItemPedidoDulceria::where('producto_dulceria_id', $dulceria->id)->exists();
        
        if ($tienePedidos) {
            return redirect()->back()
                ->with('warning', 'No se puede eliminar el producto porque tiene pedidos asociados. Puedes desactivarlo en su lugar.');
        }

        // Eliminar imagen si existe
        if ($dulceria->imagen) {
            Storage::disk('public')->delete($dulceria->imagen);
        }

        $dulceria->delete();

        return redirect()->route('admin.dulceria.index')
            ->with('success', 'Producto eliminado exitosamente');
    }

    public function toggleStatus($id)
    {
        $dulceria = ProductoDulceria::findOrFail($id);
        $dulceria->update(['activo' => !$dulceria->activo]);
        
        $estado = $dulceria->activo ? 'activado' : 'desactivado';
        
        return redirect()->back()
            ->with('success', "Producto {$estado} exitosamente");
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
        $pedido = PedidoDulceria::findOrFail($id);
        $request->validate([
            'estado' => 'required|in:pendiente,confirmado,listo,entregado'
        ]);

        $estadoAnterior = $pedido->estado;
        $pedido->update(['estado' => $request->estado]);

        return redirect()->back()->with('success', 
            "Estado del pedido #{$pedido->codigo_pedido} cambiado de '{$estadoAnterior}' a '{$request->estado}'"
        );
    }
}