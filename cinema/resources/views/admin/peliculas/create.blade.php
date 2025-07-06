{{-- resources/views/admin/peliculas/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Nueva Película')
@section('page-title', 'Nueva Película')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.peliculas.index') }}">Películas</a></li>
<li class="breadcrumb-item active">Nueva</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus me-2"></i>Agregar Nueva Película
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.peliculas.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row g-3">
                        <!-- Título -->
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Título *</label>
                            <input type="text" class="form-control @error('titulo') is-invalid @enderror" 
                                   name="titulo" value="{{ old('titulo') }}" required>
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Clasificación -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Clasificación *</label>
                            <select class="form-select @error('clasificacion') is-invalid @enderror" name="clasificacion" required>
                                <option value="">Seleccionar</option>
                                <option value="G" {{ old('clasificacion') == 'G' ? 'selected' : '' }}>G - General</option>
                                <option value="PG" {{ old('clasificacion') == 'PG' ? 'selected' : '' }}>PG - Parental Guidance</option>
                                <option value="PG-13" {{ old('clasificacion') == 'PG-13' ? 'selected' : '' }}>PG-13</option>
                                <option value="R" {{ old('clasificacion') == 'R' ? 'selected' : '' }}>R - Restricted</option>
                            </select>
                            @error('clasificacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Género y Duración -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Género *</label>
                            <input type="text" class="form-control @error('genero') is-invalid @enderror" 
                                   name="genero" value="{{ old('genero') }}" placeholder="Ej: Acción, Comedia" required>
                            @error('genero')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Duración (minutos) *</label>
                            <input type="number" class="form-control @error('duracion') is-invalid @enderror" 
                                   name="duracion" value="{{ old('duracion') }}" min="1" required>
                            @error('duracion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Director y Fecha de estreno -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Director *</label>
                            <input type="text" class="form-control @error('director') is-invalid @enderror" 
                                   name="director" value="{{ old('director') }}" required>
                            @error('director')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha de Estreno *</label>
                            <input type="date" class="form-control @error('fecha_estreno') is-invalid @enderror" 
                                   name="fecha_estreno" value="{{ old('fecha_estreno') }}" required>
                            @error('fecha_estreno')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Poster -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Poster</label>
                            <input type="file" class="form-control @error('poster') is-invalid @enderror" 
                                   name="poster" accept="image/*">
                            <div class="form-text">Tamaño recomendado: 1200x1800px (formato: jpg, png)</div>
                            @error('poster')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Estados -->
                        <div class="col-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="activa" id="activa" 
                                               {{ old('activa', true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="activa">
                                            Película Activa
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="destacada" id="destacada"
                                               {{ old('destacada') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="destacada">
                                            Película Destacada
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.peliculas.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-admin btn-primary">
                                    <i class="fas fa-save me-2"></i>Guardar Película
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