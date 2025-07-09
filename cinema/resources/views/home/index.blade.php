{{-- resources/views/home/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Butaca del Salchicon - La mejor experiencia cinematográfica')

@section('content')
    <!-- Hero Carousel - Películas en Estreno -->
    @if($peliculasEstreno->count() > 0)
    <section class="hero-carousel">
        <div id="peliculasCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
            <!-- Indicadores del carrusel -->
            <div class="carousel-indicators">
                @foreach($peliculasEstreno as $index => $pelicula)
                <button type="button" data-bs-target="#peliculasCarousel" data-bs-slide-to="{{ $index }}" 
                        class="{{ $index === 0 ? 'active' : '' }}" 
                        aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                        aria-label="Película {{ $index + 1 }}"></button>
                @endforeach
            </div>

            <!-- Slides del carrusel -->
            <div class="carousel-inner">
                @foreach($peliculasEstreno as $index => $pelicula)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <div class="hero-slide d-flex align-items-center text-white position-relative">
                        <!-- Imagen de fondo -->
                        <div class="hero-background" 
                             style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.6)), 
                                    url('{{ $pelicula->poster ? asset('storage/' . $pelicula->poster) : asset('images/posters/placeholder.jpg') }}');">
                        </div>
                        
                        <!-- Contenido del slide -->
                        <div class="container hero-content">
                            <div class="row align-items-center">
                                <div class="col-lg-6 col-md-8">
                                    <div class="mb-3">
                                        <span class="badge badge-premium fs-6 px-3 py-2 rounded-pill">
                                            <i class="fas fa-star me-1"></i>EN ESTRENO
                                        </span>
                                        @if($pelicula->destacada)
                                        <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill ms-2">
                                            <i class="fas fa-crown me-1"></i>DESTACADA
                                        </span>
                                        @endif
                                    </div>
                                    
                                    <h1 class="display-4 fw-bold mb-3 animate-fade-in">{{ $pelicula->titulo }}</h1>
                                    <p class="lead mb-4 animate-fade-in-delay">{{ Str::limit($pelicula->descripcion, 150) }}</p>
                                    
                                    <div class="mb-4 animate-fade-in-delay-2">
                                        <span class="badge bg-warning text-dark me-2 fs-6">{{ $pelicula->genero }}</span>
                                        <span class="badge bg-info me-2 fs-6">{{ $pelicula->getDuracionFormateada() }}</span>
                                        <span class="badge bg-secondary fs-6">{{ $pelicula->clasificacion }}</span>
                                    </div>
                                    
                                    <div class="d-flex flex-wrap gap-3 animate-fade-in-delay-3">
                                        <a href="{{ route('pelicula.show', $pelicula) }}" class="btn btn-warning btn-lg">
                                            <i class="fas fa-ticket-alt me-2"></i>Comprar Entradas
                                        </a>
                                        <a href="{{ route('pelicula.show', $pelicula) }}" class="btn btn-outline-light btn-lg">
                                            <i class="fas fa-play me-2"></i>Ver Detalles
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6 col-md-4 text-center d-none d-md-block">
                                    <div class="poster-container animate-zoom-in">
                                        <img src="{{ $pelicula->poster ? asset('storage/' . $pelicula->poster) : asset('images/posters/placeholder.jpg') }}" 
                                             alt="{{ $pelicula->titulo }}" 
                                             class="img-fluid movie-poster-hero shadow-lg">
                                        
                                        <!-- Información adicional flotante -->
                                        <div class="poster-info">
                                            <div class="rating-badge">
                                                <i class="fas fa-star text-warning"></i>
                                                <span class="ms-1">{{ $pelicula->fecha_estreno->format('d M') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Controles del carrusel -->
            <button class="carousel-control-prev" type="button" data-bs-target="#peliculasCarousel" data-bs-slide="prev">
                <div class="carousel-control-icon">
                    <i class="fas fa-chevron-left fa-2x"></i>
                </div>
                <span class="visually-hidden">Anterior</span>
            </button>
            
            <button class="carousel-control-next" type="button" data-bs-target="#peliculasCarousel" data-bs-slide="next">
                <div class="carousel-control-icon">
                    <i class="fas fa-chevron-right fa-2x"></i>
                </div>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>
    </section>
    @endif

    <!-- Filtros Rápidos -->
    <section class="py-4 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Por Película</label>
                                    <select class="form-select" id="filtro-pelicula">
                                        <option value="">Qué quieres ver</option>
                                        @foreach($peliculasEstreno as $pelicula)
                                            <option value="{{ $pelicula->id }}">{{ $pelicula->titulo }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Por ciudad</label>
                                    <select class="form-select" id="filtro-ciudad">
                                        <option value="">Dónde Estás</option>
                                        <option value="1">Lima</option>
                                        <option value="2">Arequipa</option>
                                        <option value="3">Trujillo</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Por sede</label>
                                    <select class="form-select" id="filtro-sede">
                                        <option value="">Elige tu cine</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Por Fecha</label>
                                    <input type="date" class="form-control" id="filtro-fecha" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <button class="btn btn-primary btn-lg px-5" id="btn-filtrar">
                                    <i class="fas fa-search me-2"></i>Filtrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Películas en Estreno - Grid -->
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">
                    <i class="fas fa-fire text-warning me-2"></i>
                    En Estreno
                </h2>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" id="btn-en-estreno">En Estreno</button>
                    <button class="btn btn-outline-secondary" id="btn-proximos">Próximos Estrenos</button>
                </div>
            </div>

            <div class="row g-4" id="peliculas-container">
                @foreach($peliculasEstreno as $pelicula)
                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                    <div class="card h-100 movie-card">
                        <div class="position-relative">
                            <img src="{{ $pelicula->poster ? asset('storage/' . $pelicula->poster) : asset('images/posters/placeholder.jpg') }}" 
                                 class="card-img-top movie-poster" alt="{{ $pelicula->titulo }}">
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-warning text-dark">{{ $pelicula->clasificacion }}</span>
                            </div>
                        </div>
                        <div class="card-body p-3 d-flex flex-column">
                            <h6 class="card-title fw-bold mb-2" style="line-height: 1.2; min-height: 2.4em; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $pelicula->titulo }}
                            </h6>
                            <p class="card-text small text-muted mb-2">{{ $pelicula->genero }}</p>
                            <p class="card-text small text-muted mb-3">{{ $pelicula->getDuracionFormateada() }}</p>
                            <div class="mt-auto">
                                <a href="{{ route('pelicula.show', $pelicula) }}" class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-ticket-alt me-1"></i>Comprar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('peliculas') }}" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-th me-2"></i>Ver más películas
                </a>
            </div>
        </div>
    </section>

    <!-- Membresía Socio -->
    <section class="py-5 bg-primary text-white position-relative overflow-hidden">
        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10">
            <img src="{{ asset('images/socio-bg.jpg') }}" class="w-100 h-100 object-fit-cover" alt="Socio Background">
        </div>
        <div class="container position-relative">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <img src="{{ asset('images/socio-card.png') }}" alt="Tarjeta Socio" class="img-fluid" style="max-height: 120px;">
                        </div>
                        <div class="col-md-9">
                            <h3 class="fw-bold mb-2">Únete y conviértete en Socio</h3>
                            <p class="mb-0">¿Estás listo para vivir la más grande experiencia y disfrutar los mejores beneficios?</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <a href="#" class="btn btn-warning btn-lg px-5">
                        <i class="fas fa-crown me-2"></i>Únete
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
/* Estilos del carrusel hero - CORREGIDO PARA NAVBAR */
.hero-carousel {
    height: 100vh;
    min-height: 600px;
    overflow: hidden;
    position: relative;
    top: 0;
}

.hero-slide {
    height: 100vh;
    min-height: 600px;
    position: relative;
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.hero-content {
    position: relative;
    z-index: 2;
    height: 100%;
    display: flex;
    align-items: center;
    padding-top: 80px; /* CORREGIDO: Espacio para evitar que el navbar tape el contenido */
}

.movie-poster-hero {
    max-height: 500px;
    border-radius: 15px;
    transition: transform 0.3s ease;
}

.poster-container {
    position: relative;
}

.poster-info {
    position: absolute;
    top: 15px;
    right: 15px;
}

.rating-badge {
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: bold;
}

/* Indicadores del carrusel - CORREGIDOS */
.carousel-indicators {
    bottom: 40px; /* CORREGIDO: Más espacio desde abajo para ser visibles */
    z-index: 3;
}

.carousel-indicators [data-bs-target] {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin: 0 5px;
    background-color: rgba(255,255,255,0.5);
    border: 2px solid rgba(255,255,255,0.8);
    transition: all 0.3s ease;
}

.carousel-indicators .active {
    background-color: #ffc107;
    border-color: #ffc107;
    transform: scale(1.2);
}

/* Controles del carrusel */
.carousel-control-prev,
.carousel-control-next {
    width: 5%;
    opacity: 0.8;
    transition: opacity 0.3s ease;
    z-index: 3;
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
    opacity: 1;
}

.carousel-control-icon {
    background: rgba(0,0,0,0.5);
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.carousel-control-icon:hover {
    background: rgba(0,0,0,0.8);
    transform: scale(1.1);
}

/* Animaciones */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes zoomIn {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fade-in {
    animation: fadeInUp 0.8s ease-out;
}

.animate-fade-in-delay {
    animation: fadeInUp 0.8s ease-out 0.2s both;
}

.animate-fade-in-delay-2 {
    animation: fadeInUp 0.8s ease-out 0.4s both;
}

.animate-fade-in-delay-3 {
    animation: fadeInUp 0.8s ease-out 0.6s both;
}

.animate-zoom-in {
    animation: zoomIn 1s ease-out 0.3s both;
}

/* Badge premium mejorado */
.badge-premium {
    background: linear-gradient(45deg, #FFD700, #FFA500);
    color: #000;
    font-weight: bold;
    text-shadow: none;
}

/* Efectos hover para los posters */
.poster-container:hover .movie-poster-hero {
    transform: scale(1.05);
}

/* Responsive - CORREGIDO */
@media (max-width: 768px) {
    .hero-carousel {
        height: 80vh;
        min-height: 500px;
    }
    
    .hero-slide {
        height: 80vh;
        min-height: 500px;
    }
    
    .hero-content {
        padding-top: 60px; /* CORREGIDO: Menos padding en móviles */
    }
    
    .movie-poster-hero {
        max-height: 300px;
    }
    
    .carousel-control-icon {
        width: 50px;
        height: 50px;
    }
    
    .display-4 {
        font-size: 2.5rem;
    }
}

@media (max-width: 576px) {
    .hero-carousel {
        height: 70vh;
        min-height: 400px;
    }
    
    .hero-slide {
        height: 70vh;
        min-height: 400px;
    }
    
    .hero-content {
        padding-top: 50px; /* CORREGIDO: Aún menos padding en móviles pequeños */
    }
    
    .movie-poster-hero {
        max-height: 250px;
    }
    
    .display-4 {
        font-size: 2rem;
    }
    
    .carousel-control-prev,
    .carousel-control-next {
        width: 8%;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    console.log('🎬 Carrusel de películas inicializado');
    
    // Inicializar carrusel con configuración personalizada
    var carousel = new bootstrap.Carousel(document.getElementById('peliculasCarousel'), {
        interval: 5000,
        wrap: true,
        touch: true
    });
    
    // Pausar carrusel al hacer hover
    $('#peliculasCarousel').hover(
        function() {
            carousel.pause();
        },
        function() {
            carousel.cycle();
        }
    );
    
    // Filtro de películas - cargar sedes según ciudad seleccionada
    $('#filtro-ciudad').change(function() {
        const ciudadId = $(this).val();
        $('#filtro-sede').html('<option value="">Elige tu cine</option>');
        
        if (ciudadId) {
            $.get(`/api/ciudades/${ciudadId}/cines`)
                .done(function(cines) {
                    cines.forEach(function(cine) {
                        $('#filtro-sede').append(`<option value="${cine.id}">${cine.nombre}</option>`);
                    });
                });
        }
    });

    // Botón filtrar - FUNCIONALIDAD CORREGIDA
    $('#btn-filtrar').click(function() {
        const peliculaId = $('#filtro-pelicula').val();
        const ciudadId = $('#filtro-ciudad').val();
        const sedeId = $('#filtro-sede').val();
        const fecha = $('#filtro-fecha').val();

        // Si se seleccionó una película específica, ir directamente a sus detalles
        if (peliculaId) {
            window.location.href = `/pelicula/${peliculaId}`;
            return;
        }

        // Si no se seleccionó película específica, ir a la página de películas con filtros
        let url = '/peliculas';
        const params = [];
        
        if (ciudadId) params.push(`ciudad_id=${ciudadId}`);
        if (sedeId) params.push(`sede_id=${sedeId}`);
        if (fecha) params.push(`fecha=${fecha}`);

        if (params.length > 0) {
            url += '?' + params.join('&');
        }

        window.location.href = url;
    });

    // Toggle entre en estreno y próximos
    $('#btn-proximos').click(function() {
        $(this).removeClass('btn-outline-secondary').addClass('btn-outline-primary');
        $('#btn-en-estreno').removeClass('btn-outline-primary').addClass('btn-outline-secondary');
        
        // Cargar próximos estrenos via AJAX
        $.get('/api/peliculas/proximos-estrenos')
            .done(function(peliculas) {
                updatePeliculasContainer(peliculas);
            });
    });

    $('#btn-en-estreno').click(function() {
        $(this).removeClass('btn-outline-secondary').addClass('btn-outline-primary');
        $('#btn-proximos').removeClass('btn-outline-primary').addClass('btn-outline-secondary');
        location.reload();
    });

    // Función para actualizar el contenedor de películas
    function updatePeliculasContainer(peliculas) {
        let html = '';
        peliculas.forEach(function(pelicula) {
            html += `
                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                    <div class="card h-100 movie-card">
                        <div class="position-relative">
                            <img src="${pelicula.poster ? '/storage/' + pelicula.poster : '/images/posters/placeholder.jpg'}" 
                                 class="card-img-top movie-poster" alt="${pelicula.titulo}">
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-warning text-dark">${pelicula.clasificacion}</span>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold" style="line-height: 1.2; min-height: 2.4em; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${pelicula.titulo}</h6>
                            <p class="card-text small text-muted mb-2">${pelicula.genero}</p>
                            <p class="card-text small text-muted">${pelicula.duracion} min</p>
                            <a href="/pelicula/${pelicula.id}" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-ticket-alt me-1"></i>Comprar
                            </a>
                        </div>
                    </div>
                </div>
            `;
        });
        $('#peliculas-container').html(html);
    }
    
    // Efecto parallax suave en el carrusel (opcional)
    $(window).scroll(function() {
        var scrolled = $(this).scrollTop();
        var rate = scrolled * -0.5;
        $('.hero-background').css('transform', 'translate3d(0, ' + rate + 'px, 0)');
    });
});
</script>
@endpush