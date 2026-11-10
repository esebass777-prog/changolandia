<?php
require __DIR__ . '/../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi dirección de entrega · Changolandia</title>
    <!-- Leaflet: la librería que dibuja el mapa de OpenStreetMap -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="../estilos.css">
</head>
<body>
    <main class="tarjeta ubicacion">
        <h1>Mi dirección de entrega</h1>
        <p>Sesión: <strong><?= e($_SESSION['nombre']) ?></strong></p>

        <button type="button" id="btn-capturar">Capturar mi ubicación de entrega</button>
        <p id="mensaje" hidden></p>
        <p id="datos" hidden></p>

        <div id="mapa"></div>

        <h2>Últimas direcciones capturadas</h2>
        <ol id="historial" class="historial"></ol>

        <p><a href="../menu.php">Volver al menú</a></p>
    </main>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="ubicacion.js"></script>
</body>
</html>