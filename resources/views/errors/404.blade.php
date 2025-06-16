 <!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>404 | ZombieBurguer</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap + Iconos + Google Fonts -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Creepster&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">

  <style>
    :root {
      --zombie-green: #64dd17;
      --blood-red: #d50000;
      --dark: #1c1c1c;
    }

    body {
      margin: 0;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: url('https://st2.depositphotos.com/5698376/8618/v/450/depositphotos_86180498-stock-illustration-halloween-night-wallpaper-with-zombies.jpg') center/cover no-repeat;
      font-family: 'Montserrat', sans-serif;
      overflow: hidden;
      position: relative;
    }

    .zombie-scene {
      text-align: center;
      max-width: 700px;
      padding: 2rem;
      background: rgba(0, 0, 0, 0.75);
      border-radius: 20px;
      color: #fff;
      animation: fadeUp 1s ease-out forwards;
      box-shadow: 0 0 25px rgba(0, 0, 0, 0.8);
    }

    @keyframes fadeUp {
      from {opacity: 0; transform: translateY(40px);}
      to {opacity: 1; transform: translateY(0);}
    }

    .zombie-title {
      font-family: 'Creepster', cursive;
      font-size: 6rem;
      color: var(--zombie-green);
      text-shadow: 3px 3px var(--blood-red);
      margin-bottom: 0.3rem;
    }

    .zombie-subtitle {
      font-size: 1.5rem;
      margin-bottom: 1rem;
    }

    .zombie-graphic {
      font-size: 7rem;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 1rem;
      color: var(--blood-red);
      text-shadow: 0 0 10px var(--zombie-green);
    }

    .zombie-graphic i {
      animation: float 2s ease-in-out infinite;
    }

    .zombie-icon {
      width: 64px;
      height: 64px;
      filter: drop-shadow(0 0 5px var(--zombie-green));
      animation: rotateZombie 8s infinite linear;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0);}
      50% { transform: translateY(-10px);}
    }

    @keyframes rotateZombie {
      0% { transform: rotate(0deg);}
      100% { transform: rotate(360deg);}
    }

    .btn-zombie {
      background-color: var(--blood-red);
      border: none;
      padding: 0.75rem 2rem;
      font-size: 1.2rem;
      font-weight: bold;
      color: white;
      border-radius: 10px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.4);
      transition: all 0.3s ease;
    }

    .btn-zombie:hover {
      background-color: var(--zombie-green);
      color: #000;
      transform: scale(1.05);
    }

    .zombie-footer {
      margin-top: 2rem;
      font-size: 1rem;
      color: #aaa;
      font-style: italic;
    }

  </style>
</head>
<body>

  <div class="zombie-scene">
    <div class="zombie-title">404</div>
    <div class="zombie-graphic">
      <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQJ6zv2XF467T8pYDjf6W7RXzIE5ZoLDzlcjQ&s" alt="Zombie" class="zombie-icon">
      <i class="bi bi-skull"></i>
      <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTk1-RuzAq-Z4o3KDmfPY3MryD4QXlvzs-NybU5NB-UhvdoBtvjNP4XBiMeYrnqsLjPn9s&usqp=CAU" alt="Zombie" class="zombie-icon">
    </div>
    <div class="zombie-subtitle">¡Cerebro no encontrado!</div>
    <p>La hamburguesa zombie ha devorado esta página. Pero no te preocupes, puedes regresar al inicio y pedir otra con doble carne y cerebro fresco.</p>
    <a href="{{ route('login') }}" class="btn btn-zombie">
      <i class="bi bi-arrow-left-circle"></i> Volver a la base
    </a>

    <div class="zombie-footer">ZombieBurguer - Donde los muertos también comen 🍔🧟</div>
  </div>

</body>
</html>
