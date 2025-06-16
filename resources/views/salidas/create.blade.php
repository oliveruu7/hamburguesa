@extends('layouts.admin')
@section('title','Registrar salida')

@section('content')
<div class="container py-4">

  <form id="formSalida" action="{{ route('salidas.store') }}" method="POST">
    @csrf

    {{-- Encabezado --}}
    <div class="card shadow border-0 mb-4">
      <div class="card-header text-white" style="background:#008080">
        <h5 class="mb-0"><i class="bi bi-box-arrow-up me-2"></i> Nueva salida de insumos</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-bold" style="color:#008080">Fecha *</label>
            <input type="date" name="fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
          </div>
          <div class="col-md-8">
            <label class="form-label fw-bold" style="color:#008080">Observación</label>
            <input type="text" name="observacion" maxlength="100" class="form-control">
          </div>
        </div>
      </div>
    </div>

    {{-- Detalle --}}
    <div class="card shadow border-0 mb-4">
      <div class="card-header text-white d-flex justify-content-between" style="background:#008080">
        <span><i class="bi bi-clipboard-data me-2"></i> Insumos</span>
        <button type="button" class="btn btn-light btn-sm fw-bold" onclick="agregarFila()">+</button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" id="detalleTbl">
            <thead style="background:#008080;color:#fff" class="text-center">
              <tr>
                <th>Insumo</th>
                <th>Cantidad</th>
                <th></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Botones --}}
    <div class="d-flex justify-content-between">
      <a href="{{ route('salidas.index') }}" class="btn btn-secondary">
        <i class="bi bi-x-circle"></i> Cancelar
      </a>
      <button class="btn text-white" style="background:#008080">
        <i class="bi bi-check-circle"></i> Guardar
      </button>
    </div>
  </form>
</div>
@endsection

@push('js')
<script>
const insumos = @json($insumos);

function agregarFila(){
  const idx  = document.querySelectorAll('#detalleTbl tbody tr').length;
  const fila = `
  <tr>
    <td>
      <select name="detalles[${idx}][idinsumo]" class="form-select" required>
        <option value="">--</option>
        ${insumos.map(i=>`<option value="${i.idinsumo}">${i.nombre}</option>`).join('')}
      </select>
    </td>
    <td><input type="number" min="0.01" step="0.01" name="detalles[${idx}][cantidad]" class="form-control text-end" required></td>
    <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove();">−</button></td>
  </tr>`;
  document.querySelector('#detalleTbl tbody').insertAdjacentHTML('beforeend', fila);
}
</script>
@endpush
