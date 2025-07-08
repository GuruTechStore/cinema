{{-- resources/views/admin/dulceria/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Gestión de Dulcería')
@section('page-title', 'Productos de Dulcería')

@section('breadcrumb')
<li class="breadcrumb-item active">Dulcería</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-candy-cane me-2"></i>Gestión de Productos
                </h5>
                <a href="{{ route('admin.dulceria.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nuevo Producto
                </a>
            </div>
            
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <select class="form-select" id="filtro-categoria">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="filtro-estado">
                            <option value="">Todos los estados</option>
                            <option value="1">Activos</option>
                            <option value="0">Inactivos</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="buscar-producto" placeholder="Buscar producto...">
                    </div>
                </div>

                <!-- Estadísticas rápidas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3>{{ $productos->total() }}</h3>
                                <p class="mb-0">Total Productos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3>{{ $productos->where('activo', true)->count() }}</h3>
                                <p class="mb-0">Activos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h3>{{ $productos->where('es_combo', true)->count() }}</h3>
                                <p class="mb-0">Combos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3>{{ $categorias->count() }}</h3>
                                <p class="mb-0">Categorías</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de productos -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Imagen</th>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Estado</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productos as $producto)
                            <tr class="producto-row" 
                                data-categoria="{{ $producto->categoria_dulceria_id }}" 
                                data-estado="{{ $producto->activo ? 1 : 0 }}"
                                data-nombre="{{ strtolower($producto->nombre) }}">
                                <td>
                                    <span class="badge bg-secondary">#{{ $producto->id }}</span>
                                </td>
                                
                                <td>
                                    @if($producto->imagen)
                                        <img src="{{ asset('storage/' . $producto->imagen) }}" 
                                             alt="{{ $producto->nombre }}" 
                                             class="rounded"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                
                                <td>
                                    <div>
                                        <strong>{{ $producto->nombre }}</strong>
                                        @if($producto->descripcion)
                                            <br><small class="text-muted">{{ Str::limit($producto->descripcion, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                
                                <td>
                                    <span class="badge bg-info">{{ $producto->categoria->nombre }}</span>
                                </td>
                                
                                <td>
                                    <strong class="text-success">S/ {{ number_format($producto->precio, 2) }}</strong>
                                </td>
                                
                                <td>
                                    @if($producto->activo)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Activo
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times me-1"></i>Inactivo
                                        </span>
                                    @endif
                                </td>
                                
                                <td>
                                    @if($producto->es_combo)
                                        <span class="badge bg-warning">
                                            <i class="fas fa-boxes me-1"></i>Combo
                                        </span>
                                    @else
                                        <span class="badge bg-primary">
                                            <i class="fas fa-cookie-bite me-1"></i>Individual
                                        </span>
                                    @endif
                                </td>
                                
                                <td>
                                    <small class="text-muted">
                                        {{ $producto->created_at->format('d/m/Y') }}
                                    </small>
                                </td>
                                
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.dulceria.show', $producto) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <a href="{{ route('admin.dulceria.edit', $producto) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <form method="POST" 
                                              action="{{ route('admin.dulceria.toggle-status', $producto) }}" 
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-{{ $producto->activo ? 'warning' : 'success' }}" 
                                                    title="{{ $producto->activo ? 'Desactivar' : 'Activar' }}"
                                                    onclick="return confirm('¿Cambiar estado del producto?')">
                                                <i class="fas fa-{{ $producto->activo ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        
                                        <form method="POST" 
                                              action="{{ route('admin.dulceria.destroy', $producto) }}" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="Eliminar"
                                                    onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-candy-cane fa-3x mb-3"></i>
                                        <h5>No hay productos registrados</h5>
                                        <p>Comienza agregando tu primer producto de dulcería</p>
                                        <a href="{{ route('admin.dulceria.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Crear Producto
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($productos->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $productos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Filtro por categoría
    $('#filtro-categoria').change(function() {
        filtrarProductos();
    });
    
    // Filtro por estado
    $('#filtro-estado').change(function() {
        filtrarProductos();
    });
    
    // Búsqueda por nombre
    $('#buscar-producto').on('keyup', function() {
        filtrarProductos();
    });
    
    function filtrarProductos() {
        const categoria = $('#filtro-categoria').val();
        const estado = $('#filtro-estado').val();
        const busqueda = $('#buscar-producto').val().toLowerCase();
        
        $('.producto-row').each(function() {
            let mostrar = true;
            
            // Filtro por categoría
            if (categoria && $(this).data('categoria') != categoria) {
                mostrar = false;
            }
            
            // Filtro por estado
            if (estado !== '' && $(this).data('estado') != estado) {
                mostrar = false;
            }
            
            // Filtro por búsqueda
            if (busqueda && !$(this).data('nombre').includes(busqueda)) {
                mostrar = false;
            }
            
            $(this).toggle(mostrar);
        });
        
        // Mostrar mensaje si no hay resultados
        const visibles = $('.producto-row:visible').length;
        if (visibles === 0) {
            if (!$('#no-results').length) {
                $('tbody').append(`
                    <tr id="no-results">
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-search fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No se encontraron productos con los filtros aplicados</p>
                        </td>
                    </tr>
                `);
            }
        } else {
            $('#no-results').remove();
        }
    }
    
    // Confirmación para cambios de estado
    $('form[action*="toggle-status"]').submit(function(e) {
        return confirm('¿Estás seguro de cambiar el estado de este producto?');
    });
    
    // Confirmación para eliminación
    $('form[action*="destroy"]').submit(function(e) {
        return confirm('¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.');
    });
});
</script>
@endpush