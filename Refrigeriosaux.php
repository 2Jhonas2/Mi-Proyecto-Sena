<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); // Asegúrate de que la sesión esté iniciada

// Verifica si el usuario está autenticado y tiene el rol adecuado
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['ID_ROL']) || $_SESSION['ID_ROL'] != 2) { // Asumiendo rol '2' es para auxiliar
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

// Obtener la dirección del usuario actual
$direccion_usuario_actual = '';
$query_direccion = $conexion->query("SELECT ID_UBICACION FROM usuarios WHERE ID_USUARIOS = '$id_usuario_actual' LIMIT 1");

if ($query_direccion->num_rows > 0) {
    $direccion_usuario_actual = $query_direccion->fetch_assoc()['ID_UBICACION'];
}

// Establecer el estado "disponible" al agregar un nuevo refrigerio
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descripcion = $_POST['DESCRIPCION_REFRIGERIO'];
    $tipo = $_POST['TIPO_REFRIGERIO'];
    $imagen = $_FILES['IMAGEN_REFRIGERIO']['name'];

    // Aquí puedes mover el archivo de imagen a la carpeta deseada
    // move_uploaded_file($_FILES['IMAGEN_REFRIGERIO']['tmp_name'], "ruta/donde/guardar/".$imagen);

    // Obtener la fecha y hora actuales automáticamente desde la base de datos
    $sql_insert = "INSERT INTO refrigerio (IMAGEN_REFRIGERIO, DESCRIPCION_REFRIGERIO, TIPO_REFRIGERIO, FECHA_REFRIGERIO, HORA_REFRIGERIO, ESTADO_REFRIGERIO, ID_UBICACION) VALUES (?, ?, ?, NOW(), NOW(), 'disponible', ?)";
    $stmt = $conexion->prepare($sql_insert);
    $stmt->bind_param("sssi", $imagen, $descripcion, $tipo, $direccion_usuario_actual);
    $stmt->execute();
    $stmt->close();
}

// Consultar los refrigerios de la sede del usuario o con ID_UBICACION = 4
$sql = "SELECT r.ID_REFRIGERIO, r.IMAGEN_REFRIGERIO, r.DIA_REFRIGERIO, r.DESCRIPCION_REFRIGERIO, r.FECHA_REFRIGERIO, r.HORA_REFRIGERIO, r.TIPO_REFRIGERIO, r.ESTADO_REFRIGERIO 
        FROM refrigerio r 
        JOIN ubicaciones u ON r.ID_UBICACION = u.ID_UBICACION 
        WHERE u.ID_UBICACION = ? OR u.ID_UBICACION = 4
        ORDER BY r.ID_REFRIGERIO DESC LIMIT 3"; 

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $direccion_usuario_actual); // Pasar el ID de ubicación del usuario actual
$stmt->execute();
$result = $stmt->get_result();

$imagenes = []; // Crear un arreglo de imágenes

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Calcular la diferencia de tiempo
        $fecha_refrigerio = new DateTime($row['FECHA_REFRIGERIO'] . ' ' . $row['HORA_REFRIGERIO']);
        $fecha_actual = new DateTime();
        $intervalo = $fecha_refrigerio->diff($fecha_actual);
        
        // Cambiar el estado a "agotado" si han pasado más de 3 horas
        if ($intervalo->h >= 3 || ($intervalo->h == 2 && $intervalo->i > 0)) {
            $estado = 'agotado';
        } else {
            $estado = 'disponible';
        }

        // Actualizar el estado en la base de datos
        $sql_update = "UPDATE refrigerio SET ESTADO_REFRIGERIO = ? WHERE ID_REFRIGERIO = ?";
        $stmt_update = $conexion->prepare($sql_update);
        $stmt_update->bind_param("si", $estado, $row['ID_REFRIGERIO']);
        $stmt_update->execute();
        $stmt_update->close();

        $imagenes[] = [
            'url' => $row['IMAGEN_REFRIGERIO'],
            'dia'=> $row['DIA_REFRIGERIO'],
            'descripcion' => $row['DESCRIPCION_REFRIGERIO'],
            'fecha' => $row['FECHA_REFRIGERIO'],
            'hora' => $row['HORA_REFRIGERIO'],
            'tipo' => $row['TIPO_REFRIGERIO'],
            'estado' => $estado
        ];
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Patio De Refrigerios</title>
    <link rel="icon" href="IMG/logo-pag.png">
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="CSS/Refrigerio.css">
</head>
<body class="loggedin">
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
                        <a href="inicioaux.php"><span>🏡</span>Inicio</a>
                        <a href="Refrigeriosaux.php"><span>🍔</span>Refrigerios</a>
                        <a href="Perfilaux.php"><span>👤</span>Perfil</a>
                        <a href="#" class="C-S" onclick="confirmarCerrarSesion(event)"><span>🔒</span>Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <div>
        <article>
            <h2 class="Ah2">Refrigerios</h2>
            <div class="carrusel">
                <div class="atras">
                    <img id="atras" src="img/atras.svg" alt="atras" loading="lazy">
                </div>

                <div class="imagenes">
                    <div id="img">
                        <?php
                        if (count($imagenes) > 0) {
                            foreach ($imagenes as $imagen) {
                                echo '<img class="img" src="' . $imagen['url'] . '" alt="Refrigerio" loading="lazy">';
                            }
                        } else {
                            echo '<img class="img" src="../IMG/placeholder.jpg" alt="No hay imagenes disponibles" loading="lazy">'; // Imagen de placeholder
                        }
                        ?>
                    </div>

                    <div id="texto" class="texto">
                        <?php
                        if (count($imagenes) > 0) {
                            echo '<h2>' . $imagenes[0]['dia'] . '</h2>';
                            echo '<p><strong>Descripción:</strong> ' . $imagenes[0]['descripcion'] . '</p>';
                            echo '<p><strong>Fecha:</strong> ' . $imagenes[0]['fecha'] . '</p>';
                            echo '<p><strong>Hora:</strong> ' . $imagenes[0]['hora'] . '</p>';
                            echo '<p><strong>Tipo de Refrigerio:</strong> ' . $imagenes[0]['tipo'] . '</p>';

                            // Mostrar el estado con la clase adecuada
                            echo '<p><strong>Estado:</strong> <span class="estado-confirmado ' . $imagenes[0]['estado'] . '">' . ucfirst($imagenes[0]['estado']) . '</span></p>';
                        } else {
                            echo '<h2>No hay refrigerios disponibles.</h2>';
                        }
                        ?>
                    </div>
                </div>

                <div class="adelante" id="adelante">
                    <img src="img/adelante.svg" alt="adelante" loading="lazy">
                </div>
            </div>

            <div class="puntos" id="puntos"></div>

            <script>
                let imagenes = <?php echo json_encode($imagenes); ?>; // Pasar el arreglo de imágenes a JavaScript
            </script>
            <section>
                <button id="abrirModal" class="agregar"><span>+</span></button>

                <dialog id="modal">
                    <h2 class="Ah2">Agregar un Nuevo Refrigerio</h2>
                    <form id="form-refrigerio" enctype="multipart/form-data" action="agregar_refrigerio.php" method="post">
                        <input type="text" id="DESCRIPCION_REFRIGERIO" name="DESCRIPCION_REFRIGERIO" required placeholder="Descripción"><br><br>

                        <select id="TIPO_REFRIGERIO" name="TIPO_REFRIGERIO" required>
                            <option value="" selected disabled>Tipo Refrigerio</option>
                            <option value="Jornada Escolar">Jornada Escolar</option>
                            <option value="Articulacion">Articulacion</option>
                        </select><br><br>

                        <label for="imagen">Imagen:</label>
                        <input type="file" id="IMAGEN_REFRIGERIO" name="IMAGEN_REFRIGERIO" accept="image/*" required><br><br>

                        <div class="btn-container">
                            <button type="submit" class="btn">Agregar Refrigerio</button>
                            <button id="cerrarModal"><span>❌</span></button>
                        </div>
                    </form>
                </dialog>
            </section>
        </article>
    </div>  
    <div>
        <aside>
        <div id="cursos">
                <?php 
                    $cursos_result = $conexion->query("SELECT ID_CURSO FROM curso WHERE ID_UBICACION = '$direccion_usuario_actual'");
    
                    while ($curso = $cursos_result->fetch_assoc()) {
                    echo '<a href="CURSOAUX-' . $curso['ID_CURSO'] . '.php" class="curso_' . $curso['ID_CURSO'] . '">' . $curso['ID_CURSO'] . '</a>';
                    }
                ?>
            </div>
        </aside>
    </div>
    <script src="JS/refrigerios.js"></script>
</body>
</html>
