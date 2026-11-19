<?php
// Lista los clientes con su foto de perfil y su galería:  GET api/usuarios.php
// SOLO para el administrador.
// Copiar a: foto/api/usuarios.php

require __DIR__ . '/../../conexion.php';
require __DIR__ . '/../../tienda.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Inicia sesión.']);
    exit;
}
if (!es_admin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Solo el administrador puede ver esto.']);
    exit;
}

asegurar_tabla_fotos($pdo);

$usuarios = $pdo->query('SELECT id, nombre, correo FROM usuarios ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);

// Solo se piden los ids (nunca la columna "datos"): los bytes se descargan después con api/foto.php?id=
$filas = $pdo->query("SELECT id, usuario_id, rol_foto FROM fotos
                      WHERE datos IS NOT NULL AND usuario_id IS NOT NULL
                      ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$perfiles   = [];
$galerias   = [];
foreach ($filas as $f) {
    $uid = (int) $f['usuario_id'];
    $url = 'api/foto.php?id=' . (int) $f['id'];
    if ($f['rol_foto'] === 'perfil') {
        if (!isset($perfiles[$uid])) {      // la más reciente (vienen ordenadas por id DESC)
            $perfiles[$uid] = $url;
        }
    } else {
        $galerias[$uid][] = $url;
    }
}

$salida = array_map(function ($u) use ($perfiles, $galerias) {
    $uid = (int) $u['id'];
    return [
        'id'      => $uid,
        'nombre'  => $u['nombre'],
        'correo'  => $u['correo'],
        'perfil'  => $perfiles[$uid] ?? null,
        'galeria' => $galerias[$uid] ?? [],
    ];
}, $usuarios);

echo json_encode(['clientes' => $salida]);
