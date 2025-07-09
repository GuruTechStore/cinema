{{-- resources/views/home/peliculas.blade.php --}}
@extends('layouts.app')

@section('title', 'Películas - Butaca del Salchichon')

@section('content')
    <!-- Page Header -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-3">Películas</h1>
                    <p class="lead">Descubre los últimos estrenos y próximas películas</p>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-film display-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Filtros -->
    <section class="py-4 bg-light">
        <div class="container">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Buscar película</label>
                            <input type="text" class="form-control" name="buscar" 
                                   placeholder="Título de la película..." value="{{ request('buscar') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Género</label>
                            <select class="form-select" name="genero">
                                <option value="">Todos</option>
                                @foreach($generos as $genero)
                                    <option value="{{ $genero }}" {{ request('genero') == $genero ? 'selected' : '' }}>
                                        {{ $genero }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Ciudad</label>
                            <select class="form-select" name="ciudad_id">
                                <option value="">Todas</option>
                                @foreach($ciudades as $ciudad)
                                    <option value="{{ $ciudad->id }}" {{ request('ciudad_id') == $ciudad->id ? 'selected' : '' }}>
                                        {{ $ciudad->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Fecha</label>
                            <input type="date" class="form-control" name="fecha" value="{{ request('fecha') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Ordenar</label>
                            <select class="form-select" name="orden">
                                <option value="fecha_estreno" {{ request('orden') == 'fecha_estreno' ? 'selected' : '' }}>Fecha estreno</option>
                                <option value="titulo" {{ request('orden') == 'titulo' ? 'selected' : '' }}>Título A-Z</option>
                                <option value="popularidad" {{ request('orden') == 'popularidad' ? 'selected' : '' }}>Popularidad</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Películas Grid -->
    <section class="py-5">
        <div class="container">
            @if($peliculas->count() > 0)
                <div class="row g-4">
                    @foreach($peliculas as $pelicula)
                        @include('components.movie-card', ['pelicula' => $pelicula])
                    @endforeach
                </div>

                <!-- Paginación -->
                @if($peliculas->hasPages())
                    <div class="d-flex justify-content-center mt-5">
                        {{ $peliculas->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-film display-1 text-muted mb-3"></i>
                    <h3 class="text-muted">No se encontraron películas</h3>
                    <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                    <a href="{{ route('peliculas') }}" class="btn btn-primary">
                        <i class="fas fa-refresh me-2"></i>Ver todas las películas
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection