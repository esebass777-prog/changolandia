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
            box-shadow: 0 18px 44px rgba(92, 58, 33, .22);
            border: 3px solid var(--crema);
        }
        .marco img { display: block; width: 100%; max-height: 58vh; height: auto; object-fit: contain; }

        .hero h1 { margin: 22px 0 6px; font-size: clamp(1.9rem, 5vw, 3rem); line-height: 1.1; }
        .hero p { margin: 0 auto 22px; max-width: 40ch; font-size: 1.15rem; color: var(--suave); }

        .acciones { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; }
        .acciones .boton { width: auto; padding: 14px 36px; font-size: 1.1rem; }
        .acciones .contorno {
            background: #fff;
            color: var(--cafe);
            border: 2px solid var(--cafe);
            box-shadow: none;
        }
        .acciones .contorno:hover { background: var(--cafe); color: #fff; }



        /* Changos de fondo: solo en esta portada, tenues y repartidos por toda la página */
        body { position: relative; }
        body::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            opacity: .2;
            background:
                url("/img/changos/chupete-a.webp") no-repeat left 2vw  top 5%  / clamp(84px, 9.5vw, 150px),
                url("/img/changos/mono-a.webp")    no-repeat right 14vw top 9%  / clamp(70px, 8vw, 130px),
                url("/img/changos/sonrisa-b.webp") no-repeat right 1.5vw top 31% / clamp(80px, 10vw, 160px),
                url("/img/changos/espuma-b.webp")  no-repeat left 15vw top 33% / clamp(66px, 7.5vw, 120px),
                url("/img/changos/sonrisa-a.webp") no-repeat left 1vw  top 56% / clamp(88px, 10.5vw, 165px),
                url("/img/changos/chupete-b.webp") no-repeat right 15vw top 58% / clamp(66px, 7.5vw, 120px),
                url("/img/changos/espuma-a.webp")  no-repeat right 2vw  top 80% / clamp(84px, 9.5vw, 150px),
                url("/img/changos/mono-b.webp")    no-repeat left 14vw top 82% / clamp(70px, 8vw, 130px);
        }
        @media (max-width: 900px) {
            body::before {
                background:
                    url("/img/changos/sonrisa-b.webp") no-repeat right 6px bottom 8px / 78px,
                    url("/img/changos/mono-a.webp")    no-repeat left 6px bottom 8px / 74px;
            }
        }

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
