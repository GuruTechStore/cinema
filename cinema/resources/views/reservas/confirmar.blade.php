{{-- resources/views/reservas/confirmar.blade.php --}}
@extends('layouts.app')

@section('title', 'Confirmar Reserva - CinePlanet')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>CONFIRMACIÓN DE PAGO
                        </h3>
                        <p class="mb-0">Confirmación de pago del asiento que reservó.</p>
                    </div>

                    <div class="card-body p-4">
                        <div class="row">
                            <!-- Detalle del horario -->
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3">Detalle del horario</h5>
                                
                                <div class="mb-3">
                                    <strong>Película</strong>
                                    <p class="mb-1">{{ $funcion->pelicula->titulo }}</p>
                                </div>

                                <div class="mb-3">
                                    <strong>Fecha</strong>
                                    <p class="mb-1">{{ $funcion->fecha_funcion->format('l, d M Y') }}</p>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <strong>Tipo</strong>
                                        <p class="mb-1">{{ $funcion->tipo }}</p>
                                    </div>
                                    <div class="col-6">
                                        <strong>Hora</strong>
                                        <p class="mb-1">{{ $funcion->hora_funcion->format('H:i') }}</p>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <strong>Tickets ({{ $totalBoletos }})</strong>
                                    <p class="mb-1">{{ implode(', ', $asientosSeleccionados) }}</p>
                                </div>
                            </div>

                            <!-- Resumen del pedido -->
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <h5 class="fw-bold mb-3">Resumen del pedido</h5>
                                    
                                    <div class="mb-3">
                                        <h6>Detalles de la transacción</h6>
                                        <div class="d-flex justify-content-between">
                                            <span>ASIENTO REGULAR</span>
                                            <span>{{ formatPrice($funcion->precio) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>X{{ $totalBoletos }}</span>
                                            <span>{{ formatPrice($funcion->precio * $totalBoletos) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>TARIFA SERVICIO</span>
                                            <span>{{ formatPrice($funcion->tarifa_servicio) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>X{{ $totalBoletos }}</span>
                                            <span>{{ formatPrice($funcion->tarifa_servicio * $totalBoletos) }}</span>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="d-flex justify-content-between fw-bold fs-5">
                                        <span>Total Pagar</span>
                                        <span>{{ formatPrice($precioTotal) }}</span>
                                    </div>
                                </div>

                                <!-- Método de pago -->
                                <div class="mt-4">
                                    <h6 class="fw-bold">Método de pago</h6>
                                    <button class="btn btn-outline-primary w-100 mb-2" id="btn-yape">
                                        <img src="{{ asset('images/icons/yape.png') }}" alt="Yape" style="height: 20px;" class="me-2">
                                        Yape
                                    </button>
                                    
                                    <div class="text-center my-2">
                                        <small class="text-muted">Ver todo</small>
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-6">
                                            <button class="btn btn-outline-secondary w-100" id="btn-visa">
                                                <img src="{{ asset('images/icons/visa.png') }}" alt="Visa" style="height: 20px;">
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-outline-secondary w-100" id="btn-mastercard">
                                                <img src="{{ asset('images/icons/mastercard.png') }}" alt="MasterCard" style="height: 20px;">
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('reserva.procesar', $funcion) }}" id="form-pago">
                                    @csrf
                                    <input type="hidden" name="asientos" value="{{ json_encode($asientosSeleccionados) }}">
                                    <input type="hidden" name="metodo_pago" id="metodo_pago">
                                    
                                    <button type="submit" class="btn btn-warning w-100 mt-4" id="btn-comprar" disabled>
                                        <i class="fas fa-shopping-cart me-2"></i>Comprar entradas
                                    </button>
                                </form>

                                <div class="text-center mt-3">
                                    <a href="{{ route('reserva.asientos', $funcion) }}" class="btn btn-link">
                                        <i class="fas fa-arrow-left me-1"></i>volver
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
    $('.btn[id^="btn-"]').click(function() {
        // Reset all buttons
        $('.btn[id^="btn-"]').removeClass('btn-primary').addClass('btn-outline-primary btn-outline-secondary');
        
        // Activate selected button
        $(this).removeClass('btn-outline-primary btn-outline-secondary').addClass('btn-primary');
        
        // Set payment method
        let metodo = '';
        if (this.id === 'btn-yape') metodo = 'yape';
        else if (this.id === 'btn-visa') metodo = 'visa';
        else if (this.id === 'btn-mastercard') metodo = 'mastercard';
        
        $('#metodo_pago').val(metodo);
        $('#btn-comprar').prop('disabled', false);
    });
});
</script>
@endpush