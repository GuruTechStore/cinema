<?php
// app/Http/Controllers/ReservaController.php - CORREGIDO

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Funcion;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller
{
    public function __construct()
    {
        // Método correcto para aplicar middleware en Laravel
        $this->middleware('auth');
    }

    public function seleccionarAsientos(Funcion $funcion)
    {
        $asientosOcupados = $funcion->getAsientosOcupados();
        $todosLosAsientos = $funcion->sala->generarAsientos();

        return view('reservas.seleccionar-asientos', compact('funcion', 'asientosOcupados', 'todosLosAsientos'));
    }

    public function confirmarReserva(Request $request, Funcion $funcion)
    {
        $request->validate([
            'asientos' => 'required|array|min:1',
            'asientos.*' => 'string',
        ]);

        // Verificar que los asientos estén disponibles
        $asientosOcupados = $funcion->getAsientosOcupados();
        $asientosSeleccionados = $request->asientos;

        foreach ($asientosSeleccionados as $asiento) {
            if (in_array($asiento, $asientosOcupados)) {
                return redirect()->back()->with('error', 'Uno o más asientos ya están ocupados');
            }
        }

        $totalBoletos = count($asientosSeleccionados);
        $precioTotal = ($funcion->precio * $totalBoletos) + $funcion->tarifa_servicio;

        return view('reservas.confirmar', compact('funcion', 'asientosSeleccionados', 'totalBoletos', 'precioTotal'));
    }

    public function procesar(Request $request, Funcion $funcion)
    {
        $request->validate([
            'asientos' => 'required|array|min:1',
            'metodo_pago' => 'required|in:yape,visa,mastercard',
        ]);

        $asientosSeleccionados = $request->asientos;
        $totalBoletos = count($asientosSeleccionados);
        $precioTotal = ($funcion->precio * $totalBoletos) + $funcion->tarifa_servicio;

        $reserva = Reserva::create([
            'user_id' => Auth::id(),
            'funcion_id' => $funcion->id,
            'codigo_reserva' => Reserva::generarCodigoReserva(),
            'clave_seguridad' => Reserva::generarClaveSeguridad(),
            'asientos' => $asientosSeleccionados,
            'total_boletos' => $totalBoletos,
            'precio_boleto' => $funcion->precio,
            'tarifa_servicio' => $funcion->tarifa_servicio,
            'monto_total' => $precioTotal,
            'metodo_pago' => $request->metodo_pago,
            'estado' => 'confirmada',
        ]);

        return redirect()->route('reservas.boleta', $reserva)
            ->with('success', '¡Reserva confirmada exitosamente!');
    }

    public function boleta(Reserva $reserva)
    {
        // Verificar que la reserva pertenece al usuario autenticado
        if ($reserva->user_id !== Auth::id()) {
            abort(403);
        }

        return view('reservas.boleta', compact('reserva'));
    }

    public function misReservas()
    {
        $reservas = Auth::user()->reservas()
            ->with('funcion.pelicula', 'funcion.sala.cine')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('reservas.mis-reservas', compact('reservas'));
    }
}

?>