<?php
// Copiar a: C:\xampp\htdocs\seguir\menu.php
// Menú de las páginas protegidas (se incluye con include 'menu.php').
?>
<nav class="menu">
    <a href="menu.php">Inicio</a>
    <a href="catalogo.php">Catálogo</a>
    <?php if (es_admin()): ?>
    <a href="dashboard.php">Métricas</a>
    <a href="crud/productos.php">Inventario</a>
    <?php endif; ?>
    <a href="#" id="cambiar-privacidad">Privacidad</a>
    <a href="foto/index.php">Mi perfil</a>
    <a href="logout.php">Salir</a>
</nav>
