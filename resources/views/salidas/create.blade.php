 {{-- resources/views/salidas/create.blade.php --}}
@extends('layouts.admin')
@section('title','Registrar salida')

@section('content')
<div class="container py-4">

  {{-- ===== Alertas de sesión ===== --}}
  @foreach (['success'=>'success','error'=>'danger','info'=>'warning'] as $t=>$cls)
    @if(session($t))
      <div class="alert alert-{{ $cls }} alert-dismissible fade show d-flex align-items-center gap-2">
        <i class="bi bi-{{ $t=='success'?'check':'x' }}-circle-fill fs-5"></i>
        <span>{{ session($t) }}</span>
        <button class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
  @endforeach

  {{-- ===== Errores ===== --}}
  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <strong>Corrige los siguientes errores:</strong>
      <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- ===== Sugerencias rápidas ===== --}}
  @if(isset($necesarioDia,$necesarioFin))
    <div class="alert alert-info">
      <h5 class="mb-2"><i class="bi bi-lightbulb-fill text-primary me-2"></i>Sugerencias de insumos</h5>
      <div class="row small">
        <div class="col-md-6">
          <strong>Día normal (≈ 50–60 hamb.)</strong>
          <ul class="mb-2">
            @foreach($necesarioDia as $id=>$c)
              <li>{{ $insumos->firstWhere('idinsumo',$id)->nombre }}: {{ $c }}</li>
            @endforeach
          </ul>
        </div>
        <div class="col-md-6">
          <strong>Fin de semana (≈ 100–120 hamb.)</strong>
          <ul class="mb-0">
            @foreach($necesarioFin as $id=>$c)
              <li>{{ $insumos->firstWhere('idinsumo',$id)->nombre }}: {{ $c }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  @endif

  <form id="formSalida" action="{{ route('salidas.store') }}" method="POST">
    @csrf

    {{-- ===== Encabezado ===== --}}
    <div class="card shadow border-0 mb-4">
      <div class="card-header text-white" style="background:#008080">
        <h5 class="mb-0"><i class="bi bi-box-arrow-up me-2"></i>Nueva salida de insumos</h5>
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

    {{-- ===== Tabla fija ===== --}}
    <div class="card shadow border-0 mb-4">
      <div class="card-header text-white" style="background:#008080">
        <i class="bi bi-clipboard-data me-2"></i>Insumos (8 fijos)
      </div>
      <div class="card-body p-0">
        <table class="table table-bordered mb-0" id="detalleTbl">
          <thead class="text-center" style="background:#008080;color:#fff">
            <tr><th>Insumo</th><th class="w-25">Cantidad</th></tr>
          </thead>
          <tbody><!-- se rellenará vía JS --></tbody>
        </table>
      </div>
    </div>

    {{-- ===== Botones ===== --}}
    <div class="d-flex justify-content-between align-items-center">
      <a href="{{ route('salidas.index') }}" class="btn btn-secondary">
        <i class="bi bi-x-circle"></i> Cancelar
      </a>

      <div style="display:flex; align-items:center; gap:10px">
  <button type="button" id="auto50" class="btn text-white btn-sm fw-bold"
          style="background:#cb4335;border-color:#cb4335">
    Auto&nbsp;50&nbsp;Hamb.
  </button>
  
  <button type="button" id="auto100" class="btn text-white btn-sm fw-bold"
          style="background:#cb4335;border-color:#cb4335">
    Auto&nbsp;100&nbsp;Hamb.
  </button>
</div>


      <button class="btn text-white" style="background:#008080">
        <i class="bi bi-check-circle"></i> Guardar
      </button>
    </div>
  </form>
</div>
@endsection

@push('js')
<script>
/* === datos PHP ↦ JS === */
const insumos   = @json($insumos);      // catálogo
const sug50     = @json($necesarioDia); // 50 hamburguesas
const sug100    = @json($necesarioFin); // 100 hamburguesas

/* === Helpers === */
function pintar(obj){
  const tb = document.querySelector('#detalleTbl tbody');
  tb.innerHTML = '';
  let idx = 0;
  Object.entries(obj).forEach(([id,cant])=>{
    const nombre = insumos.find(i=>i.idinsumo==id).nombre;
    tb.insertAdjacentHTML('beforeend',`
      <tr>
        <td>
          <input type="hidden" name="detalles[${idx}][idinsumo]" value="${id}">
          ${nombre}
        </td>
        <td>
          <input type="number" name="detalles[${idx}][cantidad]"
                 class="form-control text-end" value="${cant}"
                 readonly step="0.01">
        </td>
      </tr>`);
    idx++;
  });
}

/* === Listeners === */
document.getElementById('auto50').addEventListener('click',()=>pintar(sug50));
document.getElementById('auto100').addEventListener('click',()=>pintar(sug100));
</script>
@endpush
