 @extends('layouts.admin')
@section('title', 'Editar Receta')

@section('content')
<div class="container py-4">

  <h3 class="text-primary mb-4">
    <i class="bi bi-pencil-square me-2"></i> Editar Receta
  </h3>

  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
      <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Corrige los siguientes errores:</strong>
      <ul class="mt-2 mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <form action="{{ route('recetas.update', $receta) }}" method="POST" class="card shadow border-0">
    @csrf @method('PUT')
    <div class="card-body row g-3">

      {{-- Hamburguesa --}}
      <div class="col-md-6">
        <label class="form-label fw-semibold">Hamburguesa <span class="text-danger">*</span></label>
        <select name="idhamburguesa" class="form-select" required disabled>
          <option value="">-- Selecciona --</option>
          @foreach($hamburguesas as $h)
            <option value="{{ $h->idhamburguesa }}" {{ $receta->idhamburguesa == $h->idhamburguesa ? 'selected' : '' }}>
              {{ $h->nombre }}
            </option>
          @endforeach
        </select>
        <input type="hidden" name="idhamburguesa" value="{{ $receta->idhamburguesa }}">
      </div>

      {{-- Insumo --}}
      <div class="col-md-6">
        <label class="form-label fw-semibold">Insumo <span class="text-danger">*</span></label>
        <select name="idinsumo" class="form-select" required disabled>
          <option value="">-- Selecciona --</option>
          @foreach($insumos as $i)
            <option value="{{ $i->idinsumo }}" {{ $receta->idinsumo == $i->idinsumo ? 'selected' : '' }}>
              {{ $i->nombre }}
            </option>
          @endforeach
        </select>
        <input type="hidden" name="idinsumo" value="{{ $receta->idinsumo }}">
      </div>

      {{-- Cantidad necesaria --}}
      <div class="col-md-6">
        <label class="form-label fw-semibold">Cantidad Necesaria <span class="text-danger">*</span></label>
        <input type="number" name="cantidad_necesaria" class="form-control" step="0.01" min="0.01"
               value="{{ old('cantidad_necesaria', $receta->cantidad_necesaria) }}" required>
      </div>

    </div>

    <div class="card-footer d-flex justify-content-between mt-4">
      <a href="{{ route('recetas.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left-circle me-1"></i> Cancelar
      </a>
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-save-fill me-1"></i> Guardar Cambios
      </button>
    </div>
  </form>
</div>
@endsection
