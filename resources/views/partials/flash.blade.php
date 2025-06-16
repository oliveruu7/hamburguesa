{{-- resources/views/partials/toast.blade.php --}}

@php
    /*  Muestra:
        • session('success')  → verde
        • session('error')    → rojo
        • primer error de $errors (validación) si no hay session('error')
    */
    $err = session('error') ?: ($errors->any() ? $errors->first() : null);
@endphp

@foreach ([
    'success' => session('success'),
    'error'   => $err,
] as $type => $msg)
    @if($msg)
        <div  class="toast position-fixed top-0 end-0 m-3 text-bg-{{ $type=='success' ? 'success' : 'danger' }}"
              role="alert" data-bs-delay="5000" data-bs-autohide="true">
            <div class="d-flex">
                <div class="toast-body">{{ $msg }}</div>
                <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif
@endforeach

@once  {{-- evita insertar el script más de una vez --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.toast')
                    .forEach(t => new bootstrap.Toast(t).show());
        });
    </script>
@endonce
