 @extends('layouts.admin')
@section('title','Insumos registrados')

@section('content')
<div class="container-fluid">

    {{-- Encabezado --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold" style="color:#008080">
                <i class="bi bi-archive me-2"></i> Insumos registrados en Alamacén
            </h2>
        </div>
        <div class="col-md-6 text-end">
            @permiso('insumos.create')
                <a href="{{ route('insumos.create') }}" class="btn btn-success shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Crear nuevo insumo
                </a>
            @endpermiso
        </div>
    </div>

    {{-- Buscador --}}
    <form method="GET" class="mb-3">
        <div class="input-group shadow-sm">
            <input name="q" value="{{ request('q') }}" class="form-control solo-letras" placeholder="Buscar insumo…">
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-search"></i>
            </button>
            @if(request('q'))
                <a href="{{ route('insumos.index') }}" class="btn btn-outline-danger">Limpiar</a>
            @endif
        </div>
    </form>

    {{-- === ALERTAS DE SESIÓN === --}}
@foreach (['success' => 'success',
           'error'   => 'danger',
           'info'    => 'info',
           'warning' => 'warning'] as $key => $color)
  @if (session($key))
    <div class="alert alert-{{ $color }} alert-dismissible fade show" role="alert">
      {{ session($key) }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
@endforeach


    {{-- Tabla de insumos --}}
    <div class="card shadow border-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle text-center mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th class="text-start">Nombre</th>
                        <th>Unidad</th>
                        <th class="text-start">Descripción</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($insumos as $i)
                        <tr>
                            <td>{{ $i->idinsumo }}</td>
                            <td class="text-start fw-semibold">{{ $i->nombre }}</td>
                            <td>{{ $i->unidad }}</td>
                            <td class="text-start">{{ Str::limit($i->descripcion, 50, '…') }}</td>
                            <td class="fw-bold text-success">{{ number_format($i->stock_actual) }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    @permiso('insumos.edit')
                                        <a href="{{ route('insumos.edit', $i) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                    @endpermiso

                                    @permiso('insumos.delete')
                                        <form id="form-delete-{{ $i->idinsumo }}" action="{{ route('insumos.destroy', $i) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="confirmarEliminacion({{ $i->idinsumo }})">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    @endpermiso
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay insumos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginación --}}
    @if ($insumos->hasPages())
        <div class="mt-4 d-flex justify-content-end">
            {{ $insumos->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Este insumo será eliminado del sistema.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        customClass: {
            title: 'fw-bold',
            confirmButton: 'px-4',
            cancelButton: 'px-4'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-delete-' + id).submit();
        }
    });
}

// ✅ Evitar espacio al inicio de inputs y bloquear SPACE como primer carácter
document.querySelectorAll("input[type='text'], input[type='search'], textarea").forEach(el => {
    // Bloquea espacio como primer carácter con keydown
    el.addEventListener('keydown', function(e) {
        if (e.key === ' ' && this.selectionStart === 0) {
            e.preventDefault();
        }
    });

    // Limpia espacios iniciales si se pegan o escriben
    el.addEventListener('input', function () {
        this.value = this.value.replace(/^\s+/, '');
    });
});

// ✅ Solo letras para inputs con clase .solo-letras
document.querySelectorAll(".solo-letras").forEach(el => {
    el.addEventListener('input', function () {
        this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]/g, '');
    });
});
</script>
@endpush
