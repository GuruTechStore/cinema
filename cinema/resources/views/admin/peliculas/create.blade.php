{{-- resources/views/admin/peliculas/create.blade.php  --}}
@extends('layouts.admin')

@section('title', 'Nueva Película')
@section('page-title', 'Nueva Película')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.peliculas.index') }}">Películas</a></li>
<li class="breadcrumb-item active">Nueva</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <form method="POST" action="{{ route('admin.peliculas.store') }}" enctype="multipart/form-data" id="peliculaForm">
            @csrf
            
            <!-- Información Básica -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-film me-2"></i>Información Básica
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Título -->
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Título *</label>
                            <input type="text" class="form-control @error('titulo') is-invalid @enderror" 
                                   name="titulo" value="{{ old('titulo') }}" required placeholder="Ej: Deadpool & Wolverine">
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
                                <option value="NC-17" {{ old('clasificacion') == 'NC-17' ? 'selected' : '' }}>NC-17</option>
                            </select>
                            @error('clasificacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Director -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Director *</label>
                            <input type="text" class="form-control @error('director') is-invalid @enderror" 
                                   name="director" value="{{ old('director') }}" required placeholder="Ej: Shawn Levy">
                            @error('director')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Género -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Género *</label>
                            <input type="text" class="form-control @error('genero') is-invalid @enderror" 
                                   name="genero" value="{{ old('genero') }}" required 
                                   placeholder="Ej: Acción, Comedia, Ciencia Ficción">
                            @error('genero')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Duración -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Duración (minutos) *</label>
                            <input type="number" class="form-control @error('duracion') is-invalid @enderror" 
                                   name="duracion" value="{{ old('duracion') }}" required min="1" max="300"
                                   placeholder="120">
                            @error('duracion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Idioma -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Idioma</label>
                            <select class="form-select @error('idioma') is-invalid @enderror" name="idioma">
                                <option value="">Seleccionar</option>
                                <option value="Español" {{ old('idioma') == 'Español' ? 'selected' : '' }}>Español</option>
                                <option value="Inglés" {{ old('idioma') == 'Inglés' ? 'selected' : '' }}>Inglés</option>
                                <option value="Inglés/Español" {{ old('idioma') == 'Inglés/Español' ? 'selected' : '' }}>Inglés/Español</option>
                            </select>
                            @error('idioma')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Fecha de Estreno -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Fecha de Estreno *</label>
                            <input type="date" class="form-control @error('fecha_estreno') is-invalid @enderror" 
                                   name="fecha_estreno" value="{{ old('fecha_estreno') }}" required
                                   min="{{ date('Y-m-d') }}">
                            @error('fecha_estreno')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Descripción y Sinopsis -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-align-left me-2"></i>Descripción y Detalles
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Descripción Corta -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Descripción Corta</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      name="descripcion" rows="3" 
                                      placeholder="Descripción breve que aparecerá en las tarjetas (máximo 200 caracteres)"
                                      maxlength="200">{{ old('descripcion') }}</textarea>
                            <div class="form-text">Máximo 200 caracteres. Aparece en las tarjetas de película.</div>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Sinopsis -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Sinopsis Completa</label>
                            <textarea class="form-control @error('sinopsis') is-invalid @enderror" 
                                      name="sinopsis" rows="4" 
                                      placeholder="Sinopsis detallada de la película">{{ old('sinopsis') }}</textarea>
                            <div class="form-text">Descripción completa que aparece en la página de detalles.</div>
                            @error('sinopsis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Reparto -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Reparto Principal</label>
                            <input type="text" class="form-control @error('reparto') is-invalid @enderror" 
                                   name="reparto" value="{{ old('reparto') }}" 
                                   placeholder="Ej: Ryan Reynolds, Hugh Jackman, Emma Corrin">
                            <div class="form-text">Separar nombres con comas.</div>
                            @error('reparto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Multimedia -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-image me-2"></i>Multimedia
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Poster -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Poster de la Película</label>
                            <input type="file" class="form-control @error('poster') is-invalid @enderror" 
                                   name="poster" accept="image/*" id="posterInput">
                            <div class="form-text">Formatos: JPG, PNG, WEBP. Máximo 2MB. Tamaño recomendado: 300x450px</div>
                            @error('poster')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <!-- Vista previa del poster -->
                            <div class="mt-3" id="posterPreview" style="display: none;">
                                <img id="posterImage" src="" alt="Vista previa" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        </div>

                        <!-- Trailer -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">URL del Trailer (YouTube)</label>
                            <input type="url" class="form-control @error('trailer_url') is-invalid @enderror" 
                                   name="trailer_url" value="{{ old('trailer_url') }}" 
                                   placeholder="https://www.youtube.com/watch?v=...">
                            <div class="form-text">URL de YouTube del trailer oficial.</div>
                            @error('trailer_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog me-2"></i>Configuración
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Estado -->
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="activa" value="1" 
                                       id="activaSwitch" {{ old('activa', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="activaSwitch">
                                    Película Activa
                                </label>
                            </div>
                            <div class="form-text">Si está desactivada, no aparecerá en la web pública.</div>
                        </div>

                        <!-- Destacada -->
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="destacada" value="1" 
                                       id="destacadaSwitch" {{ old('destacada') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="destacadaSwitch">
                                    Película Destacada
                                </label>
                            </div>
                            <div class="form-text">Aparecerá en la sección de películas destacadas.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Programación Inmediata (Opcional) -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-plus me-2"></i>Programación Inicial (Opcional)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="programarAhora">
                        <label class="form-check-label fw-bold" for="programarAhora">
                            Programar funciones inmediatamente después de crear la película
                        </label>
                    </div>
                    
                    <div id="programacionSection" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> Después de crear la película, serás redirigido automáticamente 
                            al formulario de programación donde podrás asignar cines, salas y horarios.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Crear Película
                        </button>
                        <a href="{{ route('admin.peliculas.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-label.fw-bold {
    color: #495057;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-text {
    font-size: 0.875em;
    color: #6c757d;
}

#posterPreview img {
    border: 2px solid #dee2e6;
    border-radius: 8px;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-lg {
    padding: 0.75rem 2rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Vista previa del poster
    const posterInput = document.getElementById('posterInput');
    const posterPreview = document.getElementById('posterPreview');
    const posterImage = document.getElementById('posterImage');
    
    posterInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                posterImage.src = e.target.result;
                posterPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            posterPreview.style.display = 'none';
        }
    });
    
    // Mostrar/ocultar sección de programación
    const programarAhora = document.getElementById('programarAhora');
    const programacionSection = document.getElementById('programacionSection');
    
    programarAhora.addEventListener('change', function() {
        programacionSection.style.display = this.checked ? 'block' : 'none';
    });
    
    // Validación del formulario
    const form = document.getElementById('peliculaForm');
    form.addEventListener('submit', function(e) {
        // Aquí puedes agregar validaciones adicionales si es necesario
        const titulo = document.querySelector('input[name="titulo"]').value.trim();
        if (titulo.length < 2) {
            e.preventDefault();
            alert('El título debe tener al menos 2 caracteres.');
            return false;
        }
        
        // Si está marcado programar ahora, agregar un campo hidden
        if (programarAhora.checked) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'programar_inmediatamente';
            hiddenInput.value = '1';
            form.appendChild(hiddenInput);
        }
    });
    
    // Auto-completar campos basado en el título (opcional)
    const tituloInput = document.querySelector('input[name="titulo"]');
    tituloInput.addEventListener('blur', function() {
    });
});
</script>
@endpush