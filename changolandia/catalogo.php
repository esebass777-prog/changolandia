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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo · <?= e(TIENDA) ?></title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="/tema.css?v=1">
</head>
<body>
    <main class="tarjeta ancha">
    <?php include 'menu_nav.php'; ?>
        <h1>Catálogo de <?= e(TIENDA) ?></h1>

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
                    <p class="precio">$<?= number_format((float) $p['precio'], 2) ?></p>
                    <p class="duracion"><?= e($p['categoria']) ?> · <?= $agotado ? 'Agotado' : (int) $p['stock'] . ' disponibles' ?></p>
                    <div class="colores" hidden>
                        <?php foreach (colores_de($p['categoria']) as $color): ?>
                            <button type="button" class="chip-color" data-color="<?= e($color) ?>" data-track="Color: <?= e($p['nombre']) ?> · <?= e($color) ?>" style="--c: <?= e(color_hex($color)) ?>"><?= e($color) ?></button>
                        <?php endforeach; ?>
                    </div>
                    <p class="color-elegido" hidden></p>
                    <p class="aviso-carrito" role="status" hidden></p>
                    <div class="acciones-producto">
                        <button type="button" class="btn-carrito" data-id="<?= (int) $p['id_producto'] ?>" data-track="Agregar al carrito: <?= e($p['nombre']) ?>" <?= $agotado ? 'disabled' : '' ?>>🛒 Agregar al carrito</button>
                        <button type="button" class="btn-color btn-contorno" data-track="Escoger color: <?= e($p['nombre']) ?>" <?= $agotado ? 'disabled' : '' ?>>🎨 Escoger color</button>
                        <button type="button" class="interes btn-claro" data-track="Me interesa: <?= e($p['nombre']) ?>" <?= $agotado ? 'disabled' : '' ?>>Me interesa</button>
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

        // Carrito: cada tarjeta recuerda el color que se escogió y lo manda con el artículo.
        document.querySelectorAll('.producto').forEach((tarjeta) => {
            const panel    = tarjeta.querySelector('.colores');
            const elegido  = tarjeta.querySelector('.color-elegido');
            const aviso    = tarjeta.querySelector('.aviso-carrito');
            const btnColor = tarjeta.querySelector('.btn-color');
            const btnAgregar = tarjeta.querySelector('.btn-carrito');
            let color = '';

            function avisar(texto, clase, conEnlace = false) {
                aviso.textContent = texto + ' ';
                aviso.className = 'aviso-carrito ' + clase;
                if (conEnlace) {
                    const enlace = document.createElement('a');
                    enlace.href = 'foto/index.php';
                    enlace.textContent = 'Ver mi carrito';
                    aviso.append(enlace);
                }
                aviso.hidden = false;
            }

            btnColor.addEventListener('click', () => { panel.hidden = !panel.hidden; });

            panel.addEventListener('click', (evento) => {
                const chip = evento.target.closest('.chip-color');
                if (!chip) return;
                panel.querySelectorAll('.chip-color').forEach((c) => c.classList.remove('activo'));
                chip.classList.add('activo');
                color = chip.dataset.color;
                elegido.textContent = 'Color: ' + color;
                elegido.hidden = false;
                aviso.hidden = true;
            });

            btnAgregar.addEventListener('click', async () => {
                if (!color) {
                    panel.hidden = false;
                    avisar('Primero escoge un color.', 'error-carrito');
                    return;
                }
                btnAgregar.disabled = true;
                try {
                    const respuesta = await fetch('carrito_api.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ accion: 'agregar', producto_id: Number(btnAgregar.dataset.id), color }),
                    });
                    const json = await respuesta.json();
                    if (!respuesta.ok) {
                        avisar(json.error || 'No se pudo agregar.', 'error-carrito');
                    } else {
                        avisar('✓ Agregado (' + color + ').', 'ok-carrito', true);
                    }
                } catch (error) {
                    avisar('Sin conexión, intenta de nuevo.', 'error-carrito');
                } finally {
                    btnAgregar.disabled = false;
                }
            });
        });
    </script>
    <script src="tracker.js"></script>
</body>
</html>
