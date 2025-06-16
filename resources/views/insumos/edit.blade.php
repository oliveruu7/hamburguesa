@extends('layouts.admin')
@section('title', 'Editar insumo')

@section('content')
<div class="container py-4">

  {{-- Alertas --}}
  @foreach (['success', 'error', 'info'] as $type)
    @if(session($type))
      <div class="alert alert-{{ $type == 'success' ? 'success' : ($type == 'info' ? 'warning' : 'danger') }} alert-dismissible fade show">
        <i class="bi {{ $type == 'success' ? 'bi-check-circle-fill' : ($type == 'info' ? 'bi-info-circle-fill' : 'bi-exclamation-triangle-fill') }}"></i>
        {{ session($type) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
  @endforeach

  {{-- Formulario --}}
  <div class="card shadow border-0">
    <div class="card-header text-white" style="background:#00bcd4;">
      <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Editar insumo</h5>
    </div>

    <div class="card-body">
      <form id="formEditarInsumo" method="POST" action="{{ route('insumos.update', $insumo) }}" novalidate>
        @csrf @method('PUT')
        <div class="row g-3">

          {{-- Nombre --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-tag-fill"></i> Nombre <span class="text-danger">*</span></label>
            <input name="nombre" value="{{ old('nombre', $insumo->nombre) }}" class="form-control" maxlength="50" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ 0-9]+" autocomplete="off">
            <div class="invalid-feedback">Campo requerido. Máx. 50 caracteres alfanuméricos.</div>
          </div>

          {{-- Unidad --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-boxes"></i> Unidad de medida <span class="text-danger">*</span></label>
            <input name="unidad" value="{{ old('unidad', $insumo->unidad) }}" class="form-control" maxlength="20" required>
            <div class="invalid-feedback">Campo requerido. Máx. 20 caracteres.</div>
          </div>

          {{-- Descripción --}}
          <div class="col-md-12">
            <label class="form-label fw-semibold"><i class="bi bi-card-text"></i> Descripción (opcional)</label>
            <textarea name="descripcion" class="form-control" rows="3" maxlength="255">{{ old('descripcion', $insumo->descripcion) }}</textarea>
            <div class="invalid-feedback">Máx. 255 caracteres.</div>
          </div>
        </div>

        {{-- Botones --}}
        <div class="mt-4 d-flex justify-content-between">
          <a href="{{ route('insumos.index') }}" class="btn btn-secondary px-4">
            <i class="bi bi-x-circle-fill me-1"></i> Cancelar
          </a>
          <button id="btnActualizar" class="btn btn-primary px-4 opacity-50" disabled>
            <i class="bi bi-save-fill me-1"></i> Actualizar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('js')
<script>
const form = document.getElementById('formEditarInsumo');
const btn  = document.getElementById('btnActualizar');

const original = {
  nombre: '{{ addslashes($insumo->nombre) }}',
  unidad: '{{ addslashes($insumo->unidad) }}',
  descripcion: `{{ addslashes($insumo->descripcion ?? '') }}`
};

// Validación en tiempo real
form.addEventListener('input', () => {
  const nombre = form.nombre.value.trim();
  const unidad = form.unidad.value.trim();
  const descripcion = form.descripcion.value.trim();

  const cambiado = nombre !== original.nombre ||
                   unidad !== original.unidad ||
                   descripcion !== original.descripcion;

  const valido = form.checkValidity();
  btn.disabled = !valido || !cambiado;
  btn.classList.toggle('opacity-50', btn.disabled);
});
</script>
@endpush
