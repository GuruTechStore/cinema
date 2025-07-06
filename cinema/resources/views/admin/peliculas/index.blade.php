{{-- resources/views/admin/peliculas/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Gestión de Películas')
@section('page-title', 'Películas')

@section('breadcrumb')
<li class="breadcrumb-item active">Películas</li>
@endsection

@section('page-actions')
<a href="{{ route('admin.peliculas.create') }}" class="btn btn-admin btn-primary">
    <i class="fas fa-plus me-2"></i>Nueva Película
</a>
@endsection

@section('content')
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="buscar" 
                           placeholder="Buscar por título..." value="{{ request('buscar') }}">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="estado">
                        <option value="">Todos los estados</option>
                        <option value="activa" {{ request('estado') == 'activa' ? 'selected' : '' }}>Activas</option>
                        <option value="inactiva" {{ request('estado') == 'inactiva' ? 'selected' : '' }}>Inactivas</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="genero">
                        <option value="">Todos los géneros</option>
                        <option value="Acción">Acción</option>
                        <option value="Comedia">Comedia</option>
                        <option value="Drama">Drama</option>
                        <option value="Terror">Terror</option>
                        <option value="Animación">Animación</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Películas -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-film me-2"></i>Lista de Películas
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Poster</th>
                            <th>Título</th>
                            <th>Género</th>
                            <th>Duración</th>
                            <th>Estreno</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peliculas as $pelicula)
                        <tr>
                            <td>
                                <img src="{{ $pelicula->poster ? asset('storage/' . $pelicula->poster) : asset('images/posters/') }}" 
                                     alt="{{ $pelicula->titulo }}" class="rounded" style="width: 50px; height: 75px; object-fit: cover;">
                            </td>
                            <td>
                                <strong>{{ $pelicula->titulo }}</strong>
                                <br>
                                <small class="text-muted">{{ $pelicula->director }}</small>
                            </td>
                            <td>{{ $pelicula->genero }}</td>
                            <td>{{ $pelicula->getDuracionFormateada() }}</td>
                            <td>{{ $pelicula->fecha_estreno->format('d M Y') }}</td>
                            <td>
                                @if($pelicula->activa)
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-secondary">Inactiva</span>
                                @endif
                                
                                @if($pelicula->destacada)
                                    <span class="badge bg-warning text-dark">Destacada</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.peliculas.show', $pelicula) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.peliculas.edit', $pelicula) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.peliculas.programar-funciones', $pelicula) }}" 
                                       class="btn btn-sm btn-outline-success" title="Programar">
                                        <i class="fas fa-calendar-plus"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.peliculas.destroy', $pelicula) }}" 
                                          class="d-inline" onsubmit="return confirm('¿Eliminar esta película?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-film display-1 text-muted mb-3"></i>
                                <h5 class="text-muted">No hay películas</h5>
                                <p class="text-muted">Agrega tu primera película</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($peliculas->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $peliculas->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection