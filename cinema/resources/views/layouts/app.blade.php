{{-- resources/views/layouts/app.blade.php  --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Butaca del Salchichon')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-blue: #2B47C5;
            --secondary-blue: #1A237E;
            --accent-orange: #FF8C00;
            --accent-yellow: #FFD700;
            --dark-bg: #1A1A2E;
            --light-gray: #F8F9FA;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar-brand img {
            height: 45px;
        }

        .hero-banner {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            min-height: 500px;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .btn-primary {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
            border-radius: 25px;
            padding: 10px 30px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: var(--secondary-blue);
            border-color: var(--secondary-blue);
        }

        .btn-warning {
            background-color: var(--accent-orange);
            border-color: var(--accent-orange);
            color: white;
            border-radius: 25px;
            padding: 10px 30px;
            font-weight: 600;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .movie-poster {
            aspect-ratio: 2/3;
            object-fit: cover;
            border-radius: 15px;
        }

        .cinema-image {
            aspect-ratio: 16/9;
            object-fit: cover;
            border-radius: 15px;
        }

        .footer {
            background: linear-gradient(135deg, var(--dark-bg), var(--secondary-blue));
            color: white;
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            margin: 0 10px;
            position: relative;
        }

        .navbar-nav .nav-link:hover {
            color: var(--accent-orange) !important;
        }

        .badge-premium {
            background: linear-gradient(45deg, var(--accent-yellow), var(--accent-orange));
            color: black;
            font-weight: bold;
        }

        .seat {
            width: 35px;
            height: 35px;
            margin: 2px;
            border-radius: 8px;
            border: 2px solid #ddd;
            background: #f8f9fa;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .seat:hover {
            transform: scale(1.1);
        }

        .seat.occupied {
            background: #dc3545;
            color: white;
            cursor: not-allowed;
        }

        .seat.selected {
            background: var(--primary-blue);
            color: white;
            border-color: var(--primary-blue);
        }

        .seat.available {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }

        .loading-spinner {
            display: none;
        }

        .loading .loading-spinner {
            display: inline-block;
        }

        .loading .loading-text {
            display: none;
        }

        .navbar-toggler {
            border: none;
            outline: none;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        .dropdown-item {
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: var(--light-gray);
            color: var(--primary-blue);
        }

        @media (max-width: 768px) {
            .hero-banner {
                min-height: 400px;
            }
            
            .seat {
                width: 30px;
                height: 30px;
                font-size: 10px;
            }

            .navbar-nav .nav-link {
                margin: 5px 0;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset('images/logos/logo.png') }}" alt="Butaca del Salchichon" class="me-2">
                <i class="fas fa-film text-warning fs-2 me-2"></i>
                <span class="fw-bold fs-4">Butaca del Salchichon</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('home') }}">
                            <i class="fas fa-home me-1"></i>Películas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('sedes') }}">
                            <i class="fas fa-map-marker-alt me-1"></i>Sedes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('dulceria.index') }}">
                            <i class="fas fa-candy-cane me-1"></i>Dulcería
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    @auth
                        {{-- Usuario autenticado --}}
                        <li class="nav-item">
                            <a class="nav-link text-white position-relative" href="{{ route('dulceria.carrito') }}">
                                <i class="fas fa-shopping-cart fs-5"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark" id="carrito-count">
                                    {{ session('carrito_dulceria') ? array_sum(array_column(session('carrito_dulceria'), 'cantidad')) : 0 }}
                                </span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle fs-4 me-1"></i>
                                <span>{{ userName() }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('reservas.mis-reservas') }}">
                                    <i class="fas fa-ticket-alt me-2"></i>Mis Reservas
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('dulceria.mis-pedidos') }}">
                                    <i class="fas fa-shopping-bag me-2"></i>Mis Pedidos
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        {{-- Usuario no autenticado --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle fs-4 me-1"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus me-2"></i>Registrarse
                                </a></li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-5 py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-film text-warning fs-2 me-3"></i>
                        <h5 class="text-white mb-0">Butaca del Salchichon</h5>
                    </div>
                    <p class="text-light">La mejor experiencia cinematográfica en Perú. Disfruta de los últimos estrenos en nuestras cómodas salas.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-warning fs-4"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-warning fs-4"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-warning fs-4"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-warning fs-4"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-warning mb-3">Películas</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('peliculas') }}" class="text-light text-decoration-none">En Cartelera</a></li>
                        <li><a href="{{ route('peliculas') }}" class="text-light text-decoration-none">Próximos Estrenos</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Preventa</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-warning mb-3">Cines</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('sedes') }}" class="text-light text-decoration-none">Nuestras Sedes</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Horarios</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Precios</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-warning mb-3">Servicios</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('dulceria.index') }}" class="text-light text-decoration-none">Dulcería</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Salas Premium</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Eventos</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-warning mb-3">Ayuda</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none">Contacto</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Preguntas Frecuentes</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Términos y Condiciones</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 text-light">
            <div class="text-center text-light">
                <p>&copy; {{ date('Y') }} Butaca del Salchichon. Todos los derechos reservados.</p>
                <p class="small">Desarrollado para la mejor experiencia cinematográfica</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
<script>
// Configuración global AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Función para mostrar mensajes de éxito/error
function showAlert(message, type = 'success') {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('body').append(alertHtml);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}

// Función para formatear precios
function formatPrice(price) {
    return 'S/ ' + parseFloat(price).toFixed(2);
}

// Función para actualizar contador del carrito
function updateCartCount(count) {
    $('#carrito-count').text(count);
    
    // Animación del contador
    $('#carrito-count').addClass('animate__animated animate__pulse');
    setTimeout(() => {
        $('#carrito-count').removeClass('animate__animated animate__pulse');
    }, 1000);
}

// Función para confirmar acciones
function confirmAction(message = '¿Estás seguro?') {
    return confirm(message);
}

// Función para mostrar spinner de carga
function showLoadingSpinner(element) {
    const originalText = element.html();
    element.data('original-text', originalText);
    element.html('<i class="fas fa-spinner fa-spin me-2"></i>Cargando...');
    element.prop('disabled', true);
}

function hideLoadingSpinner(element) {
    const originalText = element.data('original-text');
    element.html(originalText);
    element.prop('disabled', false);
}

// Inicialización cuando el DOM esté listo
$(document).ready(function() {
    console.log('App.blade.php JavaScript cargado');
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Tooltip initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Smooth scrolling for anchor links - VERSIÓN CORREGIDA
    $('a[href^="#"]').on('click', function(event) {
        // SOLO aplicar a enlaces que realmente apunten a anclas válidas
        var href = $(this).attr('href');
        if (href && href.startsWith('#') && href.length > 1) {
            var target = $(href);
            if (target.length) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 100
                }, 1000);
            }
        }
    });

    // Handle dropdown hover on desktop
    $('.dropdown').hover(
        function() {
            if ($(window).width() > 768) {
                $(this).addClass('show');
                $(this).find('.dropdown-menu').addClass('show');
            }
        },
        function() {
            if ($(window).width() > 768) {
                $(this).removeClass('show');
                $(this).find('.dropdown-menu').removeClass('show');
            }
        }
    );

    // Update cart count on page load
    @auth
        updateCartCount({{ session('carrito_dulceria') ? array_sum(array_column(session('carrito_dulceria'), 'cantidad')) : 0 }});
    @endauth
    
    // Protección para formularios - evitar interferencias
    $('form').on('submit', function(e) {
        console.log('Formulario enviándose a:', $(this).attr('action'));
        console.log('Método del formulario:', $(this).attr('method'));
        // No hacer preventDefault aquí, dejar que el formulario se envíe naturalmente
    });
});

// Global error handler for AJAX
$(document).ajaxError(function(event, xhr, settings, thrownError) {
    if (xhr.status === 419) {
        showAlert('Tu sesión ha expirado. Por favor, recarga la página.', 'warning');
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    } else if (xhr.status === 500) {
        showAlert('Error interno del servidor. Por favor, intenta de nuevo.', 'danger');
    } else if (xhr.status === 403) {
        showAlert('No tienes permisos para realizar esta acción.', 'warning');
    } else {
        showAlert('Ocurrió un error inesperado. Por favor, intenta de nuevo.', 'danger');
    }
});
</script>
    @stack('scripts')
</body>
</html>