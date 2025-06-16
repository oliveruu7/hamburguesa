 {{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

    <meta charset="UTF-8">
    <title>@yield('title', 'Panel Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap, Íconos y Google Fonts --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            margin: 0;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* -------- SIDEBAR -------- */
        .sidebar {
            width: 250px;
            background: #1e3c72;
            color: #fff;
            transition: transform .3s;
            z-index: 1050;
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        .sidebar-header {
            padding: 1rem;
            text-align: center;
            border-bottom: 1px solid #444;
        }

        .sidebar-header img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .sidebar .nav-link {
            color: #fff;
            padding: 10px 20px;
            display: flex;
            gap: 10px;
            transition: background .3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: #2a5298;
        }

        /* -------- TOPBAR -------- */
        .topbar {
            height: 60px;
            background: #fff;
            color: #1e3c72;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        .menu-toggle {
            font-size: 1.5rem;
            background: none;
            border: none;
            color: #1e3c72;
        }

        /* -------- CONTENIDO -------- */
        .main-content {
            flex-grow: 1;
            padding: 25px;
        }

        @media(max-width:768px){
            .sidebar {
                position: absolute;
                height: 100%;
            }
            .main-content {
                padding-top: 70px;
            }
        }
    </style>
</head>
<body>

<div class="wrapper">

    {{-- ========= SIDEBAR ========= --}}
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ Auth::user()->perfil_link ?? 'https://via.placeholder.com/60' }}" alt="avatar">
            <h6>{{ Auth::user()->nombre ?? 'Invitado' }}</h6>
            <small class="badge bg-success">● En línea</small>
            <p class="mt-1 mb-0"><strong>Rol:</strong> {{ Auth::user()->rol->nombre ?? 'Sin rol' }}</p>
        </div>

        <ul class="nav flex-column mt-2">
            @permiso('main.menu.view')
                <li>
                    <a class="nav-link {{ request()->routeIs('admin') ? 'active' : '' }}" href="{{ route('admin') }}">
                        <i class="bi bi-house-door-fill"></i> Inicio
                    </a>
                </li>
            @endpermiso

            @permiso('products.index')
                <li>
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                        <i class="bi bi-grid-fill"></i> Productos
                    </a>
                </li>
            @endpermiso

            @permiso('users.index')
                <li>
                    <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                        <i class="bi bi-person-lines-fill"></i> Usuarios
                    </a>
                </li>
            @endpermiso

            @permiso('roles.index')
                <li>
                    <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                        <i class="bi bi-shield-lock-fill"></i> Roles
                    </a>
                </li>
            @endpermiso

            @permiso('sales.index')
                <li>
                    <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                        <i class="bi bi-cart-check"></i> Ventas
                    </a>
                </li>
            @endpermiso

            @permiso('clientes.index')
                <li>
                    <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" href="{{ route('clientes.index') }}">
                        <i class="bi bi-people-fill"></i> Clientes
                    </a>
                </li>
            @endpermiso

            @permiso('insumos.index')
                <li>
                    <a class="nav-link {{ request()->routeIs('insumos.*') ? 'active' : '' }}" href="{{ route('insumos.index') }}">
                        <i class="bi bi-box-seam"></i> Almacen
                    </a>
                </li>
            @endpermiso

            @permiso('recetas.index')
                <li>
                    <a class="nav-link {{ request()->routeIs('recetas.*') ? 'active' : '' }}" href="{{ route('recetas.index') }}">
                      <i class="bi bi-list-ul"></i> Recetas
                    </a>
                </li>
            @endpermiso

           @permiso('compras.index')
<li>
    <a class="nav-link {{ request()->routeIs('compras.*') ? 'active' : '' }}" href="{{ route('compras.index') }}">
        <i class="bi bi-bag-check-fill"></i> Compras
    </a>
</li>
@endpermiso


@permiso('salidas.index')
  <li>
    <a class="nav-link {{ request()->routeIs('salidas.*') ? 'active' : '' }}" href="{{ route('salidas.index') }}">
      <i class="bi bi-box-arrow-up"></i> Salidas
    </a>
  </li>
@endpermiso

            {{-- Cerrar sesión --}}
            <li>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button class="nav-link btn btn-link text-start text-white" style="padding-left:20px;">
                        <i class="bi-truck"></i> Cerrar sesión
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    {{-- ========= ÁREA PRINCIPAL ========= --}}
    <div class="flex-grow-1">

        {{-- -------- TOPBAR -------- --}}
        <div class="topbar">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>

            <h6 class="m-0">@yield('title', 'Panel de Administración')</h6>

            <div class="actions d-flex align-items-center gap-3">
                <span id="fechaActual" class="small text-muted"></span>
                <i class="bi bi-bell-fill fs-5 text-secondary"></i>
                <i class="bi bi-envelope-fill fs-5 text-secondary"></i>
                <div class="user-info d-flex align-items-center gap-2">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nombre ?? 'Invitado') }}&background=1e3c72&color=fff"
                         alt="avatar" width="35" height="35" class="rounded-circle">
                    <strong class="text-dark">{{ Auth::user()->nombre ?? 'Invitado' }}</strong>
                </div>
            </div>
        </div>

        {{-- -------- CONTENIDO DINÁMICO -------- --}}
        <div class="main-content">
            @yield('content')
        </div>
    </div>
</div>

 

{{-- Función toggle y fecha --}}
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('hidden');
    }

    document.getElementById('fechaActual').textContent =
        new Date().toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('js')
</body>
</html>
