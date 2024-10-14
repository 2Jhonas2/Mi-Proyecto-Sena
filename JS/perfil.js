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
    // Obtener el modal y los botones
    const modal = document.getElementById('modal1');
    const abrirModal = document.getElementById('abrirModal1');
    const cerrarModal = document.getElementById('cerrarModal1');

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

// Función para confirmar el cierre de sesión
function confirmarCerrarSesion(event) {
    event.preventDefault(); // Evita que el enlace se siga

    // Pregunta al usuario si realmente desea cerrar sesión
    var confirmar = confirm("¿Estás seguro de que quieres cerrar sesión?");
    if (confirmar) {
        window.location.href = "cerrar_sesion.php"; // Redirige a cerrar_sesion.php
    }
}

document.getElementById("registrationForm").addEventListener("submit", function(event) {
    const password = document.getElementById("password").value;
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/;

    if (!passwordRegex.test(password)) {
        alert("La contraseña debe tener al menos 8 caracteres, incluyendo al menos una letra mayúscula, una letra minúscula y un número.");
        event.preventDefault(); // Evita el envío del formulario si no cumple con los requisitos
    }
});