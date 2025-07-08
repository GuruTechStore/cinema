{{-- resources/views/admin/peliculas/index.blade.php--}}
@extends('layouts.admin')

@section('title', 'Gestión de Películas')
@section('page-title', 'Películas')

@section('breadcrumb')
<li class="breadcrumb-item active">Películas</li>
@endsection

@section('content')
    <!-- Header con Título y Botones -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Gestión de Películas</h2>
            <p class="text-muted mb-0">Administra el catálogo de películas del cinema</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.peliculas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nueva Película
            </a>
            <a href="{{ route('admin.peliculas.programacion-masiva') }}" class="btn btn-success">
                <i class="fas fa-rocket me-2"></i>Programación Masiva
            </a>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-2"></i>Exportar
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-primary text-white rounded-circle me-3">
                            <i class="fas fa-film"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">{{ $peliculas->total() }}</h3>
                            <small class="text-muted">Total Películas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-success text-white rounded-circle me-3">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">{{ \App\Models\Pelicula::where('activa', true)->count() }}</h3>
                            <small class="text-muted">Activas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-warning text-white rounded-circle me-3">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">{{ \App\Models\Pelicula::where('destacada', true)->count() }}</h3>
                            <small class="text-muted">Destacadas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-info text-white rounded-circle me-3">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">{{ \App\Models\Funcion::whereDate('fecha_funcion', '>=', today())->distinct('pelicula_id')->count() }}</h3>
                            <small class="text-muted">En Cartelera</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros Avanzados -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>Filtros
                </h6>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosAvanzados">
                    <i class="fas fa-chevron-down me-1"></i>Avanzados
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <!-- Filtros Básicos -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Buscar</label>
                    <input type="text" class="form-control" name="buscar" 
                           placeholder="Título, director, género..." 
                           value="{{ request('buscar') }}">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold">Estado</label>
                    <select class="form-select" name="estado">
                        <option value="">Todos</option>
                        <option value="activa" {{ request('estado') == 'activa' ? 'selected' : '' }}>Activas</option>
                        <option value="inactiva" {{ request('estado') == 'inactiva' ? 'selected' : '' }}>Inactivas</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Género</label>
                    <select class="form-select" name="genero">
                        <option value="">Todos los géneros</option>
                        @if(isset($generos))
                            @foreach($generos as $genero)
                                <option value="{{ $genero }}" {{ request('genero') == $genero ? 'selected' : '' }}>
                                    {{ $genero }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Filtrar
                        </button>
                    </div>
                </div>

                <!-- Filtros Avanzados (Colapsables) -->
                <div class="collapse col-12" id="filtrosAvanzados">
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Fecha Estreno (Desde)</label>
                            <input type="date" class="form-control" name="fecha_desde" value="{{ request('fecha_desde') }}">
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Fecha Estreno (Hasta)</label>
                            <input type="date" class="form-control" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Ordenar por</label>
                            <select class="form-select" name="orden">
                                <option value="created_at" {{ request('orden') == 'created_at' ? 'selected' : '' }}>Fecha de creación</option>
                                <option value="titulo" {{ request('orden') == 'titulo' ? 'selected' : '' }}>Título</option>
                                <option value="fecha_estreno" {{ request('orden') == 'fecha_estreno' ? 'selected' : '' }}>Fecha de estreno</option>
                                <option value="director" {{ request('orden') == 'director' ? 'selected' : '' }}>Director</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Dirección</label>
                            <select class="form-select" name="direccion">
                                <option value="desc" {{ request('direccion') == 'desc' ? 'selected' : '' }}>Descendente</option>
                                <option value="asc" {{ request('direccion') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>

            @if(request()->hasAny(['buscar', 'estado', 'genero', 'fecha_desde', 'fecha_hasta']))
            <div class="mt-3">
                <a href="{{ route('admin.peliculas.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-times me-1"></i>Limpiar Filtros
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Lista de Películas -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    Lista de Películas ({{ $peliculas->total() }} resultados)
                </h6>
                <div class="d-flex gap-3 align-items-center">
                    <!-- Botón Nueva Película adicional -->
                    <a href="{{ route('admin.peliculas.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Agregar Película
                    </a>
                    
                    <!-- Selector de vista -->
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary active" data-view="grid" title="Vista en cuadrícula">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="btn btn-outline-secondary" data-view="list" title="Vista en lista">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($peliculas->count() > 0)
                <!-- Vista Grid (Por defecto) -->
                <div id="gridView" class="row g-4">
                    @foreach($peliculas as $pelicula)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card movie-card h-100 shadow-sm border-0">
                            <div class="position-relative">
                                <img src="{{ getPosterUrl($pelicula->poster) }}" 
                                     alt="{{ $pelicula->titulo }}" 
                                     class="card-img-top movie-poster">
                                
                                <!-- Badges de estado -->
                                <div class="position-absolute top-0 start-0 p-2">
                                    @if($pelicula->activa)
                                        <span class="badge bg-success">Activa</span>
                                    @else
                                        <span class="badge bg-secondary">Inactiva</span>
                                    @endif
                                </div>
                                
                                <div class="position-absolute top-0 end-0 p-2">
                                    <span class="badge bg-dark">{{ $pelicula->clasificacion }}</span>
                                    @if($pelicula->destacada)
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-star"></i>
                                        </span>
                                    @endif
                                </div>

                                <!-- Overlay con acciones -->
                                <div class="position-absolute bottom-0 start-0 end-0 p-2 movie-overlay">
                                    <div class="d-grid gap-1">
                                        <a href="{{ route('admin.peliculas.show', $pelicula) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Ver Detalles
                                        </a>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.peliculas.edit', $pelicula) }}" 
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit me-1"></i>Editar
                                            </a>
                                            <a href="{{ route('admin.peliculas.programar-funciones', $pelicula) }}" 
                                               class="btn btn-success btn-sm">
                                                <i class="fas fa-calendar-plus me-1"></i>Funciones
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <h6 class="card-title mb-2">{{ Str::limit($pelicula->titulo, 30) }}</h6>
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-user me-1"></i>{{ $pelicula->director }}
                                </p>
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-calendar me-1"></i>{{ $pelicula->fecha_estreno->format('d/m/Y') }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ $pelicula->funciones()->count() }} funciones</small>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                onclick="toggleStatus({{ $pelicula->id }})" 
                                                title="Activar/Desactivar">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Vista Lista (Oculta por defecto) -->
                <div id="listView" class="d-none">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Película</th>
                                    <th>Director</th>
                                    <th>Género</th>
                                    <th>Estreno</th>
                                    <th>Estado</th>
                                    <th>Funciones</th>
                                    <th width="150">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($peliculas as $pelicula)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ getPosterUrl($pelicula->poster) }}" 
                                                 alt="{{ $pelicula->titulo }}" 
                                                 class="rounded me-2" 
                                                 style="width: 40px; height: 60px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0">{{ $pelicula->titulo }}</h6>
                                                <small class="text-muted">{{ $pelicula->clasificacion }}</small>
                                                @if($pelicula->destacada)
                                                    <i class="fas fa-star text-warning ms-1"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $pelicula->director }}</td>
                                    <td>{{ Str::limit($pelicula->genero, 30) }}</td>
                                    <td>{{ $pelicula->fecha_estreno->format('d/m/Y') }}</td>
                                    <td>
                                        @if($pelicula->activa)
                                            <span class="badge bg-success">Activa</span>
                                        @else
                                            <span class="badge bg-secondary">Inactiva</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $pelicula->funciones()->count() }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.peliculas.show', $pelicula) }}" 
                                               class="btn btn-outline-primary" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.peliculas.edit', $pelicula) }}" 
                                               class="btn btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.peliculas.programar-funciones', $pelicula) }}" 
                                               class="btn btn-outline-success" title="Programar funciones">
                                                <i class="fas fa-calendar-plus"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Paginación -->
                @if($peliculas->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $peliculas->appends(request()->query())->links() }}
                </div>
                @endif

            @else
                <!-- Estado vacío -->
                <div class="text-center py-5">
                    <i class="fas fa-film display-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No hay películas</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['buscar', 'estado', 'genero']))
                            No se encontraron películas con los filtros aplicados.
                        @else
                            Aún no has agregado ninguna película al sistema.
                        @endif
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        @if(request()->hasAny(['buscar', 'estado', 'genero']))
                            <a href="{{ route('admin.peliculas.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-times me-1"></i>Limpiar Filtros
                            </a>
                        @endif
                        <a href="{{ route('admin.peliculas.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Agregar Primera Película
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
<style>
.stats-card {
    transition: transform 0.2s;
    border-radius: 12px;
}

.stats-card:hover {
    transform: translateY(-2px);
}

.stats-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.movie-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
}

.movie-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.movie-poster {
    height: 250px;
    object-fit: cover;
}

.movie-overlay {
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.movie-card:hover .movie-overlay {
    opacity: 1;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.card {
    border-radius: 12px;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
}

@media (max-width: 768px) {
    .movie-poster {
        height: 200px;
    }
    
    .stats-icon {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cambiar vista entre grid y lista
    document.querySelectorAll('[data-view]').forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            
            // Actualizar botones activos
            document.querySelectorAll('[data-view]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Mostrar/ocultar vistas
            if (view === 'grid') {
                document.getElementById('gridView').classList.remove('d-none');
                document.getElementById('listView').classList.add('d-none');
            } else {
                document.getElementById('gridView').classList.add('d-none');
                document.getElementById('listView').classList.remove('d-none');
            }
            
            // Guardar preferencia en localStorage
            localStorage.setItem('admin_movies_view', view);
        });
    });

    // Restaurar vista preferida
    const savedView = localStorage.getItem('admin_movies_view');
    if (savedView) {
        document.querySelector(`[data-view="${savedView}"]`)?.click();
    }

    // Función para cambiar estado de película
    window.toggleStatus = function(peliculaId) {
        if (confirm('¿Cambiar el estado de esta película?')) {
            // Crear formulario dinámico para enviar POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/peliculas/${peliculaId}/toggle-status`;
            
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(token);
            
            document.body.appendChild(form);
            form.submit();
        }
    };

    // Auto-submit de filtros cuando cambian ciertos campos
    document.querySelector('select[name="estado"]')?.addEventListener('change', function() {
        this.form.submit();
    });

    document.querySelector('select[name="genero"]')?.addEventListener('change', function() {
        this.form.submit();
    });
});
</script>
@endpush