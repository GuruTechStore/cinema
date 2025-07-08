{{-- resources/views/admin/peliculas/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Editar Película')
@section('page-title', 'Editar Película')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.peliculas.index') }}">Películas</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.peliculas.show', $pelicula) }}">{{ $pelicula->titulo }}</a></li>
<li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>Editar: {{ $pelicula->titulo }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.peliculas.update', $pelicula) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <!-- Título -->
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Título *</label>
                            <input type="text" class="form-control @error('titulo') is-invalid @enderror" 
                                   name="titulo" value="{{ old('titulo', $pelicula->titulo) }}" required>
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Clasificación -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Clasificación *</label>
                            <select class="form-select @error('clasificacion') is-invalid @enderror" name="clasificacion" required>
                                <option value="">Seleccionar</option>
                                <option value="G" {{ old('clasificacion', $pelicula->clasificacion) == 'G' ? 'selected' : '' }}>G - General</option>
                                <option value="PG" {{ old('clasificacion', $pelicula->clasificacion) == 'PG' ? 'selected' : '' }}>PG - Parental Guidance</option>
                                <option value="PG-13" {{ old('clasificacion', $pelicula->clasificacion) == 'PG-13' ? 'selected' : '' }}>PG-13</option>
                                <option value="R" {{ old('clasificacion', $pelicula->clasificacion) == 'R' ? 'selected' : '' }}>R - Restricted</option>
                                <option value="NC-17" {{ old('clasificacion', $pelicula->clasificacion) == 'NC-17' ? 'selected' : '' }}>NC-17</option>
                            </select>
                            @error('clasificacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Director -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Director *</label>
                            <input type="text" class="form-control @error('director') is-invalid @enderror" 
                                   name="director" value="{{ old('director', $pelicula->director) }}" required>
                            @error('director')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Género -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Género *</label>
                            <select class="form-select @error('genero') is-invalid @enderror" name="genero" required>
                                <option value="">Seleccionar</option>
                                <option value="Acción" {{ old('genero', $pelicula->genero) == 'Acción' ? 'selected' : '' }}>Acción</option>
                                <option value="Aventura" {{ old('genero', $pelicula->genero) == 'Aventura' ? 'selected' : '' }}>Aventura</option>
                                <option value="Comedia" {{ old('genero', $pelicula->genero) == 'Comedia' ? 'selected' : '' }}>Comedia</option>
                                <option value="Drama" {{ old('genero', $pelicula->genero) == 'Drama' ? 'selected' : '' }}>Drama</option>
                                <option value="Terror" {{ old('genero', $pelicula->genero) == 'Terror' ? 'selected' : '' }}>Terror</option>
                                <option value="Ciencia Ficción" {{ old('genero', $pelicula->genero) == 'Ciencia Ficción' ? 'selected' : '' }}>Ciencia Ficción</option>
                                <option value="Animación" {{ old('genero', $pelicula->genero) == 'Animación' ? 'selected' : '' }}>Animación</option>
                                <option value="Documental" {{ old('genero', $pelicula->genero) == 'Documental' ? 'selected' : '' }}>Documental</option>
                                <option value="Romance" {{ old('genero', $pelicula->genero) == 'Romance' ? 'selected' : '' }}>Romance</option>
                                <option value="Thriller" {{ old('genero', $pelicula->genero) == 'Thriller' ? 'selected' : '' }}>Thriller</option>
                            </select>
                            @error('genero')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Duración -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Duración (min) *</label>
                            <input type="number" class="form-control @error('duracion') is-invalid @enderror" 
                                   name="duracion" value="{{ old('duracion', $pelicula->duracion) }}" min="1" required>
                            @error('duracion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Idioma -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Idioma</label>
                            <select class="form-select @error('idioma') is-invalid @enderror" name="idioma">
                                <option value="">Seleccionar</option>
                                <option value="Español" {{ old('idioma', $pelicula->idioma) == 'Español' ? 'selected' : '' }}>Español</option>
                                <option value="Inglés" {{ old('idioma', $pelicula->idioma) == 'Inglés' ? 'selected' : '' }}>Inglés</option>
                                <option value="Subtitulada" {{ old('idioma', $pelicula->idioma) == 'Subtitulada' ? 'selected' : '' }}>Subtitulada</option>
                                <option value="Doblada" {{ old('idioma', $pelicula->idioma) == 'Doblada' ? 'selected' : '' }}>Doblada</option>
                            </select>
                            @error('idioma')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Fecha de Estreno -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha de Estreno *</label>
                            <input type="date" class="form-control @error('fecha_estreno') is-invalid @enderror" 
                                   name="fecha_estreno" value="{{ old('fecha_estreno', $pelicula->fecha_estreno->format('Y-m-d')) }}" required>
                            @error('fecha_estreno')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Sinopsis -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Sinopsis</label>
                            <textarea class="form-control @error('sinopsis') is-invalid @enderror" 
                                      name="sinopsis" rows="4" placeholder="Descripción de la película...">{{ old('sinopsis', $pelicula->sinopsis) }}</textarea>
                            @error('sinopsis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Reparto -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Reparto</label>
                            <textarea class="form-control @error('reparto') is-invalid @enderror" 
                                      name="reparto" rows="3" placeholder="Actores principales separados por comas...">{{ old('reparto', $pelicula->reparto) }}</textarea>
                            @error('reparto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Poster Actual -->
                        @if($pelicula->poster)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Poster Actual</label>
                            <div class="text-center">
                                <img src="{{ asset('storage/' . $pelicula->poster) }}" 
                                     alt="{{ $pelicula->titulo }}" 
                                     class="img-thumbnail" 
                                     style="max-height: 200px;">
                            </div>
                        </div>
                        @endif

                        <!-- Nuevo Poster -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ $pelicula->poster ? 'Cambiar Poster' : 'Poster' }}</label>
                            <input type="file" class="form-control @error('poster') is-invalid @enderror" 
                                   name="poster" accept="image/*">
                            <div class="form-text">JPG, PNG, GIF. Máximo 2MB</div>
                            @error('poster')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- URL del Trailer -->
                        <div class="col-12">
                            <label class="form-label fw-bold">URL del Trailer (YouTube)</label>
                            <input type="url" class="form-control @error('trailer_url') is-invalid @enderror" 
                                   name="trailer_url" value="{{ old('trailer_url', $pelicula->trailer_url) }}" 
                                   placeholder="https://www.youtube.com/embed/...">
                            @error('trailer_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Checkboxes -->
                        <div class="col-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="activa" value="1" 
                                               {{ old('activa', $pelicula->activa) ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            Película Activa
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="destacada" value="1" 
                                               {{ old('destacada', $pelicula->destacada) ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            Película Destacada
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.peliculas.show', $pelicula) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Actualizar Película
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection