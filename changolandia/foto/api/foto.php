<?php
// Entrega los bytes de una foto guardada en la base de datos:  GET api/foto.php?id=123
// Solo la ve su dueño o el administrador.

require __DIR__ . '/../../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit;
}

$id   = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT usuario_id, tipo, datos FROM fotos WHERE id = ? AND datos IS NOT NULL');
$stmt->execute([$id]);
$fila = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$fila) {
    http_response_code(404);
    exit;
}
if (!es_admin() && (int) $fila['usuario_id'] !== (int) $_SESSION['usuario_id']) {
    http_response_code(403);
    exit;
}

$datos = is_resource($fila['datos']) ? stream_get_contents($fila['datos']) : $fila['datos'];
session_write_close();

header('Content-Type: ' . $fila['tipo']);
header('Content-Length: ' . strlen($datos));
header('Cache-Control: private, max-age=604800');
header('X-Content-Type-Options: nosniff');
echo $datos;
