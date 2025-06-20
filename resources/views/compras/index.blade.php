 @extends('layouts.admin')
@section('title', 'Compras')

@section('content')
<div class="container py-4">

  {{-- ===== Encabezado y botón ===== --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold" style="color:#008080">
      <i class="bi bi-cart-check me-2"></i> Lista de Compras
    </h3>
    @permiso('compras.create')
      <a href="{{ route('compras.create') }}" class="btn btn-success shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Nueva compra
      </a>
    @endpermiso
  </div>

  {{-- ===== Alertas de sesión ===== --}}
  @foreach (['success' => 'success', 'error' => 'danger', 'info' => 'info'] as $tipo => $clase)
    @if(session($tipo))
      <div class="alert alert-{{ $clase }} alert-dismissible fade show" role="alert">
        <i class="bi bi-{{ $clase == 'success' ? 'check' : ($clase == 'danger' ? 'x-circle' : 'info-circle') }}-fill me-2"></i>
        {{ session($tipo) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
          
        </tr>
      </thead>
      <tbody>
        @forelse($compras as $c)
          <tr>
            <td>{{ $c->idcompra }}</td>
            <td class="text-start">{{ $c->proveedor->nombre }}</td>
            <td>{{ $c->usuario->nombre }}</td>
            <td>{{ \Carbon\Carbon::parse($c->fecha)->format('d/m/Y H:i') }}</td>
            <td class="fw-bold">{{ number_format($c->total, 2, ',', '.') }}</td>
            <td>
              <span class="badge {{ $c->estado === 'Registrada' ? 'bg-success' : 'bg-danger' }}">
                {{ $c->estado }}
              </span>
            </td>
             
          </tr>
        @empty
          <tr><td colspan="7" class="text-muted py-4">No hay compras registradas.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- ===== Paginación en español ===== --}}
  @if ($compras->hasPages())
    <div class="d-flex justify-content-center mt-4">
      <nav>
        <ul class="pagination">
          {{-- Anterior --}}
          @if ($compras->onFirstPage())
            <li class="page-item disabled"><span class="page-link">Anterior</span></li>
          @else
            <li class="page-item">
              <a class="page-link" href="{{ $compras->previousPageUrl() }}">Anterior</a>
            </li>
          @endif

          {{-- Números --}}
          @foreach ($compras->getUrlRange(1, $compras->lastPage()) as $page => $url)
            <li class="page-item {{ $page == $compras->currentPage() ? 'active' : '' }}">
              <a class="page-link" href="{{ $url }}">{{ $page }}</a>
            </li>
          @endforeach

          {{-- Siguiente --}}
          @if ($compras->hasMorePages())
            <li class="page-item">
              <a class="page-link" href="{{ $compras->nextPageUrl() }}">Siguiente</a>
            </li>
          @else
            <li class="page-item disabled"><span class="page-link">Siguiente</span></li>
          @endif
        </ul>
      </nav>
    </div>
  @endif

</div>
@endsection
