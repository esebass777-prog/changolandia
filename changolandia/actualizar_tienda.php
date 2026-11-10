<?php
// Se abre UNA vez (con la sesión iniciada): /actualizar_tienda.php
// 1) Crea la tabla productos si no existe.
// 2) Le agrega la columna "categoria" si todavía no la tiene.
// 3) Si la tabla está vacía, carga los artículos de ejemplo.
//    Para volver a cargarlos aunque ya haya productos: /actualizar_tienda.php?sembrar=1

require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$pdo->exec("
CREATE TABLE IF NOT EXISTS productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100)  NOT NULL,
    descripcion TEXT          NOT NULL,
    precio      DECIMAL(10,2) NOT NULL,
    stock       INT           NOT NULL DEFAULT 0,
    categoria   VARCHAR(40)   NOT NULL DEFAULT 'General'
)");

$existe = $pdo->query("
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'productos' AND COLUMN_NAME = 'categoria'
")->fetchColumn();

$mensajes = [];
if (!$existe) {
    $pdo->exec("ALTER TABLE productos ADD COLUMN categoria VARCHAR(40) NOT NULL DEFAULT 'General'");
    $mensajes[] = 'Se agregó la columna categoria.';
}

$total = (int) $pdo->query('SELECT COUNT(*) FROM productos')->fetchColumn();

if ($total === 0 || isset($_GET['sembrar'])) {
    $articulos = [
        // [nombre, descripción, precio, stock, categoría]
        ['Licorera portabotella chango de resina', 'Chango de resina color café que sostiene tu botella de vino.', 625.86, 12, 'Figuras y decoración'],
        ['Chango sentado de resina', 'Figura decorativa de chango sentado, ideal para repisa o escritorio.', 480.00, 8, 'Figuras y decoración'],
        ['Chango con plátano para escritorio', 'Figurita pequeña de chango abrazando un plátano.', 190.00, 20, 'Figuras y decoración'],
        ['Bebé reborn chango 43 cm', 'Silicón suave y realista, con pijama de dinosaurios.', 2500.00, 3, 'Bebés reborn'],
        ['Mini bebé reborn chango 30 cm', 'Versión compacta, cuerpo suave y ojos pintados a mano.', 1450.00, 5, 'Bebés reborn'],
        ['Disfraz infantil de chango', 'Mono con gorro de orejas y pechera. Tallas 4 a 10.', 420.00, 15, 'Disfraces'],
        ['Disfraz de chango para adulto', 'Mono completo con capucha y cola. Talla única.', 590.00, 9, 'Disfraces'],
        ['Pijama kigurumi de chango', 'Pijama de una pieza, felpa suave, con capucha de orejas.', 520.00, 14, 'Pijamas'],
        ['Pijama infantil de chango, 2 piezas', 'Playera y pantalón de algodón con estampado de changos.', 340.00, 18, 'Pijamas'],
        ['Peluche chango 30 cm', 'Peluche clásico de felpa, lavable.', 180.00, 25, 'Peluches'],
        ['Peluche chango brazos largos', 'Chango colgante con brazos largos y velcro en las manos.', 150.00, 30, 'Peluches'],
        ['Mochila escolar de chango', 'Mochila con dos compartimentos y cara de chango en relieve.', 380.00, 16, 'Mochilas y bolsas'],
        ['Pañalera estampada de chango', 'Bolsa con asas y varios bolsillos interiores.', 450.00, 7, 'Mochilas y bolsas'],
        ['Lonchera térmica de chango', 'Lonchera con aislante térmico y cierre.', 190.00, 22, 'Mochilas y bolsas'],
        ['Cuaderno profesional de changos', '100 hojas, raya, pasta dura.', 65.00, 60, 'Escolar'],
        ['Set de lápices y goma de chango', '6 lápices HB y una goma con forma de chango.', 85.00, 40, 'Escolar'],
        ['Estuche de chango', 'Estuche de tela con cierre y cara de chango.', 95.00, 35, 'Escolar'],
        ['Llavero chango de silicón', 'Llavero suave y resistente, en varios colores.', 45.00, 80, 'Llaveros'],
        ['Llavero peluche chango', 'Mini peluche con argolla metálica.', 70.00, 50, 'Llaveros'],
        ['Toalla con capucha de chango', 'Toalla de baño infantil con capucha y orejas.', 260.00, 12, 'Toallas'],
        ['Toalla de mano bordada de chango', 'Toalla de algodón con chango bordado.', 110.00, 28, 'Toallas'],
        ['Máquina manual tipo chango M2', 'Pequeña, para remaches y ojillos.', 1100.00, 4, 'Herramientas'],
        ['Máquina manual chango funcional M6', 'Para remaches, ojillos y forrar botones.', 1999.00, 2, 'Herramientas'],
    ];
    $stmt = $pdo->prepare('INSERT INTO productos (nombre, descripcion, precio, stock, categoria) VALUES (?, ?, ?, ?, ?)');
    foreach ($articulos as $a) {
        $stmt->execute($a);
    }
    $mensajes[] = 'Se cargaron ' . count($articulos) . ' artículos de ejemplo.';
}

echo $mensajes ? implode(' ', $mensajes) : 'La tienda ya estaba actualizada.';
echo ' <a href="catalogo.php">Ver el catálogo</a>';
