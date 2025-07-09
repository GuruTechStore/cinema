{{-- resources/views/home/sedes.blade.php --}}
@extends('layouts.app')

@section('title', 'Nuestras Sedes - Butaca del Salchichon')

@section('content')
    <!-- Page Header -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-3">Sedes</h1>
                    <p class="lead">Selecciona la programación del cine que deseas ver</p>
                    <div class="mt-3">
                        <span class="badge bg-warning text-dark me-2">
                            <i class="fas fa-building me-1"></i>{{ $totalCines ?? $cines->count() }} Cines
                        </span>
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-map-marker-alt me-1"></i>{{ $ciudadesConCines ?? $ciudades->count() }} Ciudades
                        </span>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-map-marker-alt display-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Filtros -->
    <section class="py-4 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form method="GET" class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Por Ciudad</label>
                                    <select class="form-select" id="filtro-ciudad" name="ciudad_id">
                                        <option value="">Todas las ciudades</option>
                                        @foreach($ciudades as $ciudad)
                                            <option value="{{ $ciudad->id }}" {{ request('ciudad_id') == $ciudad->id ? 'selected' : '' }}>
                                                {{ $ciudad->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Buscar</label>
                                    <input type="text" class="form-control" name="buscar" 
                                           placeholder="Nombre del cine..." value="{{ request('buscar') }}">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter me-1"></i>Filtrar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cines Grid -->
    <section class="py-5">
        <div class="container">
            @if($cines->count() > 0)
                @if(is_object($cines) && method_exists($cines, 'isEmpty') && !$cines->isEmpty())
                    {{-- Si $cines es una colección agrupada --}}
                    @foreach($cines as $ciudadNombre => $cinesCiudad)
                        <div class="mb-5">
                            <h3 class="fw-bold text-primary mb-4">
                                <i class="fas fa-map-marker-alt me-2"></i>{{ $ciudadNombre }}
                                <span class="badge bg-primary ms-2">{{ $cinesCiudad->count() }} cines</span>
                            </h3>
                            <div class="row g-4">
                                @foreach($cinesCiudad as $cine)
                                    @include('components.cine-card', ['cine' => $cine])
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- Si $cines es una colección normal --}}
                    <div class="row g-4">
                        @foreach($cines as $cine)
                            @include('components.cine-card', ['cine' => $cine])
                        @endforeach
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search display-1 text-muted mb-3"></i>
                    <h3 class="text-muted">No se encontraron cines</h3>
                    <p class="text-muted">Intenta con otro filtro de búsqueda</p>
                    <a href="{{ route('sedes') }}" class="btn btn-primary">
                        <i class="fas fa-refresh me-2"></i>Ver todos los cines
                    </a>
                </div>
            @endif

            <!-- Botón Ver Más -->
            @if($cines->count() > 0)
                <div class="text-center mt-5">
                    <button class="btn btn-outline-primary btn-lg" id="btn-ver-mas" style="display: none;">
                        <i class="fas fa-plus me-2"></i>Ver más cines
                    </button>
                </div>
            @endif
        </div>
    </section>

    <!-- Información Adicional -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h4 class="fw-bold mb-3">¿Por qué elegir Butaca del Salchichon?</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <i class="fas fa-star text-warning fs-3 me-3"></i>
                                <div>
                                    <h6 class="fw-bold">Mejor Experiencia</h6>
                                    <p class="text-muted mb-0">Salas equipadas con la mejor tecnología audiovisual</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <i class="fas fa-map-marker-alt text-primary fs-3 me-3"></i>
                                <div>
                                    <h6 class="fw-bold">Ubicaciones Convenientes</h6>
                                    <p class="text-muted mb-0">Cines en los mejores centros comerciales</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <i class="fas fa-candy-cane text-warning fs-3 me-3"></i>
                                <div>
                                    <h6 class="fw-bold">Dulcería Completa</h6>
                                    <p class="text-muted mb-0">Los mejores snacks para tu experiencia</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <i class="fas fa-mobile-alt text-success fs-3 me-3"></i>
                                <div>
                                    <h6 class="fw-bold">Compra Online</h6>
                                    <p class="text-muted mb-0">Reserva tus boletos desde cualquier lugar</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 bg-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-gift display-4 mb-3"></i>
                            <h5 class="card-title">¡Ofertas Especiales!</h5>
                            <p class="card-text">Únete a nuestro programa de fidelidad y obtén descuentos exclusivos</p>
                            <a href="#" class="btn btn-warning">
                                <i class="fas fa-crown me-2"></i>Ser Socio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form on city change
    $('#filtro-ciudad').change(function() {
        $(this).closest('form').submit();
    });

    // Show more functionality (if needed)
    let itemsShown = 6;
    const totalItems = $('.cine-card').length;

    if (totalItems > itemsShown) {
        $('.cine-card:nth-child(n+' + (itemsShown + 1) + ')').hide();
        $('#btn-ver-mas').show();
    }

    $('#btn-ver-mas').click(function() {
        itemsShown += 6;
        $('.cine-card:nth-child(-n+' + itemsShown + ')').fadeIn();
        
        if (itemsShown >= totalItems) {
            $(this).hide();
        }
    });
});
</script>
@endpush
