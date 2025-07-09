{{-- resources/views/dulceria/carrito.blade.php --}}
@extends('layouts.app')

@section('title', 'Carrito de Dulcería - Butaca del Salchichon')

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
                                    @if($item['imagen'])
                                        <img src="{{ asset('storage/' . $item['imagen']) }}" 
                                             class="img-fluid rounded" 
                                             alt="{{ $item['nombre'] }}"
                                             style="width: 80px; height: 80px; object-fit: cover;"
                                             onerror="this.src='{{ asset('images/dulceria/placeholder-dulceria.jpg') }}'">
                                    @else
                                        <img src="{{ asset('images/dulceria/placeholder-dulceria.jpg') }}" 
                                             class="img-fluid rounded" 
                                             alt="{{ $item['nombre'] }}"
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <h6 class="fw-bold">{{ $item['nombre'] }}</h6>
                                    <p class="text-muted small mb-0">Precio unitario: S/ {{ number_format($item['precio'], 2) }}</p>
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
                                        S/ {{ number_format($item['precio'] * $item['cantidad'], 2) }}
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
                                <span>S/ {{ number_format($item['precio'] * $item['cantidad'], 2) }}</span>
                            </div>
                            @endforeach
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total a Pagar</span>
                            <span id="total-general">S/ {{ number_format($total, 2) }}</span>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <a href="{{ route('dulceria.checkout') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-credit-card me-2"></i>Proceder al Pago
                            </a>
                            <a href="{{ route('dulceria.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Seguir Comprando
                            </a>
                        </div>

                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Compra 100% segura
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        @if(!empty($carrito))
        <!-- Productos Relacionados -->
        <div class="row mt-5">
            <div class="col-12">
                <h4 class="mb-4">
                    <i class="fas fa-star me-2 text-warning"></i>
                    Productos Recomendados
                </h4>
                <div class="row g-3">
                    <!-- Aquí podrías mostrar productos recomendados -->
                    <div class="col-md-3">
                        <div class="card border-0 bg-light">
                            <div class="card-body text-center py-4">
                                <i class="fas fa-plus-circle fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-2">¿Algo más?</p>
                                <a href="{{ route('dulceria.index') }}" class="btn btn-sm btn-outline-primary">
                                    Ver más productos
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection

@push('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
$(document).ready(function() {
    // Incrementar cantidad
    $('.btn-plus').click(function() {
        const productoId = $(this).data('producto');
        const input = $(`.cantidad-input[data-producto="${productoId}"]`);
        let cantidad = parseInt(input.val());
        if (cantidad < 10) {
            cantidad++;
            input.val(cantidad);
            actualizarCantidad(productoId, cantidad);
        }
    });

    // Decrementar cantidad
    $('.btn-minus').click(function() {
        const productoId = $(this).data('producto');
        const input = $(`.cantidad-input[data-producto="${productoId}"]`);
        let cantidad = parseInt(input.val());
        if (cantidad > 1) {
            cantidad--;
            input.val(cantidad);
            actualizarCantidad(productoId, cantidad);
        }
    });

    // Cambio directo en el input
    $('.cantidad-input').change(function() {
        const productoId = $(this).data('producto');
        let cantidad = parseInt($(this).val());
        
        // Validar rango
        if (cantidad < 1) {
            cantidad = 1;
            $(this).val(cantidad);
        } else if (cantidad > 10) {
            cantidad = 10;
            $(this).val(cantidad);
        }
        
        actualizarCantidad(productoId, cantidad);
    });

    // Eliminar producto
    $('.btn-eliminar').click(function() {
        const productoId = $(this).data('producto');
        const nombreProducto = $(this).closest('.row').find('h6').text();
        
        if (confirm(`¿Estás seguro de eliminar "${nombreProducto}" del carrito?`)) {
            eliminarProducto(productoId);
        }
    });

    // Función para actualizar cantidad
    function actualizarCantidad(productoId, cantidad) {
        const precio = parseFloat($(`.cantidad-input[data-producto="${productoId}"]`).data('precio'));
        
        $.ajax({
            url: '{{ route("dulceria.actualizar-carrito") }}',
            method: 'POST',
            data: {
                producto_id: productoId,
                cantidad: cantidad,
                _token: $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            if (response.success) {
                // Actualizar subtotal del producto
                $(`.subtotal[data-producto="${productoId}"]`).text('S/ ' + response.subtotal.toFixed(2));
                
                // Actualizar total general
                actualizarTotalGeneral();
                
                showToast('Carrito actualizado', 'success');
            } else {
                showToast(response.message || 'Error al actualizar', 'error');
            }
        })
        .fail(function() {
            showToast('Error al actualizar el carrito', 'error');
        });
    }

    // Función para eliminar producto
    function eliminarProducto(productoId) {
        $.ajax({
            url: `/dulceria/carrito/eliminar/${productoId}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            // Remover fila del DOM
            $(`.row[data-producto="${productoId}"]`).fadeOut(300, function() {
                $(this).remove();
                actualizarTotalGeneral();
                verificarCarritoVacio();
            });
            
            showToast('Producto eliminado del carrito', 'success');
        })
        .fail(function() {
            showToast('Error al eliminar el producto', 'error');
        });
    }

    // Función para actualizar total general
    function actualizarTotalGeneral() {
        let total = 0;
        $('.subtotal').each(function() {
            const subtotalText = $(this).text().replace('S/ ', '').replace(',', '');
            total += parseFloat(subtotalText) || 0;
        });
        
        $('#total-general').text('S/ ' + total.toFixed(2));
        
        // Actualizar resumen de items
        actualizarResumenItems();
    }

    // Función para actualizar resumen de items
    function actualizarResumenItems() {
        const resumenContainer = $('#resumen-items');
        resumenContainer.empty();
        
        $('.row[data-producto]').each(function() {
            const productoId = $(this).data('producto');
            const nombre = $(this).find('h6').text();
            const cantidad = $(this).find('.cantidad-input').val();
            const subtotal = $(this).find('.subtotal').text();
            
            resumenContainer.append(`
                <div class="d-flex justify-content-between mb-2">
                    <span>${nombre} x${cantidad}</span>
                    <span>${subtotal}</span>
                </div>
            `);
        });
    }

    // Función para verificar si el carrito está vacío
    function verificarCarritoVacio() {
        if ($('.row[data-producto]').length === 0) {
            location.reload(); // Recargar para mostrar mensaje de carrito vacío
        }
    }

    // Función para mostrar toasts
    function showToast(message, type = 'info') {
        let toastContainer = $('.toast-container');
        if (toastContainer.length === 0) {
            toastContainer = $('<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>');
            $('body').append(toastContainer);
        }
        
        const toastId = 'toast-' + Date.now();
        const bgClass = {
            'success': 'bg-success',
            'error': 'bg-danger',
            'warning': 'bg-warning',
            'info': 'bg-info'
        }[type] || 'bg-info';

        const toast = $(`
            <div id="${toastId}" class="toast ${bgClass} text-white" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        
        toastContainer.append(toast);
        
        // Mostrar toast
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();
        
        // Remover del DOM después de que se oculte
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }

    // Inicializar total al cargar la página
    actualizarTotalGeneral();
});
</script>
@endpush