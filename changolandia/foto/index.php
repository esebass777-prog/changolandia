<?php
require __DIR__ . '/../conexion.php';
require __DIR__ . '/../tienda.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

$usuarioId = (int) $_SESSION['usuario_id'];
$nombre    = $_SESSION['nombre'];

asegurar_tabla_fotos($pdo);

$stmt = $pdo->prepare('SELECT correo FROM usuarios WHERE id = ?');
$stmt->execute([$usuarioId]);
$correo = (string) $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT id FROM fotos WHERE usuario_id = ? AND rol_foto = 'perfil' AND datos IS NOT NULL ORDER BY id DESC LIMIT 1");
$stmt->execute([$usuarioId]);
$idPerfil = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil · Changolandia</title>
    <link rel="stylesheet" href="../estilos.css">
    <link rel="stylesheet" href="/tema.css?v=1">
    <style>
        .cliente { border: 2px solid var(--borde); border-radius: 16px; padding: 12px; margin-bottom: 12px; background: #fff; }
        .cliente-cabecera { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
        .cliente-cabecera h3 { margin: 0; }
        .cliente-cabecera .correo { margin: 0; color: var(--suave); font-size: .85rem; word-break: break-all; }
        .cliente .avatar { width: 56px; height: 56px; }
        .cliente .avatar.inicial { font-size: 1.4rem; }
        .cliente .sin-fotos { margin: 0; color: var(--suave); font-size: .9rem; }
    </style>
</head>
<body>
    <main class="tarjeta foto">
        <h1>Mi perfil</h1>

        <!-- Foto de perfil y nombre -->
        <section class="perfil-cabecera">
            <img id="avatar" class="avatar" alt="Mi foto de perfil" <?= $idPerfil ? 'src="api/foto.php?id=' . (int) $idPerfil . '"' : 'hidden' ?>>
            <div id="avatar-inicial" class="avatar inicial" aria-hidden="true" <?= $idPerfil ? 'hidden' : '' ?>><?= e(mb_strtoupper(mb_substr($nombre, 0, 1))) ?></div>
            <div>
                <h2 id="perfil-nombre"><?= e($nombre) ?></h2>
                <?php if ($correo !== ''): ?><p class="correo"><?= e($correo) ?></p><?php endif; ?>
                <span class="etiqueta"><?= es_admin() ? 'Administrador' : 'Cliente' ?></span>
            </div>
        </section>

        <!-- Carrito -->
        <section>
            <h2>Mi carrito</h2>
            <p id="carrito-vacio" class="info" hidden>Todavía no agregas productos. <a href="../catalogo.php">Ir al catálogo</a></p>
            <ul id="carrito-lista" class="carrito-lista"></ul>
            <p id="carrito-total" class="carrito-total" hidden></p>
        </section>

        <!-- Agregar fotos: cámara o archivo -->
        <section>
            <h2>Agregar una foto</h2>

            <video id="video" autoplay playsinline muted hidden></video>
            <div class="botones">
                <button type="button" id="btn-camara">Activar cámara</button>
                <button type="button" id="btn-tomar" disabled>Tomar foto</button>
            </div>

            <label>o elige una foto de tu equipo
                <input type="file" id="archivo" accept="image/*">
            </label>

            <img id="vista-previa" alt="Vista previa de la foto" hidden>

            <fieldset class="opciones-foto">
                <legend>Guardarla como</legend>
                <label><input type="radio" name="rol" value="perfil" checked> Mi foto de perfil</label>
                <label><input type="radio" name="rol" value="galeria"> Una foto de mi galería</label>
            </fieldset>

            <button type="button" id="btn-guardar" disabled>Guardar foto</button>
            <p id="mensaje" hidden></p>
        </section>

        <!-- Galería -->
        <section>
            <h2>Mi galería</h2>
            <?php if (es_admin()): ?>
                <label class="solo-admin"><input type="checkbox" id="ver-todas"> Ver las fotos de todos los clientes</label>
            <?php endif; ?>
            <p id="galeria-vacia" class="info" hidden>Todavía no tienes fotos en tu galería.</p>
            <div id="galeria" class="galeria"></div>
        </section>

        <?php if (es_admin()): ?>
        <!-- Clientes: foto de perfil y galería de cada usuario (solo administrador) -->
        <section>
            <h2>Clientes registrados</h2>
            <p id="clientes-vacio" class="info" hidden>Todavía no hay clientes registrados.</p>
            <div id="clientes"></div>
        </section>
        <?php endif; ?>

        <p><a href="../menu.php">Volver al menú</a></p>
    </main>
    <script src="carrito.js"></script>
    <script src="foto.js"></script>
    <script src="../tracker.js"></script>
</body>
</html>
