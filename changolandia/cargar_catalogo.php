<?php
// Abrir UNA vez con la sesión iniciada:  /cargar_catalogo.php
// 1) Agrega la columna "imagen" a productos si no existe.
// 2) Con ?confirmar=1 BORRA los productos actuales y carga 40 artículos
//    (4 por categoría), cada uno con su foto de img/catalogo/.

require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$existe = $pdo->query("
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'productos' AND COLUMN_NAME = 'imagen'
")->fetchColumn();
if (!$existe) {
    $pdo->exec("ALTER TABLE productos ADD COLUMN imagen VARCHAR(60) NULL");
}

if (!isset($_GET['confirmar'])) {
    echo 'Esto reemplaza TODOS los productos actuales por los 40 del catálogo. ';
    echo '<a href="cargar_catalogo.php?confirmar=1">Sí, cargar el catálogo</a>';
    exit;
}

// categoría => [nombre base de la foto, nombre del artículo, descripción, precios (4), existencias (4)]
$catalogo = [
    'Figuras y decoración' => ['articulo',    'Figura decorativa de chango', 'Figura de resina para repisa o escritorio.',        [480, 625.86, 190, 350],  [8, 12, 20, 10]],
    'Bebés reborn'         => ['bebes',       'Bebé reborn chango',          'Silicón suave y realista, hecho con detalle.',      [2500, 1450, 1900, 2100], [3, 5, 4, 2]],
    'Disfraces'            => ['disfraz',     'Disfraz de chango',           'Disfraz con capucha, orejas y cola.',               [420, 590, 380, 450],     [15, 9, 12, 7]],
    'Pijamas'              => ['pijama',      'Pijama de chango',            'Pijama suave y cómoda con diseño de chango.',       [520, 340, 450, 390],     [14, 18, 10, 16]],
    'Peluches'             => ['peluche',     'Peluche de chango',           'Peluche de felpa suave, ideal para regalo.',        [180, 150, 220, 260],     [25, 30, 18, 12]],
    'Mochilas y bolsas'    => ['mochila',     'Mochila de chango',           'Mochila o bolsa con diseño de chango.',             [380, 450, 190, 320],     [16, 7, 22, 11]],
    'Escolar'              => ['escolar',     'Artículo escolar de chango',  'Útiles escolares con diseño de chango.',            [65, 85, 95, 120],        [60, 40, 35, 25]],
    'Llaveros'             => ['llavero',     'Llavero de chango',           'Llavero resistente con figura de chango.',          [45, 70, 55, 60],         [80, 50, 65, 70]],
    'Toallas'              => ['toallas',     'Toalla de chango',            'Toalla de algodón con diseño de chango.',           [260, 110, 180, 220],     [12, 28, 15, 10]],
    'Herramientas'         => ['herramienta', 'Máquina manual tipo chango',  'Para remaches, ojillos y forrar botones.',          [1100, 1999, 1450, 1250], [4, 2, 3, 3]],
];

$pdo->exec('DELETE FROM productos');
$stmt = $pdo->prepare('INSERT INTO productos (nombre, descripcion, precio, stock, categoria, imagen) VALUES (?, ?, ?, ?, ?, ?)');

$total = 0;
foreach ($catalogo as $categoria => [$base, $nombre, $descripcion, $precios, $existencias]) {
    for ($i = 0; $i < 4; $i++) {
        $foto = $i === 0 ? $base : $base . ($i + 1);   // pijama, pijama2, pijama3, pijama4
        $stmt->execute(["$nombre · modelo " . ($i + 1), $descripcion, $precios[$i], $existencias[$i], $categoria, $foto]);
        $total++;
    }
}

echo "Se cargaron $total artículos. <a href=\"catalogo.php\">Ver el catálogo</a>";
