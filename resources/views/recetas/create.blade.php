@extends('layouts.admin')
@section('title','Registrar receta')

@section('content')
<div class="container py-4">
  <div class="card shadow border-0">
    <div class="card-header text-white" style="background-color: #008080;">
      <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i> Registrar receta</h5>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('recetas.store') }}">
        @csrf
        <div class="row g-3">

           <div class="col-md-6">
  <label class="form-label fw-semibold">
    <i class="bi bi-hamburger"></i> Selecciona una hamburguesa <span class="text-danger">*</span>
  </label>
  <select name="idhamburguesa" class="form-select" required>
    <option value="" disabled selected>-- Selecciona --</option>
    @foreach ($hamburguesas as $h)
      <option value="{{ $h->idhamburguesa }}">{{ $h->nombre }}</option>
    @endforeach
  </select>
  <div class="invalid-feedback">Debe seleccionar una hamburguesa.</div>
</div>


          <div class="col-md-6">
            <label class="form-label fw-semibold">Insumo *</label>
            <select name="idinsumo" class="form-select @error('idinsumo') is-invalid @enderror" required>
              <option value="" disabled selected>Seleccione...</option>
              @foreach($insumos as $i)
                <option value="{{ $i->idinsumo }}" {{ old('idinsumo') == $i->idinsumo ? 'selected' : '' }}>
                  {{ $i->nombre }}
                </option>
              @endforeach
            </select>
            @error('idinsumo') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Cantidad necesaria *</label>
            <input type="number" name="cantidad_necesaria" step="0.01" min="0.01" class="form-control @error('cantidad_necesaria') is-invalid @enderror" value="{{ old('cantidad_necesaria') }}" required>
            @error('cantidad_necesaria') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="mt-4 d-flex justify-content-end">
          <a href="{{ route('recetas.index') }}" class="btn btn-secondary me-2">
            <i class="bi bi-x-circle-fill"></i> Cancelar
          </a>
          <button type="submit" class="btn btn-success px-4">
            <i class="bi bi-save-fill"></i> Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
