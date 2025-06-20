{{-- resources/views/roles/edit.blade.php --}}
@extends('layouts.admin')
@section('title','Editar Rol')

@section('content')
<div class="container py-4">

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-octagon-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

  {{-- ─── Mensajes de error ─── --}}
  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

 @if(session('info'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif


  <form action="{{ route('roles.update',$rol->idrol) }}" method="POST">
    @csrf @method('PUT')

    {{-- ───────── DATOS BÁSICOS ───────── --}}
    <div class="card shadow border-0 mb-4">
      <div class="card-header" style="background:#f39c12;color:#fff">
        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Editar Rol</h5>
      </div>

      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Nombre *</label>
            <input name="nombre" class="form-control" required
                   value="{{ old('nombre',$rol->nombre) }}">
          </div>
          <div class="col-md-8">
            <label class="form-label fw-semibold">Descripción</label>
            <input name="descripcion" class="form-control"
                   value="{{ old('descripcion',$rol->descripcion) }}">
          </div>
        </div>

        {{-- ─── Acceso completo ─── --}}
        <h5 class="text-center my-3">Acceso completo</h5>
        <div class="d-flex justify-content-center gap-4 mb-2">
          @php $fa = old('full_access',
                         $rol->permisos->isEmpty() ? 'yes' : 'no'); @endphp

          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="full_access"
                   value="yes" id="fa_yes" {{ $fa=='yes'?'checked':'' }}>
            <label class="form-check-label" for="fa_yes">Sí</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="full_access"
                   value="no" id="fa_no" {{ $fa=='no'?'checked':'' }}>
            <label class="form-check-label" for="fa_no">No</label>
          </div>
        </div>
      </div>
    </div>

    {{-- ───────── PERMISOS ───────── --}}
    <div class="row g-3 mb-3">
      @foreach($grupos as $mod=>$permisos)
        <div class="col-md-4">
          <div class="card shadow-sm h-100">
            <div class="card-header fw-bold" style="background:#f8f9fa">
              {{ ucwords(str_replace('_',' ',$mod)) }}
            </div>

            <div class="card-body" style="max-height:260px;overflow:auto">
              @foreach($permisos as $p)
                @php
                  $checked = in_array(
                      $p->idpermiso,
                      old('permisos',$rol->permisos->pluck('idpermiso')->toArray())
                  );
                @endphp
                <div class="form-check">
                  <input class="form-check-input perm" type="checkbox"
                         name="permisos[]" value="{{ $p->idpermiso }}"
                         id="perm{{ $p->idpermiso }}" {{ $checked?'checked':'' }}>
                  <label class="form-check-label small text-muted"
                         for="perm{{ $p->idpermiso }}">
                    {{ $p->nombre }}
                    @if($p->descripcion)<em>({{ $p->descripcion }})</em>@endif
                  </label>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- ───────── BOTONES ───────── --}}
    <button class="btn text-white" style="background:#2471a3">
        <i class="bi bi-save2"></i> Actualizar
    </button>
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
@endsection

@push('js')
<script>
/* Habilita / deshabilita check‑boxes según “Acceso completo” */
const yes = document.getElementById('fa_yes');
const no  = document.getElementById('fa_no');
const cb  = document.querySelectorAll('.perm');

function toggle(disabled){
  cb.forEach(x=>{
      x.disabled = disabled;
      if(disabled) x.checked = true;      // marca todos si es acceso total
  });
}
toggle(yes.checked);          // al cargar

yes.addEventListener('change',()=>toggle(true));
no .addEventListener('change',()=>toggle(false));
</script>
@endpush
