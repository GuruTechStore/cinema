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
Route::get('/api/peliculas/{pelicula}/funciones', function(Request $request, $peliculaId) {
    try {
        // Logs iniciales
        \Log::info('=== API FUNCIONES INICIADA ===', [
            'pelicula_id' => $peliculaId,
            'request_data' => $request->all(),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);

        // 1. Buscar película
        $pelicula = \App\Models\Pelicula::find($peliculaId);
        if (!$pelicula) {
            \Log::error('Película no encontrada', ['pelicula_id' => $peliculaId]);
            return response()->json(['error' => 'Película no encontrada'], 404);
        }

        // 2. Parámetros
        $fecha = $request->get('fecha');
        $ciudadId = $request->get('ciudad_id');
        
        if (!$fecha || !$ciudadId) {
            \Log::error('Parámetros faltantes', ['fecha' => $fecha, 'ciudad_id' => $ciudadId]);
            return response()->json(['error' => 'Fecha y ciudad son requeridos'], 400);
        }

        \Log::info('Parámetros validados', [
            'pelicula' => $pelicula->titulo,
            'fecha_estreno' => $pelicula->fecha_estreno->format('Y-m-d'),
            'fecha_consulta' => $fecha,
            'ciudad_id' => $ciudadId
        ]);

        // 3. Validar fecha de estreno
        $fechaConsulta = \Carbon\Carbon::parse($fecha);
        if ($fechaConsulta->lt($pelicula->fecha_estreno)) {
            \Log::info('Fecha anterior al estreno - retornando vacío');
            return response()->json([]);
        }

        // 4. Consulta simple y directa
        $funciones = \App\Models\Funcion::select([
                'funciones.id',
                'funciones.hora_funcion', 
                'funciones.formato',
                'funciones.tipo',
                'funciones.precio',
                'funciones.tarifa_servicio',
                'salas.id as sala_id',
                'salas.nombre as sala_nombre',
                'cines.id as cine_id',
                'cines.nombre as cine_nombre',
                'cines.direccion as cine_direccion',
                'ciudades.id as ciudad_id',
                'ciudades.nombre as ciudad_nombre'
            ])
            ->join('salas', 'funciones.sala_id', '=', 'salas.id')
            ->join('cines', 'salas.cine_id', '=', 'cines.id')
            ->join('ciudades', 'cines.ciudad_id', '=', 'ciudades.id')
            ->where('funciones.pelicula_id', $pelicula->id)
            ->where('funciones.fecha_funcion', $fecha)
            ->where('ciudades.id', $ciudadId)
            ->orderBy('funciones.hora_funcion')
            ->get();

        \Log::info('Consulta ejecutada', [
            'funciones_encontradas' => $funciones->count(),
            'primera_funcion' => $funciones->first()
        ]);

        // 5. Transformar a formato esperado por el frontend
        $funcionesFormatted = $funciones->map(function($funcion) {
            return [
                'id' => $funcion->id,
                'hora_funcion' => $funcion->hora_funcion,
                'formato' => $funcion->formato,
                'tipo' => $funcion->tipo,
                'precio' => $funcion->precio,
                'tarifa_servicio' => $funcion->tarifa_servicio,
                'sala' => [
                    'id' => $funcion->sala_id,
                    'nombre' => $funcion->sala_nombre,
                    'cine' => [
                        'id' => $funcion->cine_id,
                        'nombre' => $funcion->cine_nombre,
                        'direccion' => $funcion->cine_direccion,
                        'ciudad' => [
                            'id' => $funcion->ciudad_id,
                            'nombre' => $funcion->ciudad_nombre
                        ]
                    ]
                ]
            ];
        });

        \Log::info('Respuesta preparada', [
            'count' => $funcionesFormatted->count(),
            'memoria_usada' => memory_get_usage(true)
        ]);

        return response()->json($funcionesFormatted);

    } catch (\Exception $e) {
        \Log::error('ERROR CRÍTICO EN API', [
            'pelicula_id' => $peliculaId ?? 'null',
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'request' => $request->all(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'error' => 'Error interno del servidor',
            'message' => app()->environment('local') ? $e->getMessage() : 'Error al cargar funciones',
            'debug' => [
                'pelicula_id' => $peliculaId ?? 'null',
                'line' => app()->environment('local') ? $e->getLine() : null
            ]
        ], 500);
    }
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
Route::get('/api/test/peliculas/{pelicula}/funciones', function(Request $request, $peliculaId) {
    \Log::info('=== TEST API INICIADA ===', [
        'pelicula_id' => $peliculaId,
        'request' => $request->all()
    ]);

    try {
        $pelicula = \App\Models\Pelicula::find($peliculaId);
        $fecha = $request->get('fecha');
        $ciudadId = $request->get('ciudad_id');

        if (!$pelicula || !$fecha || !$ciudadId) {
            return response()->json(['error' => 'Parámetros faltantes'], 400);
        }

        // Consulta muy simple para probar
        $funciones = \DB::table('funciones')
            ->join('salas', 'funciones.sala_id', '=', 'salas.id')
            ->join('cines', 'salas.cine_id', '=', 'cines.id')
            ->join('ciudades', 'cines.ciudad_id', '=', 'ciudades.id')
            ->select(
                'funciones.id',
                'funciones.hora_funcion',
                'funciones.formato',
                'funciones.tipo',
                'funciones.precio',
                'salas.nombre as sala_nombre',
                'cines.nombre as cine_nombre',
                'cines.direccion as cine_direccion'
            )
            ->where('funciones.pelicula_id', $peliculaId)
            ->where('funciones.fecha_funcion', $fecha)
            ->where('ciudades.id', $ciudadId)
            ->orderBy('funciones.hora_funcion')
            ->get();

        return response()->json([
            'debug' => true,
            'count' => $funciones->count(),
            'funciones' => $funciones->map(function($f) {
                return [
                    'id' => $f->id,
                    'hora_funcion' => $f->hora_funcion,
                    'formato' => $f->formato,
                    'tipo' => $f->tipo,
                    'precio' => $f->precio,
                    'sala' => [
                        'nombre' => $f->sala_nombre,
                        'cine' => [
                            'nombre' => $f->cine_nombre,
                            'direccion' => $f->cine_direccion
                        ]
                    ]
                ];
            })
        ]);

    } catch (\Exception $e) {
        \Log::error('Error en test API', [
            'error' => $e->getMessage(),
            'line' => $e->getLine()
        ]);

        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine()
        ], 500);
    }
});
