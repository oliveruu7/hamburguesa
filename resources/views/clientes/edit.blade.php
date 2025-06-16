@extends('layouts.admin')
@section('title','Editar cliente')

@section('content')
<div class="container py-4">
  <div class="card shadow border-0">
    <div class="card-header text-white" style="background:#008080;">
      <h5 class="mb-0"><i class="bi bi-pencil-fill me-2"></i> Editar cliente</h5>
    </div>
    <div class="card-body">
      <form id="formEditarCliente" method="POST" action="{{ route('clientes.update', $cliente) }}" novalidate>
        @csrf @method('PUT')
        <div class="row g-3">

          {{-- NOMBRE --}}
          <div class="col-md-6">
            <label for="nombre" class="form-label fw-semibold">
              <i class="bi bi-person-fill"></i> Nombre <span class="text-danger">*</span>
            </label>
            <input type="text" name="nombre" id="nombre"
                   value="{{ old('nombre', $cliente->nombre) }}"
                   class="form-control"
                   maxlength="25"
                   pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]{3,25}"
                   required autocomplete="off">
            <div class="valid-feedback">Correcto</div>
            <div class="invalid-feedback">Solo letras (3–25 caracteres).</div>
          </div>

          {{-- CI --}}
          <div class="col-md-6">
            <label for="ci" class="form-label fw-semibold">
              <i class="bi bi-card-text"></i> CI <span class="text-danger">*</span>
            </label>
            <input type="text" name="ci" id="ci"
                   value="{{ old('ci', $cliente->ci) }}"
                   class="form-control"
                   maxlength="8" minlength="7"
                   pattern="\d{7,8}" inputmode="numeric"
                   required>
            <div class="valid-feedback">Correcto</div>
            <div class="invalid-feedback">Ingrese 7 a 8 dígitos numéricos.</div>
          </div>

          {{-- TELÉFONO --}}
          <div class="col-md-6">
            <label for="telefono" class="form-label fw-semibold">
              <i class="bi bi-telephone-fill"></i> Teléfono
            </label>
            <input type="text" name="telefono" id="telefono"
                   value="{{ old('telefono', $cliente->telefono) }}"
                   class="form-control"
                   maxlength="8" minlength="7"
                   pattern="\d{7,8}" inputmode="numeric">
            <div class="valid-feedback">Correcto</div>
            <div class="invalid-feedback">Ingrese un número válido (7–8 dígitos).</div>
          </div>
        </div>

        {{-- BOTONES --}}
        <div class="mt-4 d-flex justify-content-between">
          <a href="{{ route('clientes.index') }}" class="btn btn-secondary px-4">
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
const form = document.getElementById('formEditarCliente');
const btn = document.getElementById('btnActualizar');

const original = {
  nombre: '{{ addslashes($cliente->nombre) }}',
  ci: '{{ addslashes($cliente->ci) }}',
  telefono: '{{ addslashes($cliente->telefono ?? '') }}'
};

const camposTexto = ['nombre'];
const opcionales = ['telefono'];

form.addEventListener('input', (e) => {
  const el = e.target;
  const nombre = el.name;

  el.classList.remove('is-valid', 'is-invalid');

  if (camposTexto.includes(nombre)) {
    el.value = el.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ ]+/g, '').replace(/\s{2,}/g, ' ').trimStart();
  }

  if (['ci', 'telefono'].includes(nombre)) {
    el.value = el.value.replace(/\D/g, '');
  }

  if (opcionales.includes(nombre) && el.value.trim() === '') return;

  if (el.value.trim() !== '') {
    el.classList.add(el.checkValidity() ? 'is-valid' : 'is-invalid');
  }

  validarCambios();
});

function validarCambios() {
  const nombre = form.nombre.value.trim();
  const ci = form.ci.value.trim();
  const telefono = form.telefono.value.trim();

  const cambios = nombre !== original.nombre || ci !== original.ci || telefono !== original.telefono;
  const valido = form.checkValidity();

  btn.disabled = !(valido && cambios);
  btn.classList.toggle('opacity-50', btn.disabled);
}
</script>
@endpush
