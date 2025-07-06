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
        <div class="card-body p-3 d-flex flex-column">
            <h6 class="card-title fw-bold mb-2" style="line-height: 1.2; min-height: 2.4em; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                {{ $pelicula->titulo }}
            </h6>
            
            <p class="card-text small text-muted mb-2">{{ $pelicula->genero }}</p>
            <p class="card-text small text-muted mb-3">{{ $pelicula->getDuracionFormateada() }}</p>
            
            {{-- Botón al final --}}
            <div class="mt-auto">
                <a href="{{ route('pelicula.show', $pelicula) }}" class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-ticket-alt me-1"></i>Comprar
                </a>
            </div>
        </div>
    </div>
</div>