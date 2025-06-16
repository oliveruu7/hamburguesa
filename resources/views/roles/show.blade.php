@extends('layouts.admin')
@section('title','Detalle de Rol')

@section('content')
<div class="container py-4">
    <div class="card shadow border-0">
        <div class="card-header" style="background:#6f42c1;color:#fff;">
            <h5 class="mb-0"><i class="bi bi-eye-fill me-2"></i> Detalle del Rol</h5>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Nombre:</dt>
                <dd class="col-sm-9">{{ $rol->nombre }}</dd>

                <dt class="col-sm-3">Descripción:</dt>
                <dd class="col-sm-9">{{ $rol->descripcion ?? 'Sin descripción' }}</dd>

                <dt class="col-sm-3">Estado:</dt>
                <dd class="col-sm-9">
                    @if($rol->estado)
                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> Activo</span>
                    @else
                        <span class="text-danger"><i class="bi bi-x-circle-fill"></i> Inactivo</span>
                    @endif
                </dd>
            </dl>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary mt-3">
                <i class="bi bi-arrow-left-circle"></i> Volver
            </a>
        </div>
    </div>
</div>
@endsection
