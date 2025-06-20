{{-- resources/views/proveedores/edit.blade.php --}}
@extends('layouts.admin')
@section('title','Editar proveedor')

@section('content')
<div class="container py-4">

    {{-- ─── ALERTA GLOBAL DE VALIDACIÓN ─── --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Corrige los siguientes campos:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- alertas flash de éxito/error/info (opcional) --}}
    @foreach(['success','info'] as $t)
        @if(session($t))
            <div class="alert alert-{{ $t=='success' ? 'success' : 'info' }} alert-dismissible fade show" role="alert">
                {{ session($t) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <div class="card shadow border-0">
        <div class="card-header text-white" style="background-color:#f39c12;">
            <h5 class="mb-0"><i class="bi bi-pencil-fill me-2"></i> Editar proveedor</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('proveedores.update',$proveedor) }}" novalidate>
                @csrf @method('PUT')

                {{-- Nombre --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Nombre <span class="text-danger">*</span>
                    </label>
                    <input  name="nombre" id="nombre"
                            value="{{ old('nombre',$proveedor->nombre) }}"
                            maxlength="20"
                            class="form-control @error('nombre') is-invalid @enderror"
                            required>
                     
                </div>

                {{-- Teléfono --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input  name="telefono" id="telefono"
                            value="{{ old('telefono',$proveedor->telefono) }}"
                            maxlength="8"
                            class="form-control @error('telefono') is-invalid @enderror">
                     
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input  name="email" type="email"
                            value="{{ old('email',$proveedor->email) }}"
                            class="form-control @error('email') is-invalid @enderror">
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('proveedores.index') }}" class="btn btn-secondary px-4">
                        <i class="bi bi-x-circle-fill me-1"></i> Cancelar
                    </a>
                    <button class="btn btn-primary px-4">
                        <i class="bi bi-save-fill me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
/* ====== Validación en vivo (igual que antes) ====== */
const nombreInput   = document.getElementById('nombre');
const telefonoInput = document.getElementById('telefono');
let touchedNombre = false, touchedTel = false;

nombreInput.addEventListener('input', () => {
  touchedNombre = true;
  nombreInput.value = nombreInput.value.replace(/[^A-Za-zÁÉÍÓÚÜáéíóúüÑñ ]/g,'');
  validarNombre();
});
nombreInput.addEventListener('blur', ()=>{ touchedNombre=true; validarNombre(); });

telefonoInput.addEventListener('input', () => {
  touchedTel = true;
  telefonoInput.value = telefonoInput.value.replace(/\D/g,'');
  validarTel();
});
telefonoInput.addEventListener('blur', ()=>{ touchedTel=true; validarTel(); });

function validarNombre(){
  if (!touchedNombre) return;
  const len = nombreInput.value.length;
  toggleInvalid(nombreInput, len===0 || len>20);
}
function validarTel(){
  if (!touchedTel) return;
  const len = telefonoInput.value.length;
  toggleInvalid(telefonoInput, len!==0 && (len<7 || len>8));
}
function toggleInvalid(el, condition){
  el.classList.toggle('is-invalid', condition);
  el.classList.toggle('is-valid', !condition);
}

/* autocerrar alertas */
setTimeout(()=>{document.querySelectorAll('.alert-dismissible').forEach(a=>bootstrap.Alert.getOrCreateInstance(a).close());},4000);
</script>
@endpush
