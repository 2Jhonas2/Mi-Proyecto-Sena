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
});

document.addEventListener('DOMContentLoaded', function () {
    const modal1 = document.getElementById('modal1');
    const abrirModal1 = document.getElementById('abrirModal1');
    const cerrarModal1 = document.getElementById('cerrarModal1');

    // Verificar si el navegador soporta el elemento <dialog>
    if (typeof modal1.showModal === "function") {
        // Abrir el modal
        abrirModal1.addEventListener('click', function () {
            modal1.showModal(); // Muestra el modal
        });

        // Cerrar el modal
        cerrarModal1.addEventListener('click', function () {
            modal1.close(); // Cierra el modal
        });

        // Si se hace clic fuera del contenido del modal, también se cierra
        modal1.addEventListener('click', function (event) {
            if (event.target === modal1) {
                modal1.close();
            }
        });
    } else {
        alert("El elemento <dialog> no es soportado en este navegador.");
    }
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
