<?php
// Configuración de la tienda: el nombre y las categorías viven aquí,
// así se cambian en un solo lugar para todo el sitio.

const TIENDA = 'Changolandia';

function categorias(): array
{
    return [
        'Figuras y decoración' => '🐒',
        'Bebés reborn'         => '👶',
        'Disfraces'            => '🎭',
        'Pijamas'              => '🌙',
        'Peluches'             => '🧸',
        'Mochilas y bolsas'    => '🎒',
        'Escolar'              => '✏️',
        'Llaveros'             => '🔑',
        'Toallas'              => '🛁',
        'Herramientas'         => '🔧',
    ];
}

// Si una categoría no es válida, se usa "General" (nunca se confía en el formulario).
function categoria_valida(string $categoria): string
{
    return array_key_exists($categoria, categorias()) ? $categoria : 'General';
}

// Colores que se pueden escoger según la categoría del artículo.
function colores_por_categoria(): array
{
    return [
        'Figuras y decoración' => ['Café', 'Beige', 'Colores surtidos'],
        'Bebés reborn'         => ['Café claro', 'Café oscuro'],
        'Disfraces'            => ['Café', 'Beige', 'Chocolate'],
        'Pijamas'              => ['Beige', 'Azul marino', 'Gris', 'Café'],
        'Peluches'             => ['Café', 'Beige', 'Rojizo'],
        'Mochilas y bolsas'    => ['Café', 'Beige', 'Rosa'],
        'Escolar'              => ['Multicolor', 'Rosa', 'Azul', 'Verde'],
        'Llaveros'             => ['Café', 'Naranja', 'Azul'],
        'Toallas'              => ['Amarillo', 'Rosa', 'Azul', 'Morado'],
        'Herramientas'         => ['Naranja', 'Azul', 'Rosa', 'Amarillo'],
    ];
}

function colores_de(string $categoria): array
{
    return colores_por_categoria()[$categoria] ?? ['Único'];
}

// Color de la bolita que se dibuja junto al nombre del color.
function color_hex(string $nombre): string
{
    $mapa = [
        'Café' => '#7b4a24', 'Café claro' => '#b07a4a', 'Café oscuro' => '#4a2c14', 'Chocolate' => '#3b2415',
        'Beige' => '#e8d5b5', 'Colores surtidos' => '#f5c518', 'Azul marino' => '#1f2a56', 'Gris' => '#8a8f98',
        'Rojizo' => '#b4532a', 'Rosa' => '#f48fb1', 'Multicolor' => '#ff8a3d', 'Azul' => '#4a90e2',
        'Verde' => '#5cb85c', 'Naranja' => '#ff8c1a', 'Amarillo' => '#f5c518', 'Morado' => '#9b7bd6',
    ];
    return $mapa[$nombre] ?? '#cccccc';
}

// En la base solo se guarda el nombre base de la foto (por ejemplo "pijama2").
// Aquí se busca el archivo real en img/catalogo/ y se devuelve una ruta que sirve desde cualquier carpeta.
function foto_producto($base)
{
    $base = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $base);
    if ($base === '') {
        return null;
    }
    foreach (['webp', 'jpg', 'jpeg', 'png'] as $extension) {
        if (is_file(__DIR__ . "/img/catalogo/$base.$extension")) {
            return "/img/catalogo/$base.$extension";
        }
    }
    return null;
}

// El carrito vive en la base de datos, ligado a la cuenta de cada cliente.
function asegurar_tabla_carrito(PDO $pdo): void
{
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS carrito (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id  INT          NOT NULL,
        producto_id INT          NOT NULL,
        color       VARCHAR(30)  NOT NULL,
        cantidad    INT UNSIGNED NOT NULL DEFAULT 1,
        creado_en   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY articulo_unico (usuario_id, producto_id, color)
    )");
}

// Las fotos se guardan DENTRO de la base de datos (columna datos): en Render los archivos
// subidos al disco se borran en cada despliegue, la base de datos no.
function asegurar_tabla_fotos(PDO $pdo): void
{
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS fotos (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT          NULL,
        nombre     VARCHAR(100) NOT NULL,
        archivo    VARCHAR(150) NOT NULL UNIQUE,
        tipo       VARCHAR(20)  NOT NULL,
        bytes      INT UNSIGNED NOT NULL,
        creado_en  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    $columnas = $pdo->query("
        SELECT COLUMN_NAME FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'fotos'
    ")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('datos', $columnas, true)) {
        $pdo->exec('ALTER TABLE fotos ADD COLUMN datos MEDIUMBLOB NULL');
    }
    if (!in_array('rol_foto', $columnas, true)) {
        $pdo->exec("ALTER TABLE fotos ADD COLUMN rol_foto VARCHAR(10) NOT NULL DEFAULT 'galeria'");
    }
}
