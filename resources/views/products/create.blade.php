@extends('layouts.admin')
@section('title','Crear Producto')

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
      <h5 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i> Crear nuevo producto</h5>
    </div>

    <div class="card-body">
      <form id="productForm" method="POST" action="{{ route('products.store') }}" novalidate>
        @csrf
        <div class="row g-3">

          {{-- NOMBRE --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-tag-fill"></i> Nombre <span class="text-danger">*</span></label>
            <input name="nombre" id="nombre" value="{{ old('nombre') }}" class="form-control"
                   maxlength="25" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]{3,25}" required autocomplete="off">
            <div class="valid-feedback">Correcto</div>
            <div class="invalid-feedback">Solo letras y espacios (3–25).</div>
          </div>

          {{-- CATEGORÍA --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-tags-fill"></i> Categoría <span class="text-danger">*</span></label>
            <select name="idcategoria" id="idcategoria" class="form-select" required>
              <option value="" disabled selected>Seleccione…</option>
              @foreach($categorias as $c)
                <option value="{{ $c->idcategoria }}" {{ old('idcategoria') == $c->idcategoria ? 'selected' : '' }}>
                  {{ $c->nombre }}
                </option>
              @endforeach
            </select>
            <div class="valid-feedback">Correcto</div>
            <div class="invalid-feedback">Seleccione una categoría.</div>
          </div>

          {{-- PRECIO --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-cash-stack"></i> Precio (Bs) <span class="text-danger">*</span></label>
            <input name="precio_unitario" id="precio" type="number" value="{{ old('precio_unitario') }}"
                   class="form-control" min="1" step="1" required>
            <div class="valid-feedback">Correcto</div>
            <div class="invalid-feedback">Ingrese un precio válido (mínimo 1).</div>
          </div>

          {{-- DESCRIPCIÓN --}}
          <div class="col-12">
            <label class="form-label fw-semibold"><i class="bi bi-card-text"></i> Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
          </div>

          {{-- IMAGEN URL --}}
          <div class="col-12">
            <label class="form-label fw-semibold"><i class="bi bi-image-fill"></i> Imagen (URL)</label>
            <input name="imagenUrl" id="imagenUrl" class="form-control"
                   type="url" maxlength="255"
                   value="{{ old('imagenUrl') }}"
                   placeholder="https://ejemplo.com/imagen.jpg"
                   pattern="https?://.+" 
                   title="Ingresa una URL válida que comience con http:// o https://">
            <div class="invalid-feedback">URL no válida o demasiado larga</div>
          </div>
        </div>

        {{-- BOTONES --}}
        <div class="mt-4 d-flex justify-content-between">
          <a href="{{ route('products.index') }}" class="btn btn-secondary px-4">
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
const form = document.getElementById('productForm');
const btn  = document.getElementById('btnGuardar');
const opcional     = ['descripcion'];
const textoLimpio  = ['nombre'];

validarTodo();

form.addEventListener('input', e => {
  const el = e.target;
  const name = el.name;

  el.classList.remove('is-valid', 'is-invalid');

  if (opcional.includes(name) && el.value.trim() === '') return;

  if (textoLimpio.includes(name)) {
    el.value = el.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ ]+/g, '').replace(/\s{2,}/g, ' ').trimStart();
  }

  if (name === 'precio_unitario') {
    if (/^0[0-9]/.test(el.value)) {
      el.value = el.value.replace(/^0+/, '');
    }
    el.setCustomValidity(el.value < 1 ? 'Precio mínimo: 1' : '');
  }

  if (name === 'imagenUrl') {
    const urlVal = el.value.trim();
    const isValidUrl = /^https?:\/\/[^\s]{1,253}\.[^\s]{2,}$/i.test(urlVal);

    if (urlVal.length > 255) {
      el.setCustomValidity('La URL no debe superar los 255 caracteres.');
    } else if (urlVal !== '' && !isValidUrl) {
      el.setCustomValidity('La URL no es válida. Asegúrate de que comience con http:// o https://');
    } else {
      el.setCustomValidity('');
    }
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
