{{-- resources/views/admin/dulceria/show.blade.php --}}
@extends('layouts.admin')

@section('title', $dulceria->nombre)
@section('page-title', 'Producto: ' . $dulceria->nombre)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dulceria.index') }}">Dulcería</a></li>
<li class="breadcrumb-item active">{{ $dulceria->nombre }}</li>
@endsection

@section('content')
<div class="row">
    <!-- Información del Producto -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-candy-cane me-2"></i>Información del Producto
                </h5>
                <div>
                    <span class="badge {{ $dulceria->activo ? 'bg-success' : 'bg-danger' }} fs-6">
                        {{ $dulceria->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                    @if($dulceria->es_combo)
                        <span class="badge bg-warning fs-6 ms-2">Combo</span>
                    @endif
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <!-- Imagen -->
                    <div class="col-md-4">
                        @if($dulceria->imagen)
                            <img src="{{ asset('storage/' . $dulceria->imagen) }}" 
                                 alt="{{ $dulceria->nombre }}" 
                                 class="img-fluid rounded shadow-sm">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center shadow-sm" 
                                 style="height: 200px;">
                                <div class="text-center text-muted">
                                    <i class="fas fa-image fa-3x mb-2"></i>
                                    <p>Sin imagen</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Detalles -->
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">ID:</th>
                                <td><span class="badge bg-secondary">#{{ $dulceria->id }}</span></td>
                            </tr>
                            <tr>
                                <th>Nombre:</th>
                                <td><strong class="fs-5">{{ $dulceria->nombre }}</strong></td>
                            </tr>
                            <tr>
                                <th>Categoría:</th>
                                <td><span class="badge bg-info">{{ $dulceria->categoria->nombre }}</span></td>
                            </tr>
                            <tr>
                                <th>Precio:</th>
                                <td><strong class="text-success fs-4">S/ {{ number_format($dulceria->precio, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <th>Tipo:</th>
                                <td>
                                    @if($dulceria->es_combo)
                                        <span class="badge bg-warning">
                                            <i class="fas fa-boxes me-1"></i>Combo
                                        </span>
                                    @else
                                        <span class="badge bg-primary">
                                            <i class="fas fa-cookie-bite me-1"></i>Producto Individual
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    @if($dulceria->activo)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Activo
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times me-1"></i>Inactivo
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        @if($dulceria->descripcion)
                        <div class="mt-3">
                            <h6>Descripción:</h6>
                            <p class="text-muted">{{ $dulceria->descripcion }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas de Ventas -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Estadísticas de Ventas
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h4 class="text-primary">{{ $totalVendido ?? 0 }}</h4>
                            <p class="mb-0">Total Vendido</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h4 class="text-success">S/ {{ number_format($ingresosTotales ?? 0, 2) }}</h4>
                            <p class="mb-0">Ingresos Totales</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h4 class="text-warning">{{ $ventasEsteMes ?? 0 }}</h4>
                            <p class="mb-0">Ventas Este Mes</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h4 class="text-info">{{ $ventasHoy ?? 0 }}</h4>
                            <p class="mb-0">Ventas Hoy</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de Pedidos -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>Últimos Pedidos
                </h5>
            </div>
            <div class="card-body">
                @if($ultimosPedidos && $ultimosPedidos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ultimosPedidos as $item)
                                <tr>
                                    <td>{{ $item->pedido->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $item->pedido->user->name }}</td>
                                    <td>{{ $item->cantidad }}</td>
                                    <td>S/ {{ number_format($item->subtotal, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $item->pedido->estado == 'confirmado' ? 'success' : 'warning' }}">
                                            {{ ucfirst($item->pedido->estado) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-shopping-bag fa-3x mb-3"></i>
                        <p>No hay pedidos registrados para este producto</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Panel de Acciones -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cogs me-2"></i>Acciones
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.dulceria.edit', $dulceria) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Editar Producto
                    </a>
                    
                    <form method="POST" action="{{ route('admin.dulceria.toggle-status', $dulceria) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-{{ $dulceria->activo ? 'warning' : 'success' }} w-100"
                                onclick="return confirm('¿Cambiar estado del producto?')">
                            <i class="fas fa-{{ $dulceria->activo ? 'pause' : 'play' }} me-2"></i>
                            {{ $dulceria->activo ? 'Desactivar' : 'Activar' }}
                        </button>
                    </form>
                    
                    <a href="{{ route('admin.dulceria.create') }}" class="btn btn-outline-info">
                        <i class="fas fa-plus me-2"></i>Crear Nuevo Producto
                    </a>
                    
                    <hr>
                    
                    <form method="POST" action="{{ route('admin.dulceria.destroy', $dulceria) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100"
                                onclick="return confirm('¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.')">
                            <i class="fas fa-trash me-2"></i>Eliminar Producto
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Información Técnica -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Información Técnica
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th>Creado:</th>
                        <td>{{ $dulceria->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Actualizado:</th>
                        <td>{{ $dulceria->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>ID Categoría:</th>
                        <td>#{{ $dulceria->categoria_dulceria_id }}</td>
                    </tr>
                    @if($dulceria->imagen)
                    <tr>
                        <th>Archivo Imagen:</th>
                        <td>
                            <small class="text-muted">{{ basename($dulceria->imagen) }}</small>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Vista Previa Pública -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-eye me-2"></i>Vista Previa
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Así se ve en la dulcería pública:</p>
                
                <!-- Simulación del card público -->
                <div class="card border">
                    <div class="position-relative">
                        @if($dulceria->imagen)
                            <img src="{{ asset('storage/' . $dulceria->imagen) }}" 
                                 class="card-img-top" 
                                 style="height: 150px; object-fit: cover;" 
                                 alt="{{ $dulceria->nombre }}">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 150px;">
                                <i class="fas fa-candy-cane fa-2x text-muted"></i>
                            </div>
                        @endif
                        
                        @if($dulceria->es_combo)
                            <span class="position-absolute top-0 end-0 badge bg-warning m-2">Combo</span>
                        @endif
                    </div>
                    
                    <div class="card-body p-3">
                        <h6 class="card-title">{{ $dulceria->nombre }}</h6>
                        @if($dulceria->descripcion)
                            <p class="card-text small text-muted">
                                {{ Str::limit($dulceria->descripcion, 60) }}
                            </p>
                        @endif
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h6 text-success mb-0">S/ {{ number_format($dulceria->precio, 2) }}</span>
                            <button class="btn btn-sm btn-outline-primary" disabled>
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                @if(!$dulceria->activo)
                    <div class="alert alert-warning mt-2 mb-0">
                        <small><i class="fas fa-exclamation-triangle me-1"></i>
                        Este producto no aparece públicamente porque está inactivo</small>
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
    // Confirmaciones para acciones
    $('form[action*="destroy"]').submit(function(e) {
        return confirm('¿Estás seguro de eliminar este producto?\n\nEsta acción no se puede deshacer.\nTodos los pedidos relacionados mantendrán la información del producto.');
    });

    $('form[action*="toggle-status"]').submit(function(e) {
        const isActive = {{ $dulceria->activo ? 'true' : 'false' }};
        const action = isActive ? 'desactivar' : 'activar';
        return confirm(`¿Estás seguro de ${action} este producto?`);
    });
});
</script>
@endpush