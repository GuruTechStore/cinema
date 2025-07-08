{{-- resources/views/admin/dulceria/index.blade.php--}}
@extends('layouts.admin')

@section('title', 'Gestión de Dulcería')
@section('page-title', 'Productos de Dulcería')

@section('breadcrumb')
<li class="breadcrumb-item active">Dulcería</li>
@endsection

@push('styles')
<style>
/* PAGINACIÓN ARREGLADA - SIN SVG GIGANTES */
.pagination-sm .page-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.25rem;
    min-width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 1px;
    border: 1px solid #dee2e6;
    color: #6c757d;
    background-color: #fff;
    text-decoration: none;
}

.pagination-sm .page-link:hover {
    z-index: 2;
    color: #0a58ca;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.pagination-sm .page-item.active .page-link {
    z-index: 3;
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.pagination-sm .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
    opacity: 0.65;
}

.pagination {
    margin-bottom: 0;
    gap: 0;
}

/* Responsive */
@media (max-width: 576px) {
    .pagination-sm .page-link {
        min-width: 28px;
        height: 28px;
        font-size: 0.75rem;
        padding: 0.125rem 0.25rem;
    }
    
    .pagination .page-item:not(.active):not(.disabled):not(:first-child):not(:last-child):not(:nth-child(2)):not(:nth-last-child(2)) {
        display: none;
    }
}

/* Estilos para las cards */
.producto-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: 1px solid #e3e6f0;
}

.producto-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.producto-image {
    height: 200px;
    object-fit: cover;
    border-radius: 0.375rem 0.375rem 0 0;
}

.badge-estado {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.filtros-container {
    background: #f8f9fc;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
    border: 1px solid #e3e6f0;
}
</style>
@endpush

@section('content')
<div class="row">
    <!-- Estadísticas principales -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Productos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProductos }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Productos Activos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $productosActivos }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Ventas Hoy</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ventasHoy }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Ingresos Totales</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ formatPrice($ingresosTotales) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-candy-cane me-2"></i>Gestión de Productos
                </h6>
                <a href="{{ route('admin.dulceria.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-2"></i>Nuevo Producto
                </a>
            </div>
            
            <div class="card-body">
                <!-- Filtros -->
                <div class="filtros-container">
                    <form method="GET" action="{{ route('admin.dulceria.index') }}" id="filtros-form">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small text-muted">Categoría</label>
                                <select class="form-select form-select-sm" name="categoria" onchange="this.form.submit()">
                                    <option value="">Todas las categorías</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" 
                                                {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted">Estado</label>
                                <select class="form-select form-select-sm" name="estado" onchange="this.form.submit()">
                                    <option value="">Todos los estados</option>
                                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                                    <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Buscar producto</label>
                                <input type="text" class="form-control form-control-sm" name="buscar" 
                                       value="{{ request('buscar') }}" placeholder="Nombre del producto..."
                                       onkeypress="if(event.key==='Enter') this.form.submit()">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small text-muted">&nbsp;</label>
                                <div class="d-flex gap-1">
                                    <button type="submit" class="btn btn-outline-primary btn-sm flex-fill">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <a href="{{ route('admin.dulceria.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Grid de productos -->
                @if($productos->count() > 0)
                    <div class="row">
                        @foreach($productos as $producto)
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                            <div class="card producto-card h-100">
                                <div class="position-relative">
                                    <img src="{{ getDulceriaImageUrl($producto->imagen, $producto->nombre) }}" 
                                         class="card-img-top producto-image" 
                                         alt="{{ $producto->nombre }}"
                                         onerror="this.src='{{ asset('images/dulceria/placeholder.jpg') }}'">
                                    
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <span class="badge badge-estado {{ $producto->activo ? 'bg-success' : 'bg-danger' }}">
                                            {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </div>
                                    
                                    @if($producto->es_combo)
                                    <div class="position-absolute top-0 start-0 p-2">
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-gift me-1"></i>Combo
                                        </span>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title font-weight-bold mb-2">{{ $producto->nombre }}</h6>
                                    <p class="card-text text-muted small mb-2">
                                        {{ $producto->categoria->nombre }}
                                    </p>
                                    <p class="card-text text-muted small flex-grow-1">
                                        {{ Str::limit($producto->descripcion, 80) }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="h5 mb-0 text-primary font-weight-bold">
                                            {{ formatPrice($producto->precio) }}
                                        </span>
                                        <small class="text-muted">
                                            ID: #{{ $producto->id }}
                                        </small>
                                    </div>
                                    
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.dulceria.show', $producto) }}" 
                                           class="btn btn-outline-info btn-sm flex-fill">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.dulceria.edit', $producto) }}" 
                                           class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.dulceria.toggle-status', $producto) }}" 
                                              class="d-inline flex-fill">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-outline-{{ $producto->activo ? 'warning' : 'success' }} btn-sm w-100"
                                                    onclick="return confirm('¿Cambiar estado del producto?')">
                                                <i class="fas fa-{{ $producto->activo ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- PAGINACIÓN PERSONALIZADA SIN SVG -->
                    @if($productos->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted small">
                                Mostrando {{ $productos->firstItem() }} a {{ $productos->lastItem() }} 
                                de {{ $productos->total() }} resultados
                            </div>
                            <div>
                                <nav aria-label="Page Navigation">
                                    <ul class="pagination pagination-sm mb-0">
                                        {{-- Botón Anterior --}}
                                        @if ($productos->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link">‹</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $productos->previousPageUrl() }}" rel="prev">‹</a>
                                            </li>
                                        @endif

                                        {{-- Números de página --}}
                                        @php
                                            $start = max($productos->currentPage() - 2, 1);
                                            $end = min($start + 4, $productos->lastPage());
                                            $start = max($end - 4, 1);
                                        @endphp

                                        @if($start > 1)
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $productos->url(1) }}">1</a>
                                            </li>
                                            @if($start > 2)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                        @endif

                                        @for ($page = $start; $page <= $end; $page++)
                                            @if ($page == $productos->currentPage())
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $page }}</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $productos->url($page) }}">{{ $page }}</a>
                                                </li>
                                            @endif
                                        @endfor

                                        @if($end < $productos->lastPage())
                                            @if($end < $productos->lastPage() - 1)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $productos->url($productos->lastPage()) }}">{{ $productos->lastPage() }}</a>
                                            </li>
                                        @endif

                                        {{-- Botón Siguiente --}}
                                        @if ($productos->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $productos->nextPageUrl() }}" rel="next">›</a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link">›</span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-candy-cane fa-3x mb-3 text-gray-300"></i>
                            <h5 class="text-gray-600">No hay productos registrados</h5>
                            <p class="text-gray-500">
                                @if(request()->hasAny(['categoria', 'estado', 'buscar']))
                                    No se encontraron productos con los filtros aplicados.
                                    <br>
                                    <a href="{{ route('admin.dulceria.index') }}" class="btn btn-outline-secondary btn-sm mt-2">
                                        <i class="fas fa-times me-1"></i>Limpiar filtros
                                    </a>
                                @else
                                    Comienza agregando tu primer producto de dulcería
                                @endif
                            </p>
                            @if(!request()->hasAny(['categoria', 'estado', 'buscar']))
                                <a href="{{ route('admin.dulceria.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Crear Primer Producto
                                </a>
                            @endif
                        </div>
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
    // Auto-submit en filtros
    $('select[name="categoria"], select[name="estado"]').change(function() {
        $('#filtros-form').submit();
    });
    
    // Buscar con Enter
    $('input[name="buscar"]').keypress(function(e) {
        if (e.which === 13) {
            $('#filtros-form').submit();
        }
    });
    
    // Confirmación de cambios de estado
    $('form[action*="toggle-status"]').submit(function(e) {
        const isActive = $(this).find('button i').hasClass('fa-pause');
        const action = isActive ? 'desactivar' : 'activar';
        const productName = $(this).closest('.card').find('.card-title').text().trim();
        
        if (!confirm(`¿Estás seguro de ${action} el producto "${productName}"?`)) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush