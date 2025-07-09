{{-- resources/views/reservas/seleccionar-asientos.blade.php --}}
@extends('layouts.app')

@section('title', 'Seleccionar Asientos - Butaca del Salchichon')

@section('content')
    <div class="container py-5">
        <div class="row">
            <!-- Información de la función -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-film me-2"></i>{{ $funcion->pelicula->titulo }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Ubicación:</strong> {{ $funcion->sala->cine->nombre }}</p>
                                <p><strong>Fecha:</strong> {{ $funcion->fecha_funcion->format('d M Y') }}</p>
                                <p><strong>Hora:</strong> {{ $funcion->hora_funcion->format('H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Tipo:</strong> {{ $funcion->tipo }}</p>
                                <p><strong>Sala:</strong> {{ $funcion->sala->nombre }}</p>
                                <p><strong>Formato:</strong> {{ $funcion->formato }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pantalla -->
                <div class="text-center mb-4">
                    <div class="bg-dark text-white py-2 px-4 rounded-pill d-inline-block">
                        <i class="fas fa-tv me-2"></i>PANTALLA
                    </div>
                </div>

                <!-- Mapa de Asientos -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h5>Elige el asiento que ocuparás durante la proyección de la película.</h5>
                        </div>

                        <!-- Leyenda -->
                        <div class="row mb-4 text-center">
                            <div class="col-3">
                                <div class="seat occupied"></div>
                                <small>Ocupado</small>
                            </div>
                            <div class="col-3">
                                <div class="seat available"></div>
                                <small>Disponible</small>
                            </div>
                            <div class="col-3">
                                <div class="seat selected"></div>
                                <small>Seleccionado</small>
                            </div>
                            <div class="col-3">
                                <small class="text-muted">Selecciona hasta 8 asientos</small>
                            </div>
                        </div>

                        <!-- Grid de Asientos -->
                        <div class="seats-container text-center">
                            @php
                                $filas = range('A', chr(65 + $funcion->sala->filas - 1));
                                $asientosPorFila = $funcion->sala->asientos_por_fila;
                            @endphp

                            @foreach($filas as $fila)
                                <div class="seat-row mb-2">
                                    <span class="row-label me-3 fw-bold">{{ $fila }}</span>
                                    @for($numero = 1; $numero <= $asientosPorFila; $numero++)
                                        @php
                                            $asientoId = $fila . $numero;
                                            $ocupado = in_array($asientoId, $asientosOcupados);
                                        @endphp
                                        <div class="seat {{ $ocupado ? 'occupied' : 'available' }}" 
                                             data-seat="{{ $asientoId }}"
                                             {{ $ocupado ? 'data-occupied="true"' : '' }}>
                                            {{ $numero }}
                                        </div>
                                    @endfor
                                    <span class="row-label ms-3 fw-bold">{{ $fila }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen de la compra -->
            <div class="col-lg-4">
                <div class="card shadow-sm position-sticky" style="top: 20px;">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>Resumen del pedido
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6>Detalles de la transacción</h6>
                            <div class="d-flex justify-content-between">
                                <span>ASIENTO REGULAR</span>
                                <span>{{ formatPrice($funcion->precio) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>X<span id="cantidad-boletos">0</span></span>
                                <span id="subtotal-boletos">{{ formatPrice(0) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>TARIFA DE SERVICIO</span>
                                <span>{{ formatPrice($funcion->tarifa_servicio) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>X<span id="cantidad-servicio">0</span></span>
                                <span id="subtotal-servicio">{{ formatPrice(0) }}</span>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total Pagar</span>
                            <span id="total-pagar">{{ formatPrice(0) }}</span>
                        </div>

                        <div class="mt-3">
                            <h6>Asientos seleccionados:</h6>
                            <div id="asientos-seleccionados" class="text-muted">
                                Ningún asiento seleccionado
                            </div>
                        </div>

                        <form method="POST" action="{{ route('reserva.confirmar', $funcion) }}" id="form-reserva">
                            @csrf
                            <input type="hidden" name="asientos" id="input-asientos">
                            
                            <button type="submit" class="btn btn-primary w-100 mt-4" id="btn-continuar" disabled>
                                <i class="fas fa-arrow-right me-2"></i>DETALLES
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let asientosSeleccionados = [];
    const precioAsiento = {{ $funcion->precio }};
    const tarifaServicio = {{ $funcion->tarifa_servicio }};
    const maxAsientos = 8;

    $('.seat.available').click(function() {
        const asientoId = $(this).data('seat');
        
        if ($(this).hasClass('selected')) {
            // Deseleccionar
            $(this).removeClass('selected').addClass('available');
            asientosSeleccionados = asientosSeleccionados.filter(a => a !== asientoId);
        } else {
            // Seleccionar
            if (asientosSeleccionados.length >= maxAsientos) {
                showAlert(`Máximo ${maxAsientos} asientos por compra`, 'warning');
                return;
            }
            $(this).removeClass('available').addClass('selected');
            asientosSeleccionados.push(asientoId);
        }

        actualizarResumen();
    });

function actualizarResumen() {
    const cantidad = asientosSeleccionados.length;
    const subtotalBoletos = cantidad * precioAsiento;
    const subtotalServicio = cantidad * tarifaServicio;
    const total = subtotalBoletos + subtotalServicio;

    $('#cantidad-boletos').text(cantidad);
    $('#cantidad-servicio').text(cantidad);
    $('#subtotal-boletos').text(formatPrice(subtotalBoletos));
    $('#subtotal-servicio').text(formatPrice(subtotalServicio));
    $('#total-pagar').text(formatPrice(total));

    if (cantidad > 0) {
        $('#asientos-seleccionados').text(asientosSeleccionados.join(', '));
        $('#btn-continuar').prop('disabled', false);
        
        // Limpiar inputs anteriores
        $('input[name="asientos[]"]').remove();
        
        // Crear un input hidden por cada asiento seleccionado
        asientosSeleccionados.forEach(function(asiento) {
            $('#form-reserva').append('<input type="hidden" name="asientos[]" value="' + asiento + '">');
        });
        
    } else {
        $('#asientos-seleccionados').text('Ningún asiento seleccionado');
        $('#btn-continuar').prop('disabled', true);
        
        // Limpiar inputs de asientos
        $('input[name="asientos[]"]').remove();
    }
}});
</script>
@endpush