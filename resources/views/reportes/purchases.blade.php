{{-- resources/views/reports/purchases.blade.php --}}
@extends('layouts.admin')
@section('title', 'Reporte de Compras')

@section('content')
<div class="container-fluid">

  {{-- ===== Encabezado + exportar ===== --}}
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-2 mb-md-0" style="color:#008080">
      <i class="bi bi-clipboard-data me-2"></i> Reporte de Compras
    </h2>

    {{-- Botón de exportar (opcional) --}}
    <a href="{{ route('reports.purchases', array_merge(request()->all(), ['export'=>'xlsx'])) }}"
       class="btn btn-outline-success shadow-sm">
      <i class="bi bi-file-earmark-excel me-1"></i> Exportar Excel
    </a>
  </div>

  {{-- ===== Alertas de sesión ===== --}}
  @foreach (['success'=>'success','error'=>'danger'] as $t=>$cls)
    @if(session($t))
      <div class="alert alert-{{ $cls }} alert-dismissible fade show d-flex align-items-center gap-2">
        <i class="bi bi-{{ $cls=='success' ? 'check-circle' : 'x-circle' }}-fill fs-5"></i>
        <span>{{ session($t) }}</span>
        <button class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
  @endforeach

  {{-- ===== Filtros ===== --}}
  <form method="GET" class="card shadow-sm mb-4">
    <div class="card-body row gy-2 gx-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label fw-semibold mb-0">Desde</label>
        <input type="date" name="fecha_desde" class="form-control"
               value="{{ request('fecha_desde') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold mb-0">Hasta</label>
        <input type="date" name="fecha_hasta" class="form-control"
               value="{{ request('fecha_hasta') }}">
      </div>
      <div class="col-md-4">
        <label class="form-label fw-semibold mb-0">Proveedor</label>
        <select name="proveedor" class="form-select">
          <option value="">Todos…</option>
          @foreach($proveedores as $p)
            <option value="{{ $p->idproveedor }}"
              {{ request('proveedor')==$p->idproveedor ? 'selected' : '' }}>
              {{ $p->nombre }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 d-grid">
        <button class="btn text-white" style="background:#008080">
          <i class="bi bi-funnel-fill me-1"></i> Filtrar
        </button>
      </div>
    </div>
  </form>

  {{-- ===== Tabla ===== --}}
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-bordered align-middle mb-0">
        <thead class="text-center" style="background:#008080;color:#fff">
          <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Proveedor</th>
            <th>Usuario</th>
            <th>Total&nbsp;(Bs)</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($compras as $c)
            <tr class="text-center">
              <td>{{ $c->idcompra }}</td>
              <td>{{ \Carbon\Carbon::parse($c->fecha)->format('d/m/Y H:i') }}</td>
              <td class="text-start">{{ $c->proveedor->nombre }}</td>
              <td>{{ $c->usuario->nombre }}</td>
              <td class="fw-bold">{{ number_format($c->total,2,',','.') }}</td>
              <td>
                <span class="badge {{ $c->estado==='Registrada' ? 'bg-success' : 'bg-danger' }}">
                  {{ $c->estado }}
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">
                No se encontraron compras con los filtros aplicados.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ===== Paginación (en español) ===== --}}
  @if ($compras->hasPages())
    <div class="d-flex justify-content-center my-3">
      {{ $compras->withQueryString()
                 ->onEachSide(1)
                 ->links('vendor.pagination.bootstrap-5-es') }}
    </div>
  @endif
</div>
@endsection
