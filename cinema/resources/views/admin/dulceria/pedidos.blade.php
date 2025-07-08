{{-- resources/views/admin/dulceria/pedidos.blade.php --}}
@extends('layouts.admin')

@section('title', 'Gestión de Pedidos Dulcería')
@section('page-title', 'Pedidos de Dulcería')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dulceria.index') }}">Dulcería</a></li>
<li class="breadcrumb-item active">Pedidos</li>
@endsection

@push('styles')
<style>
/* Estilos personalizados para pedidos */
.pedido-card {
    transition: all 0.3s ease;
    border-left: 4px solid #dee2e6;
}

.pedido-card.confirmado { border-left-color: #ffc107; }
.pedido-card.listo { border-left-color: #198754; }
.pedido-card.entregado { border-left-color: #6c757d; }

.pedido-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.estado-badge {
    font-size: 0.85rem;
    padding: 0.5rem 1rem;
    border-radius: 50px;
}

.producto-item {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 0.5rem;
    margin-bottom: 0.5rem;
    border-left: 3px solid #007bff;
}

.tiempo-transcurrido {
    font-size: 0.8rem;
    color: #6c757d;
}

.btn-estado {
    min-width: 120px;
    border-radius: 20px;
}

.notificacion-nueva {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.stats-mini {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 1rem;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .btn-estado { min-width: auto; }
    .producto-item { font-size: 0.85rem; }
}
</style>
@endpush

@section('content')
<!-- Estadísticas superiores -->
<div class="row g-4 mb-4">
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-shopping-bag text-primary fa-2x mb-2"></i>
                <h3 class="text-primary mb-1">{{ number_format($totalPedidos ?? 0) }}</h3>
                <p class="mb-0 small">Total Pedidos</p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
        <div class="card stats-card success">
            <div class="card-body text-center">
                <i class="fas fa-calendar-day text-success fa-2x mb-2"></i>
                <h3 class="text-success mb-1">{{ number_format($pedidosHoy ?? 0) }}</h3>
                <p class="mb-0 small">Pedidos Hoy</p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
        <div class="card stats-card warning">
            <div class="card-body text-center">
                <i class="fas fa-clock text-warning fa-2x mb-2"></i>
                <h3 class="text-warning mb-1">{{ number_format($pedidosPendientes ?? 0) }}</h3>
                <p class="mb-0 small">Por Preparar</p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
        <div class="card stats-card info">
            <div class="card-body text-center">
                <i class="fas fa-bell text-info fa-2x mb-2"></i>
                <h3 class="text-info mb-1">{{ number_format($pedidosListos ?? 0) }}</h3>
                <p class="mb-0 small">Listos</p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-dollar-sign text-success fa-2x mb-2"></i>
                <h3 class="text-success mb-1">S/ {{ number_format($ingresosTotales ?? 0, 2) }}</h3>
                <p class="mb-0 small">Ingresos Totales</p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-chart-line text-info fa-2x mb-2"></i>
                <h3 class="text-info mb-1">S/ {{ number_format($ingresosHoy ?? 0, 2) }}</h3>
                <p class="mb-0 small">Ingresos Hoy</p>
            </div>
        </div>
    </div>
</div>

<!-- Panel de control y filtros -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i>Filtros y Controles
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                        <i class="fas fa-sync-alt me-1"></i>Actualizar
                    </button>
                    <button class="btn btn-outline-success btn-sm" id="auto-refresh-toggle">
                        <i class="fas fa-play me-1"></i>Auto-actualizar
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtros principales -->
                <form method="GET" class="mb-3" id="filtros-form">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label small">Estado:</label>
                            <select name="estado" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option value="confirmado" {{ request('estado') == 'confirmado' ? 'selected' : '' }}>
                                    🔄 Confirmado
                                </option>
                                <option value="listo" {{ request('estado') == 'listo' ? 'selected' : '' }}>
                                    🔔 Listo
                                </option>
                                <option value="entregado" {{ request('estado') == 'entregado' ? 'selected' : '' }}>
                                    ✅ Entregado
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Desde:</label>
                            <input type="date" name="fecha_desde" class="form-control form-control-sm" 
                                   value="{{ request('fecha_desde', date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Hasta:</label>
                            <input type="date" name="fecha_hasta" class="form-control form-control-sm" 
                                   value="{{ request('fecha_hasta', date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Cliente:</label>
                            <input type="text" name="usuario" class="form-control form-control-sm" 
                                   value="{{ request('usuario') }}" placeholder="Nombre o email">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Buscar:</label>
                            <input type="text" name="buscar" class="form-control form-control-sm" 
                                   value="{{ request('buscar') }}" placeholder="Código o producto">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label small">&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Filtros rápidos -->
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-outline-secondary btn-sm filtro-rapido" data-estado="">
                        Todos ({{ $totalPedidos ?? 0 }})
                    </button>
                    <button class="btn btn-outline-warning btn-sm filtro-rapido" data-estado="confirmado">
                        Por Preparar ({{ $pedidosPendientes ?? 0 }})
                    </button>
                    <button class="btn btn-outline-success btn-sm filtro-rapido" data-estado="listo">
                        Listos ({{ $pedidosListos ?? 0 }})
                    </button>
                    <button class="btn btn-outline-info btn-sm filtro-rapido" data-estado="entregado">
                        Entregados
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="filtrarHoy()">
                        Solo Hoy
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de pedidos -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Lista de Pedidos
                    @if(isset($pedidos) && $pedidos->total() > 0)
                        <span class="badge bg-primary">{{ $pedidos->total() }}</span>
                    @endif
                </h5>
                <div id="tiempo-actualizacion" class="small text-muted">
                    Actualizado: <span id="ultima-actualizacion">{{ date('H:i:s') }}</span>
                </div>
            </div>
            
            <div class="card-body p-0">
                @if(isset($pedidos) && $pedidos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 120px;">Código</th>
                                    <th style="width: 200px;">Cliente</th>
                                    <th>Productos</th>
                                    <th style="width: 120px;">Total</th>
                                    <th style="width: 150px;">Estado</th>
                                    <th style="width: 150px;">Tiempo</th>
                                    <th style="width: 120px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pedidos as $pedido)
                                <tr class="pedido-row" data-pedido-id="{{ $pedido->id }}" data-estado="{{ $pedido->estado }}">
                                    <td>
                                        <div>
                                            <strong class="text-primary">{{ $pedido->codigo_pedido }}</strong>
                                            <br>
                                            <small class="text-muted">#{{ $pedido->id }}</small>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div>
                                            <strong>{{ $pedido->user->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $pedido->user->email }}</small>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="productos-lista">
                                            @foreach($pedido->items as $item)
                                                <div class="producto-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="badge bg-primary me-2">{{ $item->cantidad }}x</span>
                                                        <strong>{{ $item->producto->nombre }}</strong>
                                                    </div>
                                                    <small class="text-muted">S/ {{ number_format($item->subtotal, 2) }}</small>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="text-center">
                                            <h5 class="text-success mb-1">S/ {{ number_format($pedido->monto_total, 2) }}</h5>
                                            <small class="text-muted">{{ ucfirst($pedido->metodo_pago) }}</small>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="text-center">
                                            <select class="form-select form-select-sm estado-select btn-estado" 
                                                    data-pedido-id="{{ $pedido->id }}"
                                                    data-estado-actual="{{ $pedido->estado }}">
                                                <option value="confirmado" {{ $pedido->estado == 'confirmado' ? 'selected' : '' }}>
                                                    🔄 Confirmado
                                                </option>
                                                <option value="listo" {{ $pedido->estado == 'listo' ? 'selected' : '' }}>
                                                    🔔 Listo
                                                </option>
                                                <option value="entregado" {{ $pedido->estado == 'entregado' ? 'selected' : '' }}>
                                                    ✅ Entregado
                                                </option>
                                            </select>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="text-center">
                                            <div><strong>{{ $pedido->created_at->format('H:i') }}</strong></div>
                                            <small class="text-muted">{{ $pedido->created_at->format('d/m') }}</small>
                                            <div class="tiempo-transcurrido">
                                                {{ $pedido->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <a href="{{ route('dulceria.boleta', $pedido) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Ver boleta" target="_blank">
                                                <i class="fas fa-receipt me-1"></i>Boleta
                                            </a>
                                            
                                            @if($pedido->estado == 'confirmado')
                                            <button class="btn btn-sm btn-success cambio-rapido" 
                                                    data-pedido-id="{{ $pedido->id }}" 
                                                    data-nuevo-estado="listo"
                                                    title="Marcar como listo">
                                                <i class="fas fa-bell me-1"></i>Listo
                                            </button>
                                            @endif
                                            
                                            @if($pedido->estado == 'listo')
                                            <button class="btn btn-sm btn-info cambio-rapido" 
                                                    data-pedido-id="{{ $pedido->id }}" 
                                                    data-nuevo-estado="entregado"
                                                    title="Marcar como entregado">
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
                    @if($pedidos->hasPages())
                        <div class="d-flex justify-content-center p-3 border-top">
                            {{ $pedidos->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <!-- Estado vacío -->
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay pedidos</h4>
                        <p class="text-muted">No se encontraron pedidos con los filtros aplicados</p>
                        <button class="btn btn-primary" onclick="limpiarFiltros()">
                            <i class="fas fa-refresh me-2"></i>Limpiar Filtros
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmarCambioModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Cambio de Estado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-question-circle fa-3x text-warning"></i>
                </div>
                <p class="text-center">¿Estás seguro de cambiar el estado del pedido?</p>
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-6">
                            <strong>Pedido:</strong><br>
                            <span id="modal-codigo-pedido"></span>
                        </div>
                        <div class="col-6">
                            <strong>Cliente:</strong><br>
                            <span id="modal-cliente"></span>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <strong>Estado actual:</strong><br>
                            <span id="modal-estado-actual"></span>
                        </div>
                        <div class="col-6">
                            <strong>Nuevo estado:</strong><br>
                            <span id="modal-estado-nuevo"></span>
                        </div>
                    </div>
                </div>
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

<!-- Toast para notificaciones -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="toast-notificacion" class="toast" role="alert">
        <div class="toast-header">
            <i class="fas fa-bell text-primary me-2"></i>
            <strong class="me-auto">Notificación</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="toast-mensaje">
            <!-- Mensaje dinámico -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Variables globales
    let autoRefresh = false;
    let refreshInterval;
    let pendingStateChange = null;

    // Inicialización
    initializeEventHandlers();
    updateLastRefreshTime();

    // Configuración de eventos
    function initializeEventHandlers() {
        // Auto-refresh toggle
        $('#auto-refresh-toggle').click(toggleAutoRefresh);
        
        // Filtros rápidos
        $('.filtro-rapido').click(function() {
            const estado = $(this).data('estado');
            $('select[name="estado"]').val(estado);
            $('#filtros-form').submit();
        });

        // Cambio de estado via select
        $('.estado-select').change(handleEstadoChange);
        
        // Cambio rápido de estado
        $('.cambio-rapido').click(handleCambioRapido);
        
        // Confirmación de cambio
        $('#confirmar-cambio-estado').click(confirmarCambioEstado);
        
        // Búsqueda en tiempo real
        $('input[name="buscar"]').on('input', debounce(function() {
            $('#filtros-form').submit();
        }, 500));
    }

    // Auto-refresh functionality
    function toggleAutoRefresh() {
        autoRefresh = !autoRefresh;
        const btn = $('#auto-refresh-toggle');
        
        if (autoRefresh) {
            btn.removeClass('btn-outline-success').addClass('btn-success')
               .html('<i class="fas fa-pause me-1"></i>Pausar');
            startAutoRefresh();
            showToast('Auto-actualización activada', 'success');
        } else {
            btn.removeClass('btn-success').addClass('btn-outline-success')
               .html('<i class="fas fa-play me-1"></i>Auto-actualizar');
            stopAutoRefresh();
            showToast('Auto-actualización pausada', 'info');
        }
    }

    function startAutoRefresh() {
        refreshInterval = setInterval(function() {
            if (!$('.modal').hasClass('show')) {
                refreshData();
            }
        }, 30000); // 30 segundos
    }

    function stopAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    }

    function refreshData() {
        $.get(window.location.href, function(data) {
            // Actualizar solo la tabla sin recargar la página completa
            const newTableBody = $(data).find('tbody').html();
            if (newTableBody) {
                $('tbody').html(newTableBody);
                initializeEventHandlers();
                updateLastRefreshTime();
                
                // Verificar nuevos pedidos
                checkForNewOrders();
            }
        }).fail(function() {
            showToast('Error al actualizar datos', 'error');
        });
    }

    function updateLastRefreshTime() {
        $('#ultima-actualizacion').text(new Date().toLocaleTimeString());
    }

    // Manejo de cambios de estado
    function handleEstadoChange() {
        const pedidoId = $(this).data('pedido-id');
        const estadoActual = $(this).data('estado-actual');
        const estadoNuevo = $(this).val();
        
        if (estadoActual !== estadoNuevo) {
            prepareModa l(pedidoId, estadoActual, estadoNuevo);
        }
    }

    function handleCambioRapido() {
        const pedidoId = $(this).data('pedido-id');
        const estadoNuevo = $(this).data('nuevo-estado');
        const estadoActual = $(this).closest('tr').find('.estado-select').data('estado-actual');
        
        prepareModal(pedidoId, estadoActual, estadoNuevo);
    }

    function prepareModal(pedidoId, estadoActual, estadoNuevo) {
        const row = $(`tr[data-pedido-id="${pedidoId}"]`);
        const codigoPedido = row.find('td:first strong').text();
        const cliente = row.find('td:nth-child(2) strong').text();

        $('#modal-codigo-pedido').text(codigoPedido);
        $('#modal-cliente').text(cliente);
        $('#modal-estado-actual').text(getEstadoTexto(estadoActual));
        $('#modal-estado-nuevo').text(getEstadoTexto(estadoNuevo));

        pendingStateChange = {
            pedidoId: pedidoId,
            estadoNuevo: estadoNuevo
        };

        $('#confirmarCambioModal').modal('show');
    }

    function confirmarCambioEstado() {
        if (!pendingStateChange) return;

        const { pedidoId, estadoNuevo } = pendingStateChange;
        
        // Mostrar loading
        $('#confirmar-cambio-estado').html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...')
                                     .prop('disabled', true);

        // Enviar cambio
        $.ajax({
            url: `/admin/dulceria-pedidos/${pedidoId}/estado`,
            method: 'PATCH',
            data: {
                estado: estadoNuevo,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#confirmarCambioModal').modal('hide');
                showToast('Estado cambiado exitosamente', 'success');
                
                // Actualizar la fila
                updateRowState(pedidoId, estadoNuevo);
                
                // Actualizar estadísticas
                refreshData();
            },
            error: function() {
                showToast('Error al cambiar el estado', 'error');
            },
            complete: function() {
                $('#confirmar-cambio-estado').html('<i class="fas fa-check me-2"></i>Confirmar Cambio')
                                             .prop('disabled', false);
                pendingStateChange = null;
            }
        });
    }

    function updateRowState(pedidoId, nuevoEstado) {
        const row = $(`tr[data-pedido-id="${pedidoId}"]`);
        row.attr('data-estado', nuevoEstado);
        row.find('.estado-select').val(nuevoEstado).data('estado-actual', nuevoEstado);
        
        // Actualizar botones de acción
        const actionsCell = row.find('td:last-child .d-flex');
        actionsCell.find('.cambio-rapido').remove();
        
        if (nuevoEstado === 'confirmado') {
            actionsCell.append(`
                <button class="btn btn-sm btn-success cambio-rapido" 
                        data-pedido-id="${pedidoId}" 
                        data-nuevo-estado="listo">
                    <i class="fas fa-bell me-1"></i>Listo
                </button>
            `);
        } else if (nuevoEstado === 'listo') {
            actionsCell.append(`
                <button class="btn btn-sm btn-info cambio-rapido" 
                        data-pedido-id="${pedidoId}" 
                        data-nuevo-estado="entregado">
                    <i class="fas fa-check me-1"></i>Entregar
                </button>
            `);
        }
        
        // Re-attach events
        actionsCell.find('.cambio-rapido').click(handleCambioRapido);
        
        // Animación visual
        row.addClass('estado-cambiado');
        setTimeout(() => row.removeClass('estado-cambiado'), 2000);
    }

    // Utilidades
    function getEstadoTexto(estado) {
        const estados = {
            'confirmado': '🔄 Confirmado',
            'listo': '🔔 Listo para recoger',
            'entregado': '✅ Entregado'
        };
        return estados[estado] || estado;
    }

    function showToast(mensaje, tipo = 'info') {
        $('#toast-mensaje').text(mensaje);
        const toast = new bootstrap.Toast(document.getElementById('toast-notificacion'));
        toast.show();
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function checkForNewOrders() {
        // Verificar si hay nuevos pedidos pendientes
        const pendientes = $('.estado-select option[value="confirmado"]:selected').length;
        
        if (pendientes > 0) {
            document.title = `(${pendientes}) Pedidos Dulcería - Admin`;
            
            // Notificación del navegador si está permitido
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('Pedidos pendientes', {
                    body: `Tienes ${pendientes} pedidos por preparar`,
                    icon: '/favicon.ico',
                    tag: 'pedidos-pendientes'
                });
            }
        } else {
            document.title = 'Pedidos Dulcería - Admin';
        }
    }

    // Funciones globales para botones
    window.filtrarHoy = function() {
        const hoy = new Date().toISOString().split('T')[0];
        $('input[name="fecha_desde"]').val(hoy);
        $('input[name="fecha_hasta"]').val(hoy);
        $('#filtros-form').submit();
    };

    window.limpiarFiltros = function() {
        window.location.href = window.location.pathname;
    };

    // Solicitar permisos de notificación al cargar
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }

    // Keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl + R para refresh
        if (e.ctrlKey && e.keyCode === 82) {
            e.preventDefault();
            refreshData();
        }
        
        // Ctrl + A para toggle auto-refresh
        if (e.ctrlKey && e.keyCode === 65) {
            e.preventDefault();
            toggleAutoRefresh();
        }
        
        // Escape para cerrar modales
        if (e.keyCode === 27) {
            $('.modal').modal('hide');
        }
    });

    // Indicadores visuales de tiempo
    function updateTimeIndicators() {
        $('.tiempo-transcurrido').each(function() {
            const row = $(this).closest('tr');
            const estado = row.data('estado');
            const createdTime = new Date(row.find('td:last-child div:first strong').text());
            const now = new Date();
            const diffMinutes = Math.floor((now - createdTime) / (1000 * 60));
            
            // Cambiar color según el tiempo transcurrido y estado
            if (estado === 'confirmado' && diffMinutes > 15) {
                row.addClass('table-warning');
            } else if (estado === 'listo' && diffMinutes > 30) {
                row.addClass('table-danger');
            }
        });
    }

    // Actualizar indicadores cada minuto
    setInterval(updateTimeIndicators, 60000);
    updateTimeIndicators(); // Ejecutar inmediatamente

    // Efectos sonoros para notificaciones (opcional)
    function playNotificationSound() {
        try {
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IAAAAAAQAAABAAAA...');
            audio.volume = 0.3;
            audio.play().catch(() => {}); // Ignorar errores si no se puede reproducir
        } catch (e) {
            // Silenciar errores de audio
        }
    }

    // Exportar datos (función adicional)
    window.exportarPedidos = function() {
        const params = new URLSearchParams(window.location.search);
        params.append('export', 'excel');
        window.location.href = `/admin/dulceria-pedidos?${params.toString()}`;
    };

    // Imprimir reporte
    window.imprimirReporte = function() {
        window.print();
    };

    // Función para marcar todos como listos (batch operation)
    window.marcarTodosListos = function() {
        const pendientes = $('.estado-select').filter(function() {
            return $(this).val() === 'confirmado';
        });
        
        if (pendientes.length === 0) {
            showToast('No hay pedidos pendientes', 'info');
            return;
        }
        
        if (confirm(`¿Marcar ${pendientes.length} pedidos como listos?`)) {
            let completed = 0;
            
            pendientes.each(function() {
                const pedidoId = $(this).data('pedido-id');
                
                $.ajax({
                    url: `/admin/dulceria-pedidos/${pedidoId}/estado`,
                    method: 'PATCH',
                    data: {
                        estado: 'listo',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        completed++;
                        if (completed === pendientes.length) {
                            showToast('Todos los pedidos marcados como listos', 'success');
                            refreshData();
                        }
                    }
                });
            });
        }
    };

    // CSS dinámico para estados
    const style = document.createElement('style');
    style.textContent = `
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
        
        @media print {
            .btn, .modal, .toast-container { display: none !important; }
            .card { border: none !important; box-shadow: none !important; }
        }
    `;
    document.head.appendChild(style);
});

// Función global para actualización manual
function actualizarPedidos() {
    location.reload();
}

// Función global para notificaciones de escritorio
function activarNotificaciones() {
    if ('Notification' in window) {
        Notification.requestPermission().then(function(permission) {
            if (permission === 'granted') {
                new Notification('Notificaciones activadas', {
                    body: 'Recibirás alertas de nuevos pedidos',
                    icon: '/favicon.ico'
                });
            }
        });
    }
}

// Service Worker para notificaciones offline (avanzado)
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {
        // Ignorar errores si no hay service worker
    });
}
</script>
@endpush