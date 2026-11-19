<?php
// Abrir UNA vez, con sesión de administrador:  /actualizar_precios.php
// Cambia SOLO los precios (por el nombre de la foto de cada artículo).
// No borra nada: conserva los carritos, las existencias y los nombres.

require 'conexion.php';
requerir_admin();

$precios = [
    'articulo' => 349,
    'articulo2' => 699,
    'articulo3' => 899,
    'articulo4' => 159,
    'bebes' => 2499,
    'bebes2' => 389,
    'bebes3' => 429,
    'bebes4' => 499,
    'disfraz' => 449,
    'disfraz2' => 279,
    'disfraz3' => 499,
    'disfraz4' => 429,
    'pijama' => 549,
    'pijama2' => 499,
    'pijama3' => 649,
    'pijama4' => 599,
    'peluche' => 449,
    'peluche2' => 549,
    'peluche3' => 289,
    'peluche4' => 899,
    'mochila' => 1499,
    'mochila2' => 349,
    'mochila3' => 499,
    'mochila4' => 329,
    'escolar' => 189,
    'escolar2' => 149,
    'escolar3' => 119,
    'escolar4' => 99,
    'llavero' => 149,
    'llavero2' => 219,
    'llavero3' => 179,
    'llavero4' => 229,
    'toallas' => 249,
    'toallas2' => 279,
    'toallas3' => 189,
    'toallas4' => 199,
    'herramienta' => 1899,
    'herramienta2' => 249,
    'herramienta3' => 3499,
    'herramienta4' => 159,
];

$stmt = $pdo->prepare('UPDATE productos SET precio = ? WHERE imagen = ?');
$cambiados = 0;
foreach ($precios as $imagen => $precio) {
    $stmt->execute([$precio, $imagen]);
    $cambiados += $stmt->rowCount();
}

echo 'Precios revisados: ' . count($precios) . '. Cambiaron: ' . $cambiados . '. ';
echo '<a href="catalogo.php">Ver el catálogo</a>';
