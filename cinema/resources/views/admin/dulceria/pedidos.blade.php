{{-- resources/views/admin/dulceria/pedidos.blade.php --}}
@extends('layouts.admin')

@section('title', 'Gestión de Pedidos - Dulcería')
@section('page-title', 'Gestión de Pedidos')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dulceria.index') }}">Dulcería</a></li>
<li class="breadcrumb-item active">Pedidos</li>
@endsection

@push('styles')
<style>
    .estado-cambiado {
        animation: highlight 2s ease-in-out;
    }
    
    @keyframes highlight {
        0%, 100% { background-color: transparent; }
        50% { background-color: #fff3cd; }
    }
    
    .table-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
    
    .table-danger {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }
    
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
    }
    
    @media print {
        .btn, .modal, .toast-container { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
    }
</style>
@endpush

@section('content')
<!-- Toast Container -->
<div class="toast-container"></div>

<!-- Estadísticas Rápidas -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $totalPedidos }}</h4>
                        <p class="mb-0">Total Pedidos</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-shopping-bag fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $pedidosPendientes }}</h4>
                        <p class="mb-0">Pendientes</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $pedidosListos }}</h4>
                        <p class="mb-0">Listos</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-bell fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ formatPrice($ingresosHoy) }}</h4>
                        <p class="mb-0">Ingresos Hoy</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Controles y Filtros -->
<div class="card mb-4">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>Filtros y Controles
                </h5>
            </div>
            <div class="col-md-6 text-end">
                <button type="button" class="btn btn-outline-success btn-sm" id="auto-refresh-toggle">
                    <i class="fas fa-play me-1"></i>Auto-actualizar
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="actualizarPedidos()">
                    <i class="fas fa-sync me-1"></i>Actualizar
                </button>
                <button type="button" class="btn btn-outline-warning btn-sm" onclick="marcarTodosListos()">
                    <i class="fas fa-bell me-1"></i>Marcar Todos Listos
                </button>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <form method="GET" action="{{ route('admin.dulceria.pedidos') }}" id="filtros-form">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="confirmado" {{ request('estado') == 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                        <option value="listo" {{ request('estado') == 'listo' ? 'selected' : '' }}>Listo</option>
                        <option value="entregado" {{ request('estado') == 'entregado' ? 'selected' : '' }}>Entregado</option>
                        <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Usuario</label>
                    <input type="text" name="usuario" class="form-control" placeholder="Buscar por nombre o email" value="{{ request('usuario') }}">
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('admin.dulceria.pedidos') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Limpiar
                    </a>
                </div>
            </div>
        </form>
        
        <!-- Filtros Rápidos -->
        <div class="mt-3">
            <small class="text-muted">Filtros rápidos:</small>
            <button type="button" class="btn btn-sm btn-outline-warning filtro-rapido ms-2" data-estado="confirmado">
                Confirmados
            </button>
            <button type="button" class="btn btn-sm btn-outline-info filtro-rapido" data-estado="listo">
                Listos
            </button>
            <button type="button" class="btn btn-sm btn-outline-success filtro-rapido" data-estado="entregado">
                Entregados
            </button>
        </div>
        
        <div class="mt-2">
            <small class="text-muted">
                Última actualización: <span id="ultima-actualizacion">{{ date('H:i:s') }}</span>
            </small>
        </div>
    </div>
</div>

<!-- Tabla de Pedidos -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Lista de Pedidos ({{ $pedidos->total() }})
        </h5>
    </div>
    
    <div class="card-body p-0">
        @if($pedidos->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>Productos</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Tiempo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedidos as $pedido)
                    <tr data-pedido-id="{{ $pedido->id }}" data-estado="{{ $pedido->estado }}">
                        <td>
                            <strong>#{{ $pedido->codigo_pedido }}</strong><br>
                            <small class="text-muted">{{ $pedido->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <strong>{{ $pedido->user->name }}</strong><br>
                            <small class="text-muted">{{ $pedido->user->email }}</small>
                        </td>
                        <td>
                            @foreach($pedido->items->take(2) as $item)
                                <small>
                                    {{ $item->cantidad }}x {{ $item->producto->nombre }}<br>
                                </small>
                            @endforeach
                            @if($pedido->items->count() > 2)
                                <small class="text-muted">+{{ $pedido->items->count() - 2 }} más...</small>
                            @endif
                        </td>
                        <td>
                            <strong>{{ formatPrice($pedido->monto_total) }}</strong>
                        </td>
                        <td>
                            <select class="form-select form-select-sm estado-select" 
                                    data-pedido-id="{{ $pedido->id }}" 
                                    data-estado-actual="{{ $pedido->estado }}">
                                <option value="confirmado" {{ $pedido->estado == 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                                <option value="listo" {{ $pedido->estado == 'listo' ? 'selected' : '' }}>Listo</option>
                                <option value="entregado" {{ $pedido->estado == 'entregado' ? 'selected' : '' }}>Entregado</option>
                                <option value="cancelado" {{ $pedido->estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </td>
                        <td>
                            <span class="tiempo-transcurrido" data-created-at="{{ $pedido->created_at->toISOString() }}">
                                {{ $pedido->created_at->diffForHumans() }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex">
                                <button class="btn btn-sm btn-outline-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#detallePedidoModal{{ $pedido->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                @if($pedido->estado == 'confirmado')
                                    <button class="btn btn-sm btn-success cambio-rapido ms-1" 
                                            data-pedido-id="{{ $pedido->id }}" 
                                            data-nuevo-estado="listo">
                                        <i class="fas fa-bell me-1"></i>Listo
                                    </button>
                                @elseif($pedido->estado == 'listo')
                                    <button class="btn btn-sm btn-info cambio-rapido ms-1" 
                                            data-pedido-id="{{ $pedido->id }}" 
                                            data-nuevo-estado="entregado">
                                        <i class="fas fa-check me-1"></i>Entregar
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <div class="card-footer">
            {{ $pedidos->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No hay pedidos</h5>
            <p class="text-muted">No se encontraron pedidos con los filtros aplicados</p>
        </div>
        @endif
    </div>
</div>

<!-- Modal de Confirmación de Cambio de Estado -->
<div class="modal fade" id="confirmarCambioModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exchange-alt me-2"></i>Confirmar Cambio de Estado
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Detalles del Cambio</h6>
                    <p class="mb-1"><strong>Pedido:</strong> <span id="modal-codigo-pedido"></span></p>
                    <p class="mb-1"><strong>Cliente:</strong> <span id="modal-cliente"></span></p>
                    <p class="mb-1"><strong>Estado actual:</strong> <span id="modal-estado-actual"></span></p>
                    <p class="mb-0"><strong>Nuevo estado:</strong> <span id="modal-estado-nuevo"></span></p>
                </div>
                <p>¿Estás seguro de que quieres cambiar el estado de este pedido?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="confirmar-cambio-estado">
                    <i class="fas fa-check me-2"></i>Confirmar Cambio
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modales de Detalle de Pedidos -->
@foreach($pedidos as $pedido)
<div class="modal fade" id="detallePedidoModal{{ $pedido->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-receipt me-2"></i>Detalle del Pedido #{{ $pedido->codigo_pedido }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-user me-2"></i>Información del Cliente</h6>
                        <p class="mb-1"><strong>Nombre:</strong> {{ $pedido->user->name }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $pedido->user->email }}</p>
                        <p class="mb-3"><strong>Teléfono:</strong> {{ $pedido->user->telefono ?? 'No registrado' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-info-circle me-2"></i>Información del Pedido</h6>
                        <p class="mb-1"><strong>Código:</strong> #{{ $pedido->codigo_pedido }}</p>
                        <p class="mb-1"><strong>Fecha:</strong> {{ $pedido->created_at->format('d/m/Y H:i') }}</p>
                        <p class="mb-1"><strong>Estado:</strong> 
                            <span class="badge bg-{{ $pedido->estado == 'confirmado' ? 'warning' : ($pedido->estado == 'listo' ? 'info' : 'success') }}">
                                {{ ucfirst($pedido->estado) }}
                            </span>
                        </p>
                        <p class="mb-3"><strong>Total:</strong> {{ formatPrice($pedido->monto_total) }}</p>
                    </div>
                </div>
                
                <h6><i class="fas fa-shopping-cart me-2"></i>Productos</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedido->items as $item)
                            <tr>
                                <td>{{ $item->producto->nombre }}</td>
                                <td>{{ $item->cantidad }}</td>
                                <td>{{ formatPrice($item->precio_unitario) }}</td>
                                <td>{{ formatPrice($item->subtotal) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <th colspan="3">Total</th>
                                <th>{{ formatPrice($pedido->monto_total) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
                <a href="{{ route('dulceria.boleta', $pedido) }}" class="btn btn-primary" target="_blank">
                    <i class="fas fa-print me-2"></i>Imprimir Boleta
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{ asset('js/admin/dulceria-pedidos.js') }}"></script>
@endpush