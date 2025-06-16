{{-- resources/views/usuarios/create.blade.php --}}
@extends('layouts.admin')
@section('title','Crear Usuario')

@section('content')
<div class="container py-4">

    {{-- ===== Alertas de sesión ===== --}}
    @foreach (['success','error'] as $t)
        @if(session($t))
            <div class="alert alert-{{ $t=='success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
                <i class="bi {{ $t=='success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }}"></i>
                {{ session($t) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    {{-- ===== Errores de validación del servidor (correo duplicado, etc.) ===== --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <ul class="mb-0">
                @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ===== Formulario ===== --}}
    <div class="card shadow border-0">
        <div class="card-header text-white" style="background:#00bcd4;">
            <h5 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i> Crear nuevo usuario</h5>
        </div>

        <div class="card-body">
            <form id="createForm" method="POST" action="{{ route('usuarios.store') }}" novalidate>
                @csrf
                <div class="row g-3">
                    {{-- ---------- NOMBRE ---------- --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"><i class="bi bi-person-fill"></i> Nombre <span class="text-danger">*</span></label>
                        <input name="nombre" class="form-control" value="{{ old('nombre') }}"
                               maxlength="25" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]{1,25}" required>
                        <div class="valid-feedback">Correcto</div>
                        <div class="invalid-feedback">Solo letras y espacios (máx 25).</div>
                    </div>

                    {{-- ---------- TELÉFONO ---------- --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"><i class="bi bi-telephone-fill"></i> Teléfono</label>
                        <input name="telefono" class="form-control" value="{{ old('telefono') }}"
                               pattern="\d{7,8}" maxlength="8" inputmode="numeric">
                        <div class="invalid-feedback">Solo números (7-8 dígitos).</div>
                    </div>

                    {{-- ---------- EMAIL ---------- --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"><i class="bi bi-envelope-fill"></i> Email <span class="text-danger">*</span></label>
                        <input name="email" type="email" class="form-control"
                               value="{{ old('email') }}" maxlength="30"
                               pattern="^[A-Za-z0-9._%+-]{1,25}@gmail\.com$"
                               autocomplete="off" required>
                        <div class="valid-feedback">Correcto</div>
                        <div class="invalid-feedback">Por favor, ingrese un correo electrónico válido.</div>
                    </div>

                    {{-- ---------- PASSWORD ---------- --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"><i class="bi bi-lock-fill"></i> Contraseña <span class="text-danger">*</span></label>
                        <input name="password" type="password" class="form-control"
                               minlength="6" required>
                        <div class="valid-feedback">Correcto</div>
                        <div class="invalid-feedback">Mínimo 6 caracteres.</div>
                    </div>

                    {{-- ---------- PERFIL LINK ---------- --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"><i class="bi bi-link-45deg"></i> Enlace Perfil</label>
                        <input name="perfil_link" class="form-control" value="{{ old('perfil_link') }}"
                               minlength="10" placeholder="https://mi-perfil.com">
                        <div class="invalid-feedback">URL no válida o menor a 10 (opcional).</div>
                    </div>

                    {{-- ---------- ROL ---------- --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"><i class="bi bi-person-gear"></i> Rol <span class="text-danger">*</span></label>
                        <select name="idrol" class="form-select" required>
                            <option value="" disabled {{ old('idrol') ? '' : 'selected' }}>Seleccione un rol…</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->idrol }}" {{ old('idrol')==$rol->idrol ? 'selected' : '' }}>
                                    {{ $rol->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <div class="valid-feedback">Correcto</div>
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
/* ---------- Validación reactiva ---------- */
const form = document.getElementById('createForm');
const btn  = document.getElementById('btnGuardar');
const opc  = ['telefono','perfil_link'];

form.addEventListener('input', validar);
form.addEventListener('change', validar);
validar();

function validar(){
    btn.disabled = !form.checkValidity();
    btn.classList.toggle('opacity-50', btn.disabled);

    [...form.elements].forEach(el=>{
        if(!['INPUT','SELECT','TEXTAREA'].includes(el.tagName)) return;
        const opcVacio = opc.includes(el.name) && el.value.trim()==='';

        /* limpiar previo */
        el.classList.remove('is-valid','is-invalid');

        /* aplicar según estado */
        if(el.value.trim()==='') return;                 // vacío: no marcar nada
        if(el.checkValidity() && !opcVacio){
            el.classList.add('is-valid');
        }else if(!opcVacio){
            el.classList.add('is-invalid');
        }
    });
}

/* ---------- ENTRADAS RESTRINGIDAS ---------- */
const inputNombre   = document.querySelector('input[name="nombre"]');
const inputTelefono = document.querySelector('input[name="telefono"]');

/* Solo letras y espacios (tildes y ñ incluidas) */
inputNombre.addEventListener('input', () => {
    inputNombre.value = inputNombre.value
        .replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ ]+/g, '')     // elimina números y símbolos
        .replace(/\s{2,}/g, ' ');                    // colapsa espacios dobles
});

/* Solo dígitos (máx 8) */
inputTelefono.addEventListener('input', () => {
    inputTelefono.value = inputTelefono.value
        .replace(/\D+/g, '')        // quita todo lo que no sea número
        .slice(0, 8);               // limita a 8 caracteres
});
</script>
@endpush
