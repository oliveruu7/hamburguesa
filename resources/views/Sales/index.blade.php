 @extends('layouts.admin')
@section('title','Ventas')

@section('content')
<div class="container-fluid">

  {{-- ===== Encabezado + botón ===== --}}
  <div class="row align-items-center mb-4">
    <div class="col-md-6">
      <h2 class="fw-bold" style="color:#008080">
        <i class="bi bi-cart-check-fill me-2"></i> Ventas registradas
      </h2>
    </div>
    <div class="col-md-6 text-md-end">
      @permiso('sales.create')
        <a href="{{ route('sales.create') }}" class="btn text-white shadow-sm"
           style="background:#008080">
          <i class="bi bi-plus-circle me-1"></i> Nueva venta
        </a>
      @endpermiso
    </div>
  </div>

  {{-- ===== Alertas de sesión ===== --}}
  @foreach(['success'=>'success','error'=>'danger','info'=>'info'] as $tipo=>$color)
    @if(session($tipo))
      <div class="alert alert-{{ $color }} alert-dismissible fade show" role="alert">
        {!! nl2br(e(session($tipo))) !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
  @endforeach

  {{-- ===== Tabla de ventas ===== --}}
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-bordered align-middle mb-0">
        <thead style="background:#008080;color:#fff" class="text-center">
          <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Usuario</th>
            <th>Fecha</th>
            <th>Total (Bs)</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($ventas as $v)
            <tr class="text-center">
              <td>{{ $v->idventa }}</td>
              <td class="text-start">{{ $v->cliente->nombre }}</td>
              <td>{{ $v->usuario->nombre }}</td>
              <td>{{ \Carbon\Carbon::parse($v->fecha_hora)->format('d/m/Y H:i') }}</td>
              <td class="fw-bold">Bs {{ number_format($v->total,2) }}</td>
              <td>
                <span class="badge {{ $v->estado ? 'bg-success' : 'bg-danger' }}">
                  {{ $v->estado ? 'Completada' : 'Anulada' }}
                </span>
              </td>
              <td>
                <a href="{{ route('sales.show', $v) }}" class="btn btn-sm btn-outline-primary" title="Ver detalle">
                  <i class="bi bi-eye-fill"></i>
                </a>
                @permiso('sales.edit')
                  <a href="{{ route('sales.edit', $v) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                    <i class="bi bi-pencil-fill"></i>
                  </a>
                @endpermiso
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted py-4">No hay ventas registradas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

   
</div>
@endsection
