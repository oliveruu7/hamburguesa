 @extends('layouts.admin')
@section('title','Clientes')

@section('content')
<div class="container py-4">
    {{-- === Encabezado + botón === --}}
    <div class="d-flex justify-content-between flex-wrap align-items-center mb-3">
        <h3 class="fw-bold" style="color:#008080">
            <i class="bi bi-people-fill me-2"></i> Lista de Clientes
        </h3>
        @permiso('clientes.create')
        <a href="{{ route('clientes.create') }}" class="btn btn-success shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Nuevo Cliente
        </a>
        @endpermiso
    </div>

    {{-- === Alertas globales === --}}
    @foreach (['success','error'] as $t)
        @if(session($t))
            <div class="alert alert-{{ $t=='success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
                <i class="bi {{ $t=='success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }}"></i>
                {{ session($t) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    {{-- === Tabla === --}}
    <div class="table-responsive shadow-sm">
        <table class="table table-hover table-bordered align-middle bg-white">
            <thead class="table-primary text-center">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>CI</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @forelse($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->idcliente }}</td>
                    <td class="fw-semibold">{{ $cliente->nombre }}</td>
                    <td>{{ $cliente->ci }}</td>
                    <td>{{ $cliente->telefono ?? 'No registrado' }}</td>
                    <td>
                        <div class="btn-group">
                            @permiso('clientes.edit')
                            <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            @endpermiso

                            @permiso('clientes.delete')
                            <button class="btn btn-sm btn-outline-danger" onclick="confirmarEliminacion({{ $cliente->idcliente }})" title="Eliminar">
                                <i class="bi bi-trash-fill"></i>
                            </button>

                            <form id="form-delete-{{ $cliente->idcliente }}" method="POST" action="{{ route('clientes.destroy', $cliente) }}" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                            @endpermiso
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-muted">No hay clientes registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- === Paginación === --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $clientes->withQueryString()->links() }}
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Este cliente será desactivado.",
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
</script>
@endpush
