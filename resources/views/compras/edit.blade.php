 @extends('layouts.admin')
@section('title', 'Editar compra #'.$compra->idcompra)

@section('content')
<div class="container py-4">
  {{-- ===== Encabezado ===== --}}
  <h3 class="mb-3" style="color:#008080">
    <i class="bi bi-pencil-square me-1"></i> Editar Compra #{{ $compra->idcompra }}
  </h3>

  {{-- Alertas --}}
  @foreach (['success','error','info'] as $t)
      @if(session($t))
          <div class="alert alert-{{ $t=='success'?'success':($t=='error'?'danger':'warning') }}">
              {{ session($t) }}
          </div>
      @endif
  @endforeach

  <form id="formCompra" action="{{ route('compras.update',$compra) }}" method="POST">
      @csrf @method('PUT')

      {{-- Proveedor --}}
      <div class="mb-3">
          <label class="fw-bold" style="color:#008080">Proveedor</label>
          <select name="idproveedor" class="form-select" required>
              <option value="">-- seleccione --</option>
              @foreach($proveedores as $p)
                  <option value="{{ $p->idproveedor }}"
                          {{ $p->idproveedor==$compra->idproveedor?'selected':'' }}>
                      {{ $p->nombre }}
                  </option>
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
          <tbody>
              @foreach($compra->detalles as $idx=>$d)
              <tr>
                  <td>
                      <select name="detalles[{{ $idx }}][idinsumo]" class="form-select" required>
                          <option value="">--</option>
                          @foreach($insumos as $i)
                              <option value="{{ $i->idinsumo }}"
                                  {{ $i->idinsumo==$d->idinsumo?'selected':'' }}>
                                  {{ $i->nombre }}
                              </option>
                          @endforeach
                      </select>
                  </td>
                  <td>
                      <input type="number" step="0.01"
                             name="detalles[{{ $idx }}][cantidad]"
                             class="form-control" value="{{ $d->cantidad }}"
                             oninput="recalcular()" required>
                  </td>
                  <td>
                      <input type="number" step="0.01"
                             name="detalles[{{ $idx }}][precio]"
                             class="form-control" value="{{ $d->precio }}"
                             oninput="recalcular()" required>
                  </td>
                  <td class="subtotal">0.00</td>
                  <td>
                      <button type="button" class="btn btn-outline-danger btn-sm"
                              onclick="this.closest('tr').remove(); recalcular();">−</button>
                  </td>
              </tr>
              @endforeach
          </tbody>
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
              <i class="bi bi-save me-1"></i> Guardar cambios
          </button>
      </div>
  </form>
</div>
@endsection

@push('js')
<script>
const insumos = @json($insumos);

/* Ajusta subtotales al cargar */
document.addEventListener('DOMContentLoaded', () => recalcular());

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
      <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove(); recalcular();">−</button></td>
    </tr>`;
    document.querySelector('#tablaInsumos tbody').insertAdjacentHTML('beforeend', fila);
}

function recalcular(){
    let total=0;
    document.querySelectorAll('#tablaInsumos tbody tr').forEach(tr=>{
        const c=parseFloat(tr.querySelector('[name*="[cantidad]"]').value)||0;
        const p=parseFloat(tr.querySelector('[name*="[precio]"]').value)||0;
        const sub=c*p;
        tr.querySelector('.subtotal').textContent=sub.toFixed(2);
        total+=sub;
    });
    document.getElementById('totalMostrar').textContent=total.toFixed(2);
    document.getElementById('total').value=total.toFixed(2);
}
</script>
@endpush
