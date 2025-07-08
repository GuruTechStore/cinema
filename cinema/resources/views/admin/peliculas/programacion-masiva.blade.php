{{-- resources/views/admin/peliculas/programacion-masiva.blade.php --}}
@extends('layouts.admin')

@section('title', 'Programación Masiva')
@section('page-title', 'Programación Masiva por Ciudad')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.peliculas.index') }}">Películas</a></li>
<li class="breadcrumb-item active">Programación Masiva</li>
@endsection

@section('content')
<div class="row">
    <!-- Formulario Principal -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-rocket me-2"></i>Programar Película en Toda una Ciudad
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Programación Masiva:</strong> Esta herramienta te permite programar una película 
                    en todos los cines de una ciudad específica de una sola vez, ahorrando tiempo y esfuerzo.
                </div>

                <form method="POST" id="programacionMasivaForm">
                    @csrf
                    
                    <div class="row g-3">
                        <!-- Seleccionar Película -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Película *</label>
                            <select class="form-select @error('pelicula_id') is-invalid @enderror" 
                                    name="pelicula_id" required id="peliculaSelect">
                                <option value="">Seleccionar película</option>
                                @foreach($peliculas as $pelicula)
                                    <option value="{{ $pelicula->id }}" {{ old('pelicula_id') == $pelicula->id ? 'selected' : '' }}>
                                        {{ $pelicula->titulo }} ({{ $pelicula->fecha_estreno->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('pelicula_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Seleccionar Ciudad -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ciudad *</label>
                            <select class="form-select @error('ciudad_id') is-invalid @enderror" 
                                    name="ciudad_id" required id="ciudadSelect">
                                <option value="">Seleccionar ciudad</option>
                                @foreach($ciudades as $ciudad)
                                    <option value="{{ $ciudad->id }}" {{ old('ciudad_id') == $ciudad->id ? 'selected' : '' }}>
                                        {{ $ciudad->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ciudad_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Información de la ciudad seleccionada -->
                        <div class="col-12" id="ciudadInfo" style="display: none;">
                            <div class="alert alert-light border">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <h6 class="fw-bold mb-0" id="totalCines">-</h6>
                                        <small class="text-muted">Cines</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="fw-bold mb-0" id="totalSalas">-</h6>
                                        <small class="text-muted">Salas</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="fw-bold mb-0" id="estimadoFunciones">-</h6>
                                        <small class="text-muted">Funciones estimadas</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="fw-bold mb-0" id="capacidadTotal">-</h6>
                                        <small class="text-muted">Capacidad total</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fechas -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha de Inicio *</label>
                            <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                   name="fecha_inicio" value="{{ old('fecha_inicio') }}" required
                                   min="{{ date('Y-m-d') }}" id="fechaInicio">
                            @error('fecha_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha de Fin *</label>
                            <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" 
                                   name="fecha_fin" value="{{ old('fecha_fin') }}" required
                                   min="{{ date('Y-m-d') }}" id="fechaFin">
                            @error('fecha_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Horarios -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Horarios *</label>
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="horarios[]" value="11:00" id="h1100">
                                        <label class="form-check-label" for="h1100">11:00 AM</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="horarios[]" value="14:00" id="h1400">
                                        <label class="form-check-label" for="h1400">2:00 PM</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="horarios[]" value="17:00" id="h1700">
                                        <label class="form-check-label" for="h1700">5:00 PM</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="horarios[]" value="20:00" id="h2000">
                                        <label class="form-check-label" for="h2000">8:00 PM</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="horarios[]" value="15:30" id="h1530">
                                        <label class="form-check-label" for="h1530">3:30 PM</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="horarios[]" value="18:30" id="h1830">
                                        <label class="form-check-label" for="h1830">6:30 PM</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="horarios[]" value="21:30" id="h2130">
                                        <label class="form-check-label" for="h2130">9:30 PM</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="horarios[]" value="22:30" id="h2230">
                                        <label class="form-check-label" for="h2230">10:30 PM</label>
                                    </div>
                                </div>
                            </div>
                            @error('horarios')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Configuración -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Formato *</label>
                            <select class="form-select @error('formato') is-invalid @enderror" name="formato" required>
                                <option value="">Seleccionar</option>
                                <option value="2D" {{ old('formato') == '2D' ? 'selected' : '' }}>2D</option>
                                <option value="3D" {{ old('formato') == '3D' ? 'selected' : '' }}>3D</option>
                            </select>
                            @error('formato')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tipo de Sala *</label>
                            <select class="form-select @error('tipo') is-invalid @enderror" name="tipo" required>
                                <option value="">Seleccionar</option>
                                <option value="REGULAR" {{ old('tipo') == 'REGULAR' ? 'selected' : '' }}>Regular</option>
                                <option value="GOLD CLASS" {{ old('tipo') == 'GOLD CLASS' ? 'selected' : '' }}>Gold Class</option>
                                <option value="VELVET" {{ old('tipo') == 'VELVET' ? 'selected' : '' }}>Velvet</option>
                            </select>
                            @error('tipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Precio *</label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" class="form-control @error('precio') is-invalid @enderror" 
                                       name="precio" value="{{ old('precio', '15.00') }}" 
                                       step="0.01" min="0" max="200" required>
                            </div>
                            @error('precio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="col-12">
                            <hr>
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-success btn-lg" id="btnProgramar">
                                    <i class="fas fa-rocket me-2"></i>Programar Masivamente
                                </button>
                                <a href="{{ route('admin.peliculas.index') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Volver a Películas
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Panel de Ayuda -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-question-circle me-2"></i>¿Cómo funciona?
                </h6>
            </div>
            <div class="card-body">
                <div class="step mb-3">
                    <div class="d-flex">
                        <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px;">
                            1
                        </div>
                        <div>
                            <h6 class="mb-1">Selecciona la película</h6>
                            <small class="text-muted">Elige la película que quieres programar</small>
                        </div>
                    </div>
                </div>

                <div class="step mb-3">
                    <div class="d-flex">
                        <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px;">
                            2
                        </div>
                        <div>
                            <h6 class="mb-1">Elige la ciudad</h6>
                            <small class="text-muted">Se programará en TODOS los cines de la ciudad</small>
                        </div>
                    </div>
                </div>

                <div class="step mb-3">
                    <div class="d-flex">
                        <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px;">
                            3
                        </div>
                        <div>
                            <h6 class="mb-1">Configura fechas y horarios</h6>
                            <small class="text-muted">Define el período y los horarios</small>
                        </div>
                    </div>
                </div>

                <div class="step mb-3">
                    <div class="d-flex">
                        <div class="step-number bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px;">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">¡Listo!</h6>
                            <small class="text-muted">Se crearán automáticamente todas las funciones</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consejos -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Consejos
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Usa horarios populares como 2PM, 5PM y 8PM
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Programa al menos una semana en adelante
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Ajusta precios según el tipo de sala
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check text-success me-2"></i>
                        Verifica que la fecha de inicio sea posterior al estreno
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.step-number {
    font-size: 14px;
    font-weight: bold;
    flex-shrink: 0;
}

.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

#ciudadInfo {
    transition: all 0.3s ease;
}

.alert-light {
    background-color: #f8f9fa;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const peliculaSelect = document.getElementById('peliculaSelect');
    const ciudadSelect = document.getElementById('ciudadSelect');
    const ciudadInfo = document.getElementById('ciudadInfo');
    const fechaInicio = document.getElementById('fechaInicio');
    const fechaFin = document.getElementById('fechaFin');
    const form = document.getElementById('programacionMasivaForm');

    // Información de ciudades (esto vendría del backend en una implementación real)
    const ciudadesData = {
        @foreach($ciudades as $ciudad)
        {{ $ciudad->id }}: {
            nombre: "{{ $ciudad->nombre }}",
            cines: {{ $ciudad->cines()->count() }},
            salas: {{ $ciudad->cines()->withCount('salas')->get()->sum('salas_count') }},
            capacidad: {{ $ciudad->cines()->join('salas', 'cines.id', '=', 'salas.cine_id')->sum('salas.capacidad') }}
        },
        @endforeach
    };

    // Actualizar información cuando se selecciona una ciudad
    ciudadSelect.addEventListener('change', function() {
        const ciudadId = this.value;
        
        if (ciudadId && ciudadesData[ciudadId]) {
            const data = ciudadesData[ciudadId];
            
            document.getElementById('totalCines').textContent = data.cines;
            document.getElementById('totalSalas').textContent = data.salas;
            document.getElementById('capacidadTotal').textContent = data.capacidad.toLocaleString();
            
            // Calcular funciones estimadas
            const horariosSeleccionados = document.querySelectorAll('input[name="horarios[]"]:checked').length;
            const estimado = horariosSeleccionados * data.salas;
            document.getElementById('estimadoFunciones').textContent = estimado > 0 ? estimado : '-';
            
            ciudadInfo.style.display = 'block';
        } else {
            ciudadInfo.style.display = 'none';
        }
    });

    // Actualizar estimado cuando cambian los horarios
    document.querySelectorAll('input[name="horarios[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const ciudadId = ciudadSelect.value;
            if (ciudadId && ciudadesData[ciudadId]) {
                const horariosSeleccionados = document.querySelectorAll('input[name="horarios[]"]:checked').length;
                const estimado = horariosSeleccionados * ciudadesData[ciudadId].salas;
                document.getElementById('estimadoFunciones').textContent = estimado;
            }
        });
    });

    // Actualizar fecha mínima de fin cuando cambia fecha de inicio
    fechaInicio.addEventListener('change', function() {
        fechaFin.min = this.value;
        if (fechaFin.value && fechaFin.value < this.value) {
            fechaFin.value = this.value;
        }
    });

    // Actualizar fecha mínima cuando se selecciona película
    peliculaSelect.addEventListener('change', function() {
        // Aquí podrías hacer una llamada AJAX para obtener la fecha de estreno
        // y establecerla como mínimo para fechaInicio
    });

    // Validación del formulario
    form.addEventListener('submit', function(e) {
        const horariosSeleccionados = document.querySelectorAll('input[name="horarios[]"]:checked').length;
        
        if (horariosSeleccionados === 0) {
            e.preventDefault();
            alert('Debes seleccionar al menos un horario');
            return false;
        }

        if (confirm('¿Estás seguro de que quieres programar masivamente esta película? Esta acción creará múltiples funciones en todos los cines de la ciudad seleccionada.')) {
            document.getElementById('btnProgramar').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Programando...';
            document.getElementById('btnProgramar').disabled = true;
            return true;
        } else {
            e.preventDefault();
            return false;
        }
    });

    // Seleccionar/deseleccionar todos los horarios
    const selectAllBtn = document.createElement('button');
    selectAllBtn.type = 'button';
    selectAllBtn.className = 'btn btn-sm btn-outline-primary mt-2';
    selectAllBtn.innerHTML = '<i class="fas fa-check-square me-1"></i>Seleccionar todos';
    
    selectAllBtn.addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input[name="horarios[]"]');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(cb => cb.checked = !allChecked);
        
        this.innerHTML = allChecked 
            ? '<i class="fas fa-check-square me-1"></i>Seleccionar todos'
            : '<i class="fas fa-square me-1"></i>Deseleccionar todos';
            
        // Actualizar estimado
        ciudadSelect.dispatchEvent(new Event('change'));
    });

    // Agregar botón después de los horarios
    document.querySelector('label[for="h1100"]').closest('.col-12').appendChild(selectAllBtn);
});
</script>
@endpush