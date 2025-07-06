{{-- resources/views/peliculas/show.blade.php --}}
@extends('layouts.app')

@section('title', $pelicula->titulo . ' - Butaca del Salchicon')

@section('content')
    <!-- Hero Section -->
    <section class="hero-banner d-flex align-items-center text-white position-relative" style="min-height: 60vh;">
        <div class="position-absolute top-0 start-0 w-100 h-100" 
             style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.5)), 
                    url('{{ $pelicula->poster ? asset('storage/' . $pelicula->poster) : asset('images/posters/placeholder.jpg') }}') center/cover no-repeat;">
        </div>
        <div class="container position-relative">
            <div class="row align-items-center">
                <div class="col-lg-4">
                    <img src="{{ $pelicula->poster ? asset('storage/' . $pelicula->poster) : asset('images/posters/placeholder.jpg') }}" 
                         alt="{{ $pelicula->titulo }}" class="img-fluid movie-poster shadow-lg">
                </div>
                <div class="col-lg-8 ps-lg-5">
                    <h1 class="display-4 fw-bold mb-3">{{ $pelicula->titulo }}</h1>
                    <p class="lead mb-4">{{ $pelicula->descripcion }}</p>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Género:</strong> {{ $pelicula->genero }}</p>
                            <p><strong>Duración:</strong> {{ $pelicula->getDuracionFormateada() }}</p>
                            <p><strong>Director:</strong> {{ $pelicula->director }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Clasificación:</strong> 
                                <span class="badge bg-warning text-dark">{{ $pelicula->clasificacion }}</span>
                            </p>
                            <p><strong>Estreno:</strong> {{ $pelicula->fecha_estreno->format('d M Y') }}</p>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3">
                        <a href="#horarios" class="btn btn-warning btn-lg">
                            <i class="fas fa-ticket-alt me-2"></i>Comprar Entradas
                        </a>
                        <button class="btn btn-outline-light btn-lg" data-bs-toggle="modal" data-bs-target="#trailerModal">
                            <i class="fas fa-play me-2"></i>Ver Trailer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Selección de Ciudad y Fecha -->
    <section class="py-4 bg-light" id="horarios">
        <div class="container">
            <h3 class="fw-bold mb-4 text-center">Selecciona tu función</h3>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Ciudad</label>
                                    <select class="form-select" id="select-ciudad">
                                        <option value="">Selecciona una ciudad</option>
                                        @foreach($ciudades as $ciudad)
                                            <option value="{{ $ciudad->id }}">{{ $ciudad->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Fecha</label>
                                    <input type="date" class="form-control" id="select-fecha" 
                                           value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Horarios por Cine -->
    <section class="py-5">
        <div class="container">
            <div id="cines-horarios" class="d-none">
                <h4 class="fw-bold mb-4">Horarios disponibles</h4>
                <div id="horarios-container">
                    <!-- Se llena dinámicamente con JavaScript -->
                </div>
            </div>

            <div id="no-horarios" class="text-center py-5">
                <i class="fas fa-calendar-times display-1 text-muted mb-3"></i>
                <h4 class="text-muted">Selecciona una ciudad y fecha</h4>
                <p class="text-muted">para ver los horarios disponibles</p>
            </div>
        </div>
    </section>

    <!-- Modal Trailer -->
    <div class="modal fade" id="trailerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Trailer - {{ $pelicula->titulo }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function cargarHorarios() {
        const ciudadId = $('#select-ciudad').val();
        const fecha = $('#select-fecha').val();

        if (!ciudadId || !fecha) {
            $('#cines-horarios').addClass('d-none');
            $('#no-horarios').removeClass('d-none');
            return;
        }

        $.get(`/api/peliculas/{{ $pelicula->id }}/funciones`, {
            ciudad_id: ciudadId,
            fecha: fecha
        })
        .done(function(funciones) {
            if (funciones.length === 0) {
                $('#horarios-container').html(`
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times display-3 text-muted mb-3"></i>
                        <h5 class="text-muted">No hay funciones disponibles</h5>
                        <p class="text-muted">para la fecha seleccionada</p>
                    </div>
                `);
            } else {
                mostrarHorarios(funciones);
            }
            $('#cines-horarios').removeClass('d-none');
            $('#no-horarios').addClass('d-none');
        })
        .fail(function() {
            showAlert('Error al cargar los horarios', 'danger');
        });
    }

    function mostrarHorarios(funciones) {
        const cinesGrouped = {};
        
        funciones.forEach(function(funcion) {
            const cineId = funcion.sala.cine.id;
            if (!cinesGrouped[cineId]) {
                cinesGrouped[cineId] = {
                    cine: funcion.sala.cine,
                    funciones: []
                };
            }
            cinesGrouped[cineId].funciones.push(funcion);
        });

        let html = '';
        Object.values(cinesGrouped).forEach(function(cineData) {
            html += `
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            ${cineData.cine.nombre}
                        </h5>
                        <small>${cineData.cine.direccion}</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
            `;

            // Agrupar por tipo de sala
            const tiposGrouped = {};
            cineData.funciones.forEach(function(funcion) {
                if (!tiposGrouped[funcion.tipo]) {
                    tiposGrouped[funcion.tipo] = [];
                }
                tiposGrouped[funcion.tipo].push(funcion);
            });

            Object.entries(tiposGrouped).forEach(function([tipo, funcionesTipo]) {
                html += `
                    <div class="col-md-6 col-lg-4">
                        <h6 class="fw-bold text-primary">${tipo}</h6>
                        <p class="small text-muted mb-2">S/ ${funcionesTipo[0].precio} + S/ ${funcionesTipo[0].tarifa_servicio} tarifa</p>
                        <div class="d-flex flex-wrap gap-2">
                `;

                funcionesTipo.forEach(function(funcion) {
                    html += `
                        <a href="/reserva/${funcion.id}/asientos" class="btn btn-outline-primary btn-sm">
                            ${funcion.hora_funcion.substring(0,5)}
                        </a>
                    `;
                });

                html += `
                        </div>
                    </div>
                `;
            });

            html += `
                        </div>
                    </div>
                </div>
            `;
        });

        $('#horarios-container').html(html);
    }

    $('#select-ciudad, #select-fecha').change(cargarHorarios);
});
</script>
@endpush