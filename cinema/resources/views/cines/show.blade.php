{{-- resources/views/cines/show.blade.php --}}
@extends('layouts.app')

@section('title', $cine->nombre)

@section('content')
    <!-- Hero Section del Cine -->
    <section class="hero-cinema position-relative">
        <div class="hero-image" style="background-image: url('{{ getCinemaImageUrl($cine->imagen ?? null, $cine->nombre) }}');">
            <div class="overlay"></div>
        </div>
        <div class="container position-relative text-white">
            <div class="row align-items-center min-vh-50">
                <div class="col-lg-8">
                    <div class="hero-content">
                        <div class="mb-3">
                            <span class="badge bg-warning text-dark px-3 py-2">
                                <i class="fas fa-map-marker-alt me-2"></i>{{ $cine->ciudad->nombre }}
                            </span>
                        </div>
                        <h1 class="display-4 fw-bold mb-3">{{ $cine->nombre }}</h1>
                        <p class="fs-5 mb-4">
                            <i class="fas fa-location-dot me-2"></i>{{ $cine->direccion }}
                        </p>
                        <div class="d-flex flex-wrap gap-3 mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-door-open me-2 text-warning"></i>
                                <span>{{ $cine->salas->count() }} Salas</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-chair me-2 text-warning"></i>
                                <span>{{ $cine->salas->sum('capacidad') }} Asientos</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-film me-2 text-warning"></i>
                                <span>{{ $peliculas->count() }} Películas</span>
                            </div>
                        </div>
                        <div class="d-flex gap-3">
                            <a href="#programacion" class="btn btn-warning btn-lg">
                                <i class="fas fa-calendar me-2"></i>Ver Programación
                            </a>
                            <a href="{{ route('cine.programacion', $cine) }}" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-list me-2"></i>Horarios Completos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Información del Cine -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Información Principal -->
                <div class="col-lg-8 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="card-title mb-4">
                                <i class="fas fa-info-circle text-primary me-2"></i>Información del Cine
                            </h3>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-bold text-muted">Ubicación</h6>
                                    <p class="mb-0">{{ $cine->direccion }}</p>
                                    <small class="text-muted">{{ $cine->ciudad->nombre }}</small>
                                </div>
                                
                                @if($cine->telefono)
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-bold text-muted">Teléfono</h6>
                                    <p class="mb-0">
                                        <a href="tel:{{ $cine->telefono }}" class="text-decoration-none">
                                            <i class="fas fa-phone me-2"></i>{{ $cine->telefono }}
                                        </a>
                                    </p>
                                </div>
                                @endif
                                
                                @if($cine->email)
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-bold text-muted">Email</h6>
                                    <p class="mb-0">
                                        <a href="mailto:{{ $cine->email }}" class="text-decoration-none">
                                            <i class="fas fa-envelope me-2"></i>{{ $cine->email }}
                                        </a>
                                    </p>
                                </div>
                                @endif
                                
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-bold text-muted">Horarios de Atención</h6>
                                    <p class="mb-0">
                                        <i class="fas fa-clock me-2"></i>Lunes a Domingo: 10:00 AM - 11:00 PM
                                    </p>
                                </div>
                            </div>

                            @if($cine->descripcion)
                            <div class="mt-4">
                                <h6 class="fw-bold text-muted">Descripción</h6>
                                <p class="text-muted">{{ $cine->descripcion }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Salas del Cine -->
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="fas fa-door-open text-warning me-2"></i>Nuestras Salas
                            </h5>
                            
                            @if($cine->salas->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($cine->salas as $sala)
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div>
                                            <h6 class="mb-1">{{ $sala->nombre }}</h6>
                                            <small class="text-muted">
                                                <i class="fas fa-chair me-1"></i>{{ $sala->capacidad }} asientos
                                            </small>
                                        </div>
                                        @if($sala->tipo)
                                        <span class="badge bg-primary">{{ $sala->tipo }}</span>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted text-center">No hay salas registradas</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Programación Actual -->
    <section id="programacion" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 fw-bold">Películas en Cartelera</h2>
                <p class="text-muted">Conoce las películas que están disponibles en este cine</p>
            </div>

            @if($peliculas->count() > 0)
                <div class="row g-4">
                    @foreach($peliculas as $pelicula)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card movie-card h-100">
                            <div class="position-relative">
                                <img src="{{ getPosterUrl($pelicula->poster) }}" 
                                     alt="{{ $pelicula->titulo }}" 
                                     class="card-img-top movie-poster">
                                
                                <!-- Overlay con información -->
                                <div class="position-absolute top-0 start-0 p-3">
                                    <span class="badge bg-dark">{{ $pelicula->clasificacion }}</span>
                                </div>
                                
                                @if($pelicula->destacada)
                                <div class="position-absolute top-0 end-0 p-3">
                                    <span class="badge badge-premium">
                                        <i class="fas fa-star me-1"></i>Destacada
                                    </span>
                                </div>
                                @endif
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $pelicula->titulo }}</h5>
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-tags me-1"></i>{{ $pelicula->genero }}
                                </p>
                                <p class="text-muted small mb-3">
                                    <i class="fas fa-clock me-1"></i>{{ $pelicula->getDuracionFormateada() }}
                                </p>
                                
                                <div class="mt-auto">
                                    <a href="{{ route('pelicula.show', $pelicula) }}" 
                                       class="btn btn-primary w-100">
                                        <i class="fas fa-ticket-alt me-2"></i>Ver Horarios
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Botón Ver Programación Completa -->
                <div class="text-center mt-5">
                    <a href="{{ route('cine.programacion', $cine) }}" class="btn btn-warning btn-lg">
                        <i class="fas fa-calendar-alt me-2"></i>Ver Programación Completa
                    </a>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-film display-1 text-muted mb-3"></i>
                    <h3 class="text-muted">No hay películas disponibles</h3>
                    <p class="text-muted">Actualmente no hay películas programadas en este cine</p>
                    <a href="{{ route('peliculas') }}" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Ver Todas las Películas
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- Ubicación (Mapa) -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title text-center mb-4">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>Ubicación
                            </h4>
                            
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="fw-bold">{{ $cine->nombre }}</h6>
                                    <p class="text-muted mb-3">{{ $cine->direccion }}</p>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($cine->direccion . ', ' . $cine->ciudad->nombre) }}" 
                                           target="_blank" class="btn btn-outline-primary">
                                            <i class="fas fa-directions me-2"></i>Cómo llegar
                                        </a>
                                        
                                        <a href="https://waze.com/ul?q={{ urlencode($cine->direccion . ', ' . $cine->ciudad->nombre) }}" 
                                           target="_blank" class="btn btn-outline-info">
                                            <i class="fab fa-waze me-2"></i>Abrir en Waze
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <!-- Placeholder para mapa -->
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <div class="text-center text-muted">
                                            <i class="fas fa-map display-4 mb-2"></i>
                                            <p>Mapa de Ubicación</p>
                                            <small>Haz clic en "Cómo llegar" para ver el mapa completo</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .hero-cinema {
        position: relative;
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
    }
    
    .hero-image {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }
    
    .hero-cinema .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(0,0,0,0.7), rgba(26,35,126,0.8));
    }
    
    .min-vh-50 {
        min-height: 50vh;
    }
    
    .movie-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }
    
    .movie-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }
    
    .movie-poster {
        height: 300px;
        object-fit: cover;
    }
    
    .list-group-item {
        border: none;
        border-bottom: 1px solid #eee;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
    }
    
    .badge-premium {
        background: linear-gradient(45deg, var(--accent-yellow), var(--accent-orange));
        color: black;
        font-weight: bold;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    console.log('🏢 Vista de cine cargada:', {
        cine: "{{ $cine->nombre }}",
        ciudad: "{{ $cine->ciudad->nombre }}",
        salas: {{ $cine->salas->count() }},
        peliculas: {{ $peliculas->count() }}
    });

    // Smooth scroll para la navegación interna
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 1000);
        }
    });

    // Efecto parallax suave para el hero
    $(window).scroll(function() {
        var scrolled = $(this).scrollTop();
        var rate = scrolled * -0.5;
        $('.hero-image').css('transform', 'translate3d(0, ' + rate + 'px, 0)');
    });
});
</script>
@endpush