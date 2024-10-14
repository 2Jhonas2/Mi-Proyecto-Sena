document.addEventListener('DOMContentLoaded', function () {
    // Obtener el modal y los botones
    const modal = document.getElementById('modal');
    const abrirModal = document.getElementById('abrirModal');
    const cerrarModal = document.getElementById('cerrarModal');

    // Verificar si el navegador soporta el elemento <dialog>
    if (typeof modal.showModal === "function") {
        // Abrir el modal
        abrirModal.addEventListener('click', function () {
            modal.showModal(); // Muestra el modal
        });

        // Cerrar el modal
        cerrarModal.addEventListener('click', function () {
            modal.close(); // Cierra el modal
        });

        // Si se hace clic fuera del contenido del modal, también se cierra
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                modal.close();
            }
        });
    } else {
        alert("El elemento <dialog> no es soportado en este navegador.");
    }

    // Aquí está el resto de tu código de carrusel
    let atras = document.getElementById('atras');
    let adelante = document.getElementById('adelante');
    let imagenCarrusel = document.getElementById('img');
    let descripcion = document.getElementById('texto');
    let puntos = document.getElementById('puntos');
    let titulo = document.getElementById('titulo');
    let actual = 0;

    function actualizarCarrusel() {
        if (imagenes.length > 0) {
            imagenCarrusel.innerHTML = `<img class="img" src="${imagenes[actual].url}" alt="Refrigerio" loading="lazy">`;
            descripcion.innerHTML = `
                <h1>${imagenes[actual].dia || 'No disponible'}</h1>
                <p><strong>Descripción:</strong> ${imagenes[actual].descripcion || 'No disponible'}</p>
                <p><strong>Fecha:</strong> ${imagenes[actual].fecha || 'No disponible'}</p>
                <p><strong>Hora:</strong> ${imagenes[actual].hora || 'No disponible'}</p>
                <p><strong>Tipo de Refrigerio:</strong> ${imagenes[actual].tipo || 'No disponible'}</p>
                <p><strong>Estado:</strong> <span class="estado-confirmado">${imagenes[actual].estado || 'No disponible'}</span></p>
            `;
            titulo.innerText = `Publicación del refrigerio: ${imagenes[actual].fecha || 'No disponible'}`;
        }
        posicionCarrusel();
    }

    function posicionCarrusel() {
        puntos.innerHTML = "";
        for (let i = 0; i < imagenes.length; i++) {
            puntos.innerHTML += (i === actual) ? '<p class="bold">.</p>' : '<p>.</p>';
        }
    }

    atras.addEventListener('click', function () {
        actual = (actual > 0) ? actual - 1 : imagenes.length - 1;
        actualizarCarrusel();
    });

    adelante.addEventListener('click', function () {
        actual = (actual < imagenes.length - 1) ? actual + 1 : 0;
        actualizarCarrusel();
    });

    actualizarCarrusel();

    // Log para verificar los datos de las imágenes en la consola
    console.log(imagenes);
});

// Función para confirmar el cierre de sesión
function confirmarCerrarSesion(event) {
    event.preventDefault(); // Evita que el enlace se siga

    // Pregunta al usuario si realmente desea cerrar sesión
    var confirmar = confirm("¿Estás seguro de que quieres cerrar sesión?");
    if (confirmar) {
        window.location.href = "cerrar_sesion.php"; // Redirige a cerrar_sesion.php
    }
}
    