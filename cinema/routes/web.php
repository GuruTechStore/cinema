<?php
// routes/web.php - RUTAS COMPLETAS

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PeliculaController;
use App\Http\Controllers\CineController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\DulceriaController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PeliculaController as AdminPeliculaController;
use App\Http\Controllers\Admin\DulceriaController as AdminDulceriaController;
use Carbon\Carbon;
use App\Models\Pelicula;

// RUTAS PÚBLICAS

// Página principal
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/peliculas', [HomeController::class, 'peliculas'])->name('peliculas');
Route::get('/sedes', [HomeController::class, 'sedes'])->name('sedes');

// Autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Películas 
Route::get('/pelicula/{pelicula}', [PeliculaController::class, 'show'])->name('pelicula.show');
Route::get('/pelicula/{pelicula}/calendario', [PeliculaController::class, 'calendario'])->name('pelicula.calendario');

// Dulcería
Route::get('/dulceria', [DulceriaController::class, 'index'])->name('dulceria.index');
Route::post('/dulceria/agregar-carrito', [DulceriaController::class, 'agregarAlCarrito'])->name('dulceria.agregar-carrito');
Route::get('/dulceria/carrito', [DulceriaController::class, 'verCarrito'])->name('dulceria.carrito');
Route::post('/dulceria/actualizar-carrito', [DulceriaController::class, 'actualizarCarrito'])->name('dulceria.actualizar-carrito');
Route::delete('/dulceria/eliminar-carrito/{productoId}', [DulceriaController::class, 'eliminarDelCarrito'])->name('dulceria.eliminar-carrito');

// RUTAS PROTEGIDAS (REQUIEREN LOGIN)

Route::middleware(['auth'])->group(function () {
    
    // Reservas
    Route::get('/reserva/{funcion}/asientos', [ReservaController::class, 'seleccionarAsientos'])->name('reserva.asientos');
    Route::post('/reserva/{funcion}/confirmar', [ReservaController::class, 'confirmarReserva'])->name('reserva.confirmar');
    Route::post('/reserva/{funcion}/procesar', [ReservaController::class, 'procesar'])->name('reserva.procesar');
    Route::get('/reserva/{reserva}/boleta', [ReservaController::class, 'boleta'])->name('reservas.boleta');
    Route::get('/mis-reservas', [ReservaController::class, 'misReservas'])->name('reservas.mis-reservas');

    // Dulcería (checkout requiere login)
    Route::get('/dulceria/checkout', [DulceriaController::class, 'checkout'])->name('dulceria.checkout');
    Route::post('/dulceria/procesar-pedido', [DulceriaController::class, 'procesarPedido'])->name('dulceria.procesar-pedido');
    Route::get('/dulceria/{pedido}/boleta', [DulceriaController::class, 'boleta'])->name('dulceria.boleta');
    Route::get('/mis-pedidos-dulceria', [DulceriaController::class, 'misPedidos'])->name('dulceria.mis-pedidos');
});

// RUTAS DE ADMINISTRADOR

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Gestión de Películas
    Route::resource('peliculas', AdminPeliculaController::class);
    Route::get('/peliculas/{pelicula}/programar-funciones', [AdminPeliculaController::class, 'programarFunciones'])->name('peliculas.programar-funciones');
    Route::post('/peliculas/{pelicula}/programar-funciones', [AdminPeliculaController::class, 'programarFunciones']);

    // Gestión de Dulcería
    Route::resource('dulceria', AdminDulceriaController::class);
    Route::get('/dulceria-pedidos', [AdminDulceriaController::class, 'pedidos'])->name('dulceria.pedidos');
    Route::patch('/dulceria-pedidos/{pedido}/estado', [AdminDulceriaController::class, 'cambiarEstadoPedido'])->name('dulceria.cambiar-estado');
});


// Rutas públicas de cines
Route::get('/cines', [CineController::class, 'index'])->name('cines.index');
Route::get('/cine/{cine}', [CineController::class, 'show'])->name('cine.show');
Route::get('/cine/{cine}/programacion', [CineController::class, 'programacion'])->name('cine.programacion');

// RUTAS AJAX PARA DATOS DINÁMICOS

// Para obtener funciones de un cine específico
Route::get('/api/cines/{cine}/funciones', [CineController::class, 'funcionesAjax'])->name('api.cine.funciones');

// Para obtener salas de un cine específico
Route::get('/api/cines/{cine}/salas', [CineController::class, 'salasAjax'])->name('api.cine.salas');

// Para obtener películas que se proyectan en un cine
Route::get('/api/cines/{cine}/peliculas', [CineController::class, 'peliculasAjax'])->name('api.cine.peliculas');

// Para obtener horarios disponibles
Route::get('/api/cines/{cine}/horarios', [CineController::class, 'horariosDisponibles'])->name('api.cine.horarios');

Route::get('/api/cines/{cine}/informacion', [CineController::class, 'informacion'])->name('api.cine.informacion');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/api/cines/{cine}/estadisticas', [CineController::class, 'estadisticas'])->name('api.cine.estadisticas');
});

// RUTAS PARA BÚSQUEDA Y FILTROS

Route::get('/api/cines/buscar/ubicacion', [CineController::class, 'buscarPorUbicacion'])->name('api.cines.buscar-ubicacion');
Route::get('/api/peliculas/{pelicula}/funciones', function(Request $request, Pelicula $pelicula) {
    $fecha = $request->get('fecha', Carbon::today()->format('Y-m-d'));
    $ciudadId = $request->get('ciudad_id');
    $cineId = $request->get('cine_id');
    
    $query = $pelicula->funciones()->with('sala.cine.ciudad');
    
    if ($fecha) {
        $query->where('fecha_funcion', $fecha);
    }
    
    if ($ciudadId) {
        $query->whereHas('sala.cine', function($q) use ($ciudadId) {
            $q->where('ciudad_id', $ciudadId);
        });
    }
    
    if ($cineId) {
        $query->whereHas('sala', function($q) use ($cineId) {
            $q->where('cine_id', $cineId);
        });
    }
    
    $funciones = $query->orderBy('hora_funcion')->get();
    
    return response()->json($funciones);
})->name('api.pelicula.funciones');


// API para obtener funciones de una película específica
Route::get('/api/peliculas/{pelicula}/funciones', function(Request $request, Pelicula $pelicula) {
    try {
        $fecha = $request->get('fecha', Carbon::today()->format('Y-m-d'));
        $ciudadId = $request->get('ciudad_id');
        $cineId = $request->get('cine_id');
        
        $query = $pelicula->funciones()->with(['sala.cine.ciudad']);
        
        if ($fecha) {
            $query->where('fecha_funcion', $fecha);
        }
        
        if ($ciudadId) {
            $query->whereHas('sala.cine', function($q) use ($ciudadId) {
                $q->where('ciudad_id', $ciudadId);
            });
        }
        
        if ($cineId) {
            $query->whereHas('sala', function($q) use ($cineId) {
                $q->where('cine_id', $cineId);
            });
        }
        
        $funciones = $query->orderBy('hora_funcion')->get();
        
        return response()->json($funciones);
        
    } catch (\Exception $e) {
        \Log::error('Error en API funciones: ' . $e->getMessage());
        return response()->json(['error' => 'Error interno del servidor'], 500);
    }
})->name('api.pelicula.funciones');

// API para próximos estrenos
Route::get('/api/peliculas/proximos-estrenos', function() {
    try {
        $peliculas = Pelicula::where('activa', true)
            ->where('fecha_estreno', '>', Carbon::now())
            ->orderBy('fecha_estreno', 'asc')
            ->limit(6)
            ->get();
        
        return response()->json($peliculas);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al cargar próximos estrenos'], 500);
    }
})->name('api.peliculas.proximos-estrenos');

// API para obtener cines por ciudad
Route::get('/api/ciudades/{ciudadId}/cines', function($ciudadId) {
    try {
        $cines = \App\Models\Cine::where('ciudad_id', $ciudadId)
            ->select('id', 'nombre', 'direccion')
            ->orderBy('nombre')
            ->get();
        
        return response()->json($cines);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al cargar cines'], 500);
    }
})->name('api.ciudades.cines');

// Resto de rutas API para cines
Route::get('/api/cines/{cine}/funciones', [CineController::class, 'funcionesAjax'])->name('api.cine.funciones');
Route::get('/api/cines/{cine}/salas', [CineController::class, 'salasAjax'])->name('api.cine.salas');
Route::get('/api/cines/{cine}/peliculas', [CineController::class, 'peliculasAjax'])->name('api.cine.peliculas');
Route::get('/api/cines/{cine}/horarios', [CineController::class, 'horariosDisponibles'])->name('api.cine.horarios');
Route::get('/api/cines/{cine}/informacion', [CineController::class, 'informacion'])->name('api.cine.informacion');
?>