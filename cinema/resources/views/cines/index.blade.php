{{-- resources/views/cines/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Nuestros Cines')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">Nuestros Cines</h1>
                    <p class="fs-5 mb-4">Encuentra el cine más cercano a ti y disfruta de la mejor experiencia cinematográfica</p>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-building display-1 opacity-75"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Filtros -->
    <section class="py-4 bg-light">
        <div class="container">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-search me-2"></i>Buscar cine
                            </label>
                            <input type="text" class="form-control" name="buscar" 
                                   placeholder="Nombre del cine..." 
                                   value="{{ request('buscar') }}">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-map-marker-alt me-2"></i>Ciudad
                            </label>
                            <select class="form-select" name="ciudad_id">
                                <option value="">Todas las ciudades</option>
                                @foreach($ciudades as $ciudad)
                                    <option value="{{ $ciudad->id }}" 
                                            {{ request('ciudad_id') == $ciudad->id ? 'selected' : '' }}>
                                        {{ $ciudad->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="fas fa-search me-2"></i>Buscar
                                </button>
                                <a href="{{ route('cines.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Listado de Cines -->
    <section class="py-5">
        <div class="container">
            @if($cines->count() > 0)
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h3 class="fw-bold">{{ $cines->total() }} {{ $cines->total() == 1 ? 'Cine Encontrado' : 'Cines Encontrados' }}</h3>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="text-muted mb-0">
                            Mostrando {{ $cines->firstItem() }} - {{ $cines->lastItem() }} de {{ $cines->total() }} resultados
                        </p>
                    </div>
                </div>

                <div class="row g-4">
                    @foreach($cines as $cine)
                    <div class="col-lg-6 col-xl-4">
                        <div class="card cinema-card h-100 border-0 shadow-sm">
                            <div class="position-relative">
                                <img src="{{ getCinemaImageUrl($cine->imagen ?? null, $cine->nombre) }}" 
                                     alt="{{ $cine->nombre }}" 
                                     class="card-img-top cinema-image">
                                
                                <!-- Badge de ciudad -->
                                <div class="position-absolute top-0 start-0 p-3">
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $cine->ciudad->nombre }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold">{{ $cine->nombre }}</h5>
                                <p class="text-muted mb-3">
                                    <i class="fas fa-location-dot me-2"></i>{{ $cine->direccion }}
                                </p>
                                
                                <!-- Información de salas -->
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h6 class="fw-bold text-primary mb-0">{{ $cine->salas->count() }}</h6>
                                            <small class="text-muted">Salas</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="fw-bold text-primary mb-0">{{ $cine->salas->sum('capacidad') }}</h6>
                                        <small class="text-muted">Asientos</small>
                                    </div>
                                </div>

                                <!-- Información de contacto -->
                                @if($cine->telefono)
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-phone me-2"></i>{{ $cine->telefono }}
                                </p>
                                @endif

                                <!-- Servicios/Características -->
                                <div class="mb-3">
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-secondary">Audio Digital</span>
                                        <span class="badge bg-secondary">Proyección 4K</span>
                                        @if($cine->salas->where('tipo', 'Premium')->count() > 0)
                                        <span class="badge bg-warning text-dark">Salas Premium</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Botones de acción -->
                                <div class="mt-auto">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('cine.show', $cine) }}" class="btn btn-primary">
                                            <i class="fas fa-info-circle me-2"></i>Ver Detalles
                                        </a>
                                        <a href="{{ route('cine.programacion', $cine) }}" class="btn btn-outline-warning">
                                            <i class="fas fa-calendar me-2"></i>Programación
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                @if($cines->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $cines->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
                @endif

            @else
                <!-- Estado vacío -->
                <div class="text-center py-5">
                    <i class="fas fa-search display-1 text-muted mb-4"></i>
                    <h3 class="text-muted mb-3">No se encontraron cines</h3>
                    <p class="text-muted mb-4">
                        @if(request()->hasAny(['buscar', 'ciudad_id']))
                            No hay cines que coincidan con tu búsqueda. Intenta con otros filtros.
                        @else
                            Actualmente no hay cines disponibles.
                        @endif
                    </p>
                    
                    @if(request()->hasAny(['buscar', 'ciudad_id']))
                    <a href="{{ route('cines.index') }}" class="btn btn-primary">
                        <i class="fas fa-refresh me-2"></i>Ver Todos los Cines
                    </a>
                    @endif
                </div>
            @endif
        </div>
    </section>

    <!-- Información Adicional -->
    @if($cines->count() > 0)
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h4 class="fw-bold mb-3">¿Por qué elegir nuestros cines?</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-star text-warning fs-4 me-3"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold">Calidad Premium</h6>
                                    <p class="text-muted small">Audio y video de la más alta calidad con tecnología 4K y sonido envolvente.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-chair text-warning fs-4 me-3"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold">Comodidad Total</h6>
                                    <p class="text-muted small">Asientos ergonómicos y espaciosos para tu máxima comodidad.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-warning fs-4 me-3"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold">Ubicaciones Convenientes</h6>
                                    <p class="text-muted small">Cines estratégicamente ubicados en las mejores zonas de la ciudad.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-candy-cane text-warning fs-4 me-3"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold">Dulcería Completa</h6>
                                    <p class="text-muted small">Amplia variedad de snacks y bebidas para complementar tu experiencia.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-mobile-alt display-4 text-primary mb-3"></i>
                            <h5 class="fw-bold mb-3">¿Necesitas ayuda?</h5>
                            <p class="text-muted mb-3">Contacta con nuestro equipo de atención al cliente</p>
                            <div class="d-grid gap-2">
                                <a href="tel:+51987654321" class="btn btn-primary">
                                    <i class="fas fa-phone me-2"></i>Llamar
                                </a>
                                <a href="https://wa.me/51987654321" class="btn btn-success" target="_blank">
                                    <i class="fab fa-whatsapp me-2"></i>WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
@endsection

@push('styles')
<style>
    .cinema-card {
        transition: all 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
    }
    
    .cinema-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
    }
    
    .cinema-image {
        height: 200px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .cinema-card:hover .cinema-image {
        transform: scale(1.05);
    }
    
    .hero-section {
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
    }
    
    .border-end {
        border-right: 1px solid #dee2e6 !important;
    }
    
    @media (max-width: 768px) {
        .border-end {
            border-right: none !important;
            border-bottom: 1px solid #dee2e6 !important;
            padding-bottom: 0.5rem;
            margin-bottom: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    console.log('🏢 Vista de cines cargada:', {
        total_cines: {{ $cines->total() }},
        pagina_actual: {{ $cines->currentPage() }},
        filtros_aplicados: {
            buscar: "{{ request('buscar') }}",
            ciudad_id: "{{ request('ciudad_id') }}"
        }
    });

    // Auto-submit del formulario cuando cambia la ciudad
    $('select[name="ciudad_id"]').on('change', function() {
        $(this).closest('form').submit();
    });

    // Efecto de hover mejorado para las tarjetas
    $('.cinema-card').hover(
        function() {
            $(this).find('.cinema-image').addClass('scale-effect');
        },
        function() {
            $(this).find('.cinema-image').removeClass('scale-effect');
        }
    );

    // Scroll suave para navegación
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 1000);
        }
    });
});
</script>