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
                        <select name="ID_UBICACION[]" id="ID_UBICACION" required>
                            <option value="" disabled selected >Seleccionar Direccion</option>
                            <option value="1">Sede A</option>
                            <option value="2">Sede B</option>
                            <option value="3">Sede C</option>
                            <option value="4">Todas Las Sedes</option>
                        </select>
                        <br><br>

                        <select name="ID_ROL[]" id="ID_ROL" class="llenar select-centrado" required>
                            <option value="" selected disabled>Seleccione Rol</option>
                            <option value="1">Coordinador</option>
                            <option value="2">Auxiliar</option>
                            <option value="3">Rector</option>
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

document.addEventListener('DOMContentLoaded', function() {
    const ubicacionSelect = document.getElementById('ID_UBICACION');
    const rolSelect = document.getElementById('ID_ROL');

    // Cuando se cambia "Ubicación"
    ubicacionSelect.addEventListener('change', function() {
        if (ubicacionSelect.value == '4') { // Si selecciona "Todas Las Sedes"
            rolSelect.value = '3'; // Automáticamente selecciona "Rector"
        } else if (ubicacionSelect.value != '4' && rolSelect.value == '3') {
            rolSelect.value = ''; // Deselecciona "Rector"
        }
    });

    // Cuando se cambia "Rol"
    rolSelect.addEventListener('change', function() {
        if (rolSelect.value == '3') { // Si selecciona "Rector"
            ubicacionSelect.value = '4'; // Automáticamente selecciona "Todas Las Sedes"
        } else if (rolSelect.value != '3' && ubicacionSelect.value == '4') {
            ubicacionSelect.value = ''; // Deselecciona "Todas Las Sedes"
        }
    });
});

