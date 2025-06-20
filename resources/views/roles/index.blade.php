 @extends('layouts.admin')
@section('title', 'Lista de Roles')

@section('content')
<div class="container py-4">
  {{-- ===== Encabezado ===== --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3  class="fw-bold" style="color:#008080">
      <i class="bi bi-shield-lock-fill me-2"></i> Lista de Roles
    </h3>
    <a href="{{ route('roles.create') }}" class="btn text-white shadow-sm" style="background:rgb(53, 141, 75)">
      <i class="bi bi-plus-circle me-1"></i> Nuevo Rol
    </a>
  </div>

{{-- Mensajes Flash (éxito, error, info) --}}
@if($errors->any())
  <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-3 py-2 px-3" role="alert">
    <i class="bi bi-x-circle-fill fs-5"></i>
    <div class="flex-grow-1">
      <ul class="mb-0 ps-3">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-3 py-2 px-3" role="alert">
    <i class="bi bi-x-octagon-fill fs-5"></i>
    <div class="flex-grow-1">
      {{ session('error') }}
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
@endif

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-3 py-2 px-3" role="alert">
    <i class="bi bi-check-circle-fill fs-5"></i>
    <div class="flex-grow-1">
      {{ session('success') }}
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
@endif

@if(session('info'))
  <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-3 py-2 px-3" role="alert">
    <i class="bi bi-info-circle-fill fs-5"></i>
    <div class="flex-grow-1">
      {{ session('info') }}
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
@endif

  

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
              <a href="{{ route('roles.edit', $rol) }}" class="btn btn-sm btn-outline-warning">
                <i class="bi bi-pencil-fill"></i>
              </a>

              <form action="{{ route('roles.destroy', $rol) }}" method="POST" class="d-inline form-delete">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">
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
