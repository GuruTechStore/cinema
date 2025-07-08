<?php
// app/Http/Controllers/Admin/AdminController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelicula;
use App\Models\Reserva;
use App\Models\ProductoDulceria;
use App\Models\PedidoDulceria;
use App\Models\Funcion;
use Carbon\Carbon;

class AdminController extends Controller
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

    public function dashboard()
    {
        // Estadísticas para el dashboard
        $peliculasActivas = Pelicula::where('activa', true)->count();
        
        $boletosVendidos = Reserva::where('estado', 'confirmada')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_boletos');

        $productosDulceria = ProductoDulceria::where('activo', true)->count();

        $ventasDelDia = Reserva::where('estado', 'confirmada')
            ->whereDate('created_at', Carbon::today())
            ->sum('monto_total') + 
            PedidoDulceria::where('estado', 'confirmado')
            ->whereDate('created_at', Carbon::today())
            ->sum('monto_total');

        // Ventas por mes (últimos 6 meses)
        $ventasPorMes = [];
        for ($i = 5; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $ventas = Reserva::where('estado', 'confirmada')
                ->whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->sum('monto_total');
            
            $ventasPorMes[] = [
                'mes' => $fecha->format('M Y'),
                'ventas' => $ventas
            ];
        }

        return view('admin.dashboard', compact(
            'peliculasActivas',
            'boletosVendidos', 
            'productosDulceria',
            'ventasDelDia',
            'ventasPorMes'
        ));
    }

    public function reservas(Request $request)
    {
        $query = Reserva::with(['user', 'funcion.pelicula', 'funcion.sala.cine']);

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

        if ($request->filled('pelicula')) {
            $query->whereHas('funcion.pelicula', function($q) use ($request) {
                $q->where('titulo', 'like', '%' . $request->pelicula . '%');
            });
        }

        if ($request->filled('usuario')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->usuario . '%')
                  ->orWhere('email', 'like', '%' . $request->usuario . '%');
            });
        }

        $reservas = $query->orderBy('created_at', 'desc')->paginate(20);

        // Estadísticas
        $totalReservas = Reserva::count();
        $reservasHoy = Reserva::whereDate('created_at', Carbon::today())->count();
        $ingresosTotales = Reserva::where('estado', 'confirmada')->sum('monto_total');
        $ingresosHoy = Reserva::where('estado', 'confirmada')
            ->whereDate('created_at', Carbon::today())
            ->sum('monto_total');

        return view('admin.reservas', compact(
            'reservas', 
            'totalReservas', 
            'reservasHoy', 
            'ingresosTotales', 
            'ingresosHoy'
        ));
    }

    public function reporteVentas(Request $request)
    {
        // Período por defecto: último mes
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->subMonth()->toDateString());
        $fechaFin = $request->get('fecha_fin', Carbon::now()->toDateString());

        // Ventas de boletos
        $ventasBoletos = Reserva::where('estado', 'confirmada')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha, SUM(monto_total) as total, COUNT(*) as cantidad')
            ->groupBy('fecha')
            ->orderBy('fecha_funcion')
            ->get();

        // Ventas de dulcería
        $ventasDulceria = PedidoDulceria::where('estado', 'confirmado')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha, SUM(monto_total) as total, COUNT(*) as cantidad')
            ->groupBy('fecha')
            ->orderBy('fecha_funcion')
            ->get();

        // Películas más vendidas
        $peliculasMasVendidas = Reserva::where('estado', 'confirmada')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->with('funcion.pelicula')
            ->selectRaw('COUNT(*) as total_reservas, SUM(total_boletos) as total_boletos, SUM(monto_total) as ingresos')
            ->addSelect('funcion_id')
            ->groupBy('funcion_id')
            ->having('total_reservas', '>', 0)
            ->orderBy('total_reservas', 'desc')
            ->limit(10)
            ->get();

        // Productos de dulcería más vendidos
        $productosMasVendidos = PedidoDulceria::where('estado', 'confirmado')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->with('items.producto')
            ->get()
            ->flatMap(function($pedido) {
                return $pedido->items;
            })
            ->groupBy('producto_dulceria_id')
            ->map(function($items) {
                return [
                    'producto' => $items->first()->producto,
                    'cantidad' => $items->sum('cantidad'),
                    'ingresos' => $items->sum('subtotal')
                ];
            })
            ->sortByDesc('cantidad')
            ->take(10);

        // Resumen general
        $resumen = [
            'total_boletos' => $ventasBoletos->sum('total'),
            'total_dulceria' => $ventasDulceria->sum('total'),
            'total_general' => $ventasBoletos->sum('total') + $ventasDulceria->sum('total'),
            'reservas_count' => $ventasBoletos->sum('cantidad'),
            'pedidos_count' => $ventasDulceria->sum('cantidad'),
        ];

        return view('admin.ventas', compact(
            'ventasBoletos',
            'ventasDulceria', 
            'peliculasMasVendidas',
            'productosMasVendidos',
            'resumen',
            'fechaInicio',
            'fechaFin'
        ));
    }
    public function getSalas($cineId)
{
    try {
        $cine = \App\Models\Cine::findOrFail($cineId);
        $salas = $cine->salas()->select('id', 'nombre', 'total_asientos')->get();
        
        $salasFormateadas = $salas->map(function($sala) {
            return [
                'id' => $sala->id,
                'nombre' => $sala->nombre,
                'capacidad' => $sala->total_asientos
            ];
        });
        
        return response()->json($salasFormateadas);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al cargar salas'], 500);
    }
}
}