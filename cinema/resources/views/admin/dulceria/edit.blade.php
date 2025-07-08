{{-- resources/views/admin/dulceria/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Editar Producto')
@section('page-title', 'Editar: ' . $dulceria->nombre)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dulceria.index') }}">Dulcería</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.dulceria.show', $dulceria) }}">{{ $dulceria->nombre }}</a></li>
<li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>Editar Producto
                </h5>
                <span class="badge {{ $dulceria->activo ? 'bg-success' : 'bg-danger' }}">
                    {{ $dulceria->activo ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('admin.dulceria.update', $dulceria) }}" enctype="multipart/form-data" id="form-editar-producto">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <!-- Imagen actual (si existe) -->
                        @if($dulceria->imagen)
                        <div class="col-12">
                            <label class="form-label fw-bold">Imagen Actual</label>
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $dulceria->imagen) }}" 
                                     alt="{{ $dulceria->nombre }}" 
                                     class="img-thumbnail"
                                     style="max-width: 200px; max-height: 200px;"
                                     onerror="this.src='{{ asset('images/dulceria/placeholder.jpg') }}'">
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Archivo: {{ basename($dulceria->imagen) }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label for="nombre" class="form-label fw-bold">
                                <i class="fas fa-tag me-1"></i>Nombre del Producto *
                            </label>
                            <input type="text" 
                                   class="form-control @error('nombre') is-invalid @enderror" 
                                   id="nombre" 
                                   name="nombre" 
                                   value="{{ old('nombre', $dulceria->nombre) }}" 
                                   required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Precio -->
                        <div class="col-md-6">
                            <label for="precio" class="form-label fw-bold">
                                <i class="fas fa-dollar-sign me-1"></i>Precio (S/) *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" 
                                       class="form-control @error('precio') is-invalid @enderror" 
                                       id="precio" 
                                       name="precio" 
                                       value="{{ old('precio', $dulceria->precio) }}" 
                                       step="0.01" 
                                       min="0" 
                                       required>
                                @error('precio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Categoría -->
                        <div class="col-md-6">
                            <label for="categoria_dulceria_id" class="form-label fw-bold">
                                <i class="fas fa-folder me-1"></i>Categoría *
                            </label>
                            <select class="form-select @error('categoria_dulceria_id') is-invalid @enderror" 
                                    id="categoria_dulceria_id" 
                                    name="categoria_dulceria_id" 
                                    required>
                                <option value="">Seleccionar categoría</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" 
                                            {{ old('categoria_dulceria_id', $dulceria->categoria_dulceria_id) == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria_dulceria_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Switches de estado -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-toggle-on me-1"></i>Estado del Producto
                            </label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="activo" 
                                       name="activo" 
                                       value="1"
                                       {{ old('activo', $dulceria->activo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">
                                    Producto activo
                                </label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="es_combo" 
                                       name="es_combo" 
                                       value="1"
                                       {{ old('es_combo', $dulceria->es_combo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="es_combo">
                                    Es un combo
                                </label>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="col-12">
                            <label for="descripcion" class="form-label fw-bold">
                                <i class="fas fa-align-left me-1"></i>Descripción
                            </label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="3" 
                                      placeholder="Describe el producto...">{{ old('descripcion', $dulceria->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nueva imagen -->
                        <div class="col-12">
                            <label for="imagen" class="form-label fw-bold">
                                <i class="fas fa-image me-1"></i>{{ $dulceria->imagen ? 'Cambiar Imagen' : 'Subir Imagen' }}
                            </label>
                            <input type="file" 
                                   class="form-control @error('imagen') is-invalid @enderror" 
                                   id="imagen" 
                                   name="imagen" 
                                   accept="image/*">
                            @error('imagen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Formatos: JPG, PNG, GIF. Máximo: 2MB
                            </small>
                            
                            <!-- Preview de nueva imagen -->
                            <div id="imagen-preview" class="mt-3" style="display: none;">
                                <strong>Nueva imagen:</strong><br>
                                <img id="preview-img" src="" alt="Preview" 
                                     class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Información del Producto</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Creado:</strong> {{ $dulceria->created_at->format('d/m/Y H:i') }}</p>
                                        <p class="mb-1"><strong>ID:</strong> #{{ $dulceria->id }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Última modificación:</strong> {{ $dulceria->updated_at->format('d/m/Y H:i') }}</p>
                                        <p class="mb-1"><strong>Categoría actual:</strong> {{ $dulceria->categoria->nombre }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.dulceria.show', $dulceria) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver
                                </a>
                                
                                <div>
                                    <button type="reset" class="btn btn-outline-warning me-2">
                                        <i class="fas fa-undo me-2"></i>Restablecer
                                    </button>
                                    <button type="submit" class="btn btn-success" id="btn-guardar">
                                        <i class="fas fa-save me-2"></i>Guardar Cambios
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Preview de imagen al seleccionar archivo
    $('#imagen').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-img').attr('src', e.target.result);
                $('#imagen-preview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#imagen-preview').hide();
        }
    });

    // Validación del formulario
    $('#form-editar-producto').submit(function(e) {
        const nombre = $('#nombre').val().trim();
        const precio = parseFloat($('#precio').val());
        const categoria = $('#categoria_dulceria_id').val();

        if (!nombre) {
            e.preventDefault();
            showAlert('El nombre del producto es obligatorio', 'error');
            $('#nombre').focus();
            return false;
        }

        if (!precio || precio <= 0) {
            e.preventDefault();
            showAlert('El precio debe ser mayor a 0', 'error');
            $('#precio').focus();
            return false;
        }

        if (!categoria) {
            e.preventDefault();
            showAlert('Selecciona una categoría', 'error');
            $('#categoria_dulceria_id').focus();
            return false;
        }

        // Mostrar loading en el botón
        $('#btn-guardar').html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...').prop('disabled', true);
    });

    // Función para mostrar alertas
    function showAlert(message, type = 'info') {
        const alertType = type === 'error' ? 'danger' : type;
        const icon = type === 'error' ? 'fas fa-exclamation-triangle' : 'fas fa-info-circle';
        
        const alert = `
            <div class="alert alert-${alertType} alert-dismissible fade show" role="alert">
                <i class="${icon} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.card-body').prepend(alert);
        
        // Auto-dismiss después de 5 segundos
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }

    // Contador de caracteres para descripción
    $('#descripcion').on('input', function() {
        const maxLength = 500;
        const currentLength = $(this).val().length;
        const remaining = maxLength - currentLength;
        
        if (!$('#char-counter').length) {
            $(this).after('<small id="char-counter" class="text-muted"></small>');
        }
        
        $('#char-counter').text(`${currentLength}/${maxLength} caracteres`);
        
        if (remaining < 50) {
            $('#char-counter').removeClass('text-muted').addClass('text-warning');
        } else {
            $('#char-counter').removeClass('text-warning').addClass('text-muted');
        }
    });

    // Trigger del contador al cargar
    $('#descripcion').trigger('input');
});
</script>
@endpush