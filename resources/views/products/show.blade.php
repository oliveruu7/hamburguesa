 @extends('layouts.admin')
@section('title','Detalle Producto')

@section('content')
<div class="container py-4">

    {{-- Botón volver --}}
    <div class="mb-3">
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left-circle me-1"></i> Volver 
        </a>
    </div>

    {{-- Detalle del producto --}}
    <div class="card shadow border-0">
        <div class="row g-0">
            {{-- Imagen del producto --}}
            <div class="col-lg-5">
                <img src="{{ $product->imagenUrl ?? 'https://via.placeholder.com/600x400?text=Sin+Imagen' }}"
                     alt="Imagen del producto"
                     class="img-fluid rounded-start w-100 h-100 object-fit-cover"
                     style="object-fit: cover;">
            </div>

            {{-- Información del producto --}}
            <div class="col-lg-7">
                <div class="card-body p-4">
                    <h3 class="fw-bold text-primary mb-3">
                        <i class="bi bi-box-seam me-2"></i> {{ $product->nombre }}
                    </h3>

                    <div class="mb-3">
                        <span class="text-muted fw-semibold">
                            <i class="bi bi-tags-fill me-1"></i> Categoría:
                        </span>
                        <span class="fw-bold">{{ $product->categoria->nombre }}</span>
                    </div>

                    <div class="mb-3">
                        <span class="text-muted fw-semibold">
                            <i class="bi bi-currency-dollar me-1"></i> Precio:
                        </span>
                        <span class="text-success fw-bold">Bs {{ number_format($product->precio_unitario, 2) }}</span>
                    </div>

                    <hr>

                    <h6 class="text-muted"><i class="bi bi-card-text me-1"></i> Descripción</h6>
                    <p class="mb-0">{{ $product->descripcion ?: '— Sin descripción —' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
