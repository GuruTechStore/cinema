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
                <form method="POST" action="{{ route('admin.dulceria.update', $dulceria) }}" enctype="multipart/form-data">
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
                                     style="max-width: 200px; max-height: 200px;">
                            </div>
                        </div>
                        @endif

                        <!-- Nombre -->
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Nombre del Producto *</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                   name="nombre" value="{{ old('nombre', $dulceria->nombre) }}" required
                                   placeholder="Ej: Canchita Grande">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Categoría -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Categoría *</label>
                            <select class="form-select @error('categoria_dulceria_id') is-invalid @enderror" 
                                    name="categoria_dulceria_id" required>
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

                        <!-- Descripción -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      name="descripcion" rows="3"
                                      placeholder="Descripción del producto (opcional)">{{ old('descripcion', $dulceria->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Precio -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Precio (S/) *</label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('precio') is-invalid @enderror" 
                                       name="precio" value="{{ old('precio', $dulceria->precio) }}" required
                                       placeholder="0.00">
                            </div>
                            @error('precio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tipo de producto -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tipo de Producto</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" 
                                       name="es_combo" id="es_combo" 
                                       {{ old('es_combo', $dulceria->es_combo) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="es_combo">
                                    Es un Combo
                                </label>
                                <small class="form-text text-muted d-block">
                                    Marca si este producto es un combo o paquete
                                </small>
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Estado</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" 
                                       name="activo" id="activo"
                                       {{ old('activo', $dulceria->activo) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="activo">
                                    Producto Activo
                                </label>
                                <small class="form-text text-muted d-block">
                                    Solo productos activos aparecen en la dulcería
                                </small>
                            </div>
                        </div>

                        <!-- Nueva imagen -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Cambiar Imagen</label>
                            <input type="file" class="form-control @error('imagen') is-invalid @enderror" 
                                   name="imagen" accept="image/*" id="imagen-input">
                            @error('imagen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Dejar vacío para mantener la imagen actual. Formatos: JPG, PNG, GIF. Máximo: 2MB
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
                                        <i class="fas fa-undo me-2"></i>Restaurar
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Guardar Cambios
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Acciones adicionales -->
        <div class="card mt-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>Acciones Adicionales
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <form method="POST" action="{{ route('admin.dulceria.toggle-status', $dulceria) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-{{ $dulceria->activo ? 'warning' : 'success' }} w-100"
                                    onclick="return confirm('¿Cambiar estado del producto?')">
                                <i class="fas fa-{{ $dulceria->activo ? 'pause' : 'play' }} me-2"></i>
                                {{ $dulceria->activo ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </div>
                    
                    <div class="col-md-4">
                        <a href="{{ route('admin.dulceria.create') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus me-2"></i>Crear Nuevo
                        </a>
                    </div>
                    
                    <div class="col-md-4">
                        <form method="POST" action="{{ route('admin.dulceria.destroy', $dulceria) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100"
                                    onclick="return confirm('¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.')">
                                <i class="fas fa-trash me-2"></i>Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Preview de nueva imagen
    $('#imagen-input').change(function() {
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
    $('form[action*="update"]').submit(function(e) {
        let isValid = true;
        
        // Validar nombre
        if (!$('input[name="nombre"]').val().trim()) {
            isValid = false;
            alert('El nombre del producto es requerido');
            return false;
        }
        
        // Validar categoría
        if (!$('select[name="categoria_dulceria_id"]').val()) {
            isValid = false;
            alert('Debe seleccionar una categoría');
            return false;
        }
        
        // Validar precio
        const precio = parseFloat($('input[name="precio"]').val());
        if (!precio || precio <= 0) {
            isValid = false;
            alert('El precio debe ser mayor a 0');
            return false;
        }
        
        return isValid;
    });

    // Confirmaciones para acciones peligrosas
    $('form[action*="destroy"]').submit(function(e) {
        return confirm('¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.\n\nTodos los pedidos relacionados mantendrán la información del producto.');
    });

    $('form[action*="toggle-status"]').submit(function(e) {
        const action = $(this).find('button').text().trim();
        return confirm(`¿Estás seguro de ${action.toLowerCase()} este producto?`);
    });
});
</script>
@endpush