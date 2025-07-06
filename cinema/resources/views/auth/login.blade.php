{{-- resources/views/auth/login.blade.php - MEJORADO --}}
@extends('layouts.app')

@section('title', 'Iniciar Sesión - Butaca del Salchichón')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-circle me-2"></i>Iniciar Sesión
                    </h4>
                </div>
                <div class="card-body p-4">
                    
                    {{-- Mostrar mensaje si viene de intento de compra --}}
                    @if(session('info'))
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ session('info') }}
                        </div>
                    @endif

                    {{-- Mostrar mensaje de éxito --}}
                    @if(session('success'))
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Mensaje especial si viene de compra de entradas --}}
                    @if(session('intended_url') && str_contains(session('intended_url'), 'reserva'))
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-ticket-alt me-2"></i>
                            <strong>¡Casi listo!</strong><br>
                            Inicia sesión para continuar con tu compra de entradas
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- Preservar URL de redirección --}}
                        @if(session('intended_url'))
                            <input type="hidden" name="redirect" value="{{ session('intended_url') }}">
                        @elseif(request('redirect'))
                            <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                        @endif

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>Correo Electrónico
                            </label>
                            <input 
                                type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autocomplete="email" 
                                autofocus
                                placeholder="ejemplo@correo.com"
                            >
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i>Contraseña
                            </label>
                            <div class="input-group">
                                <input 
                                    type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    id="password" 
                                    name="password" 
                                    required 
                                    autocomplete="current-password"
                                    placeholder="Tu contraseña"
                                >
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Recordarme
                            </label>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                            </button>
                        </div>
                    </form>

                    <div class="text-center">
                        <p class="mb-0">¿No tienes una cuenta?</p>
                        <a href="{{ route('register') }}{{ request('redirect') ? '?redirect=' . urlencode(request('redirect')) : '' }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-user-plus me-2"></i>Crear Cuenta
                        </a>
                    </div>
                </div>
            </div>

            {{-- Credenciales de prueba --}}
            @if(app()->environment(['local', 'testing']))
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-tools me-1"></i>Credenciales de Prueba
                        </h6>
                        <small class="text-muted">
                            <strong>Usuario:</strong> cliente@test.com | <strong>Contraseña:</strong> cliente123<br>
                            <strong>Admin:</strong> admin@salchichon.com | <strong>Contraseña:</strong> admin123
                        </small>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });
});
</script>
@endsection