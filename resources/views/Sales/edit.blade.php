@extends('layouts.admin')
@section('title', 'Editar Venta')

@section('content')
<div class="container py-4">
  <form method="POST" action="{{ route('sales.update', $venta) }}">
    @csrf
    @method('PUT')

    <div class="card shadow-sm mb-4">
      <div class="card-header text-white" style="background:#008080">
        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Editar Venta</h5>
      </div>
      <div class="card-body">
        <p><strong>Cliente:</strong> {{ $venta->cliente->nombre }}</p>
        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y H:i') }}</p>

        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead>
              <tr>
                <th>Hamburguesa</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($venta->detalles as $index => $d)
                <tr>
                  <td>
                    {{ $d->hamburguesa->nombre }}
                    <input type="hidden" name="productos[{{ $index }}][idhamburguesa]" value="{{ $d->idhamburguesa }}">
                  </td>
                  <td>
                    <input type="number" name="productos[{{ $index }}][cantidad]" min="1" class="form-control"
                           value="{{ $d->cantidad }}" required>
                  </td>
                  <td>
                    <input type="number" step="0.01" name="productos[{{ $index }}][precio_unitario]"
                           class="form-control" value="{{ $d->precio_unitario }}" required>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-between">
          <a href="{{ route('sales.index') }}" class="btn btn-secondary">Cancelar</a>
          <button class="btn text-white" style="background:#008080">
            <i class="bi bi-check-circle me-1"></i> Guardar Cambios
          </button>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
