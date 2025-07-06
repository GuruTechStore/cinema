{{-- resources/views/components/cine-card.blade.php --}}
<div class="col-lg-4 col-md-6 cine-card">
    <div class="card h-100 shadow-sm">
        <div class="position-relative">
            <img src="{{ $cine->imagen ? asset('storage/' . $cine->imagen) : asset('images/cines/' . str_replace([' ', 'CP '], ['-', 'cp-'], strtolower($cine->nombre)) . '.jpg') }}" 
                 class="card-img-top cinema-image" alt="{{ $cine->nombre }}" style="height: 200px;">
            <div class="position-absolute top-0 start-0 m-3">
                <span class="badge bg-primary">
                    <i class="fas fa-map-marker-alt me-1"></i>
                    {{ $cine->ciudad->nombre }}
                </span>
            </div>
            @if($cine->formatos)
                <div class="position-absolute top-0 end-0 m-3">
                    @foreach(explode(', ', $cine->formatos) as $formato)
                        <span class="badge bg-warning text-dark mb-1 d-block">{{ $formato }}</span>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="card-body">
            <h5 class="card-title fw-bold">{{ $cine->nombre }}</h5>
            <p class="card-text text-muted">
                <i class="fas fa-map-marker-alt me-2"></i>{{ $cine->direccion }}
            </p>
            
            @if($cine->salas && $cine->salas->count() > 0)
                <p class="card-text">
                    <i class="fas fa-door-open me-2 text-primary"></i>
                    <small class="text-muted">{{ $cine->salas->count() }} salas disponibles</small>
                </p>
            @endif

            <div class="mb-3">
                @if($cine->formatos)
                    @foreach(explode(', ', $cine->formatos) as $formato)
                        <span class="badge bg-secondary me-1">{{ $formato }}</span>
                    @endforeach
                @endif
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('cine.show', $cine) }}" class="btn btn-primary">
                    <i class="fas fa-calendar-alt me-2"></i>Ver Programación
                </a>
                <a href="#" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-map me-2"></i>Cómo llegar
                </a>
            </div>
        </div>
    </div>
</div>