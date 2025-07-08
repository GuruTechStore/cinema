{{-- resources/views/admin/peliculas/programar-funciones.blade.php --}}
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
    <!-- Formulario de Nueva Función -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus me-2"></i>Nueva Función
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
                            <img src="{{ $pelicula->poster ? asset('storage/' . $pelicula->poster) : asset('images/posters/default.jpg') }}" 
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
                                    {{ $cine->nombre }} - {{ $cine->ubicacion }}
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
                               name="fecha_funcion" value="{{ old('fecha_funcion') }}" required min="{{ date('Y-m-d') }}">
                        @error('fecha_funcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Hora -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Hora de Inicio *</label>
                        <input type="time" class="form-control @error('hora_funcion') is-invalid @enderror" 
                               name="hora_funcion" value="{{ old('hora_funcion') }}" required>
                        @error('hora_funcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                        <a href="{{ route('admin.peliculas.show', $pelicula) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Lista de Funciones Existentes -->
    <div class="col-lg-8">
        <div class="card">
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
                                @foreach($pelicula->funciones()->orderBy('fecha')->orderBy('hora_inicio')->get() as $funcion)
                                <tr>
                                    <td>
                                        <strong>{{ $funcion->fecha->format('d/m/Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $funcion->fecha->format('l') }}</small>
                                    </td>
                                    <td>{{ $funcion->hora_inicio->format('H:i') }}</td>
                                    <td>{{ $funcion->sala->cine->nombre }}</td>
                                    <td>{{ $funcion->sala->nombre }}</td>
                                    <td>S/ {{ number_format($funcion->precio, 2) }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $funcion->reservas->count() }}</span>
                                        / {{ $funcion->sala->capacidad }}
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
                                            @if(!$funcion->fecha_funcion->isPast())
                                                <button type="button" class="btn btn-outline-warning" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editarFuncionModal{{ $funcion->id }}"
                                                        title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-outline-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#eliminarFuncionModal{{ $funcion->id }}"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Editar Función -->
                                <div class="modal fade" id="editarFuncionModal{{ $funcion->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar Función</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('admin.funciones.update', $funcion) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="row g-3">
                                                        <div class="col-6">
                                                            <label class="form-label">Fecha</label>
                                                            <input type="date" class="form-control" name="fecha_funcion" 
                                                                   value="{{ $funcion->fecha_funcion->format('Y-m-d') }}" required>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label">Hora</label>
                                                            <input type="time" class="form-control" name="hora_funcion" 
                                                                   value="{{ $funcion->hora_funcion->format('H:i') }}" required>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label">Precio</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text">S/</span>
                                                                <input type="number" class="form-control" name="precio" 
                                                                       value="{{ $funcion->precio }}" step="0.01" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Actualizar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Eliminar Función -->
                                <div class="modal fade" id="eliminarFuncionModal{{ $funcion->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmar Eliminación</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>¿Eliminar la función del <strong>{{ $funcion->fecha_funcion->format('d/m/Y') }}</strong> a las <strong>{{ $funcion->hora_funcion->format('H:i') }}</strong>?</p>
                                                @if($funcion->reservas->count() > 0)
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        Esta función tiene {{ $funcion->reservas->count() }} reserva(s). Se notificará a los usuarios.
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <form method="POST" action="{{ route('admin.funciones.destroy', $funcion) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times display-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No hay funciones programadas</h5>
                        <p class="text-muted">Programa la primera función de esta película usando el formulario de la izquierda</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Programación Masiva -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-week me-2"></i>Programación Masiva
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.funciones.store-multiple') }}">
                    @csrf
                    <input type="hidden" name="pelicula_id" value="{{ $pelicula->id }}">
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Fecha Inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Fecha Fin</label>
                            <input type="date" class="form-control" name="fecha_fin" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cine</label>
                            <select class="form-select" name="cine_id_masivo" required>
                                <option value="">Seleccionar cine</option>
                                @foreach($cines as $cine)
                                    <option value="{{ $cine->id }}">{{ $cine->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Horarios</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="horarios[]" value="14:00">
                                <label class="form-check-label">14:00</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="horarios[]" value="16:30">
                                <label class="form-check-label">16:30</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="horarios[]" value="19:00">
                                <label class="form-check-label">19:00</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="horarios[]" value="21:30">
                                <label class="form-check-label">21:30</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Días de la Semana</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="dias[]" value="1">
                                <label class="form-check-label">Lunes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="dias[]" value="2">
                                <label class="form-check-label">Martes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="dias[]" value="3">
                                <label class="form-check-label">Miércoles</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="dias[]" value="4">
                                <label class="form-check-label">Jueves</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="dias[]" value="5">
                                <label class="form-check-label">Viernes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="dias[]" value="6">
                                <label class="form-check-label">Sábado</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="dias[]" value="0">
                                <label class="form-check-label">Domingo</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-calendar-plus me-2"></i>Programar Funciones Masivas
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
                        option.textContent = `${sala.nombre} (${sala.capacidad} asientos)`;
                        salaSelect.appendChild(option);
                    });
                    salaSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error al cargar salas:', error);
                });
        }
    });
});
</script>
@endpush