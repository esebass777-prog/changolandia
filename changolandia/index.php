<?php
// Portada pública de Changolandia: una sola imagen y el botón para entrar.
// No consulta la base de datos: solo mira si ya hay sesión.
session_start();
$logueado = isset($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changolandia · Artículos de changos</title>
    <style>
        :root { --guinda: #8a1538; --guinda-oscuro: #6d1030; --banana: #f5c518; }
        * { box-sizing: border-box; }
        html, body { margin: 0; }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
            padding: 24px 16px;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
            /* Blanco, igual que el fondo de la imagen: así no se nota el borde. */
            background: #fdfefe;
            color: #2b1a20;
            text-align: center;
        }

        /* width:auto + height:auto + max-*: la imagen se encoge sin deformarse. */
        .portada {
            display: block;
            width: auto;
            height: auto;
            max-width: min(92vw, 620px);
            max-height: 62vh;
        }

        .lema { margin: 0; max-width: 34ch; font-size: 1.1rem; line-height: 1.5; }

        .botones { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; }
        .btn {
            display: inline-block;
            padding: 14px 34px;
            border: 2px solid var(--guinda);
            border-radius: 999px;
            background: transparent;
            color: var(--guinda);
            font-size: 1.05rem;
            font-weight: 600;
            text-decoration: none;
        }
        .btn:hover, .btn:focus-visible { background: var(--guinda); color: #fff; }
        .btn.principal { background: var(--guinda); color: #fff; }
        .btn.principal:hover, .btn.principal:focus-visible {
            background: var(--guinda-oscuro);
            border-color: var(--guinda-oscuro);
        }
        .btn:focus-visible { outline: 3px solid var(--banana); outline-offset: 3px; }
    </style>
</head>
<body>
    <img class="portada" src="img/portada.jpg" alt="Changolandia: tienda de artículos de changos">

    <p class="lema">Figuras, bebés reborn, pijamas, mochilas, llaveros y mucho más, todo de changos.</p>

    <div class="botones">
        <?php if ($logueado): ?>
            <a class="btn principal" href="menu.php">Ir al menú</a>
        <?php else: ?>
            <a class="btn principal" href="login.php">Iniciar sesión</a>
            <a class="btn" href="registro.php">Crear cuenta</a>
        <?php endif; ?>
    </div>

    <script src="tracker.js"></script>
</body>
</html>
