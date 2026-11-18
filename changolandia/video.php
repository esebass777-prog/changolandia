<?php
// video.php: video de presentación de la tienda
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Conoce Changolandia</title>
  <link rel="stylesheet" href="estilos.css">
  <link rel="stylesheet" href="/tema.css?v=1">
</head>
<body>
  <main class="tarjeta ancha">
    <h1>Conoce un poco mas sobre changos</h1>
    <div class="video-marco">
      <video controls preload="metadata">
        <source src="video/mi-video.mp4" type="video/mp4">
        Tu navegador no soporta video.
      </video>
    </div>
    <p style="text-align:center"><a class="boton" href="menu.php" style="max-width:240px">Volver al menú</a></p>
  </main>
  <script src="tracker.js"></script>
</body>
</html>
