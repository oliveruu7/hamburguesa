@extends('layouts.admin')
@section('title','Salidas de almacén')

@section('content')
<div class="container-fluid">

  {{-- Encabezado + botón --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color:#008080">
      <i class="bi bi-box-arrow-up me-2"></i> Salidas registradas de Almacén
    </h2>
    @permiso('salidas.create')
      <a href="{{ route('salidas.create') }}" class="btn text-white" style="background:#008080">
        <i class="bi bi-plus-circle me-1"></i> Nueva salida
      </a>
    @endpermiso
  </div>

  {{-- Alertas --}}
  @foreach(['success'=>'success','error'=>'danger','info'=>'info'] as $k=>$c)
      @if(session($k))
        <div class="alert alert-{{ $c }} alert-dismissible fade show">
          {{ session($k) }}
          <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
  @endforeach

  {{-- Tabla --}}
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-bordered align-middle mb-0">
        <thead style="background:#008080;color:#fff" class="text-center">
          <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Insumos</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($salidas as $s)
            <tr class="text-center">
              <td>{{ $s->idsalida }}</td>
              <td>{{ \Carbon\Carbon::parse($s->fecha)->format('d/m/Y') }}</td>
              <td>{{ $s->usuario->nombre }}</td>
              <td>{{ $s->detalles->count() }}</td>
              <td>
                <a href="{{ route('salidas.show',$s) }}" class="btn btn-sm text-white"
                   style="background:#008080" title="Ver">
                  <i class="bi bi-eye-fill"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-muted text-center py-4">No hay salidas registradas.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Paginación --}}
  <div class="mt-3 d-flex justify-content-end">
    {{ $salidas->links() }}
  </div>
</div>
@endsection
