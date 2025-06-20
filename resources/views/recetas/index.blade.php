@extends('layouts.admin')
@section('title','Recetas')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold" style="color:#008080">
      <i class="bi bi-list-ul me-2"></i> Lista de Recetas
    </h3>
    @permiso('recetas.create')
      <a href="{{ route('recetas.create') }}" class="btn btn-success shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Nueva Receta
      </a>
    @endpermiso
  </div>

  @foreach(['success', 'error'] as $msg)
    @if(session($msg))
      <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }} alert-dismissible fade show">
        <i class="bi {{ $msg == 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }}"></i>
        {{ session($msg) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
  @endforeach

  <div class="table-responsive shadow-sm">
    <table class="table table-bordered align-middle text-center">
      <thead class="table-info text-white">
        <tr>
          <th>#</th>
          <th>Hamburguesa</th>
          <th>Insumo</th>
          <th>Cantidad</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recetas as $r)
          <tr>
            <td>{{ $r->idreceta }}</td>
            <td>{{ $r->hamburguesa->nombre }}</td>
            <td>{{ $r->insumo->nombre }}</td>
            <td>{{ $r->cantidad_necesaria }}</td>
            <td>
              @permiso('recetas.edit')
                <a href="{{ route('recetas.edit', $r) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                  <i class="bi bi-pencil-fill"></i>
                </a>
              @endpermiso
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-muted">No hay recetas registradas.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
