<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión | ZombieBurguer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            background: url('https://static.vecteezy.com/ti/vecteur-libre/p1/5607809-halloween-avec-zombies-et-lune-sur-le-cimetiere-fond-rouge-sanglant-gratuit-vectoriel.jpg') center/cover no-repeat fixed;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Montserrat', sans-serif;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.75);
            z-index: 0;
        }

        .login-card {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 18px;
            padding: 50px 30px 30px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            text-align: center;
            animation: fade-in 0.8s ease-out;
        }

        @keyframes fade-in {
            from { opacity: 0; transform: translateY(25px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .logo-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background-color: #fff;
            overflow: hidden;
            padding: 5px;
            position: absolute;
            top: -45px;
            left: 50%;
            transform: translateX(-50%);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .logo-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .login-card h4 {
            font-weight: 600;
            margin-top: 20px;
            margin-bottom: 25px;
            color: #d50000;
        }

        .form-label {
            font-weight: bold;
            text-align: left;
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .form-control {
            border-radius: 8px;
        }

        .btn-primary {
            background-color: #d50000;
            border: none;
            font-weight: 600;
            width: 100%;
            margin-top: 15px;
            border-radius: 10px;
        }

        .btn-primary:hover {
            background-color: #b71c1c;
        }

        .validation-error {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 5px;
            display: flex;
            align-items: center;
        }

        .validation-error i {
            margin-right: 5px;
        }

        .position-relative {
            position: relative;
        }

        .toggle-password {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 35px;
            color: #6c757d;
        }
    </style>
</head>
<script>
/*  Bloqueo suave de la flecha Atrás:
    - Empuja un estado “fantasma” al historial
    - Si el usuario intenta retroceder, volvemos a empujarlo,
      de modo que permanece en /login
*/
// Esto bloquea navegación hacia atrás una vez en /login
history.pushState(null, '', location.href);
window.addEventListener('popstate', () => {
    history.pushState(null, '', location.href);
});

</script>

<body>

<div class="login-card">

    <!-- Logo Flotante -->
    <div class="logo-avatar">
        <img src="{{ asset('images/LogoZ.png') }}" alt="ZombieBurguer Logo">
    </div>

    <!-- Título -->
    <h4>Iniciar Sesión</h4>

    <!-- Alerta -->
    @if(session('error'))
        <div class="alert alert-danger text-center" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Formulario -->
    <form action="{{ url('/login') }}" method="POST" novalidate>
        @csrf

         <!-- Campo Correo Electrónico -->
<div class="mb-3 text-start">
    <label class="form-label">
        Correo Electrónico <span style="color: red">*</span>
    </label>
    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
           placeholder="ejemplo@correo.com" value="{{ old('email') }}" required>
    @error('email')
        <div class="validation-error">
            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
        </div>
    @enderror
</div>

<!-- Campo Contraseña -->
<div class="mb-3 position-relative text-start">
    <label class="form-label">
        Contraseña <span style="color: red">*</span>
    </label>
    <input type="password" name="password" id="password"
           class="form-control @error('password') is-invalid @enderror"
           placeholder="******" required>
    <span class="toggle-password" id="togglePassword">
        <i class="bi bi-eye-slash-fill"></i>
    </span>
    @error('password')
        <div class="validation-error">
            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
        </div>
    @enderror
</div>

        <button type="submit" class="btn btn-primary">Ingresar</button>
    </form>
</div>

<!-- Scripts -->
<script>
    // Mostrar/ocultar contraseña
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input = document.getElementById('password');
        const icon = this.querySelector('i');
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        icon.classList.toggle('bi-eye-fill');
        icon.classList.toggle('bi-eye-slash-fill');
    });

    // Ocultar alertas luego de 6 segundos
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) alert.remove();
    }, 6000);

    
</script>

</body>
</html>
