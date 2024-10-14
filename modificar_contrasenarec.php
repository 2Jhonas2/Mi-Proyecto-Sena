<?php
session_start();

// Verifica si el usuario está autenticado y tiene el rol adecuado
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['ID_ROL']) || $_SESSION['ID_ROL'] != 3) { // Asumiendo rol '1' es para administrador
    header('Location: index.html');
    exit();
}

// Conexión a la base de datos
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'refrigerios';

date_default_timezone_set('America/Bogota');

$conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

if (mysqli_connect_error()) {
    exit('Fallo en la conexión de MySQL: ' . mysqli_connect_error());
}

// Obtener el ID del usuario actual
$id_usuario_actual = $_SESSION['ID_USUARIOS'] ?? null; // Usa null si no existe

// Obtener la lista de usuarios excepto el usuario actual
$usuarios_result = $conexion->query("SELECT u.ID_USUARIOS, u.NOMBRE_USUARIO, u.ACTIVO, 
                                     c.ACTIVO AS COORDINADOR_ACTIVO, 
                                     a.ACTIVO AS AUXILIAR_ACTIVO, 
                                     u.ID_ROL, u.FECHA_CREACION
                                     FROM usuarios u
                                     LEFT JOIN coordinador c ON u.ID_USUARIOS = c.ID_USUARIOS
                                     LEFT JOIN auxiliar a ON u.ID_USUARIOS = a.ID_USUARIOS
                                     WHERE u.ID_USUARIOS != '$id_usuario_actual'
                                     ORDER BY u.NOMBRE_USUARIO ASC;");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Patio De Refrigerios</title>
    <link rel="icon" href="IMG/logo-pag.png">
    <link rel="stylesheet" href="CSS/usuarios.css">
</head>
<body>
<div>
    <header>
        <h1 id="nombre">DIEGO MONTANA CUELLAR IED</h1>
    </header>
</div>
<div>
    <nav>
        <div>
            <ul>
                <li>
                    <a href="inicio.php"><span>🏡</span>Inicio</a>
                    <a href="usuarios.php"><span>👥</span>Usuarios</a>
                    <a href="Refrigerios.php"><span>🍔</span>Refrigerios</a>
                    <a href="Perfil.php"><span>👤</span>Perfil</a>
                    <a href="#" class="C-S" onclick="confirmarCerrarSesion(event)"><span>🔒</span>Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </nav>
</div>
<div>
    <article>
        <section>
            <main>
                <h2>Usuarios</h2>
                <table class="tablas">
                    <thead>
                        <tr>
                            <th>Nombre de Usuario</th>
                            <th>Estado</th>
                            <th>Coordinador Activo</th>
                            <th>Auxiliar Activo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $usuarios_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['NOMBRE_USUARIO']); ?></td>
                            <td><?php echo $row['ACTIVO'] ? 'Activo' : 'Inactivo'; ?></td>
                            <td><?php echo isset($row['COORDINADOR_ACTIVO']) ? ($row['COORDINADOR_ACTIVO'] ? 'Activo' : 'Inactivo') : 'No aplica'; ?></td>
                            <td><?php echo isset($row['AUXILIAR_ACTIVO']) ? ($row['AUXILIAR_ACTIVO'] ? 'Activo' : 'Inactivo') : 'No aplica'; ?></td>
                            <td>
                            <?php if ($row['ID_ROL'] == 3): // Solo para administradores ?>
                            <!-- Verificar si han pasado más de 3 horas desde la creación del perfil -->
                            <?php
                                $fecha_creacion = new DateTime($row['FECHA_CREACION']);
                                $ahora = new DateTime();
                                $interval = $fecha_creacion->diff($ahora);

                            // Convertir el intervalo en horas
                                $horas = ($interval->days * 24) + $interval->h;

                            if ($horas > 3) {
                                echo "No disponible";
                            } else {
                            ?>
                            <a href="agregar-eliminar_usuario.php?accion=eliminar&id=<?php echo $row['ID_USUARIOS']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');"><span>❌</span></a>
                            <?php if ($row['ACTIVO']): ?>
                            <a onclick="abrirModalContraseña(<?php echo $row['ID_USUARIOS']; ?>)"><span>✏️</span></a>
                            <?php endif; 
                            }
                            ?>
                                <?php elseif (in_array($row['ID_ROL'], [1, 2])): // Solo para auxiliares ?>
                                    <?php if ($row['ACTIVO']): ?>
                                        <a href="cambiar_estado_usuario.php?id=<?php echo $row['ID_USUARIOS']; ?>&estado=0">Desactivar</a>
                                    <?php else: ?>
                                        <a href="cambiar_estado_usuario.php?id=<?php echo $row['ID_USUARIOS']; ?>&estado=1">Activar</a>
                                    <?php endif; ?>
                                    <a href="agregar-eliminar_usuario.php?accion=eliminar&id=<?php echo $row['ID_USUARIOS']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');"><span>❌</span></a>
                                    <?php if ($row['AUXILIAR_ACTIVO'] || $row['COORDINADOR_ACTIVO']): ?>
                                        <a onclick="abrirModalContraseña(<?php echo $row['ID_USUARIOS']; ?>)"><span>✏️</span></a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </main>
        </section>
        <section>
            <button id="abrirModal" class="agregar"><span>+</span></button>

            <dialog id="modal">
                <h1 class="Ah2">Agregar Usuario</h1>
                <form id="form-refrigerio" enctype="multipart/form-data" action="agregar-eliminar_usuario.php" method="post" class="CAJA">
                    <div class="AGRE" id="usuariosContainer">
                        <input type="hidden" name="accion" value="agregar_usuario">
                        <input type="text" name="NOMBRE_USUARIO[]" id="NOMBRE_USUARIO" class="llenar" required placeholder="Nombre">
                        <br><br>

                        <input type="email" name="CORREO_USUARIO[]" id="CORREO_USUARIO" class="llenar" required placeholder="Correo">
                        <br><br>

                        <input type="tel" name="TELEFONO_USUARIO[]" id="TELEFONO_USUARIO" class="llenar" required placeholder="Telefono">
                        <br><br>

                        <input type="password" name="CONTRASENA_USUARIO[]" id="CONTRASENA_USUARIO" class="llenar" required placeholder="Contraseña">
                        <br><br>

                        <select name="DIRECCION_USUARIO[]" id="DIRECCION_USUARIO" required>
                            <option value="" disabled selected >Seleccionar Direccion</option>
                            <option value="Sede A">Sede A</option>
                            <option value="Sede B">Sede B</option>
                        </select>
                        <br><br>

                        <select name="ID_ROL[]" id="ID_ROL" class="llenar select-centrado" required>
                            <option value="" selected disabled>Seleccione Rol</option>
                            <option value="1">Coordinador</option>
                            <option value="2">Auxiliar</option>
                        </select>
                        <br><br>
                    </div>

                    <button type="button" id="agregarUsuario" class="other">Otro Usuario</button>
                    <button type="submit" class="other">Agregar</button>
                    <button id="cerrarModal"><span>❌</span></button>
                </form>
            </dialog>
        </section>
        <section>
            <dialog id="modalContraseña">
                <h1>Verificar Administrador</h1>
                <form id="form-verificar" action="verificar_rec.php" method="post">
                    <input type="hidden" name="id_usuario" id="id_usuario">
                    <input type="password" name="admin_password" id="admin_password" class="llenar" required placeholder="Contraseña del Administrador">
                    <br><br>
                    <button type="submit" class="other">Verificar</button>
                    <button id="cerrarModalContraseña" type="button"><span>❌</span></button>
                </form>
            </dialog>
            <div id="mod">
                <h1 class="Ah2">Modificar Contraseña</h1>
                <form action="procesar_cambio_contrasena.php" method="post" onsubmit="return validarContrasena()">
                    <input type="hidden" name="id_usuario" value="<?php echo $id_usuario_modificar; ?>">
        
                    <input type="password" name="nueva_contrasena" id="nueva_contrasena" required placeholder="Nueva Contraseña" required pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}" 
                    title="La contraseña debe tener al menos 8 caracteres, incluyendo al menos una letra mayúscula, una letra minúscula y un número.">
                    <br><br>
        
                    <input type="password" name="confirmar_contrasena" id="confirmar_contrasena" required placeholder="Confirmar Contraseña" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}" 
                    title="La contraseña debe tener al menos 8 caracteres, incluyendo al menos una letra mayúscula, una letra minúscula y un número.">
                    <br><br>
        
                    <button type="submit" class="other">Modificar Contraseña</button>
                    <a href="usuariosrec.php" id="cancelar"><span>❌</span></a>
                </form>
            </div>
        </section>
    </article>
</div>
<div>
    <aside>
    <div id="cursos">
                <?php 
                    $cursos_result = $conexion->query("SELECT ID_CURSO FROM curso ORDER BY ID_CURSO ASC");
    
                    while ($curso = $cursos_result->fetch_assoc()) {
                    echo '<a href="CURSOREC-' . $curso['ID_CURSO'] . '.php" class="curso_' . $curso['ID_CURSO'] . '">' . $curso['ID_CURSO'] . '</a>';
                    }
                ?>
            </div>
    </aside>
</div>
<footer>
	
</footer>
<script src="JS/usuario.js"></script>
</body>
</html>
