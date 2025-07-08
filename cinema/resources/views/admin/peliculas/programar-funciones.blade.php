{{-- resources/views/admin/peliculas/programar-funciones.blade.php - CORREGIDO --}}
@extends('layouts.admin')

@section('title', 'Programar Funciones')
@section('page-title', 'Programar Funciones')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.peliculas.index') }}">Películas</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.peliculas.show', $pelicula) }}">{{ $pelicula->titulo }}</a></li>
<li class="breadcrumb-item active">Programar Funciones</li>
@endsection

@section('content')
<div class="row">
    <!-- Formulario de Nueva Función Individual -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus me-2"></i>Nueva Función Individual
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.funciones.store') }}" id="funcionForm">
                    @csrf
                    <input type="hidden" name="pelicula_id" value="{{ $pelicula->id }}">
                    
                    <!-- Información de la Película -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Película</label>
                        <div class="d-flex align-items-center">
                            <img src="{{ getPosterUrl($pelicula->poster) }}" 
                                 alt="{{ $pelicula->titulo }}" 
                                 class="rounded me-2" 
                                 style="width: 40px; height: 60px; object-fit: cover;">
                            <div>
                                <strong>{{ $pelicula->titulo }}</strong>
                                <br>
                                <small class="text-muted">{{ $pelicula->getDuracionFormateada() }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Cine -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Cine *</label>
                        <select class="form-select @error('cine_id') is-invalid @enderror" name="cine_id" required id="cineSelect">
                            <option value="">Seleccionar cine</option>
                            @foreach($cines as $cine)
                                <option value="{{ $cine->id }}" {{ old('cine_id') == $cine->id ? 'selected' : '' }}>
                                    {{ $cine->nombre }} - {{ $cine->ciudad->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('cine_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Sala -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sala *</label>
                        <select class="form-select @error('sala_id') is-invalid @enderror" name="sala_id" required id="salaSelect" disabled>
                            <option value="">Seleccionar sala</option>
                        </select>
                        @error('sala_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Fecha -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha *</label>
                        <input type="date" class="form-control @error('fecha_funcion') is-invalid @enderror" 
                               name="fecha_funcion" value="{{ old('fecha_funcion') }}" required
                               min="{{ $pelicula->fecha_estreno->format('Y-m-d') }}">
                        @error('fecha_funcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Hora -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Hora *</label>
                        <input type="time" class="form-control @error('hora_funcion') is-invalid @enderror" 
                               name="hora_funcion" value="{{ old('hora_funcion') }}" required>
                        @error('hora_funcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Formato -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Formato</label>
                        <select class="form-select" name="formato">
                            <option value="2D">2D</option>
                            <option value="3D">3D</option>
                        </select>
                    </div>

                    <!-- Tipo -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de Sala</label>
                        <select class="form-select" name="tipo">
                            <option value="REGULAR">Regular</option>
                            <option value="GOLD CLASS">Gold Class</option>
                            <option value="VELVET">Velvet</option>
                        </select>
                    </div>

                    <!-- Precio -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Precio *</label>
                        <div class="input-group">
                            <span class="input-group-text">S/</span>
                            <input type="number" class="form-control @error('precio') is-invalid @enderror" 
                                   name="precio" value="{{ old('precio', '15.00') }}" step="0.01" min="0" required>
                        </div>
                        @error('precio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Programar Función
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Lista de Funciones Existentes -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar me-2"></i>Funciones de {{ $pelicula->titulo }}
                </h5>
            </div>
            <div class="card-body">
                @if($pelicula->funciones->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Cine</th>
                                    <th>Sala</th>
                                    <th>Precio</th>
                                    <th>Reservas</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pelicula->funciones()->orderBy('fecha_funcion')->orderBy('hora_funcion')->get() as $funcion)
                                <tr>
                                    <td>
                                        <strong>{{ $funcion->fecha_funcion->format('d/m/Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $funcion->fecha_funcion->format('l') }}</small>
                                    </td>
                                    <td>{{ $funcion->hora_funcion }}</td>
                                    <td>{{ $funcion->sala->cine->nombre ?? 'Sin cine' }}</td>
                                    <td>{{ $funcion->sala->nombre ?? 'Sin sala' }}</td>
                                    <td>S/ {{ number_format($funcion->precio, 2) }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $funcion->reservas->count() ?? 0 }}</span>
                                        / {{ $funcion->sala->total_asientos ?? 'N/A' }}
                                    </td>
                                    <td>
                                        @if($funcion->fecha_funcion->isPast())
                                            <span class="badge bg-secondary">Finalizada</span>
                                        @elseif($funcion->fecha_funcion->isToday())
                                            <span class="badge bg-warning">Hoy</span>
                                        @else
                                            <span class="badge bg-success">Programada</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="eliminarFuncion({{ $funcion->id }})"
                                                    title="Eliminar función">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times display-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No hay funciones programadas</h5>
                        <p class="text-muted">Programa la primera función usando el formulario de la izquierda</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Programación Masiva -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-week me-2"></i>Programación Masiva
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Programación masiva:</strong> Programa múltiples funciones de una vez seleccionando un cine, 
                    rango de fechas, días de la semana y horarios.
                </div>
                
                <form method="POST" action="{{ route('admin.funciones.store-multiple') }}" id="funcionesMasivasForm">
                    @csrf
                    <input type="hidden" name="pelicula_id" value="{{ $pelicula->id }}">
                    
                    <div class="row g-3">
                        <!-- Cine para programación masiva -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cine *</label>
                            <select class="form-select" name="cine_id_masivo" required>
                                <option value="">Seleccionar cine</option>
                                @foreach($cines as $cine)
                                    <option value="{{ $cine->id }}">
                                        {{ $cine->nombre }} - {{ $cine->ciudad->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Fechas -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Fecha Inicio *</label>
                            <input type="date" class="form-control" name="fecha_inicio" required
                                   min="{{ $pelicula->fecha_estreno->format('Y-m-d') }}"
                                   value="{{ today()->format('Y-m-d') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Fecha Fin *</label>
                            <input type="date" class="form-control" name="fecha_fin" required
                                   min="{{ $pelicula->fecha_estreno->format('Y-m-d') }}"
                                   value="{{ today()->addDays(6)->format('Y-m-d') }}">
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
                            </div>
                        </div>

                        <!-- Días de la semana -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Días de la Semana *</label>
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="dias[]" value="1" id="lunes">
                                        <label class="form-check-label" for="lunes">Lunes</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="dias[]" value="2" id="martes">
                                        <label class="form-check-label" for="martes">Martes</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="dias[]" value="3" id="miercoles">
                                        <label class="form-check-label" for="miercoles">Miércoles</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="dias[]" value="4" id="jueves">
                                        <label class="form-check-label" for="jueves">Jueves</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="dias[]" value="5" id="viernes">
                                        <label class="form-check-label" for="viernes">Viernes</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="dias[]" value="6" id="sabado">
                                        <label class="form-check-label" for="sabado">Sábado</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="dias[]" value="0" id="domingo">
                                        <label class="form-check-label" for="domingo">Domingo</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botón de envío -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-calendar-plus me-2"></i>Programar Funciones Masivas
                            </button>
                            <button type="button" class="btn btn-outline-secondary ms-2" onclick="seleccionarTodos()">
                                <i class="fas fa-check-double me-2"></i>Seleccionar Todos
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cineSelect = document.getElementById('cineSelect');
    const salaSelect = document.getElementById('salaSelect');
    
    // Cargar salas cuando se selecciona un cine
    cineSelect.addEventListener('change', function() {
        const cineId = this.value;
        salaSelect.innerHTML = '<option value="">Seleccionar sala</option>';
        salaSelect.disabled = true;
        
        if (cineId) {
            fetch(`/admin/cines/${cineId}/salas`)
                .then(response => response.json())
                .then(salas => {
                    salas.forEach(sala => {
                        const option = document.createElement('option');
                        option.value = sala.id;
                        option.textContent = `${sala.nombre} (${sala.capacidad || sala.total_asientos || 'N/A'} asientos)`;
                        salaSelect.appendChild(option);
                    });
                    salaSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error al cargar salas:', error);
                    alert('Error al cargar las salas del cine');
                });
        }
    });
    
    // Función para eliminar función
    window.eliminarFuncion = function(funcionId) {
        if (confirm('¿Estás seguro de que quieres eliminar esta función?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/funciones/${funcionId}`;
            
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(token);
            
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);
            
            document.body.appendChild(form);
            form.submit();
        }
    };
    
    // Función para seleccionar todos los horarios y días
    window.seleccionarTodos = function() {
        const horarios = document.querySelectorAll('input[name="horarios[]"]');
        const dias = document.querySelectorAll('input[name="dias[]"]');
        
        const todosMarcados = Array.from(horarios).every(cb => cb.checked) && 
                             Array.from(dias).every(cb => cb.checked);
        
        horarios.forEach(cb => cb.checked = !todosMarcados);
        dias.forEach(cb => cb.checked = !todosMarcados);
    };
    
    // Validación del formulario masivo
    document.getElementById('funcionesMasivasForm').addEventListener('submit', function(e) {
        const horariosSeleccionados = document.querySelectorAll('input[name="horarios[]"]:checked');
        const diasSeleccionados = document.querySelectorAll('input[name="dias[]"]:checked');
        
        if (horariosSeleccionados.length === 0) {
            e.preventDefault();
            alert('Debes seleccionar al menos un horario');
            return false;
        }
        
        if (diasSeleccionados.length === 0) {
            e.preventDefault();
            alert('Debes seleccionar al menos un día de la semana');
            return false;
        }
        
        const cineSeleccionado = document.querySelector('select[name="cine_id_masivo"]').value;
        if (!cineSeleccionado) {
            e.preventDefault();
            alert('Debes seleccionar un cine');
            return false;
        }
        
        // Confirmar la acción
        const totalCombinaciones = horariosSeleccionados.length * diasSeleccionados.length;
        if (confirm(`¿Estás seguro? Se crearán aproximadamente ${totalCombinaciones} funciones por semana.`)) {
            // Deshabilitar el botón para evitar doble envío
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Programando...';
            submitBtn.disabled = true;
            return true;
        } else {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush