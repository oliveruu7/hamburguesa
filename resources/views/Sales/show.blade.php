@extends('layouts.admin')
@section('title', 'Detalle de Venta')

@section('content')
<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-header text-white" style="background:#008080">
      <h5 class="mb-0"><i class="bi bi-receipt me-2"></i> Detalle de Venta</h5>
    </div>
    <div class="card-body">
      <p><strong>Cliente:</strong> {{ $venta->cliente->nombre }}</p>
      <p><strong>Vendedor:</strong> {{ $venta->usuario->nombre }}</p>
      <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y H:i') }}</p>
      <p><strong>Total:</strong> Bs {{ number_format($venta->total, 2) }}</p>

      <hr>
      <h6>Productos vendidos:</h6>
      <table class="table table-sm table-bordered">
        <thead>
          <tr>
            <th>Hamburguesa</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($venta->detalles as $d)
            <tr>
              <td>{{ $d->hamburguesa->nombre }}</td>
              <td>{{ $d->cantidad }}</td>
              <td>Bs {{ number_format($d->precio_unitario, 2) }}</td>
              <td>Bs {{ number_format($d->subtotal, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <a href="{{ route('sales.index') }}" class="btn btn-secondary mt-3">Volver</a>
    </div>
  </div>
</div>
@endsection
