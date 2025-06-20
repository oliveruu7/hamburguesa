{{-- resources/views/usuarios/show.blade.php --}}
@extends('layouts.admin')
@section('title','Perfil de Usuario')

@section('content')
<div class="container mt-5">
    <div class="card shadow border-0">
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background:#2471a3;">

            <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i> Perfil del Usuario</h5>
            <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-light">
                <i class="bi bi-arrow-left-circle"></i> Volver
            </a>
        </div>

        <div class="card-body">
            <div class="row g-4 align-items-center">

                {{-- ========== PERFIL (foto + link) ========== --}}
                <div class="col-md-3 text-center">
                    <img src="{{ $usuario->perfil_link ?: 'https://cdn-icons-png.flaticon.com/512/149/149071.png' }}"
                         class="rounded-circle img-thumbnail"
                         width="120" height="120"
                         alt="Foto de perfil">

                    <div class="mt-3">
                        @if($usuario->perfil_link)
                           
                        @else
                            <span class="badge bg-secondary w-100">Sin perfil asignado</span>
                        @endif
                    </div>
                </div>

                {{-- ========== DATOS PERSONALES ========== --}}
                <div class="col-md-9">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label text-muted"><i class="bi bi-person-fill"></i> Nombre:</label>
                            <div class="form-control">{{ $usuario->nombre }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted"><i class="bi bi-envelope-fill"></i> Email:</label>
                            <div class="form-control">{{ $usuario->email }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted"><i class="bi bi-phone-vibrate-fill"></i> Teléfono:</label>
                            <div class="form-control">{{ $usuario->telefono ?? 'No registrado' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted"><i class="bi bi-geo-alt-fill"></i> Dirección:</label>
                            <div class="form-control">{{ $usuario->direccion ?? 'No registrada' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted"><i class="bi bi-person-gear"></i> Rol:</label>
                            <div class="form-control">{{ $usuario->rol->nombre }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted"><i class="bi bi-toggle-on"></i> Estado:</label>
                            <div class="form-control">
                                @if($usuario->estado)
                                    <span class="text-success"><i class="bi bi-check-circle-fill"></i> Activo</span>
                                @else
                                    <span class="text-danger"><i class="bi bi-x-circle-fill"></i> Inactivo</span>
                                @endif
                            </div>
                        </div>

                    </div>  {{-- row --}}
                </div>

            </div> {{-- row g-4 --}}
        </div>
    </div>
</div>
@endsection
