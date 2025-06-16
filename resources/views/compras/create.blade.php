@extends('layouts.admin')
@section('title','Registrar nueva compra')

@section('content')
<div class="container py-4">

  {{-- ===== Encabezado ===== --}}
  <h3 class="mb-3" style="color:#008080">
    <i class="bi bi-bag-check-fill me-1"></i> Nueva Compra
  </h3>

  {{-- ===== Alertas de sesión ===== --}}
  @foreach (['success'=>'success','error'=>'danger','info'=>'info'] as $k=>$c)
    @if(session($k))
      <div class="alert alert-{{ $c }} alert-dismissible fade show" role="alert">
        {{ session($k) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
  @endforeach

  {{-- ===== Formulario ===== --}}
  <form id="formCompra" action="{{ route('compras.store') }}" method="POST">
    @csrf

    {{-- Proveedor --}}
    <div class="mb-3">
      <label class="fw-bold" style="color:#008080">Proveedor</label>
      <select name="idproveedor" class="form-select" required>
        <option value="">-- seleccione --</option>
        @foreach($proveedores as $p)
          <option value="{{ $p->idproveedor }}">{{ $p->nombre }}</option>
        @endforeach
      </select>
    </div>

    {{-- Tabla de insumos --}}
    <table class="table table-bordered" id="tablaInsumos">
      <thead style="background:#008080;color:#fff" class="text-center">
        <tr>
          <th>Insumo</th>
          <th>Cantidad</th>
          <th>Precio</th>
          <th>Subtotal</th>
          <th>
            <button type="button" class="btn btn-light btn-sm fw-bold"
                    onclick="agregarFila()">+</button>
          </th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>

    {{-- Total --}}
    <div class="text-end mb-4">
      <strong style="color:#008080">Total: <span id="totalMostrar">0.00</span> Bs</strong>
      <input type="hidden" name="total" id="total">
    </div>

    {{-- Botones --}}
    <div class="d-flex justify-content-between">
      <a href="{{ route('compras.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
      <button class="btn text-white" style="background:#008080">
        <i class="bi bi-save me-1"></i> Guardar
      </button>
    </div>
  </form>
</div>
@endsection

@push('js')
<script>
const insumos = @json($insumos);
/* === Agregar fila === */
function agregarFila(){
  const idx = document.querySelectorAll('#tablaInsumos tbody tr').length;
  const fila = `
  <tr>
    <td>
      <select name="detalles[${idx}][idinsumo]" class="form-select" required>
        <option value="">--</option>
        ${insumos.map(i=>`<option value="${i.idinsumo}">${i.nombre}</option>`).join('')}
      </select>
    </td>
    <td><input type="number" step="0.01" name="detalles[${idx}][cantidad]" class="form-control" oninput="recalcular()" required></td>
    <td><input type="number" step="0.01" name="detalles[${idx}][precio]"   class="form-control" oninput="recalcular()" required></td>
    <td class="subtotal">0.00</td>
    <td>
      <button type="button" class="btn btn-outline-danger btn-sm"
              onclick="this.closest('tr').remove(); recalcular();">−</button>
    </td>
  </tr>`;
  document.querySelector('#tablaInsumos tbody').insertAdjacentHTML('beforeend', fila);
}

/* === Calcular totales === */
function recalcular(){
  let total = 0;
  document.querySelectorAll('#tablaInsumos tbody tr').forEach(tr=>{
      const c = parseFloat(tr.querySelector('[name*="[cantidad]"]').value)||0;
      const p = parseFloat(tr.querySelector('[name*="[precio]"]').value)||0;
      const s = c*p;
      tr.querySelector('.subtotal').textContent = s.toFixed(2);
      total += s;
  });
  document.getElementById('totalMostrar').textContent = total.toFixed(2);
  document.getElementById('total').value = total.toFixed(2);
}
</script>
@endpush
