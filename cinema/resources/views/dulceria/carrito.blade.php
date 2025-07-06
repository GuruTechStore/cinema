{{-- resources/views/dulceria/carrito.blade.php --}}
@extends('layouts.app')

@section('title', 'Carrito de Dulcería - Butaca del Salchicon')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>Tu Carrito de Dulcería
                        </h4>
                    </div>
                    <div class="card-body">
                        @if(!empty($carrito))
                            @foreach($carrito as $productoId => $item)
                            <div class="row align-items-center border-bottom py-3" data-producto="{{ $productoId }}">
                                <div class="col-md-2">
                                    <img src="{{ $item['imagen'] ? asset('storage/' . $item['imagen']) : asset('images/dulceria/placeholder-dulceria.jpg') }}" 
                                         class="img-fluid rounded" alt="{{ $item['nombre'] }}">
                                </div>
                                <div class="col-md-4">
                                    <h6 class="fw-bold">{{ $item['nombre'] }}</h6>
                                    <p class="text-muted small mb-0">Precio unitario: {{ formatPrice($item['precio']) }}</p>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <button class="btn btn-outline-secondary btn-sm btn-minus" 
                                                data-producto="{{ $productoId }}">-</button>
                                        <input type="number" class="form-control form-control-sm text-center cantidad-input" 
                                               value="{{ $item['cantidad'] }}" min="1" max="10" 
                                               data-producto="{{ $productoId }}" data-precio="{{ $item['precio'] }}">
                                        <button class="btn btn-outline-secondary btn-sm btn-plus" 
                                                data-producto="{{ $productoId }}">+</button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <span class="fw-bold subtotal" data-producto="{{ $productoId }}">
                                        {{ formatPrice($item['precio'] * $item['cantidad']) }}
                                    </span>
                                </div>
                                <div class="col-md-1">
                                    <button class="btn btn-danger btn-sm btn-eliminar" 
                                            data-producto="{{ $productoId }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-cart display-1 text-muted mb-3"></i>
                                <h4 class="text-muted">Tu carrito está vacío</h4>
                                <p class="text-muted">Agrega algunos productos deliciosos</p>
                                <a href="{{ route('dulceria.index') }}" class="btn btn-primary">
                                    <i class="fas fa-candy-cane me-2"></i>Ir a Dulcería
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if(!empty($carrito))
            <div class="col-lg-4">
                <div class="card shadow-sm position-sticky" style="top: 20px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>Resumen del Pedido
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="resumen-items">
                            @foreach($carrito as $item)
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ $item['nombre'] }} x{{ $item['cantidad'] }}</span>
                                <span>{{ formatPrice($item['precio'] * $item['cantidad']) }}</span>
                            </div>
                            @endforeach
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total:</span>
                            <span id="total-final">{{ formatPrice($total) }}</span>
                        </div>

                        <a href="{{ route('dulceria.checkout') }}" class="btn btn-warning w-100 mt-4">
                            <i class="fas fa-credit-card me-2"></i>Proceder al Pago
                        </a>

                        <a href="{{ route('dulceria.index') }}" class="btn btn-outline-primary w-100 mt-2">
                            <i class="fas fa-arrow-left me-2"></i>Seguir Comprando
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Actualizar cantidad
    $('.btn-plus, .btn-minus').click(function() {
        const productoId = $(this).data('producto');
        const input = $(`.cantidad-input[data-producto="${productoId}"]`);
        let cantidad = parseInt(input.val());
        
        if ($(this).hasClass('btn-plus') && cantidad < 10) {
            cantidad++;
        } else if ($(this).hasClass('btn-minus') && cantidad > 1) {
            cantidad--;
        }
        
        input.val(cantidad);
        actualizarCarrito(productoId, cantidad);
    });

    // Cambio directo en input
    $('.cantidad-input').change(function() {
        const productoId = $(this).data('producto');
        let cantidad = parseInt($(this).val());
        
        if (cantidad < 1) cantidad = 1;
        if (cantidad > 10) cantidad = 10;
        
        $(this).val(cantidad);
        actualizarCarrito(productoId, cantidad);
    });

    // Eliminar producto
    $('.btn-eliminar').click(function() {
        const productoId = $(this).data('producto');
        
        if (confirm('¿Eliminar este producto del carrito?')) {
            window.location.href = `/dulceria/eliminar-carrito/${productoId}`;
        }
    });

    function actualizarCarrito(productoId, cantidad) {
        $.post('{{ route("dulceria.actualizar-carrito") }}', {
            producto_id: productoId,
            cantidad: cantidad
        })
        .done(function() {
            location.reload();
        })
        .fail(function() {
            showAlert('Error al actualizar el carrito', 'danger');
        });
    }
});
</script>
@endpush
