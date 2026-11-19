<?php
// Guarda una foto en la base de datos:  POST api/subir.php  (multipart: foto, rol = perfil | galeria)
// Cada foto queda ligada a la cuenta de quien la sube.

require __DIR__ . '/../../conexion.php';
require __DIR__ . '/../../tienda.php';
header('Content-Type: application/json; charset=utf-8');

const MAX_FOTOS_GALERIA = 30;

function responder($codigo, $datos)
{
    http_response_code($codigo);
    echo json_encode($datos);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, ['error' => 'Usa POST.']);
}
if (!isset($_SESSION['usuario_id'])) {
    responder(401, ['error' => 'Inicia sesión.']);
}

$foto = $_FILES['foto'] ?? null;
if (!$foto || $foto['error'] !== UPLOAD_ERR_OK) {
    responder(400, ['error' => 'No llegó ninguna foto.']);
}
if ($foto['size'] > 5 * 1024 * 1024) {
    responder(400, ['error' => 'La foto pesa más de 5 MB.']);
}

// Nunca confiar en el cliente: finfo revisa el CONTENIDO real del archivo.
$tipos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
$tipo  = (new finfo(FILEINFO_MIME_TYPE))->file($foto['tmp_name']);
if (!isset($tipos[$tipo])) {
    responder(400, ['error' => 'Eso no es una imagen.']);
}

$rol       = ($_POST['rol'] ?? 'galeria') === 'perfil' ? 'perfil' : 'galeria';
$usuarioId = (int) $_SESSION['usuario_id'];
asegurar_tabla_fotos($pdo);

if ($rol === 'galeria') {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM fotos WHERE usuario_id = ? AND rol_foto = 'galeria' AND datos IS NOT NULL");
    $stmt->execute([$usuarioId]);
    if ((int) $stmt->fetchColumn() >= MAX_FOTOS_GALERIA) {
        responder(400, ['error' => 'Tu galería ya tiene ' . MAX_FOTOS_GALERIA . ' fotos. Elimina alguna para agregar otra.']);
    }
}

$bytes   = file_get_contents($foto['tmp_name']);
// La columna "archivo" es única y ya no es un archivo real: se rellena con un código al azar.
$archivo = 'db_' . bin2hex(random_bytes(8)) . '.' . $tipos[$tipo];

try {
    $pdo->beginTransaction();
    if ($rol === 'perfil') {
        // Solo existe UNA foto de perfil por usuario: la nueva reemplaza a la anterior.
        $pdo->prepare("DELETE FROM fotos WHERE usuario_id = ? AND rol_foto = 'perfil'")->execute([$usuarioId]);
    }
    $stmt = $pdo->prepare(
        'INSERT INTO fotos (usuario_id, nombre, archivo, tipo, bytes, datos, rol_foto) VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->bindValue(1, $usuarioId, PDO::PARAM_INT);
    $stmt->bindValue(2, $_SESSION['nombre']);
    $stmt->bindValue(3, $archivo);
    $stmt->bindValue(4, $tipo);
    $stmt->bindValue(5, strlen($bytes), PDO::PARAM_INT);
    $stmt->bindValue(6, $bytes, PDO::PARAM_LOB);
    $stmt->bindValue(7, $rol);
    $stmt->execute();
    $id = (int) $pdo->lastInsertId();
    $pdo->commit();
} catch (PDOException $error) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    responder(500, ['error' => 'No se pudo guardar la foto en la base de datos.']);
}

responder(201, ['id' => $id, 'rol' => $rol, 'url' => 'api/foto.php?id=' . $id]);
