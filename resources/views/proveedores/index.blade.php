{{-- resources/views/proveedores/index.blade.php --}}
@extends('layouts.admin')
@section('title','Proveedores')

@section('content')
<div class="container py-4">

    {{-- ——— Encabezado + botón ——— --}}
    <div class="d-flex justify-content-between flex-wrap align-items-center mb-3">
        <h3 class="fw-bold" style="color:#008080">
            <i class="bi bi-truck me-2"></i> Lista de Proveedores
        </h3>
        @permiso('proveedores.create')
        <a href="{{ route('proveedores.create') }}" class="btn btn-success shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Nuevo Proveedor
        </a>
        @endpermiso
    </div>

    {{-- ——— Alertas flash ——— --}}
    @foreach (['success','error'] as $t)
        @if(session($t))
            <div class="alert alert-{{ $t == 'success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
                <i class="bi {{ $t == 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }} me-1"></i>
                {{ session($t) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    {{-- ——— Tabla ——— --}}
    <div class="table-responsive shadow-sm">
        <table class="table table-hover table-bordered align-middle bg-white">
            <thead class="table-primary text-center">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @forelse($proveedores as $prov)
                <tr>
                    <td>{{ $prov->idproveedor }}</td>
                    <td class="fw-semibold">{{ $prov->nombre }}</td>
                    <td>{{ $prov->telefono ?? 'No registrado' }}</td>
                    <td>{{ $prov->email ?? 'No registrado' }}</td>
                    <td>
                        <div class="btn-group">
                            @permiso('proveedores.edit')
                            <a href="{{ route('proveedores.edit', $prov) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            @endpermiso

                            @permiso('proveedores.delete')
                            <form method="POST" action="{{ route('proveedores.destroy', $prov) }}" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete" title="Eliminar">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                            @endpermiso
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-muted py-3">No hay proveedores registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ——— Paginación ——— --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $proveedores->links() }}
    </div>
</div>
@endsection

@push('js')
{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
/* ===== Autocerrar alertas de Bootstrap ===== */
setTimeout(() => {
  document.querySelectorAll('.alert-dismissible').forEach(el => {
      const alert = bootstrap.Alert.getOrCreateInstance(el);
      alert.close();
  });
}, 4000);

/* ===== SweetAlert para eliminación ===== */
document.querySelectorAll('.btn-delete').forEach(btn => {
  btn.addEventListener('click', function () {
      const form = this.closest('form');

      Swal.fire({
          title: '¿Eliminar proveedor?',
          text: "Esta acción no se puede deshacer.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Sí, eliminar',
          cancelButtonText: 'Cancelar'
      }).then((result) => {
          if (result.isConfirmed) form.submit();
      });
  });
});
</script>
@endpush
