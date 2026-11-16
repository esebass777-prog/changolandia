<?php
// Abrir UNA vez, con tu sesión iniciada:  /activar_roles.php
// 1) Agrega la columna "rol" a usuarios (todos quedan como "cliente").
// 2) Si todavía no hay ningún administrador, te hace administrador a TI.
//    Cuando ya existe uno, este archivo no cambia nada más.

require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$existe = $pdo->query("
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'rol'
")->fetchColumn();
if (!$existe) {
    $pdo->exec("ALTER TABLE usuarios ADD COLUMN rol VARCHAR(10) NOT NULL DEFAULT 'cliente'");
}

$admins = (int) $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'admin'")->fetchColumn();
if ($admins === 0) {
    $pdo->prepare("UPDATE usuarios SET rol = 'admin' WHERE id = ?")->execute([$_SESSION['usuario_id']]);
    $_SESSION['rol'] = 'admin';
    echo 'Listo: ahora eres administrador. ';
} else {
    echo 'Ya existe un administrador; no se cambió nada. ';
}
echo '<a href="menu.php">Ir al menú</a>';
