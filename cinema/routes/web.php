<?php

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
use App\Models\Pelicula;
use App\Models\Funcion;
use Carbon\Carbon;
use Illuminate\Http\Request;

// ========================================
// RUTAS PÚBLICAS
// ========================================

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

// Películas (público)
Route::get('/pelicula/{pelicula}', [PeliculaController::class, 'show'])->name('pelicula.show');
Route::get('/pelicula/{pelicula}/calendario', [PeliculaController::class, 'calendario'])->name('pelicula.calendario');

// Cines (público)
Route::get('/cines', [CineController::class, 'index'])->name('cines.index');
Route::get('/cine/{cine}', [CineController::class, 'show'])->name('cine.show');
Route::get('/cine/{cine}/programacion', [CineController::class, 'programacion'])->name('cine.programacion');

// ========================================
// DULCERÍA - RUTAS PÚBLICAS (NO REQUIEREN LOGIN)
// ========================================

Route::get('/dulceria', [DulceriaController::class, 'index'])->name('dulceria.index');
Route::post('/dulceria/agregar-carrito', [DulceriaController::class, 'agregarCarrito'])->name('dulceria.agregar-carrito');
Route::get('/dulceria/carrito', [DulceriaController::class, 'carrito'])->name('dulceria.carrito');
Route::post('/dulceria/actualizar-carrito', [DulceriaController::class, 'actualizarCarrito'])->name('dulceria.actualizar-carrito');
Route::get('/dulceria/eliminar-carrito/{producto}', [DulceriaController::class, 'eliminarCarrito'])->name('dulceria.eliminar-carrito');

// ========================================
// RUTAS API PÚBLICAS
// ========================================

// API para obtener funciones de una película específica
Route::get('/api/peliculas/{pelicula}/funciones', function(Request $request, $peliculaId) {
    try {
        \Log::info('=== API FUNCIONES INICIADA ===', [
            'pelicula_id' => $peliculaId,
            'request_data' => $request->all(),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);

        // 1. Verificar que la película existe
        $pelicula = \App\Models\Pelicula::find($peliculaId);
        if (!$pelicula) {
            \Log::error('Película no encontrada', ['pelicula_id' => $peliculaId]);
            return response()->json(['error' => 'Película no encontrada'], 404);
        }

        if (!$pelicula->activa) {
            return response()->json(['error' => 'Película no activa'], 404);
        }

        // 2. Obtener parámetros
        $fecha = $request->get('fecha', \Carbon\Carbon::today()->format('Y-m-d'));
        $ciudadId = $request->get('ciudad_id');
        $cineId = $request->get('cine_id');

        \Log::info('Parámetros de búsqueda', [
            'fecha' => $fecha,
            'ciudad_id' => $ciudadId,
            'cine_id' => $cineId
        ]);

        // 3. Usar Query Builder directo para mayor control
        $query = \DB::table('funciones')
            ->join('salas', 'funciones.sala_id', '=', 'salas.id')
            ->join('cines', 'salas.cine_id', '=', 'cines.id')
            ->join('ciudades', 'cines.ciudad_id', '=', 'ciudades.id')
            ->select(
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
            )
            ->where('funciones.pelicula_id', $peliculaId)
            ->where('funciones.fecha_funcion', $fecha);

        // 4. Aplicar filtros
        if ($ciudadId) {
            $query->where('ciudades.id', $ciudadId);
        }

        if ($cineId) {
            $query->where('cines.id', $cineId);
        }

        // 5. Obtener resultados
        $funciones = $query->orderBy('funciones.hora_funcion')->get();

        \Log::info('Funciones encontradas', [
            'count' => $funciones->count(),
            'primera_funcion' => $funciones->first() ? [
                'id' => $funciones->first()->id,
                'hora' => $funciones->first()->hora_funcion,
                'cine' => $funciones->first()->cine_nombre
            ] : null
        ]);

        // 6. Formatear para el frontend
        $funcionesFormatted = $funciones->map(function($funcion) {
            return [
                'id' => $funcion->id,
                'hora_funcion' => $funcion->hora_funcion,
                'formato' => $funcion->formato,
                'tipo' => $funcion->tipo,
                'precio' => (float) $funcion->precio,
                'tarifa_servicio' => (float) $funcion->tarifa_servicio,
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
            'debug_info' => app()->environment('local') ? [
                'pelicula_id' => $peliculaId ?? 'null',
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ] : null
        ], 500);
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

// APIs de cines
Route::get('/api/cines/{cine}/funciones', [CineController::class, 'funcionesAjax'])->name('api.cine.funciones');
Route::get('/api/cines/{cine}/salas', [CineController::class, 'salasAjax'])->name('api.cine.salas');
Route::get('/api/cines/{cine}/informacion', [CineController::class, 'informacion'])->name('api.cine.informacion');

// ========================================
// RUTAS PROTEGIDAS (REQUIEREN LOGIN)
// ========================================

Route::middleware(['auth'])->group(function () {
    
    // ===== RESERVAS - TODAS REQUIEREN LOGIN =====
    Route::get('/reserva/{funcion}/asientos', [ReservaController::class, 'seleccionarAsientos'])->name('reserva.asientos');
    Route::post('/reserva/{funcion}/confirmar', [ReservaController::class, 'confirmarReserva'])->name('reserva.confirmar');
    Route::post('/reserva/{funcion}/procesar', [ReservaController::class, 'procesar'])->name('reserva.procesar');
    Route::get('/reserva/{reserva}/boleta', [ReservaController::class, 'boleta'])->name('reservas.boleta');
    Route::get('/mis-reservas', [ReservaController::class, 'misReservas'])->name('reservas.mis-reservas');
    Route::get('/reserva/{funcion}/cine', [ReservaController::class, 'seleccionarAsientos'])->name('reservas.seleccionar-asientos');


    // ===== DULCERÍA - CHECKOUT Y PEDIDOS REQUIEREN LOGIN =====
    Route::get('/dulceria/checkout', [DulceriaController::class, 'checkout'])->name('dulceria.checkout');
    Route::post('/dulceria/procesar-pedido', [DulceriaController::class, 'procesarPedido'])->name('dulceria.procesar-pedido');
    Route::get('/dulceria/{pedido}/boleta', [DulceriaController::class, 'boleta'])->name('dulceria.boleta');
    Route::get('/mis-pedidos-dulceria', [DulceriaController::class, 'misPedidos'])->name('dulceria.mis-pedidos');
    Route::get('/dulceria/carrito-info', [DulceriaController::class, 'carritoInfo'])->name('dulceria.carrito-info');
});

// ========================================
// RUTAS DE ADMINISTRADOR
// ========================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // ===== GESTIÓN DE PELÍCULAS =====
    Route::resource('peliculas', AdminPeliculaController::class);
    Route::post('peliculas/{pelicula}/toggle-status', [AdminPeliculaController::class, 'toggleStatus'])->name('peliculas.toggle-status');
    Route::get('/peliculas/{pelicula}/programar-funciones', [AdminPeliculaController::class, 'programarFunciones'])->name('peliculas.programar-funciones');
    Route::post('/peliculas/{pelicula}/programar-funciones', [AdminPeliculaController::class, 'guardarFunciones']);
    Route::post('peliculas/{pelicula}/toggle-status', [AdminPeliculaController::class, 'toggleStatus'])
        ->name('peliculas.toggle-status');
    
    Route::post('peliculas/{pelicula}/duplicate', [AdminPeliculaController::class, 'duplicate'])
        ->name('peliculas.duplicate');
    
    Route::get('peliculas/{pelicula}/reporte', [AdminPeliculaController::class, 'reporte'])
        ->name('peliculas.reporte');
        // Programación de funciones
    Route::get('peliculas/{pelicula}/programar-funciones', [AdminPeliculaController::class, 'programarFunciones'])
        ->name('peliculas.programar-funciones');
    
    Route::post('peliculas/{pelicula}/programar-funciones', [AdminPeliculaController::class, 'guardarFunciones']);
    
    // Programación masiva
    Route::get('programacion-masiva', [AdminPeliculaController::class, 'programacionMasiva'])
        ->name('peliculas.programacion-masiva');
    
    Route::post('programacion-masiva', [AdminPeliculaController::class, 'programacionMasiva']);
    
    // ===== GESTIÓN DE DULCERÍA =====
    Route::resource('dulceria', AdminDulceriaController::class);
    Route::post('dulceria/{producto}/toggle-status', [AdminDulceriaController::class, 'toggleStatus'])->name('dulceria.toggle-status');
    
    // ===== GESTIÓN DE PEDIDOS DE DULCERÍA =====
    Route::get('/dulceria-pedidos', [AdminDulceriaController::class, 'pedidos'])->name('dulceria.pedidos');
    Route::patch('/dulceria-pedidos/{pedido}/estado', [AdminDulceriaController::class, 'cambiarEstadoPedido'])->name('dulceria.cambiar-estado');
    
    // ===== GESTIÓN DE FUNCIONES =====
    Route::post('funciones', [\App\Http\Controllers\Admin\FuncionController::class, 'store'])->name('funciones.store');
    Route::put('funciones/{funcion}', [\App\Http\Controllers\Admin\FuncionController::class, 'update'])->name('funciones.update');
    Route::delete('funciones/{funcion}', [\App\Http\Controllers\Admin\FuncionController::class, 'destroy'])->name('funciones.destroy');
    Route::post('funciones/store-multiple', [\App\Http\Controllers\Admin\FuncionController::class, 'storeMultiple'])->name('funciones.store-multiple');
    
    // ===== APIs ADMIN =====
    Route::get('cines/{cine}/salas', [AdminController::class, 'getSalas'])->name('cines.salas');
    
    // ===== REPORTES Y ESTADÍSTICAS =====
    Route::get('/ventas', [AdminController::class, 'reporteVentas'])->name('ventas');
    Route::get('/reservas', [AdminController::class, 'reservas'])->name('reservas');
    Route::get('/api/cines/{cine}/estadisticas', [CineController::class, 'estadisticas'])->name('api.cine.estadisticas');
});

// ========================================
// RUTAS DE DEBUG (SOLO EN DESARROLLO)
// ========================================

if (app()->environment('local', 'testing')) {
    
    Route::get('/debug/pelicula/{id}/funciones', function($id) {
        try {
            \Log::info('=== DEBUG INICIADO ===', ['pelicula_id' => $id]);
            
            // 1. Verificar que la película existe
            $pelicula = \App\Models\Pelicula::find($id);
            if (!$pelicula) {
                return response()->json(['error' => 'Película no encontrada'], 404);
            }
            
            \Log::info('Película encontrada', [
                'id' => $pelicula->id,
                'titulo' => $pelicula->titulo,
                'activa' => $pelicula->activa
            ]);
            
            // 2. Verificar funciones básicas
            $funcionesCount = \DB::table('funciones')
                ->where('pelicula_id', $id)
                ->count();
            
            \Log::info('Funciones en DB', ['count' => $funcionesCount]);
            
            // 3. Probar la relación Eloquent
            $funcionesEloquent = $pelicula->funciones()->count();
            
            \Log::info('Funciones vía Eloquent', ['count' => $funcionesEloquent]);
            
            // 4. Obtener una función simple
            $primeraFuncion = \DB::table('funciones')
                ->where('pelicula_id', $id)
                ->first();
            
            return response()->json([
                'debug' => true,
                'pelicula' => [
                    'id' => $pelicula->id,
                    'titulo' => $pelicula->titulo,
                    'activa' => $pelicula->activa
                ],
                'funciones_count_db' => $funcionesCount,
                'funciones_count_eloquent' => $funcionesEloquent,
                'primera_funcion' => $primeraFuncion,
                'status' => 'ok'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error en debug', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
    });

    // AGREGA esta ruta temporal para debug detallado
    Route::get('/debug/funciones-detallado/{peliculaId}', function(Request $request, $peliculaId) {
        try {
            $fecha = $request->get('fecha', '2025-07-10');
            $ciudadId = $request->get('ciudad_id', 8);
            
            $debug = [];
            
            // 1. Verificar película
            $pelicula = \App\Models\Pelicula::find($peliculaId);
            $debug['pelicula'] = $pelicula ? [
                'id' => $pelicula->id,
                'titulo' => $pelicula->titulo,
                'activa' => $pelicula->activa
            ] : null;
            
            // 2. Total de funciones de esta película
            $totalFunciones = \DB::table('funciones')
                ->where('pelicula_id', $peliculaId)
                ->count();
            $debug['total_funciones_pelicula'] = $totalFunciones;
            
            // 3. Funciones por fecha
            $funcionesPorFecha = \DB::table('funciones')
                ->where('pelicula_id', $peliculaId)
                ->selectRaw('fecha_funcion, COUNT(*) as count')
                ->groupBy('fecha_funcion')
                ->orderBy('fecha_funcion')
                ->get();
            $debug['funciones_por_fecha'] = $funcionesPorFecha;
            
            // 4. Verificar si existe la fecha específica
            $funcionesFechaEspecifica = \DB::table('funciones')
                ->where('pelicula_id', $peliculaId)
                ->where('fecha_funcion', $fecha)
                ->count();
            $debug['funciones_fecha_especifica'] = [
                'fecha' => $fecha,
                'count' => $funcionesFechaEspecifica
            ];
            
            // 5. Si hay funciones en esa fecha, verificar ciudades
            if ($funcionesFechaEspecifica > 0) {
                $ciudadesFunciones = \DB::table('funciones')
                    ->join('salas', 'funciones.sala_id', '=', 'salas.id')
                    ->join('cines', 'salas.cine_id', '=', 'cines.id')
                    ->join('ciudades', 'cines.ciudad_id', '=', 'ciudades.id')
                    ->where('funciones.pelicula_id', $peliculaId)
                    ->where('funciones.fecha_funcion', $fecha)
                    ->selectRaw('ciudades.id, ciudades.nombre, COUNT(*) as count')
                    ->groupBy('ciudades.id', 'ciudades.nombre')
                    ->get();
                $debug['ciudades_con_funciones'] = $ciudadesFunciones;
            }
            
            // 6. Verificar la consulta completa con todos los filtros
            $funcionesCompleta = \DB::table('funciones')
                ->join('salas', 'funciones.sala_id', '=', 'salas.id')
                ->join('cines', 'salas.cine_id', '=', 'cines.id')
                ->join('ciudades', 'cines.ciudad_id', '=', 'ciudades.id')
                ->where('funciones.pelicula_id', $peliculaId)
                ->where('funciones.fecha_funcion', $fecha)
                ->where('ciudades.id', $ciudadId)
                ->count();
            $debug['funciones_con_todos_filtros'] = $funcionesCompleta;
            
            // 7. Si no hay resultados, verificar qué ciudades SÍ tienen funciones
            if ($funcionesCompleta === 0) {
                $ciudadesDisponibles = \DB::table('funciones')
                    ->join('salas', 'funciones.sala_id', '=', 'salas.id')
                    ->join('cines', 'salas.cine_id', '=', 'cines.id')
                    ->join('ciudades', 'cines.ciudad_id', '=', 'ciudades.id')
                    ->where('funciones.pelicula_id', $peliculaId)
                    ->where('funciones.fecha_funcion', $fecha)
                    ->select('ciudades.id', 'ciudades.nombre')
                    ->distinct()
                    ->get();
                $debug['ciudades_disponibles_para_fecha'] = $ciudadesDisponibles;
            }
            
            // 8. Mostrar algunas funciones de ejemplo (sin filtros de ciudad)
            $funcionesEjemplo = \DB::table('funciones')
                ->join('salas', 'funciones.sala_id', '=', 'salas.id')
                ->join('cines', 'salas.cine_id', '=', 'cines.id')
                ->join('ciudades', 'cines.ciudad_id', '=', 'ciudades.id')
                ->where('funciones.pelicula_id', $peliculaId)
                ->where('funciones.fecha_funcion', $fecha)
                ->select(
                    'funciones.id',
                    'funciones.hora_funcion',
                    'cines.nombre as cine_nombre',
                    'ciudades.id as ciudad_id',
                    'ciudades.nombre as ciudad_nombre'
                )
                ->limit(5)
                ->get();
            $debug['funciones_ejemplo'] = $funcionesEjemplo;
            
            return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
    });

    Route::get('/debug-asientos', function() {
        return view('debug.asientos');
    });

    // ===== RUTAS DE DEBUG PARA STORAGE Y DULCERÍA =====
    Route::get('/debug/storage-info', function() {
        return response()->json([
            'storage_path' => storage_path('app/public'),
            'public_path' => public_path('storage'),
            'storage_link_exists' => is_link(public_path('storage')),
            'directories' => [
                'dulceria' => \Storage::disk('public')->exists('dulceria'),
                'peliculas' => \Storage::disk('public')->exists('peliculas'),
                'cines' => \Storage::disk('public')->exists('cines'),
            ],
            'files_in_dulceria' => \Storage::disk('public')->files('dulceria'),
            'storage_info' => getStorageInfo()
        ]);
    });
    
    Route::get('/debug/fix-storage', function() {
        try {
            // Crear enlace simbólico si no existe
            if (!is_link(public_path('storage'))) {
                \Artisan::call('storage:link');
            }
            
            // Asegurar que existan los directorios necesarios
            ensureStorageDirectories();
            
            // Limpiar cache
            \Artisan::call('config:clear');
            \Artisan::call('cache:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'Storage configurado correctamente',
                'storage_link' => is_link(public_path('storage')),
                'directories_created' => ['dulceria', 'peliculas', 'cines', 'usuarios']
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    });

    Route::get('/debug/test-image-upload', function() {
        try {
            // Crear una imagen de prueba
            $testImage = imagecreate(200, 200);
            $white = imagecolorallocate($testImage, 255, 255, 255);
            $black = imagecolorallocate($testImage, 0, 0, 0);
            imagestring($testImage, 5, 50, 90, 'TEST', $black);
            
            $tempPath = sys_get_temp_dir() . '/test_image.png';
            imagepng($testImage, $tempPath);
            imagedestroy($testImage);
            
            // Simular upload
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempPath,
                'test_image.png',
                'image/png',
                null,
                true
            );
            
            // Usar función helper
            $path = optimizeImage($uploadedFile, 'dulceria');
            
            return response()->json([
                'success' => true,
                'path' => $path,
                'full_url' => asset('storage/' . $path),
                'file_exists' => \Storage::disk('public')->exists($path)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    });
}
?>