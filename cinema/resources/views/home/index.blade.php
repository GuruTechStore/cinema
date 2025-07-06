{{-- resources/views/home/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Butaca del Salchicon - La mejor experiencia cinematográfica')

@section('content')
    <!-- Hero Banner -->
    @if($peliculaDestacada)
    <section class="hero-banner d-flex align-items-center text-white position-relative">
        <div class="position-absolute top-0 start-0 w-100 h-100" 
             style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.6)), 
                    url('{{ $peliculaDestacada->poster ? asset('storage/' . $peliculaDestacada->poster) : asset('images/posters/los-4-fantasticos.jpg') }}') center/cover no-repeat;">
        </div>
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <span class="badge badge-premium fs-6 px-3 py-2 rounded-pill">
                            <i class="fas fa-star me-1"></i>ESTRENO DESTACADO
                        </span>
                    </div>
                    <h1 class="display-4 fw-bold mb-3">{{ $peliculaDestacada->titulo }}</h1>
                    <p class="lead mb-4">{{ $peliculaDestacada->descripcion }}</p>
                    <div class="mb-4">
                        <span class="badge bg-warning text-dark me-2">{{ $peliculaDestacada->genero }}</span>
                        <span class="badge bg-info me-2">{{ $peliculaDestacada->getDuracionFormateada() }}</span>
                        <span class="badge bg-secondary">{{ $peliculaDestacada->clasificacion }}</span>
                    </div>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('pelicula.show', $peliculaDestacada) }}" class="btn btn-warning btn-lg">
                            <i class="fas fa-ticket-alt me-2"></i>Comprar Entradas
                        </a>
                        <a href="{{ route('pelicula.show', $peliculaDestacada) }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-play me-2"></i>Ver Detalles
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="{{ $peliculaDestacada->poster ? asset('storage/' . $peliculaDestacada->poster) : asset('images/posters/los-4-fantasticos.jpg') }}" 
                         alt="{{ $peliculaDestacada->titulo }}" class="img-fluid movie-poster shadow-lg" style="max-height: 400px;">
                </div>
            </div>
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

    <!-- Películas en Estreno -->
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

@push('scripts')
<script>
$(document).ready(function() {
    // Filtro de películas
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

    // Botón filtrar
    $('#btn-filtrar').click(function() {
        const peliculaId = $('#filtro-pelicula').val();
        const ciudadId = $('#filtro-ciudad').val();
        const sededId = $('#filtro-sede').val();
        const fecha = $('#filtro-fecha').val();

        let url = '/peliculas?';
        if (peliculaId) url += `pelicula_id=${peliculaId}&`;
        if (ciudadId) url += `ciudad_id=${ciudadId}&`;
        if (fecha) url += `fecha=${fecha}&`;

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
});
</script>
@endpush