// Carrito del perfil: pide el contenido a ../carrito_api.php y lo dibuja.
// Todo texto que viene del servidor se inserta con textContent (nunca innerHTML) para evitar XSS.

const lista  = document.getElementById('carrito-lista');
const vacio  = document.getElementById('carrito-vacio');
const total  = document.getElementById('carrito-total');
const dinero = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });

async function pedirCarrito(cuerpo) {
    const opciones = cuerpo
        ? { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(cuerpo) }
        : {};
    const respuesta = await fetch('../carrito_api.php', opciones);
    if (!respuesta.ok) throw new Error('HTTP ' + respuesta.status);
    return respuesta.json();
}

function boton(texto, accion, id, cantidad, clase = 'mini') {
    const b = document.createElement('button');
    b.type = 'button';
    b.className = clase;
    b.textContent = texto;
    b.dataset.accion = accion;
    b.dataset.id = id;
    if (cantidad !== undefined) b.dataset.cantidad = cantidad;
    return b;
}

function pintar(datos) {
    lista.replaceChildren();
    vacio.hidden = datos.items.length > 0;
    total.hidden = datos.items.length === 0;

    for (const art of datos.items) {
        const li = document.createElement('li');
        li.className = 'carrito-item';

        let imagen;
        if (art.foto) {
            imagen = document.createElement('img');
            imagen.src = art.foto;
            imagen.alt = art.nombre;
            imagen.loading = 'lazy';
        } else {
            imagen = document.createElement('div');
            imagen.className = 'sin-foto';
            imagen.textContent = art.emoji;
        }

        const info = document.createElement('div');
        info.className = 'carrito-info';
        const nombre = document.createElement('strong');
        nombre.textContent = art.nombre;
        const color = document.createElement('span');
        color.textContent = 'Color: ' + art.color + ' · ' + dinero.format(art.precio) + ' c/u';
        const cantidad = document.createElement('div');
        cantidad.className = 'carrito-cantidad';
        const numero = document.createElement('span');
        numero.textContent = art.cantidad;
        cantidad.append(
            boton('−', 'cambiar', art.id, art.cantidad - 1),
            numero,
            boton('+', 'cambiar', art.id, art.cantidad + 1)
        );
        info.append(nombre, color, cantidad);

        const derecha = document.createElement('div');
        derecha.className = 'carrito-der';
        const subtotal = document.createElement('div');
        subtotal.textContent = dinero.format(art.subtotal);
        derecha.append(subtotal, boton('Quitar', 'quitar', art.id, undefined, 'mini btn-claro'));

        li.append(imagen, info, derecha);
        lista.append(li);
    }
    total.textContent = 'Total: ' + dinero.format(datos.total) + ' (' + datos.cantidad_total + ' artículos)';
}

lista.addEventListener('click', async (evento) => {
    const b = evento.target.closest('button[data-accion]');
    if (!b) return;
    b.disabled = true;
    try {
        const cuerpo = { accion: b.dataset.accion, id: Number(b.dataset.id) };
        if (b.dataset.cantidad !== undefined) cuerpo.cantidad = Number(b.dataset.cantidad);
        pintar(await pedirCarrito(cuerpo));
    } catch (error) {
        b.disabled = false;
    }
});

pedirCarrito().then(pintar).catch(() => {
    vacio.textContent = 'No se pudo cargar el carrito. Recarga la página.';
    vacio.hidden = false;
});
