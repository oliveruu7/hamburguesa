@extends('layouts.admin')
@section('title','Editar Producto')

@section('content')
<div class="container py-4">
  @foreach (['success','error'] as $type)
    @if(session($type))
      <div class="alert alert-{{ $type == 'success' ? 'success' : 'danger' }} alert-dismissible fade show">
        <i class="bi {{ $type == 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }} me-1"></i>
        {{ session($type) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
  @endforeach

  <div class="card shadow border-0">
    <div class="card-header text-white" style="background:#00bcd4;">
      <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Editar producto</h5>
    </div>

    <div class="card-body">
      <form id="productForm" method="POST" action="{{ route('products.update', $product) }}" novalidate>
        @csrf @method('PUT')
        <div class="row g-3">

          {{-- NOMBRE --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="bi bi-tag-fill"></i> Nombre <span class="text-danger">*</span>
            </label>
            <input name="nombre" id="nombre" value="{{ old('nombre', $product->nombre) }}" class="form-control"
                   maxlength="25" pattern="[A-Za-zÀ-ÿ ]{3,25}" required>
            <div class="valid-feedback">Correcto</div>
            <div class="invalid-feedback">Solo letras y espacios (3-25).</div>
          </div>

          {{-- CATEGORÍA --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="bi bi-tags-fill"></i> Categoría <span class="text-danger">*</span>
            </label>
            <select name="idcategoria" id="idcategoria" class="form-select" required>
              <option disabled value="">Seleccione...</option>
              @foreach($categorias as $c)
                <option value="{{ $c->idcategoria }}" {{ $product->idcategoria == $c->idcategoria ? 'selected' : '' }}>
                  {{ $c->nombre }}
                </option>
              @endforeach
            </select>
            <div class="valid-feedback">Correcto</div>
            <div class="invalid-feedback">Seleccione una categoría.</div>
          </div>

          {{-- PRECIO --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="bi bi-cash"></i> Precio <span class="text-danger">*</span>
            </label>
            <input name="precio_unitario" type="number" id="precio" class="form-control"
                   value="{{ old('precio_unitario', $product->precio_unitario) }}"
                   min="1" step="1" required>
            <div class="valid-feedback">Correcto</div>
            <div class="invalid-feedback">Mínimo 1 Bs.</div>
          </div>

          {{-- DESCRIPCIÓN --}}
          <div class="col-md-12">
            <label class="form-label fw-semibold">
              <i class="bi bi-card-text"></i> Descripción
            </label>
            <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $product->descripcion) }}</textarea>
          </div>

          {{-- IMAGEN --}}
          <div class="col-md-12">
            <label class="form-label fw-semibold">
              <i class="bi bi-image"></i> Imagen (URL)
            </label>
            <input name="imagenUrl" id="imagenUrl" type="url" class="form-control"
                   value="{{ old('imagenUrl', $product->imagenUrl) }}"
                   pattern="https?://.+" maxlength="255">
            <div class="invalid-feedback">URL no válida (http/s).</div>
          </div>
        </div>

        <div class="mt-4 d-flex justify-content-between">
          <a href="{{ route('products.index') }}" class="btn btn-secondary">
            <i class="bi bi-x-circle-fill me-1"></i> Cancelar
          </a>
          <button id="btnGuardar" class="btn btn-primary px-4 opacity-50" disabled>
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
const form = document.getElementById('productForm');
const btn = document.getElementById('btnGuardar');
const opcional = ['descripcion'];
const textoLimpio = ['nombre'];

validarTodo();

form.addEventListener('input', e => {
  const el = e.target;
  const name = el.name;

  el.classList.remove('is-valid', 'is-invalid');

  if (opcional.includes(name) && el.value.trim() === '') return;

  if (textoLimpio.includes(name)) {
    el.value = el.value.replace(/[^A-Za-z\u00C0-\u00FF ]+/g, '').replace(/\s{2,}/g, ' ').trimStart();
  }

  if (name === 'precio_unitario') {
    if (/^0[0-9]/.test(el.value)) {
      el.value = el.value.replace(/^0+/, '');
    }
    el.setCustomValidity(el.value < 1 ? 'Precio mínimo: 1' : '');
  }

  if (name === 'imagenUrl') {
    const urlVal = el.value.trim();
    const isValidUrl = /^https?:\/\/[\S]{1,253}\.[\S]{2,}$/i.test(urlVal);
    if (urlVal.length > 255) {
      el.setCustomValidity('La URL no debe superar los 255 caracteres.');
    } else if (urlVal !== '' && !isValidUrl) {
      el.setCustomValidity('La URL no es válida.');
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
