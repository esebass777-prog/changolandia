<?php
// Lista las fotos:  GET api/fotos.php   (y, solo para el administrador, ?todas=1)
// Cliente: su foto de perfil y SU galería. Administrador con ?todas=1: la galería de todos.

require __DIR__ . '/../../conexion.php';
require __DIR__ . '/../../tienda.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Inicia sesión.']);
    exit;
}

$usuarioId = (int) $_SESSION['usuario_id'];
asegurar_tabla_fotos($pdo);

$stmt = $pdo->prepare("SELECT id FROM fotos WHERE usuario_id = ? AND rol_foto = 'perfil' AND datos IS NOT NULL ORDER BY id DESC LIMIT 1");
$stmt->execute([$usuarioId]);
$perfil = $stmt->fetchColumn();

if (es_admin() && isset($_GET['todas'])) {
    $stmt = $pdo->query("SELECT id, usuario_id, nombre, creado_en FROM fotos
                         WHERE rol_foto = 'galeria' AND datos IS NOT NULL ORDER BY id DESC LIMIT 200");
} else {
    $stmt = $pdo->prepare("SELECT id, usuario_id, nombre, creado_en FROM fotos
                           WHERE usuario_id = ? AND rol_foto = 'galeria' AND datos IS NOT NULL ORDER BY id DESC LIMIT 100");
    $stmt->execute([$usuarioId]);
}

$fotos = array_map(fn($f) => [
    'id'        => (int) $f['id'],
    'url'       => 'api/foto.php?id=' . (int) $f['id'],
    'usuario'   => $f['nombre'],
    'creado_en' => $f['creado_en'],
    'propia'    => (int) $f['usuario_id'] === $usuarioId,
], $stmt->fetchAll(PDO::FETCH_ASSOC));

echo json_encode([
    'perfil' => $perfil ? 'api/foto.php?id=' . (int) $perfil : null,
    'fotos'  => $fotos,
]);
