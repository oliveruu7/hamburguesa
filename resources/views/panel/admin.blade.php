@extends('layouts.admin')
@section('title', 'Panel Principal')

@section('content')
<div class="container py-4">

    {{-- Mensaje de bienvenida / éxito --}}
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @else
        <div class="alert alert-info d-flex align-items-center">
            <i class="bi bi-person-badge-fill me-2"></i>
            Bienvenido al panel de administración. Desde aquí puedes gestionar los módulos clave.
        </div>
    @endif

    <div class="row g-4">

        {{-- ====== Productos ====== --}}
        @permiso('products.index')
        <div class="col-md-4">
            <div class="card bg-light border-primary shadow-sm h-100">
                <div class="card-body text-primary d-flex align-items-center">
                    <i class="bi bi-box-seam fs-1 me-3"></i>
                    <div>
                        <h5 class="card-title mb-1">Productos</h5>
                        <p class="card-text mb-0 small">
                            {{ $totalProducts }} en inventario
                        </p>
                        <a href="{{ route('products.index') }}"
                           class="btn btn-sm btn-outline-primary mt-2">
                           Ver productos
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endpermiso

        {{-- ====== Ventas ====== --}}
        @permiso('sales.index')
        <div class="col-md-4">
            <div class="card bg-light border-success shadow-sm h-100">
                <div class="card-body text-success d-flex align-items-center">
                    <i class="bi bi-cart-check fs-1 me-3"></i>
                    <div>
                        <h5 class="card-title mb-1">Ventas</h5>
                        <p class="card-text mb-0 small">
                            {{ $ventasHoy }} ventas hoy
                        </p>
                        <a href="{{ route('sales.index') }}"
                           class="btn btn-sm btn-outline-success mt-2">
                           Ver ventas
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endpermiso

        {{-- ====== Usuarios ====== --}}
        @permiso('users.index')
        <div class="col-md-4">
            <div class="card bg-light border-dark shadow-sm h-100">
                <div class="card-body text-dark d-flex align-items-center">
                    <i class="bi bi-people-fill fs-1 me-3"></i>
                    <div>
                        <h5 class="card-title mb-1">Usuarios</h5>
                        <p class="card-text mb-0 small">
                            {{ $totalUsuarios }} registrados
                        </p>
                        <a href="{{ route('usuarios.index') }}"
                           class="btn btn-sm btn-outline-dark mt-2">
                           Ver usuarios
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endpermiso

    </div>
</div>
@endsection
