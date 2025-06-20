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

    {{--  ⬇️  Si ya compilas con Vite, pasa este bloque a resources/css/admin.css y agrega la entrada en vite.config.js --}}
    <style>
/* ===== BASE ===== */
*{box-sizing:border-box;}
html,body{margin:0;padding:0;}
body{
    font-family:'Poppins',sans-serif;
    background:#f8f9fa;
}

/* ===== ESTRUCTURA ===== */
.wrapper{
    display:flex;
    min-height:100vh;
    overflow-x:hidden;
    position:relative;
}
.sidebar{
    /* color de fondo de la barra lateral */
    width:250px;
    background:#2f4f4f;
    color:#fff;
    flex-shrink:0;
    transition:transform .3s ease,width .3s ease;
}
.main-content{
    flex-grow:1;
    padding:25px;
    transition:padding .3s ease;
}

/* ===== SIDEBAR DETALLE ===== */
.sidebar-header{
    padding:1rem;
    text-align:center;
    border-bottom:1px solid #444;
}
.sidebar-header img{
    width:60px;height:60px;border-radius:50%;margin-bottom:10px;
}
.sidebar .nav-link{
    color:#fff;
    padding:10px 20px;
    display:flex;
    gap:10px;
    transition:background .3s;
}
.sidebar .nav-link:hover,
/*color de fondo al pasar el mouse sobre los modulos */
.sidebar .nav-link.active{background:#708090;}

/* ===== TOPBAR ===== */
.topbar{
    height:60px;
    background:#fff;
    color:#1e3c72;
    border-bottom:1px solid #dee2e6;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 20px;
    position:sticky;
    top:0;
    z-index:1040;
}
.menu-toggle{
    font-size:1.5rem;
    background:none;
    border:none;
    color:#1e3c72;
}

/* ===== OVERLAY (móviles) ===== */
.overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.4);
    backdrop-filter:blur(2px);
    opacity:0;
    visibility:hidden;
    transition:opacity .3s ease;
    z-index:1030;
}

/* ===== DESKTOP (≥768 px) ===== */
@media (min-width:768px){
    /* barra lateral visible por defecto */
    body.collapsed .sidebar{display:none;}
    body.collapsed .main-content{padding-left:25px;}
}

/* ===== MOBILE (<768 px) ===== */
@media (max-width:767.98px){
    .sidebar{
        position:fixed;
        left:0;
        top:0;
        height:100vh;
        transform:translateX(-100%);
        z-index:1050;
    }
    .wrapper.sidebar-open .sidebar{transform:translateX(0);}
    .wrapper.sidebar-open .overlay{
        opacity:1;
        visibility:visible;
    }
    .main-content{padding-top:70px;} /* dejar espacio para la topbar fija */
}

/* ===== LOADING OVERLAY ===== */
.loading-overlay{
    position:fixed;
    inset:0;
    background:rgba(255,255,255,.75);
    display:flex;
    align-items:center;
    justify-content:center;
    z-index:2000;
    visibility:hidden;
    opacity:0;
    transition:opacity .2s ease;
}
.loading-overlay.active{visibility:visible;opacity:1;}
.loading-overlay .spinner{
    width:3rem;height:3rem;
    border:.5rem solid #1e3c72;
    border-top-color:transparent;
    border-radius:50%;
    animation:spin .8s linear infinite;
}
@keyframes spin{to{transform:rotate(360deg);}}

    </style>
<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
</head>
<body>

<div class="wrapper" id="wrapper">

    {{-- ===== SIDEBAR ===== --}}
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ Auth::user()->perfil_link ?? 'https://via.placeholder.com/60' }}" alt="avatar">
            <h6>{{ Auth::user()->nombre ?? 'Invitado' }}</h6>
            <small class="badge bg-success">● En línea</small>
            <p class="mt-1 mb-0"><strong>Rol:</strong> {{ Auth::user()->rol->nombre ?? 'Sin rol' }}</p>
        </div>

        <ul class="nav flex-column mt-2">
            {{-- === enlaces a módulos (sin cambios) === --}}
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
                        <i class="bi bi-grid-fill"></i> Menú ZombieBurger 
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
                        <i class="bi bi-box-seam"></i> Almacén
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
            @permiso('proveedores.index')
            <li>
              <a class="nav-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}" href="{{ route('proveedores.index') }}">
                <i class="bi bi-truck"></i> Proveedores
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

            {{-- ===== Cerrar sesión ===== --}}
<li>
    {{-- evita acción por defecto para manejarla con JS --}}
    <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="nav-link btn btn-link text-start text-white" style="padding-left:20px;">
            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
        </button>
    </form>
</li>

{{-- overlay loading, fuera de .wrapper para tapar toda la pantalla --}}
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>

        </ul>
    </nav>

    {{-- ===== ÁREA PRINCIPAL ===== --}}
    <div class="flex-grow-1">

        {{-- --- TOPBAR --- --}}
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

        {{-- --- CONTENIDO DINÁMICO --- --}}
        <div class="main-content">
            @yield('content')
        </div>
    </div>

    {{-- overlay para móviles --}}
    <div class="overlay" onclick="toggleSidebar()"></div>
</div>

{{-- ===== JS ===== --}}
<script>
function toggleSidebar(){
    const w = window.innerWidth;
    const wrapper = document.getElementById('wrapper');

    if (w >= 768){
        document.body.classList.toggle('collapsed');
    }else{
        wrapper.classList.toggle('sidebar-open');
    }
}

/* Fecha */
document.getElementById('fechaActual').textContent =
    new Date().toLocaleDateString('es-ES',{
        weekday:'long',year:'numeric',month:'long',day:'numeric'
    });

/* Confirmación de cierre de sesión */
   if (performance && performance.getEntriesByType) {
        const nav = performance.getEntriesByType("navigation")[0];
        if (nav && nav.type === "back_forward") {
            location.reload(); // 🔁 Fuerza recarga real si se usó el botón atrás
        }
    }

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('js')
</body>
</html>
