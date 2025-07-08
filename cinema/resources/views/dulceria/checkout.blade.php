{{-- resources/views/dulceria/checkout.blade.php --}}
@extends('layouts.app')

@section('title', 'Checkout - Dulcería | Butaca del Salchicon')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg">
                <div class="card-header bg-warning text-dark text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>PAGO DULCERÍA
                    </h3>
                    <p class="mb-0">Finaliza tu pedido de dulcería</p>
                </div>

                <div class="card-body p-4">
                    <div class="row">
                        <!-- Resumen del Pedido -->
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-shopping-bag me-2"></i>Resumen del Pedido
                            </h5>
                            
                            @foreach($carrito as $productoId => $item)
                                <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                                    @if($item['imagen'])
                                        <img src="{{ asset('storage/' . $item['imagen']) }}" 
                                             alt="{{ $item['nombre'] }}" 
                                             class="rounded me-3" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary rounded me-3 d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-candy-cane text-white fs-4"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $item['nombre'] }}</h6>
                                        <small class="text-muted">
                                            {{ $item['cantidad'] }}x {{ formatPrice($item['precio']) }}
                                        </small>
                                    </div>
                                    
                                    <div class="text-end">
                                        <strong>{{ formatPrice($item['precio'] * $item['cantidad']) }}</strong>
                                    </div>
                                </div>
                            @endforeach

                            <hr>
                            
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Total a Pagar:</span>
                                <span class="text-warning">{{ formatPrice($total) }}</span>
                            </div>
                        </div>

                        <!-- Método de Pago -->
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <h5 class="fw-bold mb-3">
                                    <i class="fas fa-credit-card me-2"></i>Método de Pago
                                </h5>
                                
                                <!-- Botones de métodos de pago -->
                                <button type="button" class="btn btn-outline-primary w-100 mb-2" id="btn-yape-dulceria">
                                    <img src="{{ asset('images/icons/yape.png') }}" alt="Yape" style="height: 20px;" class="me-2">
                                    Yape
                                </button>
                                
                                <div class="text-center my-2">
                                    <small class="text-muted">O elige tarjeta</small>
                                </div>

                                <div class="row g-2">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-secondary w-100" id="btn-visa-dulceria">
                                            <img src="{{ asset('images/icons/visa.png') }}" alt="Visa" style="height: 20px;">
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-secondary w-100" id="btn-mastercard-dulceria">
                                            <img src="{{ asset('images/icons/mastercard.png') }}" alt="MasterCard" style="height: 20px;">
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón de Pago -->
                            <div class="mt-4">
                                <!-- Datos ocultos -->
                                @foreach($carrito as $productoId => $item)
                                    <input type="hidden" class="producto-hidden" 
                                           data-id="{{ $productoId }}" 
                                           data-cantidad="{{ $item['cantidad'] }}"
                                           data-precio="{{ $item['precio'] }}">
                                @endforeach
                                <input type="hidden" id="metodo_pago_dulceria" value="">
                                
                                <button type="button" id="btn-pagar-dulceria" class="btn btn-warning w-100" disabled>
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    Pagar {{ formatPrice($total) }}
                                </button>
                            </div>

                            <div class="text-center mt-3">
                                <a href="{{ route('dulceria.carrito') }}" class="btn btn-link">
                                    <i class="fas fa-arrow-left me-1"></i>Volver al carrito
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    console.log('✅ Checkout dulcería cargado');
    
    // Manejar selección de método de pago
    $('#btn-yape-dulceria, #btn-visa-dulceria, #btn-mastercard-dulceria').click(function() {
        console.log('💳 Método seleccionado:', this.id);
        
        // Reset all buttons
        $('#btn-yape-dulceria, #btn-visa-dulceria, #btn-mastercard-dulceria')
            .removeClass('btn-primary btn-success')
            .addClass('btn-outline-primary btn-outline-secondary');
        
        // Activate selected button
        $(this).removeClass('btn-outline-primary btn-outline-secondary').addClass('btn-primary');
        
        // Set payment method
        let metodo = '';
        if (this.id === 'btn-yape-dulceria') metodo = 'yape';
        else if (this.id === 'btn-visa-dulceria') metodo = 'visa';
        else if (this.id === 'btn-mastercard-dulceria') metodo = 'mastercard';
        
        $('#metodo_pago_dulceria').val(metodo);
        $('#btn-pagar-dulceria').prop('disabled', false);
        
        console.log('✅ Método guardado:', metodo);
    });
    
    // Manejar pago
    $('#btn-pagar-dulceria').click(function() {
        console.log('🚀 Procesando pago dulcería...');
        
        let metodo = $('#metodo_pago_dulceria').val();
        
        if (!metodo) {
            alert('Por favor selecciona un método de pago');
            return;
        }
        
        // Cambiar botón a loading
        $(this).html('<i class="fas fa-spinner fa-spin me-2"></i>Procesando pago...').prop('disabled', true);
        
        // Crear formulario dinámico
        let form = $('<form>', {
            method: 'POST',
            action: '{{ route("dulceria.procesar-pedido") }}'
        });
        
        // Agregar CSRF
        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: '{{ csrf_token() }}'
        }));
        
        // Agregar método de pago
        form.append($('<input>', {
            type: 'hidden',
            name: 'metodo_pago',
            value: metodo
        }));
        
        console.log('📤 Enviando pedido dulcería...');
        
        // Enviar
        $('body').append(form);
        form.submit();
    });
});
</script>
@endpush