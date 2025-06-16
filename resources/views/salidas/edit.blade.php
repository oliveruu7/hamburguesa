@extends('layouts.admin')
@section('title','Editar salida #'.$salida->idsalida)

@section('content')
<div class="container py-4">

  <form id="formSalida" action="{{ route('salidas.update',$salida) }}" method="POST">
    @csrf @method('PUT')

    {{-- Cabecera --}}
    <div class="card shadow border-0 mb-4">
      <div class="card-header text-white" style="background:#008080">
        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Editar salida #{{ $salida->idsalida }}</h5>
      </div>
      <div class="card-body">
        <input type="date" name="fecha" class="form-control mb-3" value="{{ $salida->fecha }}" required>
        <input type="text" name="observacion" class="form-control" value="{{ $salida->observacion }}">
      </div>
    </div>

    {{-- Detalle --}}
    <div class="card shadow border-0 mb-4">
      <div class="card-header text-white d-flex justify-content-between" style="background:#008080">
        <span><i class="bi bi-clipboard-data me-2"></i> Insumos</span>
        <button type="button" class="btn btn-light btn-sm fw-bold" onclick="agregarFila()">+</button>
      </div>
      <div class="card-body">
        <table class="table table-bordered" id="detalleTbl">
          <thead style="background:#008080;color:#fff" class="text-center">
            <tr><th>Insumo</th><th>Cantidad</th><th></th></tr>
          </thead>
          <tbody>
            @foreach($salida->detalles as $i=>$d)
              <tr>
                <td>
                  <select name="detalles[{{ $i }}][idinsumo]" class="form-select" required>
                    <option value="">--</option>
                    @foreach($insumos as $ins)
                      <option value="{{ $ins->idinsumo }}"
                        {{ $ins->idinsumo==$d->idinsumo?'selected':'' }}>
                        {{ $ins->nombre }}
                      </option>
                    @endforeach
                  </select>
                </td>
                <td><input type="number" min="0.01" step="0.01" class="form-control text-end"
                           name="detalles[{{ $i }}][cantidad]" value="{{ $d->cantidad }}" required></td>
                <td><button type="button" class="btn btn-outline-danger btn-sm"
                            onclick="this.closest('tr').remove();">−</button></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div class="d-flex justify-content-between">
      <a href="{{ route('salidas.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
      <button class="btn text-white" style="background:#008080"><i class="bi bi-save"></i> Guardar</button>
    </div>
  </form>
</div>
@endsection

@push('js')
<script>
const insumos = @json($insumos);
function agregarFila(){ /* mismo código que create */ }
</script>
@endpush
