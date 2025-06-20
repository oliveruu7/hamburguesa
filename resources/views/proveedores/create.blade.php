 {{-- resources/views/proveedores/create.blade.php --}}
@extends('layouts.admin')
@section('title','Nuevo proveedor')

@section('content')
<div class="container py-4">

    {{-- ───── Alertas flash (éxito / error) ───── --}}
    @foreach (['success','error','info'] as $t)
        @if(session($t))
            <div class="alert alert-{{ $t=='success' ? 'success' : ($t=='error' ? 'danger' : 'info') }} alert-dismissible fade show" role="alert">
                <i class="bi {{ $t=='success' ? 'bi-check-circle-fill' : ($t=='error' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill') }} me-1"></i>
                {{ session($t) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    {{-- ───── Alertas de validación globales ───── --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Corrige los siguientes campos:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow border-0">
        <div class="card-header text-white" style="background-color:#008080;">
            <h5 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i> Registrar proveedor</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('proveedores.store') }}" novalidate>
                @csrf

                {{-- Nombre --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input name="nombre" id="nombre"
       value="{{ old('nombre') }}"
       maxlength="20"
       class="form-control"
       required>
<div class="invalid-feedback" id="nombre-feedback">Solo letras, máx. 20 caracteres.</div>

                {{-- Teléfono --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input name="telefono" id="telefono"
                           value="{{ old('telefono') }}"
                           maxlength="8"
                           class="form-control @error('telefono') is-invalid @enderror">
                    @error('telefono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="invalid-feedback">Debe tener 7 u 8 dígitos numéricos.</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input name="email" type="email"
                           value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Botones --}}
                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('proveedores.index') }}" class="btn btn-secondary px-4">
                        <i class="bi bi-x-circle-fill me-1"></i> Cancelar
                    </a>
                    <button class="btn btn-primary px-4">
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
const nombreInput   = document.getElementById('nombre');
const telefonoInput = document.getElementById('telefono');
let nombreTouched = false;
let telefonoTouched = false;

/* Nombre: solo letras */
nombreInput.addEventListener('input', () => {
  nombreTouched = true;
  nombreInput.value = nombreInput.value.replace(/[^A-Za-zÁÉÍÓÚÜáéíóúüÑñ ]/g, '');
  if (nombreTouched) validarNombre();
});
nombreInput.addEventListener('blur', () => {
  nombreTouched = true;
  validarNombre();
});

/* Teléfono: solo números */
telefonoInput.addEventListener('input', () => {
  telefonoTouched = true;
  telefonoInput.value = telefonoInput.value.replace(/\D/g,'');
  if (telefonoTouched) validarTelefono();
});
telefonoInput.addEventListener('blur', () => {
  telefonoTouched = true;
  validarTelefono();
});

/* Validadores */
function validarNombre(){
  const len = nombreInput.value.length;
  if (len === 0 || len > 20) setInvalid(nombreInput);
  else setValid(nombreInput);
}
function validarTelefono(){
  const len = telefonoInput.value.length;
  if (len === 0) clearFeedback(telefonoInput);
  else if (len < 7 || len > 8) setInvalid(telefonoInput);
  else setValid(telefonoInput);
}

/* Helpers */
function setInvalid(el){ el.classList.add('is-invalid'); el.classList.remove('is-valid'); }
function setValid(el){ el.classList.remove('is-invalid'); el.classList.add('is-valid'); }
function clearFeedback(el){ el.classList.remove('is-invalid','is-valid'); }

/* Autocierre alertas */
setTimeout(() => {
  document.querySelectorAll('.alert-dismissible').forEach(el=>{
      bootstrap.Alert.getOrCreateInstance(el).close();
  });
}, 4000);
</script>
@endpush
