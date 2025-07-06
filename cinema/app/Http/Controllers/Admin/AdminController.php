<?php
// app/Http/Controllers/Admin/AdminController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelicula;
use App\Models\Reserva;
use App\Models\ProductoDulceria;
use App\Models\PedidoDulceria;
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
}

?>
