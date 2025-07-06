{{-- resources/views/components/movie-card.blade.php --}}
<div class="col-lg-2 col-md-3 col-sm-4 col-6">
    <div class="card h-100 movie-card">
        <div class="position-relative">
            <img src="{{ $pelicula->poster ? asset('storage/' . $pelicula->poster) : asset('images/posters/placeholder.jpg') }}" 
                 class="card-img-top movie-poster" alt="{{ $pelicula->titulo }}">
            <div class="position-absolute top-0 end-0 m-2">
                <span class="badge bg-warning text-dark">{{ $pelicula->clasificacion }}</span>
            </div>
            @if($pelicula->destacada)
                <div class="position-absolute top-0 start-0 m-2">
                    <span class="badge bg-danger">DESTACADA</span>
                </div>
            @endif
        </div>
        <div class="card-body p-3">
            <h6 class="card-title fw-bold text-truncate" title="{{ $pelicula->titulo }}">
                {{ $pelicula->titulo }}
            </h6>
            <p class="card-text small text-muted mb-2">{{ $pelicula->genero }}</p>
            <p class="card-text small text-muted mb-3">{{ $pelicula->getDuracionFormateada() }}</p>
            <a href="{{ route('pelicula.show', $pelicula) }}" class="btn btn-primary btn-sm w-100">
                <i class="fas fa-ticket-alt me-1"></i>Comprar
            </a>
        </div>
    </div>
</div>