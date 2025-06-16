@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif


{{-- Alertas de éxito o error --}}
@foreach (['success', 'error'] as $msg)
    @if(session($msg))
        <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
            <i class="bi {{ $msg == 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }}"></i>
            {{ session($msg) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
@endforeach

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-x-circle-fill me-2"></i> No se pudo guardar el producto.
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
