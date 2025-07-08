{{-- resources/views/cines/programacion.blade.php --}}
@extends('layouts.app')

@section('title', 'Programación - ' . $cine->nombre)

@section('content')
    <!-- Header del Cine -->
    <section class="bg-primary text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb text-white-50 mb-2">
                            <li class="breadcrumb-item">
                                <a href="{{ route('cines.index') }}" class="text-white-50">Cines</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('cine.show', $cine) }}" class="text-white-50">{{ $cine->nombre }}</a>
                            </li>
                            <li class="breadcrumb-item active text-white">Programación</li>
                        </ol>
                    </nav>
                    <h1 class="h2 fw-bold mb-2">{{ $cine->nombre }}</h1>
                    <p class="mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>{{ $cine->direccion }}, {{ $cine->ciudad->nombre }}
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('cine.show', $cine) }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Cine
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Selector de Fecha -->
    <section class="py-4 bg-light">
        <div class="container">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-md-0">
                                <i class="fas fa-calendar me-2 text-primary"></i>
                                Selecciona una fecha
                            </h5>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" class="d-flex gap-2">
                                <input type="date" 
                                       class="form-control" 
                                       name="fecha" 
                                       value="{{ $fecha }}"
                                       min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                                       max="{{ \Carbon\Carbon::today()->addDays(30)->format('Y-m-d') }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Programación -->
    <section class="py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h3 class="fw-bold">
                        Programación para {{ \Carbon\Carbon::parse($fecha)->format('l, d \\d\\e F \\d\\e Y') }}
                    </h3>
                </div>
                <div class="col-md-4 text-md-end">
                    <p class="text-muted mb-0">
                        <i class="fas fa-clock me-2"></i>Horarios en tiempo local
                    </p>
                </div>
            </div>

            @if($funciones->count() > 0)
                @foreach($funciones as $peliculaId => $funcionesPelicula)
                    @php $pelicula = $funcionesPelicula->first()->pelicula; @endphp
                    
                    <div class="card mb-4 shadow-sm border-0">
                        <div class="card-body">
                            <div class="row">
                                <!-- Poster y Info de la Película -->
                                <div class="col-lg-3 col-md-4 mb-3 mb-md-0">
                                    <div class="d-flex">
                                        <img src="{{ getPosterUrl($pelicula->poster) }}" 
                                             alt="{{ $pelicula->titulo }}" 
                                             class="poster-small me-3">
                                        <div class="flex-grow-1">
                                            <h5 class="fw-bold mb-2">{{ $pelicula->titulo }}</h5>
                                            <div class="mb-2">
                                                <span class="badge bg-secondary me-2">{{ $pelicula->clasificacion }}</span>
                                                @if($pelicula->destacada)
                                                <span class="badge badge-premium">
                                                    <i class="fas fa-star me-1"></i>Destacada
                                                </span>
                                                @endif
                                            </div>
                                            <p class="text-muted small mb-2">
                                                <i class="fas fa-tags me-1"></i>{{ $pelicula->genero }}
                                            </p>
                                            <p class="text-muted small mb-0">
                                                <i class="fas fa-clock me-1"></i>{{ $pelicula->getDuracionFormateada() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Horarios -->
                                <div class="col-lg-9 col-md-8">
                                    <h6 class="fw-bold mb-3 text-primary">Horarios Disponibles</h6>
                                    
                                    @php
                                        $funcionesAgrupadas = $funcionesPelicula->groupBy('sala.nombre');
                                    @endphp
                                    
                                    @foreach($funcionesAgrupadas as $nombreSala => $funcionesSala)
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <h6 class="mb-0 me-3">
                                                    <i class="fas fa-door-open me-2 text-warning"></i>{{ $nombreSala }}
                                                </h6>
                                                <small class="text-muted">
                                                    ({{ $funcionesSala->first()->sala->capacidad }} asientos)
                                                </small>
                                            </div>
                                            
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($funcionesSala as $funcion)
                                                    <div class="horario-item">
                                                        <a href="{{ route('reservas.seleccionar-asientos', $funcion) }}" 
                                                           class="btn btn-outline-primary btn-sm horario-btn">
                                                            <div class="d-flex flex-column align-items-center">
                                                                <strong>{{ $funcion->hora_funcion->format('H:i') }}</strong>
                                                                <small>{{ $funcion->formato }} • {{ $funcion->tipo }}</small>
                                                                <small class="text-success">S/ {{ number_format($funcion->precio, 2) }}</small>
                                                            </div>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Información Adicional -->
                <div class="row mt-5">
                    <div class="col-lg-8">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">
                                    <i class="fas fa-info-circle me-2 text-primary"></i>Información Importante
                                </h6>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i class="fas fa-clock me-2 text-warning"></i>
                                        Se recomienda llegar 15 minutos antes del horario de la función
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-ticket-alt me-2 text-warning"></i>
                                        Los precios pueden variar según el día y la sala
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-mobile-alt me-2 text-warning"></i>
                                        Puedes mostrar tu entrada desde tu celular
                                    </li>
                                    <li class="mb-0">
                                        <i class="fas fa-candy-cane me-2 text-warning"></i>
                                        Dulcería disponible en el lobby del cine
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 mt-4 mt-lg-0">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <i class="fas fa-headset display-4 text-primary mb-3"></i>
                                <h6 class="fw-bold mb-3">¿Necesitas ayuda?</h6>
                                <p class="text-muted mb-3">Nuestro equipo está aquí para ayudarte</p>
                                <div class="d-grid gap-2">
                                    <a href="tel:{{ $cine->telefono ?? '+51987654321' }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-phone me-2"></i>Llamar al Cine
                                    </a>
                                    <a href="https://wa.me/51987654321" class="btn btn-success btn-sm" target="_blank">
                                        <i class="fab fa-whatsapp me-2"></i>WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <!-- Sin funciones -->
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times display-1 text-muted mb-4"></i>
                    <h3 class="text-muted mb-3">No hay funciones programadas</h3>
                    <p class="text-muted mb-4">
                        No hay películas programadas para el día {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }} en este cine.
                    </p>
                    
                    <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
                        <a href="?fecha={{ \Carbon\Carbon::today()->format('Y-m-d') }}" class="btn btn-primary">
                            <i class="fas fa-calendar me-2"></i>Ver Programación de Hoy
                        </a>
                        <a href="{{ route('peliculas') }}" class="btn btn-outline-primary">
                            <i class="fas fa-film me-2"></i>Ver Todas las Películas
                        </a>
                        <a href="{{ route('cine.show', $cine) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Cine
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('styles')
<style>
    .poster-small {
        width: 80px;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    
    .horario-item {
        transition: all 0.3s ease;
    }
    
    .horario-btn {
        min-width: 120px;
        border-radius: 10px;
        transition: all 0.3s ease;
        padding: 10px 15px;
    }
    
    .horario-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,123,255,0.3);
        border-color: var(--bs-primary);
        background-color: var(--bs-primary);
        color: white;
    }
    
    .badge-premium {
        background: linear-gradient(45deg, var(--accent-yellow), var(--accent-orange));
        color: black;
        font-weight: bold;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        color: rgba(255,255,255,0.5);
    }
    
    .card {
        border-radius: 15px;
        overflow: hidden;
    }
    
    @media (max-width: 768px) {
        .poster-small {
            width: 60px;
            height: 90px;
        }
        
        .horario-btn {
            min-width: 100px;
            padding: 8px 12px;
        }
        
        .d-flex.flex-wrap.gap-2 {
            gap: 0.5rem !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    console.log('📅 Programación del cine cargada:', {
        cine: "{{ $cine->nombre }}",
        fecha: "{{ $fecha }}",
        total_funciones: {{ $funciones->flatten()->count() }},
        peliculas: {{ $funciones->count() }}
    });

    // Auto-submit cuando cambia la fecha
    $('input[name="fecha"]').on('change', function() {
        $(this).closest('form').submit();
    });

    // Efecto hover para los horarios
    $('.horario-btn').hover(
        function() {
            $(this).addClass('shadow-lg');
        },
        function() {
            $(this).removeClass('shadow-lg');
        }
    );

    // Mostrar información adicional al hacer hover en los horarios
    $('.horario-btn').on('mouseenter', function() {
        const $this = $(this);
        const tooltip = $this.find('small').text();
        
        if (!$this.attr('title')) {
            $this.attr('title', 'Formato: ' + tooltip + ' • Haz clic para comprar entradas');
        }
    });

    // Confirmar antes de ir a comprar (opcional)
    $('.horario-btn').on('click', function(e) {
        const hora = $(this).find('strong').text();
        const precio = $(this).find('.text-success').text();
        
        console.log('Usuario seleccionó función:', {
            hora: hora,
            precio: precio,
            cine: "{{ $cine->nombre }}"
        });
        
        // Aquí podrías agregar analytics o confirmación si es necesario
    });

    // Resaltar el día actual si está visible
    const fechaActual = "{{ \Carbon\Carbon::today()->format('Y-m-d') }}";
    const fechaSeleccionada = "{{ $fecha }}";
    
    if (fechaActual === fechaSeleccionada) {
        $('input[name="fecha"]').addClass('border-primary');
    }
});
</script>
@endpush