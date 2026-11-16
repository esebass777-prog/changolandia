<?php
// Solo administrador: ver usuarios y cambiar su rol (admin / cliente).
require 'conexion.php';
requerir_admin();

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id  = (int) ($_POST['id'] ?? 0);
    $rol = $_POST['rol'] ?? '';
    if (!in_array($rol, ['admin', 'cliente'], true)) {
        $mensaje = 'Rol no válido.';
    } elseif ($id === (int) $_SESSION['usuario_id'] && $rol !== 'admin') {
        $mensaje = 'No puedes quitarte tu propio permiso de administrador.';
    } else {
        $pdo->prepare('UPDATE usuarios SET rol = ? WHERE id = ?')->execute([$rol, $id]);
        $mensaje = 'Rol actualizado. El usuario verá el cambio la próxima vez que inicie sesión.';
    }
}

$usuarios = $pdo->query('SELECT id, nombre, correo, rol FROM usuarios ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios y roles · Changolandia</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <main class="tarjeta ancha">
    <?php include 'menu_nav.php'; ?>
        <h1>Usuarios y roles</h1>
        <?php if ($mensaje): ?>
            <p class="info"><?= e($mensaje) ?></p>
        <?php endif; ?>
        <div class="tabla">
            <table>
                <thead><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= e($u['nombre']) ?></td>
                        <td><?= e($u['correo']) ?></td>
                        <td colspan="2">
                            <form method="post" style="display:flex;gap:8px;align-items:center;margin:0">
                                <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                <select name="rol" style="margin:0;width:auto">
                                    <?php foreach (['cliente', 'admin'] as $r): ?>
                                        <option value="<?= $r ?>" <?= ($u['rol'] ?? 'cliente') === $r ? 'selected' : '' ?>><?= $r ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" style="width:auto;padding:8px 14px">Guardar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    <script src="tracker.js"></script>
</body>
</html>
