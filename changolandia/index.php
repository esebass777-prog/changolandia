<?php
// Portada pública de Changolandia. No consulta la base de datos:
// solo mira si ya hay sesión para mostrar "Iniciar sesión" o "Ir al menú".
session_start();
$logueado = isset($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changolandia · Artículos de changos</title>
    <link rel="stylesheet" href="/tema.css?v=1">
    <style>
        body { display: block; padding: 0; text-align: center; }

        .hero { padding: 40px 16px 28px; }
        .marco {
            display: inline-block;
            max-width: min(92vw, 560px);
            padding: 14px;
            border-radius: 36px;
            background: #fff;
            box-shadow: 0 18px 44px rgba(138, 21, 56, .22);
            border: 3px solid var(--rosa-claro);
        }
        .marco img { display: block; width: 100%; max-height: 58vh; height: auto; object-fit: contain; }

        .hero h1 { margin: 22px 0 6px; font-size: clamp(1.9rem, 5vw, 3rem); line-height: 1.1; }
        .hero p { margin: 0 auto 22px; max-width: 40ch; font-size: 1.15rem; color: var(--suave); }

        .acciones { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; }
        .acciones .boton { width: auto; padding: 14px 36px; font-size: 1.1rem; }
        .acciones .contorno {
            background: #fff;
            color: var(--guinda);
            border: 2px solid var(--guinda);
            box-shadow: none;
        }
        .acciones .contorno:hover { background: var(--guinda); color: #fff; }


        footer { padding: 22px 16px 32px; color: var(--suave); font-size: .9rem; }
    </style>
</head>
<body>
    <main>
        <section class="hero">
            <div class="marco">
                <img src="img/portada.jpg" alt="Changolandia: tienda de artículos de changos">
            </div>
            <h1>¡Todo para los fans de los changos!</h1>
            <p>Figuras, bebés reborn, pijamas, mochilas, llaveros y mucho más.</p>
            <div class="acciones">
                <?php if ($logueado): ?>
                    <a class="boton" href="menu.php">Ir al menú</a>
                    <a class="boton contorno" href="catalogo.php">Ver el catálogo</a>
                <?php else: ?>
                    <a class="boton" href="login.php">Iniciar sesión</a>
                    <a class="boton contorno" href="registro.php">Crear cuenta</a>
                <?php endif; ?>
            </div>
        </section>

    </main>

    <footer>Changolandia 🍌 Hecho con cariño para los fans de los changos</footer>
    <script src="tracker.js"></script>
</body>
</html>
