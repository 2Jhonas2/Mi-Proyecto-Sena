document.getElementById('agregarUsuario').addEventListener('click', function() {
    const formContainer = document.createElement('div');
    formContainer.className = 'AGRE';
    formContainer.innerHTML = `
        <input type="hidden" name="accion" value="agregar_usuario">
                    <input type="hidden" name="accion" value="agregar_usuario">
                    <input type="text" name="NOMBRE_USUARIO[]" id="NOMBRE_USUARIO" class="llenar" required placeholder="Nombre">
                
                    <input type="email" name="CORREO_USUARIO[]" id="CORREO_USUARIO" class="llenar" required placeholder="Correo">
                
                    <input type="tel" name="TELEFONO_USUARIO[]" id="TELEFONO_USUARIO"  class="llenar" required placeholder="Telefono">
                
                    <input type="text" name="DIRECCION_USUARIO[]" id="DIRECCION_USUARIO" class="llenar" required placeholder="Dirección">
                
                    <input type="password" name="CONTRASENA_USUARIO[]" id="CONTRASENA_USUARIO" class="llenar" required placeholder="Contraseña">
                
                    <select name="ID_ROL[]" id="ID_ROL" class="llenar select-centrado" required>
						<option value="" selected disabled>Selecione Rol</option>
                        <option value="1">Coordinador</option>
                        <option value="2">Auxiliar</option>
                    </select>
    `;
    document.getElementById('usuariosContainer').appendChild(formContainer);
});