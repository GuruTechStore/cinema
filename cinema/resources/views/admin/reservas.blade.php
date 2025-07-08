{{-- resources/views/admin/reservas.blade.php --}}
@extends('layouts.admin')

@section('title', 'Gestión de Reservas')
@section('page-title', 'Reservas de Boletos')

@section('breadcrumb')
<li class="breadcrumb-item active">Reservas</li>
@endsection

@section('content')
<!-- Estadísticas rápidas -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card">
            <div class="card-body text-center">
                <h3 class="text-primary">{{ number_format($totalReservas) }}</h3>
                <p class="mb-0">Total Reservas</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card success">
            <div class="card-body text-center">
                <h3 class="text-success">{{ number_format($reservasHoy) }}</h3>
                <p class="mb-0">Reservas Hoy</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card warning">
            <div class="card-body text-center">
                <h3 class="text-warning">S/ {{ number_format($ingresosTotales, 2) }}</h3>
                <p class="mb-0">Ingresos Totales</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card info">
            <div class="card-body text-center">
                <h3 class="text-info">S/ {{ number_format($ingresosHoy, 2) }}</h3>
                <p class="mb-0">Ingresos Hoy</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-ticket-alt me-2"></i>Gestión de Reservas
                </h5>
            </div>
            
            <div class="card-body">
                <!-- Filtros -->
                <form method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <select name="estado" class="form-select">
                                <option value="">Todos los estados</option>
                                <option value="confirmada" {{ request('estado') == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                                <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="fecha_desde" class="form-control" 
                                   value="{{ request('fecha_desde') }}" placeholder="Fecha desde">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="fecha_hasta" class="form-control" 
                                   value="{{ request('fecha_hasta') }}" placeholder="Fecha hasta">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="pelicula" class="form-control" 
                                   value="{{ request('pelicula') }}" placeholder="Película">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="usuario" class="form-control" 
                                   value="{{ request('usuario') }}" placeholder="Usuario">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Tabla de reservas -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Película</th>
                                <th>Función</th>
                                <th>Asientos</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservas as $reserva)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#{{ $reserva->id }}</span>
                                    <br>
                                    <small class="text-muted">{{ $reserva->codigo_reserva }}</small>
                                </td>
                                
                                <td>
                                    <strong>{{ $reserva->user->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $reserva->user->email }}</small>
                                </td>
                                
                                <td>
                                    <strong>{{ $reserva->funcion->pelicula->titulo }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $reserva->funcion->sala->cine->nombre }}
                                    </small>
                                </td>
                                
                                <td>
                                    <strong>{{ $reserva->funcion->fecha_funcion->format('d/m/Y') }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $reserva->funcion->hora_funcion->format('H:i') }} - 
                                        {{ $reserva->funcion->sala->nombre }}
                                    </small>
                                </td>
                                
                                <td>
                                    <span class="badge bg-info">{{ $reserva->total_boletos }} boletos</span>
                                    <br>
                                    <small class="text-muted">{{ $reserva->getAsientosFormateados() }}</small>
                                </td>
                                
                                <td>
                                    <strong class="text-success">S/ {{ number_format($reserva->monto_total, 2) }}</strong>
                                    <br>
                                    <small class="text-muted">{{ ucfirst($reserva->metodo_pago) }}</small>
                                </td>
                                
                                <td>
                                    @if($reserva->estado == 'confirmada')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Confirmada
                                        </span>
                                    @elseif($reserva->estado == 'pendiente')
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Pendiente
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times me-1"></i>Cancelada
                                        </span>
                                    @endif
                                </td>
                                
                                <td>
                                    <strong>{{ $reserva->created_at->format('d/m/Y') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $reserva->created_at->format('H:i') }}</small>
                                </td>
                                
                                <td>
                                    <div class="btn-group-vertical" role="group">
                                        <a href="{{ route('reservas.boleta', $reserva) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Ver boleta" target="_blank">
                                            <i class="fas fa-receipt"></i>
                                        </a>
                                        
                                        @if($reserva->estado == 'pendiente')
                                        <button class="btn btn-sm btn-outline-success" 
                                                onclick="cambiarEstado({{ $reserva->id }}, 'confirmada')"
                                                title="Confirmar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="cambiarEstado({{ $reserva->id }}, 'cancelada')"
                                                title="Cancelar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-ticket-alt fa-3x mb-3"></i>
                                        <h5>No hay reservas</h5>
                                        <p>No se encontraron reservas con los filtros aplicados</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($reservas->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $reservas->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function cambiarEstado(reservaId, nuevoEstado) {
    const mensaje = nuevoEstado === 'confirmada' ? 
        '¿Confirmar esta reserva?' : 
        '¿Cancelar esta reserva?';
    
    if (confirm(mensaje)) {
        // Aquí podrías hacer una llamada AJAX para cambiar el estado
        fetch(`/admin/reservas/${reservaId}/estado`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                estado: nuevoEstado
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error al cambiar el estado');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cambiar el estado');
        });
    }
}

// Exportar a Excel (opcional)
function exportarExcel() {
    const params = new URLSearchParams(window.location.search);
    params.append('export', 'excel');
    window.location.href = `/admin/reservas?${params.toString()}`;
}
</script>
@endpush