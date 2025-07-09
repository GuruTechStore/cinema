{{-- resources/views/dulceria/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dulcería - Butaca del Salchichon')

@section('content')
    <!-- Header -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-3">Dulcería</h1>
                    <p class="lead">Completa tu experiencia cinematográfica con nuestros deliciosos productos</p>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-candy-cane display-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Productos por Categoría -->
    <section class="py-5">
        <div class="container">
            @foreach($categorias as $categoria)
                @if($categoria->productosActivos->count() > 0)
                <div class="mb-5">
                    <h3 class="fw-bold mb-4 text-primary">
                        <i class="fas fa-tags me-2"></i>{{ $categoria->nombre }}
                    </h3>
                    
                    <div class="row g-4">
                        @foreach($categoria->productosActivos as $producto)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="card h-100 shadow-sm product-card">
                                <div class="position-relative">
                                    <img src="{{ $producto->imagen ? asset('storage/' . $producto->imagen) : asset('images/dulceria/' . str_replace(' ', '-', strtolower($producto->nombre)) . '.jpg') }}" 
                                         class="card-img-top" alt="{{ $producto->nombre }}" style="height: 200px; object-fit: cover;">
                                    @if($producto->es_combo)
                                        <div class="position-absolute top-0 start-0 m-2">
                                            <span class="badge bg-warning text-dark">COMBO</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title fw-bold">{{ $producto->nombre }}</h6>
                                    <p class="card-text text-muted small flex-grow-1">{{ $producto->descripcion }}</p>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="fs-5 fw-bold text-primary">{{ formatPrice($producto->precio) }}</span>
                                        </div>
                                        
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="input-group input-group-sm" style="width: 100px;">
                                                <button class="btn btn-outline-secondary btn-minus" type="button" data-producto="{{ $producto->id }}">-</button>
                                                <input type="number" class="form-control text-center cantidad-input" 
                                                       value="1" min="1" max="10" data-producto="{{ $producto->id }}">
                                                <button class="btn btn-outline-secondary btn-plus" type="button" data-producto="{{ $producto->id }}">+</button>
                                            </div>
                                            <button class="btn btn-primary btn-sm flex-fill btn-agregar" 
                                                    data-producto="{{ $producto->id }}"
                                                    data-nombre="{{ $producto->nombre }}"
                                                    data-precio="{{ $producto->precio }}">
                                                <i class="fas fa-cart-plus me-1"></i>Agregar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div
                @endif
            @endforeach

            @if($categorias->sum(function($cat) { return $cat->productosActivos->count(); }) == 0)
                <div class="text-center py-5">
                    <i class="fas fa-candy-cane display-1 text-muted mb-3"></i>
                    <h3 class="text-muted">No hay productos disponibles</h3>
                    <p class="text-muted">Pronto tendremos deliciosos productos para ti</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Carrito Flotante -->
    <div class="position-fixed bottom-0 end-0 m-4" style="z-index: 1000;" id="carrito-flotante" style="display: none;">
        <div class="card shadow-lg border-0" style="width: 300px;">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <span class="fw-bold">
                    <i class="fas fa-shopping-cart me-2"></i>Carrito
                </span>
                <span class="badge bg-dark" id="items-carrito">0</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between fw-bold">
                    <span>Total:</span>
                    <span id="total-carrito">S/ 0.00</span>
                </div>
                <a href="{{ route('dulceria.carrito') }}" class="btn btn-primary w-100 mt-2">
                    <i class="fas fa-shopping-bag me-2"></i>Ver Carrito
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
// JavaScript corregido para el carrito flotante
$(document).ready(function() {
    // Incrementar cantidad
    $('.btn-plus').click(function() {
        const productoId = $(this).data('producto');
        const input = $(`.cantidad-input[data-producto="${productoId}"]`);
        let cantidad = parseInt(input.val());
        if (cantidad < 10) {
            input.val(cantidad + 1);
        }
    });

    // Decrementar cantidad
    $('.btn-minus').click(function() {
        const productoId = $(this).data('producto');
        const input = $(`.cantidad-input[data-producto="${productoId}"]`);
        let cantidad = parseInt(input.val());
        if (cantidad > 1) {
            input.val(cantidad - 1);
        }
    });

    // Agregar al carrito
    $('.btn-agregar').click(function() {
        const btn = $(this);
        const productoId = btn.data('producto');
        const cantidad = parseInt($(`.cantidad-input[data-producto="${productoId}"]`).val());

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Agregando...');

        $.ajax({
            url: '{{ route("dulceria.agregar-carrito") }}',
            method: 'POST',
            data: {
                producto_id: productoId,
                cantidad: cantidad,
                _token: $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            if (response.success) {
                showAlert(response.message, 'success');
                updateCartInfo(response.carrito_count, response.monto_total);
                
                // Reset input
                $(`.cantidad-input[data-producto="${productoId}"]`).val(1);
            } else {
                showAlert(response.message || 'Error al agregar al carrito', 'danger');
            }
        })
        .fail(function(xhr) {
            const errorMsg = xhr.responseJSON?.message || 'Error al agregar al carrito';
            showAlert(errorMsg, 'danger');
        })
        .always(function() {
            btn.prop('disabled', false).html('<i class="fas fa-cart-plus me-1"></i>Agregar');
        });
    });

    // Función para actualizar información del carrito
    function updateCartInfo(carritoCount, montoTotal) {
        // Actualizar contador en el header/navbar si existe
        $('#carrito-count').text(carritoCount);
        
        // Actualizar carrito flotante
        if (carritoCount > 0) {
            $('#carrito-flotante').show();
            $('#items-carrito').text(carritoCount);
            $('#total-carrito').text('S/ ' + parseFloat(montoTotal).toFixed(2));
        } else {
            $('#carrito-flotante').hide();
        }
    }

    // Función para obtener estado actual del carrito via AJAX
    function actualizarCarritoFlotante() {
        $.get('{{ route("dulceria.carrito-info") }}')
        .done(function(response) {
            updateCartInfo(response.carrito_count, response.monto_total);
        })
        .fail(function() {
            console.log('Error al obtener información del carrito');
        });
    }

    // Función para mostrar alertas
    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'danger' ? 'alert-danger' : 
                          type === 'warning' ? 'alert-warning' : 'alert-info';
        
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(alert);
        
        // Auto-remove después de 5 segundos
        setTimeout(function() {
            alert.alert('close');
        }, 5000);
    }

    // Inicializar carrito flotante al cargar la página
    actualizarCarritoFlotante();
});</script>
@endpush