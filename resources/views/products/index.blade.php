@extends('layouts.admin')
@section('title','Productos')

@section('content')
<div class="container-fluid">

    {{-- Encabezado --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark">
                <i class="bi bi-box-seam me-2 text-primary"></i> Productos registrados
            </h2>
        </div>
        <div class="col-md-6 text-md-end">
            @permiso('products.create')
                <a href="{{ route('products.create') }}" class="btn btn-success shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo producto
                </a>
            @endpermiso
        </div>
    </div>

    {{-- Buscador --}}
    <div class="row mb-3">
        <div class="col-md-12">
        <form method="GET" class="input-group shadow-sm">
           <button type="submit" class="btn btn-outline-secondary">
           <i class="bi bi-search"></i>
           </button>
          <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar por nombre o categoría…">
            @if(request('q'))
              <a href="{{ route('products.index') }}" class="btn btn-outline-danger">Limpiar</a>
            @endif
        </form>
        </div>
    </div>

    {{-- Alertas --}}
    @foreach (['success', 'error'] as $msg)
        @if(session($msg))
            <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
                {{ session($msg) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
    @endforeach

    {{-- Tabla --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                        <tr>
                            <td>{{ $p->idhamburguesa }}</td>
                            <td>
                                <img src="{{ $p->imagenUrl ?? 'https://via.placeholder.com/60x45?text=Img' }}"
                                     class="img-thumbnail"
                                     style="width: 60px; height: 45px; object-fit: cover;">
                            </td>
                            <td class="text-start fw-semibold">{{ $p->nombre }}</td>
                            <td>{{ $p->categoria->nombre ?? '-' }}</td>
                            <td class="text-start">{{ Str::limit($p->descripcion, 50, '…') }}</td>
                            <td class="text-end fw-bold text-success">Bs {{ number_format($p->precio_unitario, 2) }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('products.show', $p) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    @permiso('products.edit')
                                        <a href="{{ route('products.edit', $p) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                    @endpermiso
                                    @permiso('products.delete')
                                    <form id="form-delete-{{ $p->idhamburguesa }}" action="{{ route('products.destroy', $p) }}" method="POST" class="d-inline">
                                      @csrf @method('DELETE')
                                      <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                        onclick="confirmarEliminacion({{ $p->idhamburguesa }})">
                                        <i class="bi bi-trash-fill"></i>
                                      </button>
                                    </form>
                                    @endpermiso
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No se encontraron productos</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginación --}}
    <div class="mt-3 d-flex justify-content-end">
        {{ $products->links() }}
    </div>

</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Este producto será desactivado.",
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
