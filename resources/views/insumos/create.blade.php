 @extends('layouts.admin')
@section('title','Crear Insumo')

@section('content')
<div class="container py-4">

  {{-- === ALERTAS DE SESIÓN === --}}
  @foreach (['success', 'error'] as $type)
    @if(session($type))
      <div class="alert alert-{{ $type === 'success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
        <i class="bi {{ $type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }}"></i>
        {{ session($type) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
  @endforeach

  {{-- === ERRORES DE VALIDACIÓN === --}}
  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Corrige los siguientes errores:</strong>
      <ul class="mb-0 mt-2">
        @foreach ($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- === FORMULARIO === --}}
  <div class="card shadow border-0">
    <div class="card-header text-white" style="background:#2e8b57;">
      <h5 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i> Crear nuevo insumo</h5>
    </div>

    <div class="card-body">
      <form id="insumoForm" method="POST" action="{{ route('insumos.store') }}" novalidate>
        @csrf
        <div class="row g-3">

          {{-- NOMBRE --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-tag-fill"></i> Nombre <span class="text-danger">*</span></label>
            <input name="nombre" id="nombre" value="{{ old('nombre') }}" class="form-control"
                   maxlength="50" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]{3,50}" required autocomplete="off">
            <div class="valid-feedback">Correcto</div>
            <div class="invalid-feedback">Campo obligatorio.</div>
          </div>

          {{-- UNIDAD --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-bounding-box"></i> Unidad de medida <span class="text-danger">*</span></label>
            <input name="unidad" id="unidad" value="{{ old('unidad') }}" class="form-control"
                   maxlength="20" required autocomplete="off">
            <div class="valid-feedback">Correcto</div>
            <div class="invalid-feedback">Campo obligatorio  </div>
          </div>

          {{-- DESCRIPCIÓN --}}
          <div class="col-12">
            <label class="form-label fw-semibold"><i class="bi bi-card-text"></i> Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="3" maxlength="255">{{ old('descripcion') }}</textarea>
          </div>
        </div>

        {{-- BOTONES --}}
        <div class="mt-4 d-flex justify-content-between">
          <a href="{{ route('insumos.index') }}" class="btn btn-secondary px-4">
            <i class="bi bi-x-circle-fill me-1"></i> Cancelar
          </a>
          <button id="btnGuardar" class="btn btn-primary px-4 opacity-50" disabled>
            <i class="bi bi-save-fill me-1"></i> Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('js')
<script>
const form = document.getElementById('insumoForm');
const btn  = document.getElementById('btnGuardar');
const opcional     = ['descripcion'];
const textoLimpio  = ['nombre', 'unidad'];

validarTodo();

form.addEventListener('input', e => {
  const el = e.target;
  const name = el.name;

  el.classList.remove('is-valid', 'is-invalid');

  if (opcional.includes(name) && el.value.trim() === '') return;

  if (textoLimpio.includes(name)) {
    el.value = el.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ ]+/g, '').replace(/\s{2,}/g, ' ').trimStart();
  }

  if (el.value.trim() !== '' || !opcional.includes(name)) {
    el.classList.add(el.checkValidity() ? 'is-valid' : 'is-invalid');
  }

  validarTodo();
});

function validarTodo() {
  const ok = form.checkValidity();
  btn.disabled = !ok;
  btn.classList.toggle('opacity-50', !ok);
}
</script>
@endpush
