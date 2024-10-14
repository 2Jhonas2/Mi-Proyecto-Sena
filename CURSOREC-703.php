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

// Obtener la dirección del coordinador actual
$direccion_usuario_actual = '';
$query_direccion = $conexion->query("SELECT ID_UBICACION FROM usuarios WHERE ID_USUARIOS = '$id_usuario_actual' LIMIT 1");

if ($query_direccion->num_rows > 0) {
    $direccion_usuario_actual = $query_direccion->fetch_assoc()['ID_UBICACION'];
}

// Obtener el ID del usuario de la sesión
$ID_USUARIOS = $_SESSION['ID_USUARIOS'];

// Consulta para obtener los datos del curso con ID 703
$sql = "SELECT ID_CURSO, ID_UBICACION, CANTIDAD_ALUMNOS_CURSO, DIRECTOR_CURSO, ESTADO_CURSO FROM curso WHERE ID_CURSO = 703";
$stmt = $conexion->prepare($sql);
$stmt->execute();

// Vincular resultados
$stmt->bind_result($ID_CURSO, $ID_UBICACION, $CANTIDAD_ALUMNOS_CURSO, $DIRECTOR_CURSO, $ESTADO_CURSO);
$stmt->fetch();
$stmt->close(); // Cerrar la consulta después de obtener los datos

// Interpretar el estado del curso
$estadoTexto = ($ESTADO_CURSO == 1) ? "Activo" : "Inactivo";

// Obtener la fecha actual
$fechaActual = date('Y-m-d');

// Obtener la última asignación del día actual
$sqlUltimaAsignacion = "SELECT CANTIDAD_ASIGNADO, FECHA_ASIGNACION FROM refrigerios_curso WHERE ID_CURSO = ? AND DATE(FECHA_ASIGNACION) = ? ORDER BY FECHA_ASIGNACION DESC LIMIT 1";
$stmt = $conexion->prepare($sqlUltimaAsignacion);
$stmt->bind_param("is", $ID_CURSO, $fechaActual); // Cambiado a "is" para el parámetro de fecha
$stmt->execute();
$stmt->bind_result($cantidadAsignada, $fechaAsignacion);
$stmt->fetch();
$stmt->close();

// Si no hay asignación, mostrar "No Actualizado"
$mensajeAsignacion = (is_null($cantidadAsignada)) ? "No Actualizado" : htmlspecialchars($cantidadAsignada);

// Verificar si se ha enviado el formulario para actualizar datos del curso
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['CANTIDAD_ALUMNOS_CURSO'])) {
        $cantidad = $_POST['CANTIDAD_ALUMNOS_CURSO'];
        $director = $_POST['DIRECTOR_CURSO'];
        $estado = $_POST['ESTADO_CURSO']; // Obtener el estado enviado desde el formulario

        // Actualizar los datos en la base de datos
        $sqlUpdate = "UPDATE curso SET CANTIDAD_ALUMNOS_CURSO = ?, DIRECTOR_CURSO = ?, ESTADO_CURSO = ? WHERE ID_CURSO = 703";
        $stmt = $conexion->prepare($sqlUpdate);
        $stmt->bind_param("ssi", $cantidad, $director, $estado); // Quitar $id, ya que ID_CURSO es constante 703

        if ($stmt->execute()) {
            // Redirigir a CURSOREC-703.php después de la actualización exitosa
            header("Location: CURSOREC-703.php");
            exit(); // Terminar el script después de la redirección
        } else {
            echo "Error al actualizar los datos: " . $stmt->error;
        }

        $stmt->close(); // Cerrar la consulta después de ejecutar
    } elseif (isset($_POST['CANTIDAD_ASIGNADO'])) {
        $cantidadAsignada = $_POST['CANTIDAD_ASIGNADO'];

        // Obtener el ID_REFRIGERIO más reciente
        $sqlRefrigerio = "SELECT ID_REFRIGERIO FROM refrigerio ORDER BY FECHA_REFRIGERIO DESC LIMIT 1";
        $stmt = $conexion->prepare($sqlRefrigerio);
        $stmt->execute();
        $stmt->bind_result($ID_REFRIGERIO);
        $stmt->fetch();
        $stmt->close();

        // Insertar en refrigerios_curso
        $sqlInsert = "INSERT INTO refrigerios_curso (ID_CURSO, ID_REFRIGERIO, CANTIDAD_ASIGNADO, FECHA_ASIGNACION) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sqlInsert);
        $stmt->bind_param("iiis", $ID_CURSO, $ID_REFRIGERIO, $cantidadAsignada, $fechaActual);

        if ($stmt->execute()) {
            // Obtener la última cantidad asignada después de la inserción
            $sqlUltimaAsignacion = "SELECT CANTIDAD_ASIGNADO, FECHA_ASIGNACION FROM refrigerios_curso WHERE ID_CURSO = ? AND DATE(FECHA_ASIGNACION) = ? ORDER BY FECHA_ASIGNACION DESC LIMIT 1";
            $stmt = $conexion->prepare($sqlUltimaAsignacion);
            $stmt->bind_param("is", $ID_CURSO, $fechaActual);
            $stmt->execute();
            $stmt->bind_result($cantidadAsignada, $fechaAsignacion);
            $stmt->fetch();
            $stmt->close();

            // Mostrar la cantidad asignada actualizada
            $mensajeAsignacion = htmlspecialchars($cantidadAsignada);

            // Redirigir a CURSOREC-703.php después de la inserción exitosa
            header("Location: CURSOREC-703.php");
            exit(); // Terminar el script después de la redirección
        } else {
            echo "Error al insertar los datos: " . $stmt->error;
        }

        $stmt->close(); // Cerrar la consulta después de ejecutar
    } else {
        echo "Error: El campo CANTIDAD_ASIGNADO no está definido.";
    }
}

$UBICACION = '';
if ($ID_UBICACION == 1){
	$UBICACION = 'Sede A';
} elseif ($ID_UBICACION == 2){
	$UBICACION = 'Sede B';
} elseif ($ID_UBICACION == 3){
	$UBICACION = 'Sede C';
}else {
	$UBICACION = 'Desconocido';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patio De Refrigerios</title>
    <link rel="icon" href="IMG/logo-pag.png">
    <link rel="stylesheet" type="text/css" href="CSS/curso.css">
</head>
<body class="loggedin">
    <div>
        <header>
            <h1 id="nombre">
                DIEGO MONTANA CUELLAR IED
            </h1>
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
                <h2 class="Ah2"><?php echo htmlspecialchars($ID_CURSO) ?></h2>
                <div class="Datos_curso">
                    <h3>Dirección</h3>
                    <p><?php echo htmlspecialchars($UBICACION) ?></p>
                    <h3>Cantidad Alumnos</h3>
                    <p><?php echo htmlspecialchars($CANTIDAD_ALUMNOS_CURSO) ?></p>
                    <h3>Director Curso</h3>
                    <p><?php echo htmlspecialchars($DIRECTOR_CURSO) ?></p>
                    <h3>Estado</h3>
                    <p><?php echo htmlspecialchars($estadoTexto); ?></p>
                </div>
                <div class="asist_curso">
                    <h3>Asistencia De Alumnos</h3>
                    <p><?php echo $mensajeAsignacion; ?></p>
                </div>
            </section>
            <section>
                <button id="abrirModal" class="boton">Actualizar Datos</button>

                <dialog id="modal">
                    <h2>Actualizar Datos</h2>
                    <form method="POST" action="CURSOREC-703.php">
                        <input type="hidden" name="ID_CURSO" value="703">
                        <label for="CANTIDAD_ALUMNOS_CURSO">Cantidad Alumnos</label><br>
                        <input type="number" name="CANTIDAD_ALUMNOS_CURSO" value="<?php echo htmlspecialchars($CANTIDAD_ALUMNOS_CURSO) ?>" required><br>

                        <label for="DIRECTOR_CURSO">Director Curso</label><br>
                        <input type="text" name="DIRECTOR_CURSO" value="<?php echo htmlspecialchars($DIRECTOR_CURSO) ?>" required><br>
                        
                        <label for="ESTADO_CURSO">Estado</label><br>
                        <select name="ESTADO_CURSO" required>
                            <option value="1" <?php if ($ESTADO_CURSO == 1) echo 'selected'; ?>>Activo</option>
                            <option value="2" <?php if ($ESTADO_CURSO == 2) echo 'selected'; ?>>Inactivo</option>
                        </select><br><br>
                        <button type="submit" class="btn">Actualizar</button>
                    </form>
                    <button id="cerrarModal"><span>❌</span></button>
                </dialog>
                <button id="abrirModal1" class="boton">Asignar Refrigerio</button>
                <dialog id="modal1">
                    <h2>Asignar Refrigerio</h2>
                    <form method="POST" action="CURSOREC-703.php">
                        <label for="CANTIDAD_ASIGNADO">Cantidad Asignada</label><br>
                        <input type="number" name="CANTIDAD_ASIGNADO" required><br><br>
                        <button type="submit" class="btn">Asignar</button>
                    </form>
                    <button id="cerrarModal1"><span>❌</span></button>
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
    <script src="JS/cursos.js"></script>
</body>
</html>