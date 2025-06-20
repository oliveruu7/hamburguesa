@extends('layouts.admin')
@section('title','Reporte de Ventas')

@section('content')
<div class="container py-4">
  <h2 class="fw-bold mb-3" style="color:#008080">
    <i class="bi bi-receipt-cutoff me-2"></i> Ventas ({{ $desde }} – {{ $hasta }})
  </h2>

  {{-- —— filtro —— --}}
  <form class="row g-2 mb-3" method="GET">
    <div class="col-auto">
      <label class="form-label mb-0 small">Desde</label>
      <input type="date" name="desde" class="form-control" value="{{ $desde }}">
    </div>
    <div class="col-auto">
      <label class="form-label mb-0 small">Hasta</label>
      <input type="date" name="hasta" class="form-control" value="{{ $hasta }}">
    </div>
    <div class="col-auto align-self-end">
      <button class="btn btn-sm text-white" style="background:#008080">
        <i class="bi bi-search"></i> Filtrar
      </button>
    </div>
  </form>

  {{-- —— resumen —— --}}
  <div class="alert alert-info d-flex justify-content-between">
    <span><strong>Ventas:</strong> {{ $resumen->total_registros }}</span>
    <span><strong>Total Bs:</strong> {{ number_format($resumen->total_bs,2,',','.') }}</span>
  </div>

  {{-- —— tabla —— --}}
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-bordered align-middle mb-0">
        <thead style="background:#008080;color:#fff" class="text-center">
          <tr>
            <th>#</th><th>Cliente</th><th>Usuario</th>
            <th>Fecha/Hora</th><th>Total (Bs)</th>
          </tr>
        </thead>
        <tbody>
          @forelse($ventas as $v)
            <tr class="text-center">
              <td>{{ $v->idventa }}</td>
              <td class="text-start">{{ $v->cliente->nombre }}</td>
              <td>{{ $v->usuario->nombre }}</td>
              <td>{{ \Carbon\Carbon::parse($v->fecha_hora)->format('d/m/Y H:i') }}</td>
              <td class="fw-bold">{{ number_format($v->total,2,',','.') }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-muted text-center">Sin datos.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- —— paginación en español —— --}}
  <div class="mt-3 d-flex justify-content-end">
    {{ $ventas->links('vendor.pagination.bootstrap-5-es') }}
  </div>
</div>
@endsection
