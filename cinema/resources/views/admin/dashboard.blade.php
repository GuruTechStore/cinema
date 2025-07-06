{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Dashboard Administrativo')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Estadísticas Principales -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-film display-4"></i>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $peliculasActivas }}</h2>
                    <p class="mb-0">Películas Activas</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card success">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-ticket-alt display-4"></i>
                    </div>
                    <h2 class="fw-bold mb-1">{{ number_format($boletosVendidos) }}</h2>
                    <p class="mb-0">Boletos Vendidos</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card warning">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-candy-cane display-4"></i>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $productosDulceria }}</h2>
                    <p class="mb-0">Productos Dulcería</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card info">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-dollar-sign display-4"></i>
                    </div>
                    <h2 class="fw-bold mb-1">S/ {{ number_format($ventasDelDia, 2) }}</h2>
                    <p class="mb-0">Ventas del Día</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y Tablas -->
    <div class="row g-4">
        <!-- Gráfico de Ventas -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Ventas por Mes
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="ventasChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('admin.peliculas.create') }}" class="btn btn-admin btn-primary">
                            <i class="fas fa-plus me-2"></i>Nueva Película
                        </a>
                        <a href="{{ route('admin.dulceria.create') }}" class="btn btn-admin btn-success">
                            <i class="fas fa-candy-cane me-2"></i>Nuevo Producto
                        </a>
                        <a href="{{ route('admin.dulceria.pedidos') }}" class="btn btn-admin btn-warning">
                            <i class="fas fa-shopping-bag me-2"></i>Ver Pedidos
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-admin btn-danger">
                            <i class="fas fa-eye me-2"></i>Ver Sitio Web
                        </a>
                    </div>
                </div>
            </div>

            <!-- Resumen Rápido -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Resumen Rápido
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Funciones Hoy:</span>
                        <span class="fw-bold">{{ $funcionesHoy ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Reservas Pendientes:</span>
                        <span class="fw-bold text-warning">{{ $reservasPendientes ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Pedidos Dulcería:</span>
                        <span class="fw-bold text-success">{{ $pedidosDulceria ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Actividad Reciente -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Actividad Reciente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Hora</th>
                                    <th>Usuario</th>
                                    <th>Acción</th>
                                    <th>Detalles</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>14:30</td>
                                    <td>María García</td>
                                    <td>Reserva de boletos</td>
                                    <td>Lilo y Stitch - 2 asientos</td>
                                    <td><span class="badge bg-success">Confirmada</span></td>
                                </tr>
                                <tr>
                                    <td>14:25</td>
                                    <td>Carlos López</td>
                                    <td>Pedido dulcería</td>
                                    <td>Combo Pareja - S/ 25.50</td>
                                    <td><span class="badge bg-warning">Preparando</span></td>
                                </tr>
                                <tr>
                                    <td>14:20</td>
                                    <td>Ana Rodríguez</td>
                                    <td>Reserva de boletos</td>
                                    <td>Avatar - 4 asientos</td>
                                    <td><span class="badge bg-success">Confirmada</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Gráfico de ventas
    const ctx = document.getElementById('ventasChart').getContext('2d');
    const ventasChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json(array_column($ventasPorMes, 'mes')),
            datasets: [{
                label: 'Ventas (S/)',
                data: @json(array_column($ventasPorMes, 'ventas')),
                borderColor: '#2B47C5',
                backgroundColor: 'rgba(43, 71, 197, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'S/ ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Actualizar datos cada 30 segundos
    setInterval(function() {
        // Aquí podrías hacer una llamada AJAX para actualizar las estadísticas
        console.log('Actualizando estadísticas...');
    }, 30000);
});
</script>
@endpush