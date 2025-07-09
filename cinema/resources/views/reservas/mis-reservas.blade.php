{{-- resources/views/reservas/mis-reservas.blade.php --}}
@extends('layouts.app')

@section('title', 'Mis Reservas - Butaca del Salchichon')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">
                    <i class="fas fa-ticket-alt me-2 text-primary"></i>Mis Reservas
                </h2>
                <a href="{{ route('home') }}" class="btn btn-outline-primary">
                    <i class="fas fa-home me-2"></i>Volver al Inicio
                </a>
            </div>

            @if($reservas->count() > 0)
                <div class="row">
                    @foreach($reservas as $reserva)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0 fw-bold">{{ $reserva->funcion->pelicula->titulo }}</h6>
                                <small>{{ $reserva->created_at->format('d M Y - H:i') }}</small>
                            </div>
                            
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>Cine:</strong> {{ $reserva->funcion->sala->cine->nombre }}
                                </div>
                                <div class="mb-2">
                                    <strong>Sala:</strong> {{ $reserva->funcion->sala->nombre }}
                                </div>
                                <div class="mb-2">
                                    <strong>Fecha:</strong> {{ $reserva->funcion->fecha_funcion->format('d M Y') }}
                                </div>
                                <div class="mb-2">
                                    <strong>Hora:</strong> {{ $reserva->funcion->hora_funcion->format('H:i') }}
                                </div>
                                <div class="mb-2">
                                    <strong>Asientos:</strong> {{ $reserva->getAsientosFormateados() }}
                                </div>
                                <div class="mb-2">
                                    <strong>Total:</strong> <span class="fw-bold text-success">{{ formatPrice($reserva->monto_total) }}</span>
                                </div>
                                
                                <!-- Estado de la reserva -->
                                <div class="mb-3">
                                    @if($reserva->estado == 'confirmada')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Confirmada
                                        </span>
                                    @elseif($reserva->estado == 'pendiente')
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Pendiente
                                        </span>
                                    @elseif($reserva->estado == 'cancelada')
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times me-1"></i>Cancelada
                                        </span>
                                    @endif
                                </div>

                                <!-- Código de reserva destacado -->
                                <div class="bg-light p-2 rounded text-center mb-3">
                                    <small class="text-muted">Código de Reserva</small>
                                    <div class="fw-bold fs-5">{{ $reserva->codigo_reserva }}</div>
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('reservas.boleta', $reserva) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-receipt me-1"></i>Ver Boleta
                                    </a>
                                    @if($reserva->estado == 'confirmada' && $reserva->funcion->fecha_funcion->isFuture())
                                        <small class="text-muted text-center">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Presenta tu código en taquilla
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $reservas->links() }}
                </div>

            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-ticket-alt display-1 text-muted"></i>
                    </div>
                    <h4 class="text-muted mb-3">No tienes reservas aún</h4>
                    <p class="text-muted mb-4">¡Explora nuestra cartelera y reserva tu película favorita!</p>
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-film me-2"></i>Ver Cartelera
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection