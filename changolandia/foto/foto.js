// Reemplaza: foto/foto.js

const video         = document.getElementById('video');
const btnCamara     = document.getElementById('btn-camara');
const btnTomar      = document.getElementById('btn-tomar');
const archivo       = document.getElementById('archivo');
const vistaPrevia   = document.getElementById('vista-previa');
const btnGuardar    = document.getElementById('btn-guardar'); // antes buscaba 'btn-subir' (no existe)
const mensaje       = document.getElementById('mensaje');
const galeria       = document.getElementById('galeria');
const galeriaVacia  = document.getElementById('galeria-vacia');
const verTodas      = document.getElementById('ver-todas');   // solo existe para el administrador
const avatar        = document.getElementById('avatar');
const avatarInicial = document.getElementById('avatar-inicial');

let foto = null;        // la imagen lista para guardar (venga de la cámara o del archivo)
let urlTemporal = null; // dirección temporal de la vista previa

function mostrar(texto, clase) {
    mensaje.textContent = texto;
    mensaje.className = clase;
    mensaje.hidden = false;
}

// Las dos opciones (cámara y archivo) terminan aquí.
function usarFoto(imagen) {
    if (!imagen) {
        mostrar('No se pudo capturar la foto. Intenta de nuevo.', 'error');
        return;
    }
    foto = imagen;
    if (urlTemporal) URL.revokeObjectURL(urlTemporal);
    urlTemporal = URL.createObjectURL(imagen);
    vistaPrevia.src = urlTemporal;
    vistaPrevia.hidden = false;
    btnGuardar.disabled = false;
    mensaje.hidden = true;
}

// ---------- Opción 1: cámara en vivo ----------
btnCamara.addEventListener('click', async () => {
    if (!navigator.mediaDevices) {
        mostrar('La cámara solo funciona en https:// o en localhost. Usa la opción de archivo.', 'error');
        return;
    }
    try {
        video.srcObject = await navigator.mediaDevices.getUserMedia({ video: true });
        video.hidden = false;
        btnTomar.disabled = false;
    } catch (error) {
        mostrar('No se pudo abrir la cámara: ' + error.message, 'error');
    }
});

btnTomar.addEventListener('click', () => {
    if (!video.videoWidth) {
        mostrar('La cámara todavía no está lista. Espera un segundo e intenta de nuevo.', 'error');
        return;
    }
    const lienzo = document.createElement('canvas');
    lienzo.width = video.videoWidth;
    lienzo.height = video.videoHeight;
    lienzo.getContext('2d').drawImage(video, 0, 0);
    lienzo.toBlob(usarFoto, 'image/jpeg', 0.9);
});

// ---------- Opción 2: archivo del equipo ----------
archivo.addEventListener('change', () => {
    if (archivo.files[0]) usarFoto(archivo.files[0]);
});

// ---------- Guardar ----------
btnGuardar.addEventListener('click', async () => {
    if (!foto) return;

    const rol = document.querySelector('input[name="rol"]:checked').value; // 'perfil' o 'galeria'
    const datos = new FormData();
    datos.append('foto', foto, 'foto.jpg'); // el servidor ignora este nombre y pone el suyo
    datos.append('rol', rol);

    btnGuardar.disabled = true;
    try {
        const respuesta = await fetch('api/subir.php', { method: 'POST', body: datos });
        let json = {};
        try { json = await respuesta.json(); } catch (e) { /* respuesta sin JSON */ }

        if (!respuesta.ok) {
            mostrar(json.error || 'No se pudo guardar la foto (error ' + respuesta.status + ').', 'error');
            btnGuardar.disabled = false;
            return;
        }

        mostrar('Foto guardada.', 'exito');
        foto = null;
        vistaPrevia.hidden = true;
        archivo.value = '';
        // btnGuardar se queda deshabilitado hasta que haya otra foto

        if (rol === 'perfil') {
            if (json.id) {
                avatar.src = 'api/foto.php?id=' + json.id + '&t=' + Date.now();
                avatar.hidden = false;
                avatarInicial.hidden = true;
            } else {
                location.reload(); // si el servidor no devuelve el id, recarga para ver el avatar nuevo
                return;
            }
        }
        cargarGaleria();
        cargarClientes();
    } catch (error) {
        mostrar('Error de conexión: ' + error.message, 'error');
        btnGuardar.disabled = false;
    }
});

// ---------- Galería ----------
async function cargarGaleria() {
    try {
        const url = 'api/fotos.php' + (verTodas && verTodas.checked ? '?todas=1' : '');
        const respuesta = await fetch(url);
        const datos = await respuesta.json();
        galeria.innerHTML = '';
        // fotos.php responde { perfil: ..., fotos: [...] }
        const fotos = Array.isArray(datos.fotos) ? datos.fotos : [];

        for (const f of fotos) {
            const figura = document.createElement('figure');
            const img = document.createElement('img');
            const pie = document.createElement('figcaption');
            img.src = f.url;
            img.alt = 'Foto de ' + f.usuario;
            pie.textContent = f.usuario; // textContent, nunca innerHTML (evita XSS)
            figura.append(img, pie);
            galeria.append(figura);
        }
        if (galeriaVacia) galeriaVacia.hidden = fotos.length > 0;
    } catch (error) {
        mostrar('No se pudo cargar la galería: ' + error.message, 'error');
    }
}

if (verTodas) verTodas.addEventListener('change', cargarGaleria);

// ---------- Solo administrador: foto de perfil y galería de cada cliente ----------
async function cargarClientes() {
    const contenedor = document.getElementById('clientes');
    if (!contenedor) return; // la sección solo existe para el administrador

    try {
        const respuesta = await fetch('api/usuarios.php');
        const datos = await respuesta.json();
        const clientes = Array.isArray(datos.clientes) ? datos.clientes : [];
        contenedor.innerHTML = '';
        document.getElementById('clientes-vacio').hidden = clientes.length > 0;

        for (const c of clientes) {
            const tarjeta = document.createElement('article');
            tarjeta.className = 'cliente';

            // Cabecera: avatar (o inicial) + nombre + correo
            const cabecera = document.createElement('div');
            cabecera.className = 'cliente-cabecera';

            if (c.perfil) {
                const img = document.createElement('img');
                img.className = 'avatar';
                img.src = c.perfil;
                img.alt = 'Foto de perfil de ' + c.nombre;
                cabecera.append(img);
            } else {
                const inicial = document.createElement('div');
                inicial.className = 'avatar inicial';
                inicial.textContent = (c.nombre || '?').charAt(0).toUpperCase();
                cabecera.append(inicial);
            }

            const info = document.createElement('div');
            const titulo = document.createElement('h3');
            titulo.textContent = c.nombre; // textContent, nunca innerHTML (evita XSS)
            const correo = document.createElement('p');
            correo.className = 'correo';
            correo.textContent = (c.correo || '') + ' · ' + c.galeria.length + ' foto(s) en galería';
            info.append(titulo, correo);
            cabecera.append(info);
            tarjeta.append(cabecera);

            // Galería del cliente
            if (c.galeria.length) {
                const grid = document.createElement('div');
                grid.className = 'galeria';
                for (const url of c.galeria) {
                    const figura = document.createElement('figure');
                    const img = document.createElement('img');
                    img.src = url;
                    img.alt = 'Foto de ' + c.nombre;
                    img.loading = 'lazy';
                    figura.append(img);
                    grid.append(figura);
                }
                tarjeta.append(grid);
            } else {
                const vacio = document.createElement('p');
                vacio.className = 'sin-fotos';
                vacio.textContent = 'Sin fotos en la galería.';
                tarjeta.append(vacio);
            }

            contenedor.append(tarjeta);
        }
    } catch (error) {
        mostrar('No se pudo cargar la lista de clientes: ' + error.message, 'error');
    }
}

cargarGaleria();
cargarClientes();
