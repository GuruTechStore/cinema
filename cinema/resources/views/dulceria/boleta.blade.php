{{-- resources/views/dulceria/boleta.blade.php --}}
@extends('layouts.app')

@section('title', 'Boleta de Dulcería - Butaca del Salchichon')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg">
                <div class="card-header bg-warning text-dark text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-candy-cane me-2"></i>¡Pedido Confirmado!
                    </h3>
                </div>

                <div class="card-body p-4">
                    <!-- Información del pedido -->
                    <div class="text-center mb-4">
                        <div class="bg-primary text-white p-4 rounded mb-3">
                            <h4 class="fw-bold text-white">Pedido Dulcería</h4>
                            <p class="mb-0">{{ $pedido->created_at->format('d M Y - H:i') }}</p>
                            <span class="badge bg-light text-dark">{{ ucfirst($pedido->estado) }}</span>
                        </div>

                        <!-- Código de pedido -->
                        <div class="bg-warning text-dark p-3 rounded d-inline-block">
                            <strong>Código de Pedido</strong>
                            <h3 class="fw-bold">{{ $pedido->codigo_pedido }}</h3>
                        </div>
                    </div>

                    <!-- Items del pedido -->
                    <div class="card bg-light mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Productos Pedidos</h5>
                        </div>
                        <div class="card-body">
                            @foreach($pedido->items as $item)
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <strong>{{ $item->producto->nombre }}</strong>
                                    <br>
                                    <small class="text-muted">{{ formatPrice($item->precio_unitario) }} x {{ $item->cantidad }}</small>
                                </div>
                                <span class="fw-bold">{{ formatPrice($item->subtotal) }}</span>
                            </div>
                            @endforeach
                            
                            <div class="d-flex justify-content-between align-items-center pt-3 fw-bold fs-5">
                                <span>TOTAL:</span>
                                <span>{{ formatPrice($pedido->monto_total) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Estado del pedido -->
                    <div class="row text-center mb-4">
                        <div class="col-3">
                            <div class="text-success">
                                <i class="fas fa-check-circle fs-1"></i>
                                <p class="small">Confirmado</p>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="{{ $pedido->estado == 'listo' || $pedido->estado == 'entregado' ? 'text-success' : 'text-muted' }}">
                                <i class="fas fa-clock fs-1"></i>
                                <p class="small">Preparando</p>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="{{ $pedido->estado == 'listo' || $pedido->estado == 'entregado' ? 'text-success' : 'text-muted' }}">
                                <i class="fas fa-bell fs-1"></i>
                                <p class="small">Listo</p>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="{{ $pedido->estado == 'entregado' ? 'text-success' : 'text-muted' }}">
                                <i class="fas fa-smile fs-1"></i>
                                <p class="small">Entregado</p>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="text-center">
                        <button class="btn btn-warning btn-lg me-3" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Imprimir Boleta
                        </button>
                        <a href="{{ route('dulceria.mis-pedidos') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-list me-2"></i>Mis Pedidos
                        </a>
                    </div>

                    <!-- Información -->
                    <div class="alert alert-info mt-4">
                        <h6><i class="fas fa-info-circle me-2"></i>Información del Pedido:</h6>
                        <ul class="mb-0 small">
                            <li>Presenta este código en el counter de dulcería</li>
                            <li>Tu pedido estará listo en aproximadamente 10-15 minutos</li>
                            <li>Te notificaremos cuando esté listo para recoger</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection