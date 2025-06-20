@extends('layouts.admin')
@section('title','Crear Rol')

@section('content')
<div class="container py-4">
  
{{-- errores de validación --}}
@if($errors->any())
  <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-3 py-2 px-3" role="alert" style="font-size: 0.95rem;">
    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
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

{{-- errores de sesión personalizados --}}
@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-3 py-2 px-3" role="alert" style="font-size: 0.95rem;">
    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
    <div class="flex-grow-1">
      {{ session('error') }}
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
@endif



  <form action="{{ route('roles.store') }}" method="POST">
    @csrf
    <div class="card shadow border-0 mb-4">
      <div class="card-header" style="background:#2e8b57;color:#fff">
        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i> Crear un Rol</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold"> Nombre <span class="text-danger">*</span>
            </label>

            <input id="input-nombre" name="nombre" class="form-control" maxlength="15" required value="{{ old('nombre') }}">
          </div>
          <div class="col-md-8">
            <label class="form-label fw-semibold">Descripción</label>
            <input name="descripcion" class="form-control" value="{{ old('descripcion') }}">
          </div>
        </div>

        <h5 class="text-center my-3">Acceso completo</h5>
        <div class="d-flex justify-content-center gap-4 mb-2">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="full_access"
                   value="yes" id="fa_yes" {{ old('full_access')=='yes'?'checked':'' }}>
            <label class="form-check-label" for="fa_yes">Sí</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="full_access"
                   value="no" id="fa_no" {{ old('full_access','no')=='no'?'checked':'' }}>
            <label class="form-check-label" for="fa_no">No</label>
          </div>
        </div>
      </div>
    </div>

    {{-- permisos --}}
    <div class="row g-3 mb-3">
      @foreach($grupos as $mod=>$permisos)
        <div class="col-md-4">
          <div class="card shadow-sm h-100">
            <div class="card-header fw-bold" style="background:#f8f9fa">
              {{ ucwords(str_replace('_',' ',$mod)) }}
            </div>
            <div class="card-body" style="max-height:260px;overflow:auto">
              @foreach($permisos as $p)
                <div class="form-check">
                  <input class="form-check-input perm" type="checkbox"
                         name="permisos[]" value="{{ $p->idpermiso }}"
                         id="perm{{ $p->idpermiso }}"
                         {{ in_array($p->idpermiso, old('permisos',[]))?'checked':'' }}>
                  <label class="form-check-label small text-muted" for="perm{{ $p->idpermiso }}">
                    {{ $p->nombre }} @if($p->descripcion)<em>({{ $p->descripcion }})</em>@endif
                  </label>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <button class="btn text-white" style="background:#2e8b57">
        <i class="bi bi-save2"></i> Guardar
    </button>
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
@endsection

@push('js')
<script>
/* ---------- Referencias ---------- */
const yes = document.getElementById('fa_yes');   // radio "Sí"
const no  = document.getElementById('fa_no');    // radio "No"
const perms = document.querySelectorAll('.perm'); // todos los check-box

/* ---------- Lógica ---------- */
function togglePermisos(full) {
    perms.forEach(cb => {
        cb.disabled = full;          // deshabilita si es acceso completo
        cb.checked  = full ? true : false; // ✔️ marca todos o los limpia
    });
}

/* ---------- Estado inicial ---------- */
togglePermisos(yes?.checked);        // si "Sí" ya viene marcado, actúa

/* ---------- Listeners ---------- */
yes?.addEventListener('change', () => togglePermisos(true));
no ?.addEventListener('change', () => togglePermisos(false));

/* ---------- Validación campo “Nombre” ---------- */
const inputNombre = document.getElementById('input-nombre');
const soloLetras  = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]$/;

inputNombre.addEventListener('keypress', e => {
    if (!soloLetras.test(e.key) || inputNombre.value.length >= 15) {
        e.preventDefault();          // bloquea caracteres inválidos o extra
    }
});
inputNombre.addEventListener('input', () => {
    inputNombre.value = inputNombre.value
                          .replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]+/g,'')
                          .slice(0,15);
});
</script>


@endpush

