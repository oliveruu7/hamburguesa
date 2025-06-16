@extends('layouts.admin')
@section('title','Editar Usuario')

@section('content')
<div class="container py-4">

    {{-- ===== Alertas de sesión ===== --}}
    @foreach (['success','error','info'] as $t)
        @if(session($t))
            <div class="alert alert-{{ $t=='success'?'success':($t=='error'?'danger':'warning') }}
                        alert-dismissible fade show" role="alert">
                <i class="bi
                   {{ $t=='success'?'bi-check-circle-fill':
                      ($t=='error'?'bi-exclamation-triangle-fill':'bi-info-circle-fill') }}"></i>
                {{ session($t) }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    {{-- ===== Errores de validación del servidor ===== --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <ul class="mb-0">
                @foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ===== Tarjeta ===== --}}
    <div class="card shadow border-0">
        <div class="card-header text-white" style="background:#00bcd4;">
            <h5 class="mb-0"><i class="bi bi-pencil-fill me-2"></i> Editar usuario</h5>
        </div>

        <div class="card-body">
            <form id="editForm" method="POST"
                  action="{{ route('usuarios.update',$usuario->idusuario) }}"
                  novalidate autocomplete="off">
                @csrf @method('PUT')

                <div class="row g-3">
                    {{-- ---------- NOMBRE ---------- --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-person-fill"></i> Nombre <span class="text-danger">*</span>
                        </label>
                        <input name="nombre" class="form-control"
                               value="{{ old('nombre',$usuario->nombre) }}"
                               maxlength="25"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]{1,25}" required>
                        <div class="invalid-feedback">Solo letras y espacios (máx 25).</div>
                    </div>

                    {{-- ---------- TELÉFONO ---------- --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"><i class="bi bi-telephone-fill"></i> Teléfono</label>
                        <input name="telefono" class="form-control"
                               value="{{ old('telefono',$usuario->telefono) }}"
                               pattern="\d{7,8}" maxlength="8" inputmode="numeric">
                        <div class="invalid-feedback">Solo números (7-8 dígitos).</div>
                    </div>

                    {{-- ---------- EMAIL ---------- --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-envelope-fill"></i> Email <span class="text-danger">*</span>
                        </label>
                        <input name="email" type="email" class="form-control"
                               value="{{ old('email',$usuario->email) }}"
                               maxlength="30"
                               pattern="^[A-Za-z0-9._%+-]{1,25}@gmail\.com$" required>
                        @error('email')
                            @if($message==='Este correo ya está registrado.')
                                {{-- se muestra arriba --}}
                            @else
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @endif
                        @else
                            <div class="invalid-feedback">Debe terminar en @gmail.com (máx 30).</div>
                        @enderror
                    </div>

                    {{-- ---------- PASSWORD ---------- --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-lock-fill"></i> Contraseña
                            <small class="text-muted">(vacío = no cambiar)</small>
                        </label>
                        <input name="password" type="password" class="form-control"
                               minlength="6" placeholder="Nueva contraseña (opcional)">
                        <div class="invalid-feedback">Mínimo 6 caracteres.</div>
                    </div>

                    {{-- ---------- PERFIL LINK ---------- --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"><i class="bi bi-link-45deg"></i> Enlace Perfil</label>
                        <input name="perfil_link" class="form-control"
                               value="{{ old('perfil_link',$usuario->perfil_link) }}"
                               minlength="10" placeholder="https://…">
                        <div class="invalid-feedback">URL no válida (mín 10).</div>
                    </div>

                    {{-- ---------- ROL ---------- --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-person-gear"></i> Rol <span class="text-danger">*</span>
                        </label>
                        <select name="idrol" class="form-select" required>
                            <option value="" disabled>Seleccione un rol…</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->idrol }}"
                                    {{ old('idrol',$usuario->idrol)==$rol->idrol?'selected':'' }}>
                                    {{ $rol->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Seleccione un rol válido.</div>
                    </div>
                </div>

                {{-- ---------- BOTONES ---------- --}}
                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary px-4">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                    <button id="btnGuardar" class="btn btn-primary px-4 opacity-50" disabled>
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
/* -------- Validación reactiva -------- */
const form = document.getElementById('editForm');
const btn  = document.getElementById('btnGuardar');
const opc  = ['telefono','perfil_link','password'];

form.addEventListener('input', validar);
form.addEventListener('change', validar);
validar();

function validar(){
    btn.disabled = !form.checkValidity();
    btn.classList.toggle('opacity-50',btn.disabled);

    [...form.elements].forEach(el=>{
        if(!['INPUT','SELECT','TEXTAREA'].includes(el.tagName)) return;
        const opcVacio = opc.includes(el.name)&&el.value.trim()==='';
        el.classList.remove('is-valid','is-invalid');               // limpia
        if(el.value.trim()==='') return;                            // vacío
        if(el.checkValidity() && !opcVacio) el.classList.add('is-valid');
        else if(!opcVacio) el.classList.add('is-invalid');
    });
}

/* -------- Filtros de entrada -------- */
const inputNombre   = document.querySelector('input[name="nombre"]');
const inputTelefono = document.querySelector('input[name="telefono"]');

inputNombre.addEventListener('input', () => {
    inputNombre.value = inputNombre.value
        .replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ ]+/g,'')
        .replace(/\s{2,}/g,' ');
});
inputTelefono.addEventListener('input', () => {
    inputTelefono.value = inputTelefono.value
        .replace(/\D+/g,'').slice(0,8);
});

/* -------- Desvanecer alertas -------- *//*
setTimeout(()=>document.querySelectorAll('.alert').forEach(a=>{
    a.classList.remove('show');a.classList.add('fade');}),3000);*/
</script>
@endpush
