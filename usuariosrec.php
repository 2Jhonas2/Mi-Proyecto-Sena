<?php
session_start();

// Verifica si el usuario está autenticado y tiene el rol adecuado
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['ID_ROL']) || $_SESSION['ID_ROL'] != 3) {
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
$usuarios_result = $conexion->query("SELECT u.ID_USUARIOS, u.NOMBRE_USUARIO, u.ACTIVO, u.ID_ROL, 
                                     u.ID_UBICACION, 
                                     c.ACTIVO AS COORDINADOR_ACTIVO, 
                                     a.ACTIVO AS AUXILIAR_ACTIVO, 
                                     u.FECHA_CREACION
                                     FROM usuarios u
                                     LEFT JOIN coordinador c ON u.ID_USUARIOS = c.ID_USUARIOS
                                     LEFT JOIN auxiliar a ON u.ID_USUARIOS = a.ID_USUARIOS
                                     WHERE u.ID_USUARIOS != '$id_usuario_actual'
                                     ORDER BY u.NOMBRE_USUARIO ASC;");

// Definir el ID_USUARIOS de la sesión
$ID_USUARIOS = $_SESSION['ID_USUARIOS'];

// Consulta para obtener los datos del usuario
$sql = "SELECT NOMBRE_USUARIO, CORREO_USUARIO, TELEFONO_USUARIO, ID_UBICACION, ID_ROL FROM usuarios WHERE ID_USUARIOS = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('i', $ID_USUARIOS);
$stmt->execute();
$stmt->bind_result($NOMBRE_USUARIO, $CORREO_USUARIO, $TELEFONO_USUARIO, $ID_UBICACION, $ID_ROL);
$stmt->fetch();
$stmt->close();


?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Patio De Refrigerios</title>
    <link rel="icon" href="IMG/logo-pag.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="CSS/users.css">
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
                    <a href="iniciorec.php"><span>🏡</span>Inicio</a>
                    <a href="usuariosrec.php"><span>👥</span>Usuarios</a>
                    <a href="Refrigeriosrec.php"><span>🍔</span>Refrigerios</a>
                    <a href="Perfilrec.php"><span>👤</span>Perfil</a>
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
                            <th>Rol</th>
                            <th>ubicación</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $usuarios_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['NOMBRE_USUARIO']); ?></td>
                            <td><?php echo $row['ACTIVO'] ? 'Activo' : 'Inactivo'; ?></td>
                            <td><?php 
                            if ($row['ID_ROL'] == 1) {
                                 echo 'Coordinador';
                            } elseif ($row['ID_ROL'] == 2) {
                                echo 'Auxiliar';
                            } elseif ($row['ID_ROL'] == 3) {
                                echo 'Rector';
                            } else {
                                echo 'Desconocido';
                            }
                            ?></td>
                            <td><?php 
                            if ($row['ID_UBICACION'] == 1) {
                                echo 'Sede A';
                           } elseif ($row['ID_UBICACION'] == 2) {
                               echo 'Sede B';
                           } elseif ($row['ID_UBICACION'] == 3) {
                               echo 'Sede C';
                           } elseif ($row['ID_UBICACION'] == 4) {
                            echo 'Todas Las Sedes';
                           } else {
                               echo 'Desconocido';
                           }
                            ?></td>
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
                            <a href="agregar-eliminar_usuariorec.php?accion=eliminar&id=<?php echo $row['ID_USUARIOS']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');"><span>❌</span></a>
                            <?php if ($row['ACTIVO']): ?>
                            <a onclick="abrirModalContraseña(<?php echo $row['ID_USUARIOS']; ?>)"><span>✏️</span></a>
                            <?php endif; 
                            }
                            ?>
                                <?php elseif (in_array($row['ID_ROL'], [1, 2])): // Solo para auxiliares ?>
                                    <?php if ($row['ACTIVO']): ?>
                                        <a href="cambiar_estado_usuariorec.php?id=<?php echo $row['ID_USUARIOS']; ?>&estado=0">Desactivar</a>
                                    <?php else: ?>
                                        <a href="cambiar_estado_usuariorec.php?id=<?php echo $row['ID_USUARIOS']; ?>&estado=1">Activar</a>
                                    <?php endif; ?>
                                    <a href="agregar-eliminar_usuariorec.php?accion=eliminar&id=<?php echo $row['ID_USUARIOS']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');"><span>❌</span></a>
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
            <a href="Informes/fpdf/reporte-usuarios.php" target="_blank" rel="noopener noreferrer" id="informe"><i class="fa-solid fa-file-pdf"></i> Generar Informe</a>
            <button id="abrirModal" class="agregar"><span>+</span></button>

            <dialog id="modal">
                <h1 class="Ah2">Agregar Usuario</h1>
                <form id="form-refrigerio" enctype="multipart/form-data" action="agregar-eliminar_usuariorec.php" method="post" class="CAJA">
                    <div class="AGRE" id="usuariosContainer">
                        <input type="hidden" name="accion" value="agregar_usuario">
                        <input type="text" name="NOMBRE_USUARIO[]" id="NOMBRE_USUARIO" class="llenar" required placeholder="Nombre">
                        <br><br>

                        <input type="email" name="CORREO_USUARIO[]" id="CORREO_USUARIO" class="llenar" required placeholder="Correo">
                        <br><br>

                        <input type="tel" name="TELEFONO_USUARIO[]" id="TELEFONO_USUARIO" class="llenar" required placeholder="Telefono">
                        <br><br>

                        <input type="password" name="CONTRASENA_USUARIO[]" id="CONTRASENA_USUARIO" class="llenar" required placeholder="Contraseña" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}" 
                        title="La contraseña debe tener al menos 8 caracteres, incluyendo al menos una letra mayúscula, una letra minúscula y un número." >
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
<script src="JS/usuariorec.js"></script>
</body>
</html>
