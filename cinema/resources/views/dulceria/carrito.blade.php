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
                                            data-producto="{{ $productoId }}"
                                            title="Eliminar producto">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach

                            <div class="mt-3">
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-outline-danger" onclick="limpiarCarrito()">
                                        <i class="fas fa-trash me-2"></i>Vaciar Carrito
                                    </button>
                                    <div class="text-end">
                                        <p class="mb-1">Subtotal: <span id="subtotal-carrito">{{ formatPrice($total) }}</span></p>
                                        <p class="mb-0 fw-bold fs-5">Total: <span id="total-carrito">{{ formatPrice($total) }}</span></p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-cart display-1 text-muted mb-3"></i>
                                <h5 class="text-muted">Tu carrito está vacío</h5>
                                <p class="text-muted">Agrega algunos productos deliciosos de nuestra dulcería</p>
                                <a href="{{ route('dulceria.index') }}" class="btn btn-warning">
                                    <i class="fas fa-candy-cane me-2"></i>Ir a Dulcería
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Resumen del pedido -->
            @if(!empty($carrito))
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>Resumen del Pedido
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            @foreach($carrito as $item)
                            <div class="d-flex justify-content-between small mb-1">
                                <span>{{ $item['nombre'] }} ({{ $item['cantidad'] }}x)</span>
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
    console.log('🛒 Carrito de dulcería cargado');
    
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

    // Eliminar producto - VERSIÓN CORREGIDA
    $('.btn-eliminar').click(function(e) {
        e.preventDefault();
        const productoId = $(this).data('producto');
        
        console.log('🗑️ Intentando eliminar producto:', productoId);
        
        if (confirm('¿Eliminar este producto del carrito?')) {
            // Mostrar loading
            showLoadingSpinner($(this));
            
            // Usar la ruta nombrada correcta
            window.location.href = `{{ route('dulceria.eliminar-carrito', ':id') }}`.replace(':id', productoId);
        }
    });

    function actualizarCarrito(productoId, cantidad) {
        $.post('{{ route("dulceria.actualizar-carrito") }}', {
            producto_id: productoId,
            cantidad: cantidad,
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            console.log('✅ Carrito actualizado');
            location.reload();
        })
        .fail(function(xhr, status, error) {
            console.error('❌ Error al actualizar carrito:', error);
            showAlert('Error al actualizar el carrito', 'danger');
        });
    }
    
    // Función para limpiar carrito completo
    window.limpiarCarrito = function() {
        if (confirm('¿Estás seguro de vaciar todo el carrito?')) {
            // Aquí puedes agregar la funcionalidad para limpiar todo el carrito
            // Por ahora, redirigir a dulcería
            window.location.href = '{{ route("dulceria.index") }}';
        }
    };
    
    // Función para mostrar spinner de carga
    function showLoadingSpinner(element) {
        const originalHtml = element.html();
        element.data('original-html', originalHtml);
        element.html('<i class="fas fa-spinner fa-spin"></i>');
        element.prop('disabled', true);
    }
});
</script>
@endpush