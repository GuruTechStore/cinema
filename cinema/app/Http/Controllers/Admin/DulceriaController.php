<?php
// app/Http/Controllers/Admin/DulceriaController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductoDulceria;
use App\Models\CategoriaDulceria;
use App\Models\PedidoDulceria;

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
        $productos = ProductoDulceria::with('categoria')->paginate(10);
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
        ]);

        $datos = $request->all();
        $datos['es_combo'] = $request->has('es_combo');

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('dulceria', 'public');
        }

        ProductoDulceria::create($datos);

        return redirect()->route('admin.dulceria.index')
            ->with('success', 'Producto creado exitosamente');
    }

    public function show(ProductoDulceria $dulceria)
    {
        return view('admin.dulceria.show', compact('dulceria'));
    }

    public function edit(ProductoDulceria $dulceria)
    {
        $categorias = CategoriaDulceria::all();
        return view('admin.dulceria.edit', compact('dulceria', 'categorias'));
    }

    public function update(Request $request, ProductoDulceria $dulceria)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'categoria_dulceria_id' => 'required|exists:categorias_dulceria,id',
            'imagen' => 'nullable|image|max:2048',
            'es_combo' => 'boolean',
        ]);

        $datos = $request->all();
        $datos['es_combo'] = $request->has('es_combo');

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('dulceria', 'public');
        }

        $dulceria->update($datos);

        return redirect()->route('admin.dulceria.index')
            ->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy(ProductoDulceria $dulceria)
    {
        $dulceria->delete();
        return redirect()->route('admin.dulceria.index')
            ->with('success', 'Producto eliminado exitosamente');
    }

    public function pedidos()
    {
        $pedidos = PedidoDulceria::with('user', 'items.producto')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.dulceria.pedidos', compact('pedidos'));
    }

    public function cambiarEstadoPedido(Request $request, PedidoDulceria $pedido)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,confirmado,listo,entregado'
        ]);

        $pedido->update(['estado' => $request->estado]);

        return redirect()->back()->with('success', 'Estado del pedido actualizado');
    }
}

?>
