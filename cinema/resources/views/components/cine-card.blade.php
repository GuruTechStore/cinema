{{-- resources/views/components/cine-card.blade.php --}}
<div class="col">
    <div class="card cinema-card h-100 border-0 shadow-sm">
        <div class="position-relative">
            <img src="{{ getCinemaImageUrl($cine->imagen ?? null, $cine->nombre) }}" 
                 alt="{{ $cine->nombre }}" 
                 class="card-img-top cinema-image">
            
            <!-- Badge de ciudad -->
            <div class="position-absolute top-0 start-0 p-3">
                <span class="badge bg-warning text-dark fw-bold">
                    <i class="fas fa-map-marker-alt me-1"></i>{{ $cine->ciudad->nombre }}
                </span>
            </div>
            
            <!-- Badge de estado (si es necesario) -->
            <div class="position-absolute top-0 end-0 p-3">
                <span class="badge bg-success">
                    <i class="fas fa-check-circle me-1"></i>Abierto
                </span>
            </div>
        </div>
        
        <div class="card-body d-flex flex-column">
            <h5 class="card-title fw-bold mb-3">{{ $cine->nombre }}</h5>
            
            <p class="text-muted mb-3">
                <i class="fas fa-location-dot me-2"></i>{{ $cine->direccion }}
            </p>
            
            <!-- Estadísticas del cine -->
            <div class="row text-center mb-3">
                <div class="col-4">
                    <div class="cinema-stat">
                        <h6 class="fw-bold text-primary mb-0">{{ $cine->salas->count() }}</h6>
                        <small class="text-muted">Salas</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="cinema-stat border-start border-end">
                        <h6 class="fw-bold text-primary mb-0">{{ $cine->salas->sum('capacidad') }}</h6>
                        <small class="text-muted">Asientos</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="cinema-stat">
                        @php
                            $peliculasDisponibles = \App\Models\Pelicula::whereHas('funciones.sala', function($query) use ($cine) {
                                $query->where('cine_id', $cine->id);
                            })->where('activa', true)->count();
                        @endphp
                        <h6 class="fw-bold text-primary mb-0">{{ $peliculasDisponibles }}</h6>
                        <small class="text-muted">Películas</small>
                    </div>
                </div>
            </div>

            <!-- Información de contacto -->
            @if($cine->telefono)
            <p class="text-muted small mb-2">
                <i class="fas fa-phone me-2"></i>{{ $cine->telefono }}
            </p>
            @endif

            <!-- Características del cine -->
            <div class="mb-3">
                <div class="d-flex flex-wrap gap-1">
                    <span class="badge bg-light text-dark border">
                        <i class="fas fa-volume-up me-1"></i>Audio Digital
                    </span>
                    <span class="badge bg-light text-dark border">
                        <i class="fas fa-tv me-1"></i>4K
                    </span>
                    @if($cine->salas->where('tipo', 'Premium')->count() > 0)
                    <span class="badge bg-warning text-dark">
                        <i class="fas fa-crown me-1"></i>Premium
                    </span>
                    @endif
                    @if($cine->salas->where('tipo', 'VIP')->count() > 0)
                    <span class="badge bg-dark text-white">
                        <i class="fas fa-star me-1"></i>VIP
                    </span>
                    @endif
                </div>
            </div>

            <!-- Horarios de atención -->
            <p class="text-muted small mb-3">
                <i class="fas fa-clock me-2"></i>
                <strong>Horarios:</strong> Lun-Dom 10:00 AM - 11:00 PM
            </p>
            
            <!-- Botones de acción -->
            <div class="mt-auto">
                <div class="d-grid gap-2">
                    <a href="{{ route('cine.show', $cine) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-info-circle me-2"></i>Ver Detalles
                    </a>
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('cine.programacion', $cine) }}" 
                               class="btn btn-outline-warning btn-sm w-100">
                                <i class="fas fa-calendar me-1"></i>Programación
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($cine->direccion . ', ' . $cine->ciudad->nombre) }}" 
                               target="_blank" 
                               class="btn btn-outline-success btn-sm w-100">
                                <i class="fas fa-map me-1"></i>Ubicación
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer del card con información adicional -->
        <div class="card-footer bg-light border-0 py-2">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="fas fa-wifi me-1"></i>WiFi gratis
                </small>
                <small class="text-muted">
                    <i class="fas fa-parking me-1"></i>Estacionamiento
                </small>
                <small class="text-muted">
                    <i class="fas fa-wheelchair me-1"></i>Accesible
                </small>
            </div>
        </div>
    </div>
</div>

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

.cinema-stat {
    padding: 0.5rem;
}

.border-start {
    border-left: 1px solid #dee2e6 !important;
}

.border-end {
    border-right: 1px solid #dee2e6 !important;
}

@media (max-width: 768px) {
    .cinema-image {
        height: 180px;
    }
    
    .cinema-stat {
        padding: 0.25rem;
    }
    
    .border-start,
    .border-end {
        border: none !important;
    }
}
</style>