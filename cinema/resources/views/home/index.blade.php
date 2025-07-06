{{-- resources/views/home/index.blade.php --}}
@extends('layouts.app')

@section('title', 'CinePlanet - La mejor experiencia cinematográfica')

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
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold text-truncate">{{ $pelicula->titulo }}</h6>
                            <p class="card-text small text-muted mb-2">{{ $pelicula->genero }}</p>
                            <p class="card-text small text-muted">{{ $pelicula->getDuracionFormateada() }}</p>
                            <a href="{{ route('pelicula.show', $pelicula) }}" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-ticket-alt me-1"></i>Comprar
                            </a>
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
                            <h6 class="card-title fw-bold text-truncate">${pelicula.titulo}</h6>
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

{{-- resources/views/home/sedes.blade.php --}}
@extends('layouts.app')

@section('title', 'Nuestras Sedes - CinePlanet')

@section('content')
    <!-- Page Header -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-3">Sedes</h1>
                    <p class="lead">Selecciona la programación del cine que deseas ver</p>
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
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row g-3 align-items-end">
                                <div class="col-8">
                                    <label class="form-label fw-bold">Por Ciudad</label>
                                    <select class="form-select" id="filtro-ciudad">
                                        <option value="">Todas las ciudades</option>
                                        @foreach($ciudades as $ciudad)
                                            <option value="{{ $ciudad->id }}" {{ request('ciudad_id') == $ciudad->id ? 'selected' : '' }}>
                                                {{ $ciudad->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <button class="btn btn-primary w-100" id="btn-filtrar">
                                        <i class="fas fa-filter me-1"></i>Filtrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cines Grid -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                @foreach($cines as $cine)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <img src="{{ $cine->imagen ? asset('storage/' . $cine->imagen) : asset('images/cines/' . str_replace(' ', '-', strtolower($cine->nombre)) . '.jpg') }}" 
                                 class="card-img-top cinema-image" alt="{{ $cine->nombre }}" style="height: 200px;">
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge bg-primary">{{ $cine->ciudad->nombre }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold">{{ $cine->nombre }}</h5>
                            <p class="card-text text-muted">
                                <i class="fas fa-map-marker-alt me-2"></i>{{ $cine->direccion }}
                            </p>
                            <div class="mb-3">
                                @foreach($cine->getFormatosArray() as $formato)
                                    <span class="badge bg-secondary me-1">{{ $formato }}</span>
                                @endforeach
                            </div>
                            <a href="{{ route('cine.show', $cine) }}" class="btn btn-primary w-100">
                                <i class="fas fa-calendar-alt me-2"></i>Ver Programación
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($cines->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-search display-1 text-muted mb-3"></i>
                    <h3 class="text-muted">No se encontraron cines</h3>
                    <p class="text-muted">Intenta con otro filtro de búsqueda</p>
                </div>
            @endif

            <div class="text-center mt-5">
                <a href="#" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Ver más cines
                </a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#btn-filtrar').click(function() {
        const ciudadId = $('#filtro-ciudad').val();
        let url = '/sedes';
        if (ciudadId) {
            url += `?ciudad_id=${ciudadId}`;
        }
        window.location.href = url;
    });
});
</script>
@endpush

{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('title', 'Iniciar Sesión - CinePlanet')

@section('content')
<div class="min-vh-100 d-flex align-items-center py-5" style="background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0" style="border-radius: 20px;">
                    <div class="card-body p-5">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <i class="fas fa-film text-primary fs-1 mb-3"></i>
                            <h2 class="fw-bold text-primary">Iniciar Sesión</h2>
                            <p class="text-muted">Ingresa a tu cuenta para disfrutar de tus beneficios, acumular puntos y vivir al máximo la experiencia del cine</p>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required autofocus>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">¿Olvidaste tu contraseña?</label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100 py-3 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Ingresar
                            </button>

                            <!-- Register Link -->
                            <div class="text-center">
                                <p class="mb-0">¿No tienes cuenta? 
                                    <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">Regístrate</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#togglePassword').click(function() {
        const passwordField = $('#password');
        const passwordFieldType = passwordField.attr('type');
        
        if (passwordFieldType === 'password') {
            passwordField.attr('type', 'text');
            $(this).html('<i class="fas fa-eye-slash"></i>');
        } else {
            passwordField.attr('type', 'password');
            $(this).html('<i class="fas fa-eye"></i>');
        }
    });
});
</script>
@endpush

{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app')

@section('title', 'Registrarse - CinePlanet')

@section('content')
<div class="min-vh-100 d-flex align-items-center py-5" style="background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0" style="border-radius: 20px;">
                    <div class="card-body p-5">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <i class="fas fa-film text-primary fs-1 mb-3"></i>
                            <h2 class="fw-bold text-primary">ÚNETE</h2>
                            <p class="text-muted">Completa tus datos y accede a nuestro universo de beneficios</p>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            
                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Nombre</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required autofocus>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" required>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-bold">Confirmar Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>

                            <!-- Terms -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label small" for="terms">
                                    Acepto los términos y la política
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100 py-3 mb-3">
                                <i class="fas fa-user-plus me-2"></i>Unirme
                            </button>

                            <!-- Login Link -->
                            <div class="text-center">
                                <p class="mb-0">¿Tienes una cuenta? 
                                    <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Sign in</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection