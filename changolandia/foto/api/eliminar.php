<?php
// Elimina una foto:  POST api/eliminar.php  {id}
// Cada quien solo puede eliminar las suyas; el administrador puede eliminar cualquiera.

require __DIR__ . '/../../conexion.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Usa POST.']);
    exit;
}
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Inicia sesión.']);
    exit;
}

$datos = json_decode(file_get_contents('php://input'), true) ?: [];
$id    = (int) ($datos['id'] ?? 0);

if (es_admin()) {
    $pdo->prepare('DELETE FROM fotos WHERE id = ?')->execute([$id]);
} else {
    $pdo->prepare('DELETE FROM fotos WHERE id = ? AND usuario_id = ?')->execute([$id, $_SESSION['usuario_id']]);
}

echo json_encode(['ok' => true]);
