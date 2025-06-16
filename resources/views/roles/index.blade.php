 @extends('layouts.admin')
@section('title', 'Lista de Roles')

@section('content')
<div class="container py-4">
  {{-- ===== Encabezado ===== --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3  style="color:#6f42c1">
      <i class="bi bi-shield-lock-fill me-2"></i> Lista de Roles
    </h3>
    <a href="{{ route('roles.create') }}" class="btn text-white shadow-sm" style="background: #6f42c1">
      <i class="bi bi-plus-circle me-1"></i> Nuevo Rol
    </a>
  </div>

  {{-- ===== Alertas ===== --}}
  @foreach(['success','error','info'] as $t)
    @if(session($t))
      <div class="alert alert-{{ $t == 'success' ? 'success' : ($t == 'error' ? 'danger' : 'warning') }} alert-dismissible fade show">
        {{ session($t) }}
        <button class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
  @endforeach

  {{-- ===== Tabla de Roles ===== --}}
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle bg-white shadow-sm">
     <thead class="table-primary" >
        <tr>
          <th>#</th>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Estado</th>
          <th>Acciones</th>
          </tr>
      </thead>

      <tbody class="text-center">
        @forelse($roles as $rol)
          <tr>
            <td>{{ $rol->idrol }}</td>
            <td>{{ $rol->nombre }}</td>
            <td>{{ $rol->descripcion ?? 'Sin descripción' }}</td>
            <td>
              <span class="badge {{ $rol->estado ? 'bg-success' : 'bg-secondary' }}">
                {{ $rol->estado ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td>
              <a href="{{ route('roles.edit', $rol) }}" class="btn btn-sm text-white" style="background: #6f42c1">
                <i class="bi bi-pencil-square"></i>
              </a>

              <form action="{{ route('roles.destroy', $rol) }}" method="POST" class="d-inline form-delete">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">
                  <i class="bi bi-trash-fill"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-muted">No hay roles registrados.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Confirmación de eliminación de roles
document.querySelectorAll('.form-delete').forEach(form => {
  form.addEventListener('submit', e => {
    e.preventDefault();
    Swal.fire({
      title: '¿Eliminar rol?',
      text: 'Esta acción desactivará el rol.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#e3342f',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(result => {
      if (result.isConfirmed) form.submit();
    });
  });
});
</script>
@endpush
