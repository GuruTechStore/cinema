{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin') - Butaca del Salchicon</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --admin-primary: #1e40af;
            --admin-secondary: #3b82f6;
            --admin-success: #10b981;
            --admin-warning: #f59e0b;
            --admin-danger: #ef4444;
            --admin-dark: #1f2937;
            --admin-light: #f8fafc;
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
        }

        /* Sidebar */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--admin-primary), var(--admin-dark));
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        .admin-sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand h4 {
            color: white;
            margin: 0;
            font-weight: 700;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            margin: 0.25rem 1rem;
        }

        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }

        .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
        }

        /* Main Content */
        .admin-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .admin-main.expanded {
            margin-left: 0;
        }

        /* Top Navigation */
        .admin-topnav {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        /* Content Area */
        .admin-content {
            padding: 2rem;
        }

        /* Cards */
        .stats-card {
            background: white;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            position: relative;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--admin-primary);
        }

        .stats-card.success::before {
            background: var(--admin-success);
        }

        .stats-card.warning::before {
            background: var(--admin-warning);
        }

        .stats-card.info::before {
            background: var(--admin-secondary);
        }

        .stats-card.danger::before {
            background: var(--admin-danger);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        /* Buttons */
        .btn-admin {
            border-radius: 0.5rem;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--admin-dark);
            margin: 0;
        }

        /* Breadcrumb */
        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item a {
            color: var(--admin-secondary);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #6b7280;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }

            .admin-main {
                margin-left: 0;
            }

            .admin-content {
                padding: 1rem;
            }

            .admin-topnav {
                padding: 1rem;
            }
        }

        /* Utilities */
        .text-primary-admin { color: var(--admin-primary) !important; }
        .text-success-admin { color: var(--admin-success) !important; }
        .text-warning-admin { color: var(--admin-warning) !important; }
        .text-danger-admin { color: var(--admin-danger) !important; }

        .bg-primary-admin { background-color: var(--admin-primary) !important; }
        .bg-success-admin { background-color: var(--admin-success) !important; }
        .bg-warning-admin { background-color: var(--admin-warning) !important; }
        .bg-danger-admin { background-color: var(--admin-danger) !important; }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="admin-sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h4>
                <i class="fas fa-cog me-2"></i>
                Admin Panel
            </h4>
        </div>
        
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('admin.peliculas.index') }}" class="nav-link {{ request()->routeIs('admin.peliculas.*') ? 'active' : '' }}">
                    <i class="fas fa-film"></i>
                    Películas
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('admin.dulceria.index') }}" class="nav-link {{ request()->routeIs('admin.dulceria.*') && !request()->routeIs('admin.dulceria.pedidos') ? 'active' : '' }}">
                    <i class="fas fa-candy-cane"></i>
                    Productos Dulcería
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('admin.dulceria.pedidos') }}" class="nav-link {{ request()->routeIs('admin.dulceria.pedidos') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag"></i>
                    Pedidos Dulcería
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('admin.reservas') }}" class="nav-link {{ request()->routeIs('admin.reservas') ? 'active' : '' }}">
                    <i class="fas fa-ticket-alt"></i>
                    Reservas
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('admin.ventas') }}" class="nav-link {{ request()->routeIs('admin.ventas') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    Reportes
                </a>
            </li>
            
            <hr style="border-color: rgba(255,255,255,0.1); margin: 1rem;">
            
            <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link">
                    <i class="fas fa-eye"></i>
                    Ver Sitio Web
                </a>
            </li>
            
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                    @csrf
                    <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                        <i class="fas fa-sign-out-alt"></i>
                        Cerrar Sesión
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="admin-main" id="main-content">
        <!-- Top Navigation -->
        <div class="admin-topnav d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary me-3 d-md-none" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div>
                    <h1 class="page-title">@yield('page-title', 'Panel Admin')</h1>
                    @hasSection('breadcrumb')
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                @yield('breadcrumb')
                            </ol>
                        </nav>
                    @endif
                </div>
            </div>
            
            <div class="d-flex align-items-center">
                <span class="me-3">
                    <i class="fas fa-user-circle me-1"></i>
                    {{ auth()->user()->name }}
                </span>
                <span class="badge bg-primary">Admin</span>
            </div>
        </div>

        <!-- Content -->
        <div class="admin-content">
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

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script>
        // Sidebar Toggle
        document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            sidebar.classList.toggle('show');
        });

        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // CSRF Token Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    @stack('scripts')
</body>
</html>