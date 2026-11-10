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
            <p class="info">Todavía no hay artículos en esta categoría. Agrégalos desde el inventario o abre <a href="actualizar_tienda.php">actualizar_tienda.php</a> para cargar los de ejemplo.</p>
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
                    <p class="precio">$<?= number_format((float) $p['precio'], 2) ?></p>
                    <p class="duracion"><?= e($p['categoria']) ?> · <?= $agotado ? 'Agotado' : (int) $p['stock'] . ' disponibles' ?></p>
                    <button type="button" class="interes" data-track="Me interesa: <?= e($p['nombre']) ?>" <?= $agotado ? 'disabled' : '' ?>>Me interesa</button>
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
    </script>
    <script src="tracker.js"></script>
</body>
</html>
