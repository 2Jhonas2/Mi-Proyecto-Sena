document.addEventListener('DOMContentLoaded', function () {
    // Obtener el modal de agregar usuario y los botones
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

    // Funcionalidad para agregar más usuarios
    document.getElementById('agregarUsuario').addEventListener('click', function() {
        const formContainer = document.createElement('div');
        formContainer.className = 'AGRE';
        formContainer.innerHTML = `
            <h1>Otro Usuario</h1>
            <input type="hidden" name="accion" value="agregar_usuario">
            <input type="text" name="NOMBRE_USUARIO[]" class="llenar" required placeholder="Nombre">
            <br><br>
            <input type="email" name="CORREO_USUARIO[]" class="llenar" required placeholder="Correo">
            <br><br>
            <input type="tel" name="TELEFONO_USUARIO[]" class="llenar" required placeholder="Teléfono">
            <br><br>
            <input type="password" name="CONTRASENA_USUARIO[]" class="llenar" required placeholder="Contraseña">
            <br><br>
            <select name="DIRECCION_USUARIO[]" id="DIRECCION_USUARIO" required>
                <option value="" disabled selected >Seleccionar Direccion</option>
                <option value="Sede A">Sede A</option>
                <option value="Sede B">Sede B</option>
            </select>
            <br><br>
            <select name="ID_ROL[]" class="llenar select-centrado" required>
                <option value="" selected disabled>Seleccione Rol</option>
                <option value="1">Coordinador</option>
                <option value="2">Auxiliar</option>
            </select>
            <br><br>
        `;
        document.getElementById('usuariosContainer').appendChild(formContainer);
    });

    // Modal para la verificación de contraseña de administrador
    document.getElementById('cerrarModalContraseña').addEventListener('click', function() {
        document.getElementById('modalContraseña').close();
    });
});

// Función para abrir el modal de verificación del administrador
function abrirModalContraseña(idUsuario) {
    document.getElementById('id_usuario').value = idUsuario; // Guardar el ID del usuario a modificar
    document.getElementById('modalContraseña').showModal();
}

// Función para mostrar un mensaje de error
function mostrarError(errorMensaje) {
    alert(errorMensaje);
    window.history.back();  // Regresar a la página anterior
}

// Función para validar las contraseñas
function validarContrasena() {
    const nuevaContrasena = document.getElementById("nueva_contrasena").value;
    const confirmarContrasena = document.getElementById("confirmar_contrasena").value;

    if (nuevaContrasena !== confirmarContrasena) {
        alert("Las contraseñas no coinciden. Inténtalo de nuevo.");
        return false; // Evita el envío del formulario
    }
    return true; // Permite el envío del formulario
}
    
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