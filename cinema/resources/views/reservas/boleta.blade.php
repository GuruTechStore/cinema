{{-- resources/views/reservas/boleta.blade.php --}}
@extends('layouts.app')

@section('title', 'Boleta de Reserva - Butaca del Salchichon')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>¡Reserva Confirmada!
                    </h3>
                </div>

                <div class="card-body p-4">
                    <!-- Información de la reserva -->
                    <div class="text-center mb-4">
                        <div class="bg-primary text-white p-4 rounded mb-3">
                            <h4 class="fw-bold text-white">{{ $reserva->funcion->pelicula->titulo }}</h4>
                            <p class="mb-0">{{ $reserva->funcion->sala->cine->nombre }}</p>
                            <p class="mb-0">{{ $reserva->funcion->fecha_funcion->format('l, d M Y') }} - {{ $reserva->funcion->hora_funcion->format('H:i') }}</p>
                            <p class="mb-0">{{ $reserva->funcion->tipo }} - {{ $reserva->funcion->sala->nombre }}</p>
                        </div>

                        <!-- Código de reserva y QR -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="bg-warning text-dark p-3 rounded">
                                    <strong>Código Reserva</strong>
                                    <h3 class="fw-bold">{{ $reserva->codigo_reserva }}</h3>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-info text-white p-3 rounded">
                                    <strong>Clave Seguridad</strong>
                                    <h3 class="fw-bold">{{ $reserva->clave_seguridad }}</h3>
                                </div>
                            </div>
                        </div>

                        <!-- QR Code placeholder -->
                        <div class="mt-3">
                            <div class="border p-3 d-inline-block bg-light">
                                <i class="fas fa-qrcode display-1 text-muted"></i>
                                <p class="small text-muted mb-0">Código QR para acceso rápido</p>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles de Compra -->
                    <div class="card bg-light">
                        <div class="card-header">
                            <h5 class="mb-0">Detalles de Compra</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>No Orden:</strong> {{ $reserva->id }}</p>
                                    <p><strong>Asiento Regular:</strong> {{ formatPrice($reserva->precio_boleto) }} X{{ $reserva->total_boletos }}</p>
                                    <p><strong>Tarifa Servicio:</strong> {{ formatPrice($reserva->tarifa_servicio) }} X{{ $reserva->total_boletos }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Asientos:</strong> {{ $reserva->getAsientosFormateados() }}</p>
                                    <p><strong>Método de Pago:</strong> {{ ucfirst($reserva->metodo_pago) }}</p>
                                    <p class="fw-bold fs-5"><strong>TOTAL PAGAR:</strong> {{ formatPrice($reserva->monto_total) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="text-center mt-4">
                        <button class="btn btn-primary btn-lg me-3" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Imprimir Boleta
                        </button>
                        <a href="{{ route('reservas.mis-reservas') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-list me-2"></i>Mis Reservas
                        </a>
                    </div>

                    <!-- Información importante -->
                    <div class="alert alert-info mt-4">
                        <h6><i class="fas fa-info-circle me-2"></i>Información Importante:</h6>
                        <ul class="mb-0 small">
                            <li>Llega 15 minutos antes del inicio de la función</li>
                            <li>Presenta este código en taquilla o úsalo en las máquinas de autoservicio</li>
                            <li>Los asientos se liberan 5 minutos después del inicio de la función</li>
                            <li>No se permiten cambios ni devoluciones</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection