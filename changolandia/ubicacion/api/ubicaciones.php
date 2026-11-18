<?php
// Servicio que ENTREGA las últimas ubicaciones:  GET api/ubicaciones.php  → JSON

require __DIR__ . '/../../conexion.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Inicia sesión.']);
    exit;
}

// Privacidad: el administrador ve las ubicaciones de todos los clientes;
// cada cliente solo ve las suyas. Lo decide el SERVIDOR con la sesión, no el navegador.
$columnas = 'nombre, latitud, longitud, precision_m, creado_en';
if (es_admin()) {
    $stmt = $pdo->query("SELECT $columnas FROM ubicaciones ORDER BY id DESC LIMIT 200");
} else {
    $stmt = $pdo->prepare("SELECT $columnas FROM ubicaciones WHERE usuario_id = ? ORDER BY id DESC LIMIT 50");
    $stmt->execute([$_SESSION['usuario_id']]);
}
$filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// MySQL entrega los DECIMAL como texto; se convierten a número para el JSON.
$ubicaciones = array_map(fn($f) => [
    'nombre'      => $f['nombre'],
    'latitud'     => (float) $f['latitud'],
    'longitud'    => (float) $f['longitud'],
    'precision_m' => (int) $f['precision_m'],
    'creado_en'   => $f['creado_en'],
], $filas);

echo json_encode($ubicaciones);