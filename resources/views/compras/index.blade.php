@extends('layouts.admin')
@section('title', 'Compras')

@section('content')
<div class="container py-4">

  {{-- ===== Encabezado + botón ===== --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0" style="color:#008080">
      <i class="bi bi-cart-check me-2"></i> Lista de Compras
    </h3>

    @permiso('compras.create')
      <a href="{{ route('compras.create') }}" class="btn text-white shadow-sm"
         style="background:#008080">
        <i class="bi bi-plus-circle me-1"></i> Nueva compra
      </a>
    @endpermiso
  </div>

  {{-- ===== Alertas de sesión ===== --}}
  @foreach (['success'=>'success','error'=>'danger','info'=>'info'] as $key => $color)
      @if(session($key))
        <div class="alert alert-{{ $color }} alert-dismissible fade show" role="alert">
          {{ session($key) }}
          <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
  @endforeach

  {{-- ===== Tabla ===== --}}
  <div class="table-responsive shadow-sm">
    <table class="table table-bordered align-middle text-center">
      <thead style="background:#008080;color:#fff">
        <tr>
          <th>#</th>
          <th class="text-start">Proveedor</th>
          <th>Usuario</th>
          <th>Fecha</th>
          <th>Total (Bs)</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($compras as $c)
          <tr>
            <td>{{ $c->idcompra }}</td>
            <td class="text-start">{{ $c->proveedor->nombre }}</td>
            <td>{{ $c->usuario->nombre }}</td>
            <td>{{ $c->fecha }}</td>
            <td class="fw-bold">{{ number_format($c->total,2) }}</td>
            <td>
              <span class="badge {{ $c->estado=='Registrada' ? 'bg-success' : 'bg-danger' }}">
                {{ $c->estado }}
              </span>
            </td>
            <td>
              <div class="btn-group">
                @permiso('compras.edit')
                  <a href="{{ route('compras.edit',$c) }}" class="btn btn-sm text-white"
                     style="background:#008080" title="Editar">
                     <i class="bi bi-pencil-fill"></i>
                  </a>
                @endpermiso

                @permiso('compras.delete')
                  <form action="{{ route('compras.destroy',$c) }}" method="POST"
                        onsubmit="return confirm('¿Anular compra?')" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" title="Anular">
                      <i class="bi bi-trash-fill"></i>
                    </button>
                  </form>
                @endpermiso
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-muted">No hay compras registradas.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- ===== Paginación ===== --}}
  <div class="d-flex justify-content-center mt-3">
    {{ $compras->links() }}
  </div>
</div>
@endsection
