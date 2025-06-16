 @extends('layouts.admin')
@section('title','Nuevo cliente')

@section('content')
<div class="container py-4">

  {{-- === MENSAJES DE ERROR SUPERIORES === --}}
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

  <div class="card shadow border-0">
    <div class="card-header text-white" style="background-color:#008080;">
      <h5 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i> Registrar nuevo cliente</h5>
    </div>

    <div class="card-body">
      <form method="POST" action="{{ route('clientes.store') }}" id="clienteForm" novalidate>
        @csrf

        {{-- NOMBRE --}}
        <div class="mb-3">
          <label class="form-label fw-semibold">
            <i class="bi bi-person-fill"></i> Nombre <span class="text-danger">*</span>
          </label>
          <input type="text" name="nombre" id="nombre"
                 class="form-control @error('nombre') is-invalid @enderror"
                 value="{{ old('nombre') }}"
                 maxlength="25" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]{3,25}" autocomplete="off">
          <div class="valid-feedback">Correcto</div>
          <div class="invalid-feedback">Por favor, ingrese un nombre válido (mín. 3 letras, solo letras y espacios).</div>
          @error('nombre')
            <div class="text-danger small mt-1">{{ $message }}</div>
          @enderror
        </div>

        {{-- CI --}}
        <div class="mb-3">
          <label class="form-label fw-semibold">
            <i class="bi bi-card-text"></i> Cédula de Identidad (CI) <span class="text-danger">*</span>
          </label>
          <input type="text" name="ci" id="ci"
                 class="form-control @error('ci') is-invalid @enderror"
                 value="{{ old('ci') }}"
                 maxlength="8" minlength="7"
                 pattern="\d{7,8}" required inputmode="numeric"
                 oninput="this.value=this.value.replace(/\D/g,'')">
          <div class="valid-feedback">Correcto</div>
          <div class="invalid-feedback">Solo números. 7 u 8 dígitos.</div>
         
        </div>

        {{-- TELÉFONO --}}
        <div class="mb-3">
          <label class="form-label fw-semibold">
            <i class="bi bi-telephone-fill"></i> Teléfono
          </label>
          <input type="text" name="telefono" id="telefono"
                 class="form-control @error('telefono') is-invalid @enderror"
                 value="{{ old('telefono') }}"
                 maxlength="8" minlength="7"
                 pattern="\d{7,8}" inputmode="numeric"
                 oninput="this.value=this.value.replace(/\D/g,'')">
          <div class="valid-feedback">Correcto</div>
          <div class="invalid-feedback">Solo números. 7 u 8 dígitos.</div>
          @error('telefono')
            <div class="text-danger small mt-1">{{ $message }}</div>
          @enderror
        </div>

        {{-- BOTONES --}}
        <div class="d-flex justify-content-end">
          <a href="{{ route('clientes.index') }}" class="btn btn-secondary me-2">
            <i class="bi bi-x-circle-fill me-1"></i> Cancelar
          </a>
          <button type="submit" class="btn btn-success px-4 opacity-50" id="btnGuardar" disabled>
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
const form = document.getElementById('clienteForm');
const btn  = document.getElementById('btnGuardar');
const opcionales = ['telefono'];
const textoLimpio = ['nombre'];

// Validación dinámica y limpieza
form.addEventListener('input', e => {
  const el = e.target;
  const name = el.name;

  // Limpiar espacios dobles y caracteres no permitidos en 'nombre'
  if (textoLimpio.includes(name)) {
    el.value = el.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ ]+/g, '')
                       .replace(/\s{2,}/g, ' ')
                       .trimStart();
  }

  el.classList.remove('is-valid', 'is-invalid');

  if (el.value.trim() !== '' || !opcionales.includes(name)) {
    el.classList.add(el.checkValidity() ? 'is-valid' : 'is-invalid');
  }

  validarFormulario();
});

function validarFormulario() {
  const esValido = form.checkValidity();
  btn.disabled = !esValido;
  btn.classList.toggle('opacity-50', !esValido);
}
</script>
@endpush
