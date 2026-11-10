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
