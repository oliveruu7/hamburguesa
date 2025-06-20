 @extends('layouts.admin')
@section('title','Salidas de almacén')

@section('content')
<div class="container-fluid">

  {{-- ====== Encabezado + botón ====== --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color:#008080">
      <i class="bi bi-box-arrow-up me-2"></i> Salidas registradas del almacén
    </h2>
    @permiso('salidas.create')
      <a href="{{ route('salidas.create') }}" class="btn text-white" style="background:#008080">
        <i class="bi bi-plus-circle me-1"></i> Registrar salida
      </a>
    @endpermiso
  </div>

  {{-- ====== Alertas de sesión ====== --}}
  @foreach (['success'=>'success','error'=>'danger','info'=>'warning'] as $t => $cls)
    @if(session($t))
      <div class="alert alert-{{ $cls }} alert-dismissible fade show d-flex align-items-center gap-2">
        <i class="bi bi-{{ $t == 'success' ? 'check' : 'x' }}-circle-fill fs-5"></i>
        <span>{!! nl2br(e(session($t))) !!}</span>
        <button class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
  @endforeach

  {{-- ====== Tabla de salidas ====== --}}
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-bordered align-middle mb-0">
        <thead class="text-center text-white" style="background:#008080">
          <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Responsable</th>
            <th># Insumos</th>
          </tr>
        </thead>
        <tbody>
          @forelse($salidas as $s)
            <tr class="text-center">
              <td>{{ $s->idsalida }}</td>
              <td>{{ \Carbon\Carbon::parse($s->fecha)->format('d/m/Y') }}</td>
              <td>{{ $s->usuario->nombre }}</td>
              <td>{{ $s->detalles->count() }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-muted text-center py-4">
                No hay salidas registradas.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ====== Paginación ====== --}}
  <div class="mt-3 d-flex justify-content-end">
    {{ $salidas->links() }}
  </div>
</div>
@endsection
