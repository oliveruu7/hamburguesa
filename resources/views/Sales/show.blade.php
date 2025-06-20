{{-- resources/views/sales/show.blade.php --}}
@extends('layouts.admin')
@section('title','Venta #'.$venta->idventa)

@section('content')
<div class="container-lg py-4">

 {{-- ===== Breadcrumb / volver ===== --}}
<div class="mb-3">
  <a href="{{ route('sales.index') }}" class="btn btn-outline-primary d-inline-flex align-items-center">
    <i class="bi bi-arrow-left-circle me-2"></i> Volver al listado
  </a>
</div>


  {{-- ===== Encabezado ===== --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center"
         style="background:#008080">
      <h4 class="h5 text-white mb-0">
        <i class="bi bi-receipt-cutoff me-2"></i> Detalle de la venta
        <span class="badge bg-light text-dark ms-2">#{{ $venta->idventa }}</span>
      </h4>

      <span class="badge {{ $venta->estado ? 'bg-success' : 'bg-danger' }}">
        {{ $venta->estado ? 'COMPLETADA' : 'ANULADA' }}
      </span>
    </div>
  </div>

  {{-- ===== Datos generales ===== --}}
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body">
          <h6 class="fw-bold text-secondary mb-1">
            <i class="bi bi-person-badge me-1"></i> Cliente
          </h6>
          <span class="fs-6">{{ $venta->cliente->nombre }}</span>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body">
          <h6 class="fw-bold text-secondary mb-1">
            <i class="bi bi-person-check me-1"></i> Registrada por
          </h6>
          <span class="fs-6">{{ $venta->usuario->nombre }}</span>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body">
          <h6 class="fw-bold text-secondary mb-1">
            <i class="bi bi-calendar-event me-1"></i> Fecha / hora
          </h6>
          <span class="fs-6">
            {{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y H:i') }}
          </span>
        </div>
      </div>
    </div>
  </div>

  {{-- ===== Detalle de productos ===== --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header text-white fw-semibold" style="background:#008080">
      <i class="bi bi-list-ul me-2"></i> Productos vendidos
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light text-center">
          <tr>
            <th>#</th>
            <th class="text-start">Hamburguesa</th>
            <th>Cant.</th>
            <th>P. Unit (Bs)</th>
            <th class="text-end">Subtotal (Bs)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($venta->detalles as $i => $det)
            <tr class="text-center">
              <td>{{ $i + 1 }}</td>
              <td class="text-start">
                {{ optional($det->hamburguesa)->nombre ?? '— eliminada —' }}
              </td>
              <td>{{ $det->cantidad }}</td>
              <td>{{ number_format($det->precio_unitario,2) }}</td>
              <td class="text-end">{{ number_format($det->subtotal,2) }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot class="table-light">
          <tr>
            <th colspan="4" class="text-end">TOTAL</th>
            <th class="text-end fw-bold">
              Bs {{ number_format($venta->total,2) }}
            </th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

</div>
@endsection
