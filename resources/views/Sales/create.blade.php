 @extends('layouts.admin')
@section('title','Registrar Venta')

@section('content')
<div class="container py-4">

  <form id="formVenta" action="{{ route('sales.store') }}" method="POST">
    @csrf

    {{-- ===== Cliente ===== --}}
    <div class="card shadow border-0 mb-4">
      <div class="card-header text-white" style="background:#008080">
        <h5 class="mb-0"><i class="bi bi-person-fill me-2"></i> Datos del Cliente</h5>
      </div>
      <div class="card-body">
        <label class="form-label fw-bold" style="color:#008080">Cliente *</label>
        <select name="idcliente" class="form-select" required>
          <option value="">Seleccione…</option>
          @foreach($clientes as $c)
            <option value="{{ $c->idcliente }}">{{ $c->nombre }}</option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- ===== Detalle ===== --}}
    <div class="card shadow border-0 mb-4">
      <div class="card-header text-white d-flex justify-content-between" style="background:#008080">
        <span><i class="bi bi-box-seam me-2"></i> Productos</span>
        <button type="button" class="btn btn-light btn-sm fw-bold" onclick="agregarFila()">+</button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" id="detalleTbl">
            <thead style="background:#008080;color:#fff" class="text-center">
              <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- ===== Total & botones ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
      <strong style="color:#008080">Total: <span id="totalShow">0.00</span> Bs</strong>
      <div>
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">
          <i class="bi bi-x-circle"></i> Cancelar
        </a>
        <button class="btn text-white" style="background:#008080">
          <i class="bi bi-check-circle"></i> Confirmar
        </button>
      </div>
    </div>

    {{-- total oculto (opcional) --}}
    <input type="hidden" name="total_calculado" id="totalHidden">
  </form>
</div>
@endsection

@push('js')
<script>
const productos = @json($productos);

function agregarFila(){
  const idx = document.querySelectorAll('#detalleTbl tbody tr').length;
  const fila = `
  <tr>
    <td>
      <select name="productos[${idx}][idhamburguesa]" class="form-select" onchange="setPrecio(this)" required>
        <option value="">--</option>
        ${productos.map(p=>`<option value="${p.idhamburguesa}" data-precio="${p.precio_unitario}">${p.nombre}</option>`).join('')}
      </select>
    </td>
    <td><input type="number" step="0.01" name="productos[${idx}][precio_unitario]" class="form-control text-end" readonly></td>
    <td><input type="number" min="1" value="1" name="productos[${idx}][cantidad]" class="form-control text-end" oninput="calcSubtotal(this)" required></td>
    <td><input type="text" class="form-control text-end subtotal" readonly></td>
    <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove(); calcTotal();">−</button></td>
  </tr>`;
  document.querySelector('#detalleTbl tbody').insertAdjacentHTML('beforeend', fila);
}

function setPrecio(sel){
  const precio = sel.selectedOptions[0].dataset.precio || 0;
  const row = sel.closest('tr');
  row.querySelector('[name$="[precio_unitario]"]').value = precio;
  calcSubtotal(row.querySelector('[name$="[cantidad]"]'));
}

function calcSubtotal(inp){
  const row = inp.closest('tr');
  const c = parseFloat(row.querySelector('[name$="[cantidad]"]').value)||0;
  const p = parseFloat(row.querySelector('[name$="[precio_unitario]"]').value)||0;
  row.querySelector('.subtotal').value = (c*p).toFixed(2);
  calcTotal();
}

function calcTotal(){
  let t = 0;
  document.querySelectorAll('.subtotal').forEach(s=>t += parseFloat(s.value)||0);
  document.getElementById('totalShow').textContent = t.toFixed(2);
  document.getElementById('totalHidden').value  = t.toFixed(2);
}
</script>
@endpush
