<?php
// Servicio del carrito (solo para quien tiene sesión iniciada).
//   GET  carrito_api.php                       -> contenido del carrito
//   POST {accion:"agregar", producto_id, color} -> suma 1 al artículo
//   POST {accion:"cambiar", id, cantidad}       -> cambia la cantidad (0 = quitar)
//   POST {accion:"quitar", id}                  -> quita el artículo
// Cada cliente solo puede tocar SU carrito: el usuario sale de la sesión, no del navegador.

require __DIR__ . '/conexion.php';
require __DIR__ . '/tienda.php';
header('Content-Type: application/json; charset=utf-8');

const MAXIMO_POR_ARTICULO = 20;

function responder($codigo, $datos)
{
    http_response_code($codigo);
    echo json_encode($datos);
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    responder(401, ['error' => 'Inicia sesión.']);
}
$usuario = (int) $_SESSION['usuario_id'];

asegurar_tabla_carrito($pdo);

function leer_carrito(PDO $pdo, int $usuario): array
{
    $stmt = $pdo->prepare(
        'SELECT c.id, c.producto_id, c.color, c.cantidad, p.nombre, p.precio, p.imagen, p.categoria
         FROM carrito c JOIN productos p ON p.id_producto = c.producto_id
         WHERE c.usuario_id = ? ORDER BY c.id DESC'
    );
    $stmt->execute([$usuario]);

    $items = [];
    $total = 0.0;
    $cantidad = 0;
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $f) {
        $precio   = (float) $f['precio'];
        $cant     = (int) $f['cantidad'];
        $items[]  = [
            'id'       => (int) $f['id'],
            'nombre'   => $f['nombre'],
            'color'    => $f['color'],
            'cantidad' => $cant,
            'precio'   => $precio,
            'subtotal' => round($precio * $cant, 2),
            'foto'     => foto_producto($f['imagen']),
            'emoji'    => categorias()[$f['categoria']] ?? '🐵',
        ];
        $total    += $precio * $cant;
        $cantidad += $cant;
    }
    return ['items' => $items, 'total' => round($total, 2), 'cantidad_total' => $cantidad];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos  = json_decode(file_get_contents('php://input'), true) ?: [];
    $accion = $datos['accion'] ?? '';

    if ($accion === 'agregar') {
        $idProducto = (int) ($datos['producto_id'] ?? 0);
        $color      = trim((string) ($datos['color'] ?? ''));

        $stmt = $pdo->prepare('SELECT categoria, stock FROM productos WHERE id_producto = ?');
        $stmt->execute([$idProducto]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            responder(404, ['error' => 'Ese artículo ya no existe.']);
        }
        if ((int) $producto['stock'] <= 0) {
            responder(400, ['error' => 'Ese artículo está agotado.']);
        }
        // Nunca confiar en el cliente: el color debe ser uno de los que ofrece esa categoría.
        if (!in_array($color, colores_de($producto['categoria']), true)) {
            responder(400, ['error' => 'Escoge un color válido.']);
        }

        $limite = min(MAXIMO_POR_ARTICULO, (int) $producto['stock']);
        $sql = 'INSERT INTO carrito (usuario_id, producto_id, color, cantidad) VALUES (?, ?, ?, 1)
                ON DUPLICATE KEY UPDATE cantidad = LEAST(cantidad + 1, ?)';
        $pdo->prepare($sql)->execute([$usuario, $idProducto, $color, $limite]);

    } elseif ($accion === 'cambiar') {
        $id       = (int) ($datos['id'] ?? 0);
        $cantidad = (int) ($datos['cantidad'] ?? 0);
        if ($cantidad <= 0) {
            $pdo->prepare('DELETE FROM carrito WHERE id = ? AND usuario_id = ?')->execute([$id, $usuario]);
        } else {
            $sql = 'UPDATE carrito c JOIN productos p ON p.id_producto = c.producto_id
                    SET c.cantidad = LEAST(?, p.stock, ?)
                    WHERE c.id = ? AND c.usuario_id = ?';
            $pdo->prepare($sql)->execute([$cantidad, MAXIMO_POR_ARTICULO, $id, $usuario]);
        }

    } elseif ($accion === 'quitar') {
        $id = (int) ($datos['id'] ?? 0);
        $pdo->prepare('DELETE FROM carrito WHERE id = ? AND usuario_id = ?')->execute([$id, $usuario]);

    } else {
        responder(400, ['error' => 'Acción no válida.']);
    }
}

responder(200, leer_carrito($pdo, $usuario));
