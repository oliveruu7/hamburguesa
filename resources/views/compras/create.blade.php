@extends('layouts.admin')
@section('title','Registrar nueva compra')

@section('content')
<div class="container py-4">

  {{-- ===== Encabezado ===== --}}
  <h3 class="mb-3" style="color:#008080">
    <i class="bi bi-bag-check-fill me-1"></i> Nueva Compra
  </h3>

  {{-- ===== Alertas Laravel ===== --}}
  @foreach (['success'=>'success','error'=>'danger','info'=>'info'] as $k=>$c)
    @if(session($k))
      <div class="alert alert-{{ $c }} alert-dismissible fade show" role="alert">
        {{ session($k) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
  @endforeach

  {{-- ===== Alerta de error JS personalizada ===== --}}
<div id="formError" class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 d-none" role="alert">
  <i class="bi bi-exclamation-triangle-fill fs-5"></i>
  <div><strong>Corrige los campos en rojo:</strong> Solo se permiten valores mayores o iguales a <strong>1</strong>.</div>
  <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>


  {{-- ===== Formulario ===== --}}
  <form id="formCompra" action="{{ route('compras.store') }}" method="POST" novalidate onsubmit="return validarAntesDeEnviar()">
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
    <div class="table-responsive">
      <table class="table table-bordered align-middle" id="tablaInsumos">
        <thead style="background:#008080;color:#fff" class="text-center">
          <tr>
            <th style="width:30%">Insumo</th>
            <th style="width:20%">Cantidad</th>
            <th style="width:15%">Precio Unit.</th>
            <th style="width:15%">Subtotal</th>
            <th style="width:20%">
              <button type="button" class="btn btn-success btn-sm fw-bold"
                      onclick="agregarFila()">
                <i class="bi bi-plus-lg me-1"></i> Agregar
              </button>
            </th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

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

/* === Agrega una nueva fila === */
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
     {{-- …dentro de la tabla… --}}
<td>
  <input type="number" min="1" max="100000" step="1"
         name="detalles[${idx}][cantidad]"
         class="form-control text-end"
         placeholder="1"
         data-touched="0"
         oninput="marcaTocado(this); recalcular();" required>
</td>

<td>
  <input type="number" min="0.5" max="10000" step="0.01"
         name="detalles[${idx}][precio]"
         class="form-control text-end"
         placeholder="0.50"
         data-touched="0"
         oninput="marcaTocado(this); recalcular();" required>
</td>


    <td class="subtotal text-end">0.00</td>
    <td class="text-center">
      <button type="button" class="btn btn-outline-danger btn-sm"
              onclick="this.closest('tr').remove(); recalcular();">
        <i class="bi bi-x-lg"></i>
      </button>
    </td>
  </tr>`;
  document.querySelector('#tablaInsumos tbody').insertAdjacentHTML('beforeend', fila);
}
// calcula el total al seleccionar un insumo validaciones
  /* === Marcar campo como “tocado” === */
function marcaTocado(el){ el.dataset.touched = "1"; }

/* === Recalcular subtotales y total === */
/* === Recalcular y validar === */
function recalcular() {
  let total = 0;

  document.querySelectorAll('#tablaInsumos tbody tr').forEach(tr => {
    const cantInp = tr.querySelector('[name*="[cantidad]"]');
    const precInp = tr.querySelector('[name*="[precio]"]');

    const cant = parseFloat(cantInp.value) || 0;
    const prec = parseFloat(precInp.value) || 0;

    // Obtener / crear mensaje de error debajo del input
    const getErr = (inp) => {
      let el = inp.nextElementSibling;
      if (!el || !el.classList.contains('error-msg')) {
        inp.insertAdjacentHTML('afterend', '<div class="error-msg text-danger small"></div>');
        el = inp.nextElementSibling;
      }
      return el;
    };

    const cErr = getErr(cantInp);
    const pErr = getErr(precInp);

    // ===== Validar cantidad =====
    if (cantInp.dataset.touched === "1" && (cant < 1 || cant > 100000)) {
      cantInp.classList.add('is-invalid');
      cErr.textContent = 'Cantidad incoherente.';
    } else {
      cantInp.classList.remove('is-invalid');
      cErr.textContent = '';
    }

    // ===== Validar precio =====
    if (precInp.dataset.touched === "1" && (prec < 0.5 || prec > 10000)) {
      precInp.classList.add('is-invalid');
      pErr.textContent = 'Precio incoherente.';
    } else {
      precInp.classList.remove('is-invalid');
      pErr.textContent = '';
    }

    // Calcular subtotal solo si ambos valores válidos
    const sub = (cant >= 1 && cant <= 100000 && prec >= 0.5 && prec <= 10000) ? cant * prec : 0;
    tr.querySelector('.subtotal').textContent = sub.toFixed(2);
    total += sub;
  });

  document.getElementById('totalMostrar').textContent = total.toFixed(2);
  document.getElementById('total').value = total.toFixed(2);
}


/* ===== Bloquear el envío si algo está mal ===== */
document.getElementById('formCompra')
  .addEventListener('submit', e=>{
     recalcular();
     if(document.querySelectorAll('.is-invalid').length){
        e.preventDefault();
        const alerta=document.getElementById('formError');
        alerta.classList.remove('d-none');
        alerta.scrollIntoView({behavior:'smooth'});
     }
});



/* === Validación final === */
function validarAntesDeEnviar(){
  recalcular();
  const invalids = document.querySelectorAll('.is-invalid');
  const filas = document.querySelectorAll('#tablaInsumos tbody tr');

  if (invalids.length > 0 || filas.length === 0) {
    document.getElementById('formError').classList.remove('d-none');
    return false;
  }

  return true;
}

document.addEventListener('DOMContentLoaded', recalcular);
</script>
@endpush
