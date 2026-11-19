<?php
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
$nombre = $_SESSION['nombre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú · Changolandia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #fbf6ef; color: #333; }
        header { background: #3b2415; color: #fff; padding: 18px 0; text-align: center; font-size: 20px; font-weight: bold; position: relative; }
        .salir { position: absolute; right: 12px; top: 10px; background: #5c3a21; color: #fff; padding: 9px 14px; border-radius: 6px; text-decoration: none; font-size: 14px; }
        .salir:hover { background: #7a4a24; }
        main { max-width: 640px; margin: 0 auto; padding: 20px; text-align: center; }
        .foto { width: 325px; max-width: 80%; border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,.2); }
        h1 { margin: 30px 0 10px; }
        p { color: #555; margin-bottom: 35px; }
        .opcion { position: relative; display: block; padding: 18px; margin-bottom: 15px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 18px; box-shadow: 0 2px 6px rgba(0,0,0,.15); }
        .opcion i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 30px; height: 30px; line-height: 30px; border-radius: 50%; font-size: 14px; }
        .oscuro { background: #5c3a21; color: #fff; }
        .oscuro i { background: #fff; color: #5c3a21; }
        .claro { background: #fff; color: #222; border: 1px solid #ddd; }
        .claro i { background: #888; color: #fff; }
        .opcion:hover { opacity: .9; }
    </style>
    <link rel="stylesheet" href="/tema.css?v=1">
</head>
<body>
    <header class="encabezado-menu">
        <span class="titulo-menu">Changolandia 🐒</span>
        <a class="salir" href="logout.php"><i class="fa-solid fa-power-off"></i> Cerrar Sesión</a>
    </header>

    <main>
        <img class="foto" src="yoo.png" alt="Foto de perfil">
        <h1>¡Bienvenido a Changolandia, <?= e($nombre) ?>!</h1>
        <p>¿Qué artículo de changos buscas hoy?</p>

        <?php if (es_admin()): ?>
        <a class="opcion oscuro" href="crud/index.php"><i class="fa-solid fa-table-cells"></i>📦 Inventario (administrar productos)</a>
        <?php endif; ?>
        <a class="opcion claro"  href="mapa/mapa.php"><i class="fa-solid fa-location-dot"></i>🗺️ Dónde estamos</a>
        <a class="opcion claro"  href="encuesta/encuesta.php"><i class="fa-solid fa-pen"></i>📋 Encuesta de satisfacción</a>
        <a class="opcion oscuro" href="chat.php"><i class="fa-solid fa-comment"></i>💬 Atención al cliente</a>
        <a class="opcion claro" href="video.php"><i class="fa-solid fa-video"></i>🎥 Conoce la tienda</a>
        <a class="opcion oscuro" href="catalogo.php"><i class="fa-solid fa-bag-shopping"></i>🐒 Catálogo de artículos</a>
        <?php if (es_admin()): ?>
        <a class="opcion claro"  href="dashboard.php"><i class="fa-solid fa-chart-line"></i>📊 Métricas de la tienda</a> 
        <?php endif; ?>
        <a class="opcion oscuro" href="ubicacion/index.php"><i class="fa-solid fa-location-crosshairs"></i>📍 Mi dirección de entrega</a>
        <a class="opcion claro" href="foto/index.php"><i class="fa-solid fa-user"></i>👤 Mi perfil</a>
        <?php if (es_admin()): ?>
        <a class="opcion claro" href="foto/clientes.php"><i class="fa-solid fa-address-card"></i>🧑‍🤝‍🧑 Clientes y perfiles</a>
        <a class="opcion oscuro" href="usuarios.php"><i class="fa-solid fa-users"></i>👥 Usuarios y roles</a>
        <?php endif; ?>
    </main>
    <script src="tracker.js"></script>
</body>
</html>