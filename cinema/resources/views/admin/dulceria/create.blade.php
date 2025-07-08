{{-- resources/views/admin/dulceria/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Nuevo Producto')
@section('page-title', 'Crear Producto de Dulcería')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dulceria.index') }}">Dulcería</a></li>
<li class="breadcrumb-item active">Nuevo Producto</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus me-2"></i>Agregar Nuevo Producto
                </h5>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('admin.dulceria.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row g-3">
                        <!-- Nombre -->
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Nombre del Producto *</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                   name="nombre" value="{{ old('nombre') }}" required
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
                                            {{ old('categoria_dulceria_id') == $categoria->id ? 'selected' : '' }}>
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
                                      placeholder="Descripción del producto (opcional)">{{ old('descripcion') }}</textarea>
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
                                       name="precio" value="{{ old('precio') }}" required
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
                                       {{ old('es_combo') ? 'checked' : '' }}>
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
                                       name="activo" id="activo" checked
                                       {{ old('activo', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="activo">
                                    Producto Activo
                                </label>
                                <small class="form-text text-muted d-block">
                                    Solo productos activos aparecen en la dulcería
                                </small>
                            </div>
                        </div>

                        <!-- Imagen -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Imagen del Producto</label>
                            <input type="file" class="form-control @error('imagen') is-invalid @enderror" 
                                   name="imagen" accept="image/*" id="imagen-input">
                            @error('imagen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB
                            </small>
                            
                            <!-- Preview de imagen -->
                            <div id="imagen-preview" class="mt-3" style="display: none;">
                                <img id="preview-img" src="" alt="Preview" 
                                     class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.dulceria.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Cancelar
                                </a>
                                
                                <div>
                                    <button type="reset" class="btn btn-outline-warning me-2">
                                        <i class="fas fa-undo me-2"></i>Limpiar
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Crear Producto
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card de ayuda -->
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Consejos para crear productos
                </h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li><strong>Nombre descriptivo:</strong> Usa nombres claros como "Canchita Grande" o "Combo Familiar"</li>
                    <li><strong>Descripción útil:</strong> Incluye detalles que ayuden al cliente a decidir</li>
                    <li><strong>Precio correcto:</strong> Verifica que el precio sea competitivo</li>
                    <li><strong>Imagen de calidad:</strong> Usa fotos atractivas del producto</li>
                    <li><strong>Categoría correcta:</strong> Asigna a la categoría apropiada para fácil navegación</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Preview de imagen
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
    $('form').submit(function(e) {
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

    // Auto-generar descripción basada en el nombre (opcional)
    $('input[name="nombre"]').blur(function() {
        const nombre = $(this).val();
        const descripcion = $('textarea[name="descripcion"]').val();
        
        if (nombre && !descripcion) {
            if (nombre.toLowerCase().includes('combo')) {
                $('textarea[name="descripcion"]').val('Delicioso combo que incluye varios productos para disfrutar durante la película');
                $('#es_combo').prop('checked', true);
            } else if (nombre.toLowerCase().includes('canchita')) {
                $('textarea[name="descripcion"]').val('Canchita fresca y crujiente, perfecta para acompañar tu película');
            } else if (nombre.toLowerCase().includes('bebida') || nombre.toLowerCase().includes('cola') || nombre.toLowerCase().includes('agua')) {
                $('textarea[name="descripcion"]').val('Bebida refrescante para acompañar tu experiencia cinematográfica');
            }
        }
    });
});
</script>
@endpush