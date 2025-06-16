{{-- resources/views/usuarios/index.blade.php --}}
@extends('layouts.admin')
@section('title','Usuarios')

@section('content')
<div class="container py-4">

    {{-- ===== Encabezado + botón ===== --}}
    <div class="d-flex justify-content-between flex-wrap align-items-center mb-3">
        <h3 class="text-primary">
            <i class="bi bi-people-fill me-2"></i> Lista de Usuarios
        </h3>
        <a href="{{ route('usuarios.create') }}" class="btn btn-success shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Nuevo Usuario
        </a>
    </div>

    {{-- ===== Alertas globales (éxito / error) ===== --}}
    @foreach (['success','error'] as $t)
        @if(session($t))
            <div class="alert alert-{{ $t=='success' ? 'success' : 'danger' }} alert-dismissible fade show"
                 role="alert">
                <i class="bi {{ $t=='success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }}"></i>
                {{ session($t) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    {{-- ===== Buscador ===== --}}
    <form action="{{ route('usuarios.index') }}" method="GET"
          class="input-group mb-3 shadow-sm" onsubmit="return validarInput()">
        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
        <input id="buscar" name="buscar" class="form-control"
               value="{{ request('buscar') }}"
               maxlength="25"
               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ0-9@._\- ]{0,25}"
               placeholder="Buscar por nombre o email…">
        <button class="btn btn-outline-primary">Buscar</button>
    </form>

    {{-- ===== Tabla ===== --}}
    <div class="table-responsive shadow-sm">
        <table class="table table-hover table-bordered align-middle bg-white">
            <thead class="table-primary text-center">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Perfil</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="text-center">
            @forelse($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->idusuario }}</td>
                    <td class="fw-semibold">{{ $usuario->nombre }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->telefono ?? 'No registrado' }}</td>
                    <td>
                        @if($usuario->perfil_link)
                            <a href="{{ $usuario->perfil_link }}" target="_blank">
                                <img src="{{ $usuario->perfil_link }}"
                                     class="rounded-circle" width="40" height="40" alt="Perfil">
                            </a>
                        @else
                            <span class="text-muted">No asignado</span>
                        @endif
                    </td>
                    <td>{{ $usuario->rol->nombre }}</td>

                    {{-- Acciones --}}
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('usuarios.show',$usuario) }}"
                               class="btn btn-sm btn-outline-info" title="Ver">
                               <i class="bi bi-eye-fill"></i></a>

                            <a href="{{ route('usuarios.edit',$usuario) }}"
                               class="btn btn-sm btn-outline-warning" title="Editar">
                               <i class="bi bi-pencil-fill"></i></a>

                            @php $esPropio = $usuario->idusuario == Auth::id(); @endphp
                             <form id="form-delete-{{ $usuario->idusuario }}"
      action="{{ route('usuarios.destroy',$usuario) }}"
      method="POST" style="display:inline;">
    @csrf
    @method('DELETE')

    <button type="button"
            onclick="confirmarEliminacion({{ $usuario->idusuario }})"
            class="btn btn-sm btn-outline-danger"
            title="{{ $esPropio ? 'No puedes eliminar tu propia cuenta' : 'Inactivar' }}"
            {{ $esPropio ? 'disabled' : '' }}>
        <i class="bi bi-trash-fill"></i>
    </button>
</form>

                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-muted">No se encontraron usuarios.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- ===== Paginación ===== --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $usuarios->withQueryString()->links() }}
    </div>
</div>
@endsection

@push('js')
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Este usuario será desactivado.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e3342f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-delete-' + id).submit();
        }
    });
}
  /* Validar buscador */
    function validarInput(){
        const v=document.getElementById('buscar').value;
        if(!/^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9@._\- ]{0,25}$/.test(v)){
            alert('Sólo letras, números, puntos y @ (máx 25).');return false;
        }
        return true;
    }

</script>
@endpush



 