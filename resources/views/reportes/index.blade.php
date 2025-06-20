@extends('layouts.admin')
@section('title', 'Reportes')

@section('content')
<div class="container py-4">
  <h2 class="fw-bold mb-4" style="color:#008080">
    <i class="bi bi-graph-up-arrow me-2"></i> Módulo de Reportes
  </h2>

  <div class="row g-4">
    @permiso('reports.sales')
      <div class="col-md-6">
        <a href="{{ route('reports.sales') }}" class="text-decoration-none">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-4">
              <i class="bi bi-cart-check display-4 text-primary"></i>
              <h5 class="mt-3 mb-0 fw-bold text-dark">Reporte de Ventas</h5>
            </div>
          </div>
        </a>
      </div>
    @endpermiso

    @permiso('reports.purchases')
      <div class="col-md-6">
        <a href="{{ route('reports.purchases') }}" class="text-decoration-none">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-4">
              <i class="bi bi-basket display-4 text-success"></i>
              <h5 class="mt-3 mb-0 fw-bold text-dark">Reporte de Compras</h5>
            </div>
          </div>
        </a>
      </div>
    @endpermiso
  </div>
</div>
@endsection
