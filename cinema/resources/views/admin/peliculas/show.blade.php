{{-- resources/views/admin/peliculas/show.blade.php --}}
@extends('layouts.admin')

@section('title', $pelicula->titulo)
@section('page-title', 'Detalle de Película')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.peliculas.index') }}">Películas</a></li>
<li class="breadcrumb-item active">{{ $pelicula->titulo }}</li>
@endsection

@section('page-actions')
<div class="btn-group">
    <a href="{{ route('admin.peliculas.edit', $pelicula) }}" class="btn btn-admin btn-warning">
        <i class="fas fa-edit me-2"></i>Editar
    </a>
    <a href="{{ route('admin.peliculas.programar-funciones', $pelicula) }}" class="btn btn-admin btn-success">
        <i class="fas fa-calendar-plus me-2"></i>Programar Funciones
    </a>
    <button type="button" class="btn btn-admin btn-danger" data-bs-toggle="modal" data-bs-target="#eliminarModal">
        <i class="fas fa-trash me-2"></i>Eliminar
    </button>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Información Principal -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-film me-2"></i>Información de la Película
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">Título:</td>
                                <td>{{ $pelicula->titulo }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Director:</td>
                                <td>{{ $pelicula->director }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Género:</td>
                                <td>{{ $pelicula->genero }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Duración:</td>
                                <td>{{ $pelicula->getDuracionFormateada() }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Clasificación:</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $pelicula->clasificacion }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Fecha de Estreno:</td>
                                <td>{{ $pelicula->fecha_estreno->format('d/m/Y') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">Estado:</td>
                                <td>
                                    @if($pelicula->activa)
                                        <span class="badge bg-success">Activa</span>
                                    @else
                                        <span class="badge bg-secondary">Inactiva</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Destacada:</td>
                                <td>
                                    @if($pelicula->destacada)
                                        <span class="badge bg-warning text-dark">Sí</span>
                                    @else
                                        <span class="badge bg-light text-dark">No</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Idioma:</td>
                                <td>{{ $pelicula->idioma ?? 'No especificado' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Creado:</td>
                                <td>{{ $pelicula->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Actualizado:</td>
                                <td>{{ $pelicula->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($pelicula->sinopsis)
                <div class="mt-4">
                    <h6 class="fw-bold">Sinopsis:</h6>
                    <p class="text-muted">{{ $pelicula->sinopsis }}</p>
                </div>
                @endif

                @if($pelicula->reparto)
                <div class="mt-4">
                    <h6 class="fw-bold">Reparto:</h6>
                    <p class="text-muted">{{ $pelicula->reparto }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Funciones Programadas -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar me-2"></i>Funciones Programadas
                </h5>
                <a href="{{ route('admin.peliculas.programar-funciones', $pelicula) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>Nueva Función
                </a>
            </div>
            <div class="card-body">
                @if($pelicula->funciones->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Sala</th>
                                    <th>Cine</th>
                                    <th>Precio</th>
                                    <th>Reservas</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pelicula->funciones as $funcion)
                                <tr>
                                    <td>{{ $funcion->fecha_funcion ? $funcion->fecha_funcion->format('d/m/Y') : 'Sin fecha' }}</td>
                                    <td>{{ $funcion->hora_funcion ? $funcion->hora_funcion->format('H:i') : 'Sin hora' }}</td>
                                    <td>{{ $funcion->sala->nombre ?? 'Sin sala' }}</td>
                                    <td>{{ $funcion->sala->cine->nombre ?? 'Sin cine' }}</td>
                                    <td>S/ {{ number_format($funcion->precio ?? 0, 2) }}</td>
                                    <td>{{ $funcion->reservas->count() }}</td>
                                    <td>
                                        @if($funcion->fecha_funcion && $funcion->fecha_funcion->isPast())
                                            <span class="badge bg-secondary">Finalizada</span>
                                        @else
                                            <span class="badge bg-success">Activa</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times display-1 text-muted mb-3"></i>
                        <h6 class="text-muted">No hay funciones programadas</h6>
                        <p class="text-muted">Programa la primera función de esta película</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Poster -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-image me-2"></i>Poster
                </h5>
            </div>
            <div class="card-body text-center">
                <img src="{{ $pelicula->poster ? asset('storage/' . $pelicula->poster) : asset('images/posters/default.jpg') }}" 
                     alt="{{ $pelicula->titulo }}" 
                     class="img-fluid rounded shadow"
                     style="max-height: 400px;">
            </div>
        </div>

        <!-- Trailer -->
        @if($pelicula->trailer_url)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-play-circle me-2"></i>Trailer
                </h5>
            </div>
            <div class="card-body">
                <div class="ratio ratio-16x9">
                    <iframe src="{{ $pelicula->trailer_url }}" 
                            title="Trailer {{ $pelicula->titulo }}"
                            allowfullscreen></iframe>
                </div>
            </div>
        </div>
        @endif

        <!-- Estadísticas -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Estadísticas
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="mb-0">{{ $pelicula->funciones->count() }}</h4>
                            <small class="text-muted">Funciones</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="mb-0">{{ $pelicula->funciones->sum(function($f) { return $f->reservas->count(); }) }}</h4>
                        <small class="text-muted">Reservas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div class="modal fade" id="eliminarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar la película <strong>{{ $pelicula->titulo }}</strong>?</p>
                <p class="text-danger"><small>Esta acción no se puede deshacer y también eliminará todas las funciones asociadas.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="{{ route('admin.peliculas.destroy', $pelicula) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection