<?php
// Los datos de conexión vienen de las variables de entorno (se configuran en Render)
$host          = getenv('DB_HOST');
$puerto        = (int) getenv('DB_PORT');
$usuario       = getenv('DB_USER');
$contrasena    = getenv('DB_PASS');
$base_de_datos = getenv('DB_NAME');

// Para login, registro, chat y encuesta (PDO)
$pdo = new PDO(
    "mysql:host=$host;port=$puerto;dbname=$base_de_datos;charset=utf8mb4",
    $usuario,
    $contrasena,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Para el CRUD de productos (mysqli)
$conexion = mysqli_connect($host, $usuario, $contrasena, $base_de_datos, $puerto);
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
mysqli_set_charset($conexion, "utf8mb4");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// ---------- Roles: "admin" ve inventario, métricas y herramientas; "cliente" solo compra ----------

function es_admin()
{
    return ($_SESSION['rol'] ?? '') === 'admin';
}

// Para páginas solo de administrador: sin sesión -> login; con sesión pero sin permiso -> 403.
function requerir_admin()
{
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: /login.php');
        exit;
    }
    if (!es_admin()) {
        http_response_code(403);
        echo '<!DOCTYPE html><html lang="es"><meta charset="UTF-8"><title>Sin acceso</title>'
           . '<body style="font-family:system-ui,sans-serif;text-align:center;padding:60px 16px">'
           . '<h1>Sin acceso</h1><p>Esta sección es solo para el administrador de la tienda.</p>'
           . '<p><a href="/menu.php">Volver al menú</a></p></body></html>';
        exit;
    }
}
