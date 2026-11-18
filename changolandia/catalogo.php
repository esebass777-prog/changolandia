<?php
require 'conexion.php';
require 'tienda.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$todas    = categorias();
$elegida  = $_GET['categoria'] ?? '';

if ($elegida !== '' && array_key_exists($elegida, $todas)) {
    $stmt = $pdo->prepare('SELECT * FROM productos WHERE categoria = ? ORDER BY nombre');
    $stmt->execute([$elegida]);
} else {
    $elegida = '';
    $stmt = $pdo->query('SELECT * FROM productos ORDER BY categoria, nombre');
}
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Precios nuevos del catálogo (categoría => [modelo 1, 2, 3, 4]).
// Si un producto no aparece aquí, se muestra el precio de la base de datos.
$precios_nuevos = [
    'Bebés reborn'         => [1 => 950, 2 => 650, 3 => 800, 4 => 750],
    'Disfraces'            => [1 => 320, 2 => 380, 3 => 290, 4 => 340],
    'Escolar'              => [1 => 35,  2 => 45,  3 => 40,  4 => 55],
    'Figuras y decoración' => [1 => 220, 2 => 280, 3 => 120, 4 => 180],
    'Herramientas'         => [1 => 450, 2 => 380, 3 => 520, 4 => 320],
    'Llaveros'             => [1 => 45,  2 => 55,  3 => 45,  4 => 50],
    'Mochilas y bolsas'    => [1 => 280, 2 => 320, 3 => 160, 4 => 240],
    'Peluches'             => [1 => 140, 2 => 120, 3 => 170, 4 => 200],
    'Pijamas'              => [1 => 380, 2 => 260, 3 => 330, 4 => 300],
    'Toallas'              => [1 => 140, 2 => 90,  3 => 120, 4 => 110],
];

function precio_producto($p, $precios_nuevos)
{
    if (preg_match('/modelo\s+(\d+)/iu', (string) $p['nombre'], $m)) {
        $modelo = (int) $m[1];
        if (isset($precios_nuevos[$p['categoria']][$modelo])) {
            return (float) $precios_nuevos[$p['categoria']][$modelo];
        }
    }
    return (float) $p['precio'];
}

// En la base solo se guarda el nombre base de la foto (por ejemplo "pijama2").
// Aquí se busca el archivo real en img/catalogo/, con la extensión que exista.
function foto_producto($base)
{
    $base = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $base);
    if ($base === '') {
        return null;
    }
    foreach (['webp', 'jpg', 'jpeg', 'png'] as $extension) {
        $ruta = "img/catalogo/$base.$extension";
        if (is_file(__DIR__ . '/' . $ruta)) {
            return $ruta;
        }
    }
    return null;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo · <?= e(TIENDA) ?></title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="/tema.css?v=1">
    <style>
        .acciones { display: flex; flex-direction: column; gap: 8px; margin-top: 10px; }
        .acciones button { width: 100%; cursor: pointer; }
        .acciones .secundario {
            background: #fff; color: #6b4423; border: 2px solid #6b4423;
            border-radius: 999px; padding: 10px 14px; font: inherit; font-weight: 700;
        }
        .acciones .secundario:hover { background: #fff4d6; }
        .acciones .secundario.marcado { background: #6b4423; color: #fff; }
        .colores { display: none; gap: 8px; justify-content: center; flex-wrap: wrap; }
        .colores.abierto { display: flex; }
        .colores button {
            width: 28px; height: 28px; padding: 0; border-radius: 50%;
            border: 2px solid #e6d3b3; cursor: pointer;
        }
        .colores button.elegido { outline: 3px solid #6b4423; outline-offset: 2px; }
        .color-elegido { margin: 0; text-align: center; font-size: .9rem; min-height: 1.1em; }
    </style>
</head>
<body>
    <main class="tarjeta ancha">
    <?php include 'menu_nav.php'; ?>
        <h1>Catálogo de <?= e(TIENDA) ?></h1>
        <p>Todo para los fans de los changos. Lo que explores aquí queda registrado en las métricas.</p>

        <nav class="chips" aria-label="Categorías">
            <a href="catalogo.php" class="<?= $elegida === '' ? 'activa' : '' ?>">Todo</a>
            <?php foreach ($todas as $nombre => $emoji): ?>
                <a href="catalogo.php?categoria=<?= urlencode($nombre) ?>" class="<?= $elegida === $nombre ? 'activa' : '' ?>"><?= $emoji ?> <?= e($nombre) ?></a>
            <?php endforeach; ?>
        </nav>

        <?php if (!$productos): ?>
            <p class="info">Todavía no hay artículos en esta categoría.<?php if (es_admin()): ?> Agrégalos desde el <a href="crud/index.php">inventario</a>.<?php endif; ?></p>
        <?php endif; ?>

        <div class="cursos">
            <?php foreach ($productos as $p):
                $emoji   = $todas[$p['categoria']] ?? '🐵';
                $agotado = (int) $p['stock'] <= 0;
                $foto    = foto_producto($p['imagen'] ?? '');
            ?>
                <article class="curso producto">
                    <?php if ($foto): ?>
                        <img class="foto-producto" src="<?= e($foto) ?>" alt="<?= e($p['nombre']) ?>" loading="lazy">
                    <?php else: ?>
                        <p class="emoji" aria-hidden="true"><?= $emoji ?></p>
                    <?php endif; ?>
                    <h2><?= e($p['nombre']) ?></h2>
                    <p><?= e($p['descripcion']) ?></p>
                    <p class="precio">$<?= number_format(precio_producto($p, $precios_nuevos), 2) ?></p>
                    <p class="duracion"><?= e($p['categoria']) ?> · <?= $agotado ? 'Agotado' : (int) $p['stock'] . ' disponibles' ?></p>
                    <div class="acciones">
                        <button type="button" class="interes" data-track="Me interesa: <?= e($p['nombre']) ?>" <?= $agotado ? 'disabled' : '' ?>>Me interesa</button>
                        <button type="button" class="secundario carrito" data-track="Agregar al carrito: <?= e($p['nombre']) ?>" <?= $agotado ? 'disabled' : '' ?>>Agregar al carrito</button>
                        <button type="button" class="secundario escoger-color" data-track="Escoger color: <?= e($p['nombre']) ?>" <?= $agotado ? 'disabled' : '' ?>>Escoger color</button>
                        <div class="colores" aria-label="Colores disponibles">
                            <button type="button" data-color="Café" title="Café" style="background:#6b4423"></button>
                            <button type="button" data-color="Rosa" title="Rosa" style="background:#f78fb3"></button>
                            <button type="button" data-color="Amarillo" title="Amarillo" style="background:#ffd966"></button>
                            <button type="button" data-color="Guinda" title="Guinda" style="background:#8b1e3f"></button>
                            <button type="button" data-color="Azul" title="Azul" style="background:#6fa8dc"></button>
                        </div>
                        <p class="color-elegido"></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </main>
    <script>
        // Al pulsar "Me interesa" cambia el texto. Por eso el botón usa data-track:
        // así el rastreador registra un nombre fijo y no el texto ya cambiado.
        document.querySelectorAll('.interes').forEach((boton) => {
            boton.addEventListener('click', () => {
                boton.textContent = '✓ Te avisaremos';
                boton.classList.add('marcado');
            });
        });

        // Agregar al carrito: marca el botón como agregado.
        document.querySelectorAll('.carrito').forEach((boton) => {
            boton.addEventListener('click', () => {
                boton.textContent = '✓ Agregado al carrito';
                boton.classList.add('marcado');
            });
        });

        // Escoger color: muestra u oculta los círculos de color y guarda la elección.
        document.querySelectorAll('.escoger-color').forEach((boton) => {
            const caja    = boton.closest('.acciones');
            const colores = caja.querySelector('.colores');
            const texto   = caja.querySelector('.color-elegido');
            boton.addEventListener('click', () => colores.classList.toggle('abierto'));
            colores.querySelectorAll('button').forEach((c) => {
                c.addEventListener('click', () => {
                    colores.querySelectorAll('button').forEach((o) => o.classList.remove('elegido'));
                    c.classList.add('elegido');
                    texto.textContent = 'Color: ' + c.dataset.color;
                    boton.textContent = 'Color: ' + c.dataset.color;
                    boton.classList.add('marcado');
                });
            });
        });
    </script>
    <script src="tracker.js"></script>
</body>
</html>
