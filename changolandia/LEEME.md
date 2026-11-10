# Changolandia 🐒

Tienda de artículos de changos (figuras, bebés reborn, disfraces, pijamas, peluches,
mochilas, escolar, llaveros, toallas y herramientas).

## Cómo ponerla en marcha
1. Sube el proyecto a tu repositorio y espera el despliegue en Render (el Dockerfile no cambió).
2. Inicia sesión y abre UNA vez `/actualizar_tienda.php`: agrega la columna `categoria`
   a `productos` y carga 23 artículos de ejemplo si la tabla está vacía.
   (`/actualizar_tienda.php?sembrar=1` vuelve a cargarlos aunque ya haya productos.)
3. Entra a `/catalogo.php`.

## Qué cambió
- Nuevos: `tienda.php` (nombre y categorías), `catalogo.php` (reemplaza a `cursos.php`),
  `actualizar_tienda.php`.
- `crud/`: ahora maneja categoría; `editar.php` usa consultas preparadas.
- `menu.php`, `menu_nav.php`, `login.php`, `registro.php`, `dashboard.php`, `chat.php`,
  `encuesta/`, `mapa/`, `foto/`, `ubicacion/`, `video.php`: textos de tienda.
- `estilos.css` y `encuesta/estilos_encuesta.css`: paleta guinda/rosa del logo.
- Sin cambios de lógica: `tracker.js`, `panel.js`, `api/`, `conexion.php`, `foto/api`, `ubicacion/api`.

## Pendientes
- `video.php` va en la raíz y el video en `video/mi-video.mp4`.
- La clave de Google Maps está escrita en `mapa/mapa.php`: restríngela en Google Cloud.
