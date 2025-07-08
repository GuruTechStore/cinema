{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Dashboard Administrativo')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Estadísticas Principales -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="stats-icon bg-primary text-white rounded-circle mx-auto mb-3">
                        <i class="fas fa-film"></i>
                    </div>
                    <h2 class="fw-bold mb-1 text-primary">{{ $peliculasActivas }}</h2>
                    <p class="text-muted mb-0">Películas Activas</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="stats-icon bg-success text-white rounded-circle mx-auto mb-3">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <h2 class="fw-bold mb-1 text-success">{{ number_format($boletosVendidos) }}</h2>
                    <p class="text-muted mb-0">Boletos Vendidos</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="stats-icon bg-warning text-white rounded-circle mx-auto mb-3">
                        <i class="fas fa-candy-cane"></i>
                    </div>
                    <h2 class="fw-bold mb-1 text-warning">{{ $productosDulceria }}</h2>
                    <p class="text-muted mb-0">Productos Dulcería</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="stats-icon bg-info text-white rounded-circle mx-auto mb-3">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h2 class="fw-bold mb-1 text-info">S/ {{ number_format($ventasDelDia, 2) }}</h2>
                    <p class="text-muted mb-0">Ventas del Día</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y Contenido Principal -->
    <div class="row g-4">
        <!-- Gráfico de Ventas -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2 text-primary"></i>Ventas por Mes
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary active" data-periodo="6">6 Meses</button>
                            <button type="button" class="btn btn-outline-primary" data-periodo="12">1 Año</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(count($ventasPorMes) > 0)
                        <div style="height: 350px;">
                            <canvas id="ventasChart"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line display-1 text-muted mb-3"></i>
                            <h5 class="text-muted">Sin datos de ventas</h5>
                            <p class="text-muted">No hay ventas registradas para mostrar en el gráfico</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2 text-warning"></i>Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('admin.peliculas.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Nueva Película
                        </a>
                        <a href="{{ route('admin.dulceria.create') }}" class="btn btn-success">
                            <i class="fas fa-candy-cane me-2"></i>Nuevo Producto
                        </a>
                        <a href="{{ route('admin.dulceria.pedidos') }}" class="btn btn-warning">
                            <i class="fas fa-shopping-bag me-2"></i>Ver Pedidos
                        </a>
                        <a href="{{ route('home') }}" target="_blank" class="btn btn-danger">
                            <i class="fas fa-external-link-alt me-2"></i>Ver Sitio Web
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen Rápido y Actividad Reciente -->
    <div class="row g-4 mt-4">
        <!-- Resumen Rápido -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2 text-info"></i>Resumen Rápido
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-12 mb-3">
                            <h5 class="fw-bold text-primary mb-0">
                                {{ \App\Models\Funcion::whereDate('fecha_funcion', today())->count() }}
                            </h5>
                            <small class="text-muted">Funciones Hoy</small>
                        </div>
                        <div class="col-6">
                            <h6 class="fw-bold text-success mb-0">
                                {{ \App\Models\Reserva::where('estado', 'pendiente')->count() }}
                            </h6>
                            <small class="text-muted">Reservas Pendientes</small>
                        </div>
                        <div class="col-6">
                            <h6 class="fw-bold text-warning mb-0">
                                {{ \App\Models\PedidoDulceria::where('estado', 'preparando')->count() }}
                            </h6>
                            <small class="text-muted">Pedidos Dulcería</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-clock me-2 text-secondary"></i>Actividad Reciente
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Hora</th>
                                    <th>Usuario</th>
                                    <th>Acción</th>
                                    <th>Detalles</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\Reserva::with(['user', 'funcion.pelicula'])->latest()->take(5)->get() as $reserva)
                                <tr>
                                    <td>{{ $reserva->created_at->format('H:i') }}</td>
                                    <td>{{ $reserva->user->name ?? 'Usuario' }}</td>
                                    <td>Reserva de boletos</td>
                                    <td>{{ $reserva->funcion->pelicula->titulo ?? 'Película' }} - {{ $reserva->total_boletos }} asientos</td>
                                    <td>
                                        @if($reserva->estado == 'confirmada')
                                            <span class="badge bg-success">Confirmada</span>
                                        @elseif($reserva->estado == 'pendiente')
                                            <span class="badge bg-warning">Pendiente</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($reserva->estado) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">
                                        <i class="fas fa-inbox me-2"></i>No hay actividad reciente
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
.stats-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border-radius: 12px;
}

.stats-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.stats-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.card {
    border-radius: 12px;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.75rem;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .stats-icon {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }
    
    h2.fw-bold {
        font-size: 1.5rem;
    }
}
</style>
@endpush

@push('scripts')
<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
$(document).ready(function() {
    console.log('📊 Inicializando dashboard...');
    
    // Datos para el gráfico
    const ventasData = @json($ventasPorMes);
    console.log('Datos de ventas:', ventasData);
    
    @if(count($ventasPorMes) > 0)
    // Configurar gráfico de ventas
    const ctx = document.getElementById('ventasChart').getContext('2d');
    
    const ventasChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ventasData.map(item => item.mes),
            datasets: [{
                label: 'Ventas (S/)',
                data: ventasData.map(item => parseFloat(item.ventas) || 0),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#0d6efd',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#0d6efd',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            return 'Ventas: S/ ' + context.parsed.y.toLocaleString('es-PE', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6c757d'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    ticks: {
                        color: '#6c757d',
                        callback: function(value) {
                            return 'S/ ' + value.toLocaleString('es-PE');
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
    
    // Cambiar período del gráfico
    document.querySelectorAll('[data-periodo]').forEach(btn => {
        btn.addEventListener('click', function() {
            const periodo = this.dataset.periodo;
            
            // Actualizar botones activos
            document.querySelectorAll('[data-periodo]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Aquí podrías hacer una llamada AJAX para obtener datos del período seleccionado
            console.log('Cambiando período a:', periodo, 'meses');
            
            // Por ahora solo mostramos un mensaje
            showAlert('Cambiando vista a ' + periodo + ' meses...', 'info');
        });
    });
    @else
    console.warn('⚠️ No hay datos de ventas para mostrar en el gráfico');
    @endif
    
    // Actualizar estadísticas cada 30 segundos
    setInterval(function() {
        console.log('🔄 Actualizando estadísticas...');
        // Aquí podrías hacer llamadas AJAX para actualizar las estadísticas en tiempo real
    }, 30000);
    
    // Función para mostrar alertas
    function showAlert(message, type = 'info') {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', alertHtml);
        
        // Auto-remove after 3 seconds
        setTimeout(() => {
            const alert = document.querySelector('.alert:last-child');
            if (alert) {
                alert.remove();
            }
        }, 3000);
    }
    
    console.log('✅ Dashboard inicializado correctamente');
});
</script>
@endpush