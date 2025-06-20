 @extends('layouts.admin')
@section('title','Registrar receta')

@section('content')
<div class="container py-4">
  <div class="card shadow border-0">
    <div class="card-header text-white" style="background-color: #2e8b57;">
      <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i> Registrar receta</h5>
    </div>

    <div class="card-body">
      {{-- Mensajes de error general --}}
      @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>¡Error!</strong> Corrige los siguientes campos:
          <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <form method="POST" action="{{ route('recetas.store') }}" novalidate>
        @csrf
        <div class="row g-3">

          {{-- Hamburguesa --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold text-success">
              <i class="bi bi-hamburger"></i> Hamburguesa <span class="text-danger">*</span>
            </label>
            <select name="idhamburguesa" class="form-select @error('idhamburguesa') is-invalid @enderror" required>
              <option value="" disabled selected>-- Selecciona --</option>
              @foreach ($hamburguesas as $h)
                <option value="{{ $h->idhamburguesa }}" {{ old('idhamburguesa') == $h->idhamburguesa ? 'selected' : '' }}>
                  {{ $h->nombre }}
                </option>
              @endforeach
            </select>
            @error('idhamburguesa') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Insumo --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold text-success">Insumo <span class="text-danger">*</span></label>
            <select name="idinsumo" class="form-select @error('idinsumo') is-invalid @enderror" required>
              <option value="" disabled selected>-- Selecciona --</option>
              @foreach($insumos as $i)
                <option value="{{ $i->idinsumo }}" {{ old('idinsumo') == $i->idinsumo ? 'selected' : '' }}>
                  {{ $i->nombre }}
                </option>
              @endforeach
            </select>
            @error('idinsumo') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Cantidad necesaria --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold text-success">Cantidad necesaria (ej: 0.25) <span class="text-danger">*</span></label>
            <input type="number" name="cantidad_necesaria" step="0.01" min="0.01"
                   class="form-control @error('cantidad_necesaria') is-invalid @enderror"
                   value="{{ old('cantidad_necesaria') }}" required>
            @error('cantidad_necesaria') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        {{-- Botones --}}
        <div class="mt-4 d-flex justify-content-end">
          <a href="{{ route('recetas.index') }}" class="btn btn-secondary me-2">
            <i class="bi bi-x-circle-fill"></i> Cancelar
          </a>
          <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-save-fill"></i> Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('js')
<script>
const selBurger = document.querySelector('select[name="idhamburguesa"]');
const selInsumo = document.querySelector('select[name="idinsumo"]');
const inputCant = document.querySelector('input[name="cantidad_necesaria"]');

function aplicarRegla() {
    const b = +selBurger.value || 0;
    const i = +selInsumo.value || 0;

    // Restablece estado
    inputCant.setCustomValidity('');
    inputCant.classList.remove('is-invalid');

    if (reglas[b] && reglas[b][i] !== undefined) {
        const esperado = reglas[b][i];
        // Autocompleta
        if (!inputCant.value) inputCant.value = esperado;
        // Valida
        if (+inputCant.value !== esperado) {
            inputCant.setCustomValidity('Cantidad incorrecta.');
            inputCant.classList.add('is-invalid');
            inputCant.nextElementSibling.textContent =
              `Debe ser ${esperado}.`;
        }
    }
}

/* eventos */
selBurger.addEventListener('change', aplicarRegla);
selInsumo.addEventListener('change', aplicarRegla);
inputCant.addEventListener('input', aplicarRegla);
</script>
@endpush

@endsection
