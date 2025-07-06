{{-- resources/views/peliculas/show.blade.php - SOLUCIÓN COMPLETA --}}
@extends('layouts.app')

@section('title', $pelicula->titulo . ' - Butaca del Salchicon')

@section('content')
    <!-- Hero Section con imagen de fondo -->
    <section class="hero-movie" style="background-image: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('{{ $pelicula->poster ? asset('storage/' . $pelicula->poster) : asset('images/posters/placeholder.jpg') }}');">
        <div class="overlay"></div>
        <div class="container position-relative">
            <div class="row align-items-center min-vh-75">
                <div class="col-lg-4">
                    <img src="{{ $pelicula->poster ? asset('storage/' . $pelicula->poster) : asset('images/posters/placeholder.jpg') }}" 
                         alt="{{ $pelicula->titulo }}" class="img-fluid movie-poster shadow-lg rounded">
                </div>
                <div class="col-lg-8 ps-lg-5 text-white">
                    <h1 class="display-4 fw-bold mb-3">{{ $pelicula->titulo }}</h1>
                    <p class="lead mb-4">{{ $pelicula->descripcion }}</p>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Género:</strong> {{ $pelicula->genero }}</p>
                            <p><strong>Duración:</strong> {{ $pelicula->duracion }} min</p>
                            <p><strong>Director:</strong> {{ $pelicula->director }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Clasificación:</strong> 
                                <span class="badge bg-warning text-dark">{{ $pelicula->clasificacion }}</span>
                            </p>
                            <p><strong>Estreno:</strong> {{ $pelicula->fecha_estreno->format('d M Y') }}</p>
                            @if($pelicula->fecha_estreno->gt(now()))
                                <p><span class="badge bg-info">Próximo Estreno</span></p>
                            @else
                                <p><span class="badge bg-success">En Cartelera</span></p>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3">
                        @if($pelicula->fecha_estreno->lte(now()))
                            <a href="#horarios" class="btn btn-warning btn-lg">
                                <i class="fas fa-ticket-alt me-2"></i>Comprar Entradas
                            </a>
                        @else
                            <span class="btn btn-secondary btn-lg disabled">
                                <i class="fas fa-calendar me-2"></i>Próximamente
                            </span>
                        @endif
                        <button class="btn btn-outline-light btn-lg" data-bs-toggle="modal" data-bs-target="#trailerModal">
                            <i class="fas fa-play me-2"></i>Ver Trailer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($pelicula->fecha_estreno->lte(now()))
    <!-- Selección de Ciudad y Fecha -->
    <section class="py-5 bg-light" id="horarios">
        <div class="container">
            <h3 class="fw-bold mb-4 text-center">Selecciona tu función</h3>
            
            <!-- Información de fechas disponibles -->
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Funciones disponibles desde:</strong> {{ $pelicula->fecha_estreno->format('d M Y') }}
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="select-ciudad" class="form-label fw-bold">Ciudad</label>
                                    <select id="select-ciudad" class="form-select form-select-lg">
                                        <option value="">-- Selecciona una ciudad --</option>
                                        @foreach($ciudades as $ciudad)
                                            <option value="{{ $ciudad->id }}">{{ $ciudad->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="select-fecha" class="form-label fw-bold">Fecha</label>
                                    <select id="select-fecha" class="form-select form-select-lg">
                                        <option value="">-- Selecciona una fecha --</option>
                                        @php
                                            // Fecha de inicio: la mayor entre hoy y fecha de estreno
                                            $hoy = now();
                                            $fechaInicio = $pelicula->fecha_estreno->gt($hoy) ? $pelicula->fecha_estreno : $hoy;
                                            
                                            // Debug info
                                            $debug = [
                                                'hoy' => $hoy->format('Y-m-d'),
                                                'estreno' => $pelicula->fecha_estreno->format('Y-m-d'),
                                                'inicio' => $fechaInicio->format('Y-m-d')
                                            ];
                                        @endphp
                                        
                                        {{-- Debug info (remover en producción) --}}
                                        <!-- Debug: Hoy={{ $debug['hoy'] }}, Estreno={{ $debug['estreno'] }}, Inicio={{ $debug['inicio'] }} -->
                                        
                                        @for($i = 0; $i < 14; $i++)
                                            @php
                                                $fecha = $fechaInicio->copy()->addDays($i);
                                                $value = $fecha->format('Y-m-d');
                                                $display = $fecha->format('l, d M');
                                                
                                                // Etiquetas especiales
                                                if($fecha->isToday()) {
                                                    $display = 'Hoy, ' . $fecha->format('d M');
                                                } elseif($fecha->isTomorrow()) {
                                                    $display = 'Mañana, ' . $fecha->format('d M');
                                                }
                                                
                                                // Si es el día de estreno
                                                if($fecha->isSameDay($pelicula->fecha_estreno)) {
                                                    $display .= ' (Estreno)';
                                                }
                                            @endphp
                                            <option value="{{ $value }}">{{ $display }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Horarios disponibles -->
    <section class="py-5">
        <div class="container">
            <div id="no-horarios" class="text-center py-5">
                <i class="fas fa-map-marker-alt display-3 text-muted mb-3"></i>
                <h5 class="text-muted">Selecciona ciudad y fecha</h5>
                <p class="text-muted">para ver los horarios disponibles</p>
            </div>

            <div id="cines-horarios" class="d-none">
                <div id="horarios-container"></div>
            </div>
        </div>
    </section>
    @else
    <!-- Mensaje para próximos estrenos -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center">
                <h3 class="mb-4">Próximo Estreno</h3>
                <div class="alert alert-warning">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <strong>Esta película se estrena el {{ $pelicula->fecha_estreno->format('d M Y') }}</strong>
                    <br>
                    <small>Las funciones estarán disponibles a partir de esa fecha</small>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Modal del Trailer -->
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

@push('styles')
<style>
    .hero-movie {
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        min-height: 70vh;
        position: relative;
    }
    
    .hero-movie .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(0,0,0,0.8), rgba(0,0,0,0.4));
    }
    
    .movie-poster {
        max-height: 500px;
        object-fit: cover;
        border: 3px solid rgba(255,255,255,0.2);
    }
    
    .min-vh-75 {
        min-height: 75vh;
    }
    
    .horario-item {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .horario-item:hover {
        transform: translateY(-2px);
        border-color: #ffc107;
        box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
    }
    
    .cinema-header {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Vista cargada. Película:', {
        id: {{ $pelicula->id }},
        titulo: "{{ $pelicula->titulo }}",
        fecha_estreno: "{{ $pelicula->fecha_estreno->format('Y-m-d') }}",
        ya_se_estreno: {{ $pelicula->fecha_estreno->lte(now()) ? 'true' : 'false' }}
    });

    @if($pelicula->fecha_estreno->lte(now()))
    // Solo cargar funcionalidad de horarios si ya se estrenó
    $('#select-ciudad, #select-fecha').on('change', function() {
        cargarHorarios();
    });

    function cargarHorarios() {
        const ciudadId = $('#select-ciudad').val();
        const fecha = $('#select-fecha').val();

        console.log('Cargando horarios:', { 
            ciudadId, 
            fecha, 
            peliculaId: {{ $pelicula->id }},
            url: `/api/peliculas/{{ $pelicula->id }}/funciones`
        });

        if (!ciudadId || !fecha) {
            $('#cines-horarios').addClass('d-none');
            $('#no-horarios').removeClass('d-none');
            return;
        }

        // Mostrar loading
        $('#horarios-container').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando horarios disponibles...</p>
            </div>
        `);
        $('#cines-horarios').removeClass('d-none');
        $('#no-horarios').addClass('d-none');

        // Llamada AJAX
        $.ajax({
            url: `/api/peliculas/{{ $pelicula->id }}/funciones`,
            method: 'GET',
            data: {
                ciudad_id: ciudadId,
                fecha: fecha
            },
            success: function(funciones) {
                console.log('Funciones recibidas:', funciones);
                
                if (funciones.length === 0) {
                    $('#horarios-container').html(`
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times display-3 text-muted mb-3"></i>
                            <h5 class="text-muted">No hay funciones disponibles</h5>
                            <p class="text-muted">para la fecha y ciudad seleccionadas</p>
                            <small class="text-muted">Prueba con otra fecha o ciudad</small>
                        </div>
                    `);
                } else {
                    mostrarHorarios(funciones);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', {xhr, status, error, responseText: xhr.responseText});
                $('#horarios-container').html(`
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Error al cargar los horarios</strong><br>
                        <small>Por favor, intenta nuevamente. Código: ${xhr.status}</small><br>
                        <small class="text-muted">Fecha: ${fecha}, Ciudad: ${ciudadId}</small>
                    </div>
                `);
            }
        });
    }

    function mostrarHorarios(funciones) {
        const cinesGrouped = {};
        
        // Agrupar funciones por cine
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
        
        Object.values(cinesGrouped).forEach(function(grupo) {
            html += `
                <div class="card mb-4 shadow-sm">
                    <div class="card-header cinema-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 fw-bold">
                                    <i class="fas fa-building me-2 text-warning"></i>${grupo.cine.nombre}
                                </h5>
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>${grupo.cine.direccion || 'Dirección no disponible'}
                                </small>
                            </div>
                            <span class="badge bg-warning text-dark">${grupo.funciones.length} funciones</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
            `;
            
            grupo.funciones.forEach(function(funcion) {
                const hora = formatTime(funcion.hora_funcion);
                const precio = formatPrice(funcion.precio);
                
                html += `
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card horario-item h-100">
                            <div class="card-body text-center p-3">
                                <h5 class="fw-bold text-primary mb-2">${hora}</h5>
                                <div class="mb-2">
                                    <span class="badge bg-secondary me-1">${funcion.tipo}</span>
                                    <span class="badge bg-info">${funcion.formato}</span>
                                </div>
                                <small class="text-muted d-block mb-2">Sala ${funcion.sala.nombre}</small>
                                <div class="price-section mb-3">
                                    <strong class="text-success fs-5">${precio}</strong>
                                    <small class="text-muted d-block">+ tarifa servicio</small>
                                </div>
                                <a href="/reserva/${funcion.id}/asientos" class="btn btn-warning w-100 fw-bold">
                                    <i class="fas fa-shopping-cart me-1"></i>Comprar
                                </a>
                            </div>
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

    // Funciones auxiliares para formato
    function formatTime(timeString) {
        try {
            const time = new Date('2000-01-01 ' + timeString);
            return time.toLocaleTimeString('es-PE', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
        } catch(e) {
            return timeString;
        }
    }

    // Auto-seleccionar la primera fecha disponible
    if ($('#select-fecha option').length > 1) {
        $('#select-fecha').val($('#select-fecha option:eq(1)').val());
    }
    @endif
});
// JavaScript mejorado para la vista de película - Agregar al final del script en peliculas/show.blade.php

function cargarHorarios() {
    const ciudadId = $('#select-ciudad').val();
    const fecha = $('#select-fecha').val();
    const peliculaId = {{ $pelicula->id }};

    console.log('🎬 INICIANDO CARGA DE HORARIOS', { 
        ciudadId, 
        fecha, 
        peliculaId,
        url: `/api/peliculas/${peliculaId}/funciones`
    });

    // Validaciones básicas
    if (!ciudadId || !fecha) {
        $('#cines-horarios').addClass('d-none');
        $('#no-horarios').removeClass('d-none');
        console.log('❌ Faltan parámetros básicos');
        return;
    }

    // Mostrar loading con más información
    $('#horarios-container').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando horarios disponibles...</p>
            <small class="text-muted">
                Película: ${peliculaId} | Fecha: ${fecha} | Ciudad: ${ciudadId}
            </small>
        </div>
    `);
    $('#cines-horarios').removeClass('d-none');
    $('#no-horarios').addClass('d-none');

    // Configuración AJAX con debug
    const ajaxConfig = {
        url: `/api/peliculas/${peliculaId}/funciones`,
        method: 'GET',
        data: {
            ciudad_id: ciudadId,
            fecha: fecha
        },
        timeout: 10000, // 10 segundos
        beforeSend: function(xhr) {
            console.log('📡 Enviando petición AJAX...');
        },
        success: function(funciones, textStatus, xhr) {
            console.log('✅ RESPUESTA EXITOSA', {
                status: xhr.status,
                textStatus: textStatus,
                funcionesCount: Array.isArray(funciones) ? funciones.length : 'No es array',
                primeraFuncion: Array.isArray(funciones) && funciones.length > 0 ? funciones[0] : null
            });
            
            if (!Array.isArray(funciones)) {
                console.error('❌ La respuesta no es un array:', funciones);
                mostrarError('Formato de respuesta inválido');
                return;
            }
            
            if (funciones.length === 0) {
                mostrarSinFunciones();
            } else {
                // Verificar estructura de cada función
                let estructuraValida = true;
                funciones.forEach((funcion, index) => {
                    if (!funcion.sala || !funcion.sala.cine) {
                        console.error(`❌ Función ${index} tiene estructura inválida:`, funcion);
                        estructuraValida = false;
                    }
                });
                
                if (estructuraValida) {
                    mostrarHorarios(funciones);
                } else {
                    mostrarError('Datos de funciones incompletos');
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ ERROR AJAX DETALLADO', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error,
                ajaxStatus: status
            });

            let errorMessage = 'Error desconocido';
            let debugInfo = '';

            try {
                if (xhr.responseJSON) {
                    errorMessage = xhr.responseJSON.message || xhr.responseJSON.error || 'Error del servidor';
                    debugInfo = xhr.responseJSON.debug_info ? JSON.stringify(xhr.responseJSON.debug_info) : '';
                } else if (xhr.responseText) {
                    errorMessage = `Error ${xhr.status}: ${xhr.statusText}`;
                    debugInfo = xhr.responseText.substring(0, 200);
                }
            } catch (e) {
                console.error('Error parseando respuesta de error:', e);
            }

            mostrarError(errorMessage, debugInfo, xhr.status);
        },
        complete: function(xhr, status) {
            console.log('🏁 Petición completada', { status: status, responseStatus: xhr.status });
        }
    };

    // Ejecutar AJAX
    $.ajax(ajaxConfig);
}

function mostrarSinFunciones() {
    $('#horarios-container').html(`
        <div class="text-center py-5">
            <i class="fas fa-calendar-times display-3 text-muted mb-3"></i>
            <h5 class="text-muted">No hay funciones disponibles</h5>
            <p class="text-muted">para la fecha y ciudad seleccionadas</p>
            <div class="alert alert-info mt-3">
                <small>
                    <strong>Sugerencias:</strong><br>
                    • Prueba con otra fecha<br>
                    • Verifica si la película ya se estrenó<br>
                    • Intenta con otra ciudad
                </small>
            </div>
        </div>
    `);
}

function mostrarError(mensaje, debugInfo = '', codigo = '') {
    const codigoText = codigo ? `Código: ${codigo}` : '';
    const debugText = debugInfo ? `<details class="mt-2"><summary>Información técnica</summary><pre class="small">${debugInfo}</pre></details>` : '';
    
    $('#horarios-container').html(`
        <div class="alert alert-danger text-center">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Error al cargar los horarios</strong><br>
            <small>${mensaje}</small><br>
            ${codigoText ? `<small class="text-muted">${codigoText}</small>` : ''}
            ${debugText}
            <div class="mt-3">
                <button class="btn btn-outline-danger btn-sm" onclick="cargarHorarios()">
                    <i class="fas fa-retry me-1"></i>Reintentar
                </button>
            </div>
        </div>
    `);
}

function mostrarHorarios(funciones) {
    console.log('🎭 Mostrando horarios para', funciones.length, 'funciones');
    
    const cinesGrouped = {};
    
    // Agrupar funciones por cine con validación
    funciones.forEach(function(funcion, index) {
        if (!funcion.sala || !funcion.sala.cine) {
            console.warn(`⚠️ Función ${index} sin estructura completa:`, funcion);
            return;
        }
        
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
    
    Object.values(cinesGrouped).forEach(function(grupo) {
        html += `
            <div class="card mb-4 shadow-sm">
                <div class="card-header cinema-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="fas fa-building me-2 text-warning"></i>${grupo.cine.nombre}
                            </h5>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>${grupo.cine.direccion || 'Dirección no disponible'}
                            </small>
                        </div>
                        <span class="badge bg-warning text-dark">${grupo.funciones.length} funciones</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
        `;
        
        grupo.funciones.forEach(function(funcion) {
            const hora = formatTime(funcion.hora_funcion);
            const precio = formatPrice(funcion.precio);
            
            html += `
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card horario-item h-100">
                        <div class="card-body text-center p-3">
                            <h5 class="fw-bold text-primary mb-2">${hora}</h5>
                            <div class="mb-2">
                                <span class="badge bg-secondary me-1">${funcion.tipo}</span>
                                <span class="badge bg-info">${funcion.formato}</span>
                            </div>
                            <small class="text-muted d-block mb-2">Sala ${funcion.sala.nombre}</small>
                            <div class="price-section mb-3">
                                <strong class="text-success fs-5">${precio}</strong>
                                <small class="text-muted d-block">+ tarifa servicio</small>
                            </div>
                            <a href="/reserva/${funcion.id}/asientos" class="btn btn-warning w-100 fw-bold">
                                <i class="fas fa-shopping-cart me-1"></i>Comprar
                            </a>
                        </div>
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

// Funciones auxiliares mejoradas
function formatTime(timeString) {
    try {
        const time = new Date('2000-01-01 ' + timeString);
        return time.toLocaleTimeString('es-PE', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        });
    } catch(e) {
        console.warn('Error formateando hora:', timeString, e);
        return timeString;
    }
}
</script>
@endpush