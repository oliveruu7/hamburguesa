@extends('layouts.admin')
@section('title','Crear Rol')

@section('content')
<div class="container py-4">
  {{-- errores --}}
  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <form action="{{ route('roles.store') }}" method="POST">
    @csrf
    <div class="card shadow border-0 mb-4">
      <div class="card-header" style="background:#6f42c1;color:#fff">
        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i> Crear un Rol</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Nombre *</label>
            <input id="input-nombre" name="nombre" class="form-control" required value="{{ old('nombre') }}">
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

    <button class="btn text-white" style="background:#6f42c1">
        <i class="bi bi-save2"></i> Guardar
    </button>
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
@endsection

@push('js')
<script>
  const yes = document.getElementById('fa_yes');
  const no = document.getElementById('fa_no');
  const cb = document.querySelectorAll('.perm');
  function toggle(d){ cb.forEach(x=>{x.disabled=d; if(d) x.checked=true;}); }
  toggle(yes && yes.checked);
  yes && yes.addEventListener('change',()=>toggle(true));
  no  && no .addEventListener('change',()=>toggle(false));

  // Solo letras para el input nombre
  document.getElementById('input-nombre').addEventListener('keypress', function(e) {
    const char = String.fromCharCode(e.keyCode);
    const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
    if (!regex.test(char)) {
      e.preventDefault();
    }
  });
</script>
@endpush

