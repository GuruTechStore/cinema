{{-- resources/views/dulceria/mis-pedidos.blade.php --}}
@extends('layouts.app')

@section('title', 'Mis Pedidos Dulcería - Butaca del Salchichon')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">
                    <i class="fas fa-shopping-bag me-2 text-warning"></i>Mis Pedidos Dulcería
                </h2>
                <div>
                    <a href="{{ route('dulceria.index') }}" class="btn btn-warning me-2">
                        <i class="fas fa-candy-cane me-2"></i>Ver Dulcería
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-primary">
                        <i class="fas fa-home me-2"></i>Volver al Inicio
                    </a>
                </div>
            </div>

            @if($pedidos->count() > 0)
                <div class="row">
                    @foreach($pedidos as $pedido)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-warning text-dark">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold">Pedido #{{ $pedido->id }}</h6>
                                    <small>{{ $pedido->created_at->format('d M Y - H:i') }}</small>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <!-- Código de pedido destacado -->
                                <div class="bg-warning text-dark p-2 rounded text-center mb-3">
                                    <small>Código de Pedido</small>
                                    <div class="fw-bold fs-5">{{ $pedido->codigo_pedido }}</div>
                                </div>

                                <!-- Estado del pedido -->
                                <div class="text-center mb-3">
                                    @if($pedido->estado == 'confirmado')
                                        <span class="badge bg-info fs-6">
                                            <i class="fas fa-clock me-1"></i>Confirmado
                                        </span>
                                    @elseif($pedido->estado == 'preparando')
                                        <span class="badge bg-warning fs-6">
                                            <i class="fas fa-utensils me-1"></i>Preparando
                                        </span>
                                    @elseif($pedido->estado == 'listo')
                                        <span class="badge bg-success fs-6">
                                            <i class="fas fa-bell me-1"></i>Listo para recoger
                                        </span>
                                    @elseif($pedido->estado == 'entregado')
                                        <span class="badge bg-secondary fs-6">
                                            <i class="fas fa-check-circle me-1"></i>Entregado
                                        </span>
                                    @elseif($pedido->estado == 'cancelado')
                                        <span class="badge bg-danger fs-6">
                                            <i class="fas fa-times me-1"></i>Cancelado
                                        </span>
                                    @endif
                                </div>

                                <!-- Productos del pedido -->
                                <div class="mb-3">
                                    <h6 class="fw-bold">Productos:</h6>
                                    @foreach($pedido->items as $item)
                                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                        <div>
                                            <small class="fw-bold">{{ $item->producto->nombre }}</small>
                                            <br>
                                            <small class="text-muted">{{ formatPrice($item->precio_unitario) }} x {{ $item->cantidad }}</small>
                                        </div>
                                        <small class="fw-bold">{{ formatPrice($item->subtotal) }}</small>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Total -->
                                <div class="d-flex justify-content-between align-items-center fw-bold fs-5 mb-3">
                                    <span>Total:</span>
                                    <span class="text-success">{{ formatPrice($pedido->monto_total) }}</span>
                                </div>

                                <!-- Método de pago -->
                                <div class="mb-2">
                                    <strong>Método de pago:</strong> 
                                    @if($pedido->metodo_pago == 'yape')
                                        <span class="text-purple">Yape</span>
                                    @elseif($pedido->metodo_pago == 'visa')
                                        <span class="text-primary">Visa</span>
                                    @elseif($pedido->metodo_pago == 'mastercard')
                                        <span class="text-warning">Mastercard</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('dulceria.boleta', $pedido) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-receipt me-1"></i>Ver Boleta
                                    </a>
                                    
                                    @if($pedido->estado == 'listo')
                                        <div class="alert alert-success alert-sm mb-0 py-2">
                                            <small>
                                                <i class="fas fa-bell me-1"></i>
                                                ¡Tu pedido está listo! Dirígete al counter de dulcería.
                                            </small>
                                        </div>
                                    @elseif($pedido->estado == 'confirmado' || $pedido->estado == 'preparando')
                                        <div class="alert alert-info alert-sm mb-0 py-2">
                                            <small>
                                                <i class="fas fa-clock me-1"></i>
                                                Tiempo estimado: 10-15 minutos
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $pedidos->links() }}
                </div>

            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-shopping-bag display-1 text-muted"></i>
                    </div>
                    <h4 class="text-muted mb-3">No tienes pedidos aún</h4>
                    <p class="text-muted mb-4">¡Explora nuestra dulcería y disfruta de deliciosos snacks!</p>
                    <a href="{{ route('dulceria.index') }}" class="btn btn-warning btn-lg">
                        <i class="fas fa-candy-cane me-2"></i>Ver Dulcería
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection