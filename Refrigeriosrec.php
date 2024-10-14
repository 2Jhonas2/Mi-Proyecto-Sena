<?php
session_start(); // Asegúrate de que la sesión esté iniciada

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "refrigerios";

date_default_timezone_set('America/Bogota');

// Conexión a la base de datos
$conexion = new mysqli($servername, $username, $password, $dbname);

// Verificar si la conexión es correcta
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer el estado "disponible" al agregar un nuevo refrigerio
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descripcion = $_POST['DESCRIPCION_REFRIGERIO'];
    $tipo = $_POST['TIPO_REFRIGERIO'];
    $imagen = $_FILES['IMAGEN_REFRIGERIO']['name'];
    $ubicacion = $_POST['ID_UBICACION']; // Se agrega la ubicación

    // Aquí puedes mover el archivo de imagen a la carpeta deseada
    // move_uploaded_file($_FILES['IMAGEN_REFRIGERIO']['tmp_name'], "ruta/donde/guardar/".$imagen);

    // Insertar el refrigerio junto con su ubicación
    $sql_insert = "INSERT INTO refrigerio (IMAGEN_REFRIGERIO, DESCRIPCION_REFRIGERIO, TIPO_REFRIGERIO, FECHA_REFRIGERIO, HORA_REFRIGERIO, ESTADO_REFRIGERIO, ID_UBICACION) VALUES (?, ?, ?, NOW(), NOW(), 'disponible', ?)";
    $stmt = $conexion->prepare($sql_insert);
    $stmt->bind_param("sssi", $imagen, $descripcion, $tipo, $ubicacion);
    $stmt->execute();
    $stmt->close();
}

// Consultar los últimos refrigerios
$sql = "SELECT ID_REFRIGERIO, IMAGEN_REFRIGERIO, DIA_REFRIGERIO, ID_UBICACION, DESCRIPCION_REFRIGERIO, FECHA_REFRIGERIO, HORA_REFRIGERIO, TIPO_REFRIGERIO, ESTADO_REFRIGERIO FROM refrigerio ORDER BY ID_REFRIGERIO DESC LIMIT 3"; 
$result = $conexion->query($sql);

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

            // Actualizar el estado en la base de datos
            $sql_update = "UPDATE refrigerio SET ESTADO_REFRIGERIO = ? WHERE ID_REFRIGERIO = ?";
            $stmt_update = $conexion->prepare($sql_update);
            $stmt_update->bind_param("si", $estado, $row['ID_REFRIGERIO']);
            $stmt_update->execute();
            $stmt_update->close();
        } else {
            $estado = 'disponible';
        }

        // Determinar la ubicación
        $UBICACION = '';
        if (isset($row['ID_UBICACION'])) {
            $ID_UBICACION = $row['ID_UBICACION'];

            if ($ID_UBICACION == 1) {
                $UBICACION = 'Sede A';
            } elseif ($ID_UBICACION == 2) {
                $UBICACION = 'Sede B';
            } elseif ($ID_UBICACION == 3) {
                $UBICACION = 'Sede C';
            } elseif ($ID_UBICACION == 4) {
                $UBICACION = 'Para todas las sedes';
            } else {
                $UBICACION = 'Desconocido';
            }
        }

        $imagenes[] = [
            'url' => $row['IMAGEN_REFRIGERIO'],
            'dia'=> $row['DIA_REFRIGERIO'],
            'descripcion' => $row['DESCRIPCION_REFRIGERIO'],
            'fecha' => $row['FECHA_REFRIGERIO'],
            'hora' => $row['HORA_REFRIGERIO'],
            'tipo' => $row['TIPO_REFRIGERIO'],
            'estado' => $estado,
            'ubicacion' => $ID_UBICACION // Utiliza el valor de $UBICACION
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="CSS/refrigerios.css">
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
                            echo '<p><strong>Ubicación:</strong> ' . $imagenes[0]['ubicacion'] . '</p>';


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
            <a href="Informes/fpdf/reporte-refrigerio.php" target="_blank" rel="noopener noreferrer" id="informe"><i class="fa-solid fa-file-pdf"></i> Generar Informe</a>
                <button id="abrirModal" class="agregar"><span>+</span></button>

                <dialog id="modal">
                    <h2 class="Ah2">Agregar un Nuevo Refrigerio</h2>
                    <form id="form-refrigerio" enctype="multipart/form-data" action="agregar_refrigeriorec.php" method="post">
                        <input type="text" id="DESCRIPCION_REFRIGERIO" name="DESCRIPCION_REFRIGERIO" required placeholder="Descripción"><br><br>

                        <select id="TIPO_REFRIGERIO" name="TIPO_REFRIGERIO" required>
                            <option value="" selected disabled>Tipo Refrigerio</option>
                            <option value="Jornada Escolar">Jornada Escolar</option>
                            <option value="Articulacion">Articulacion</option>
                        </select><br><br>

                        <label for="ubicacion">Ubicación:</label><br>
                        <select id="ubicacion" name="ID_UBICACION" required>
                            <option value="">Seleccionar ubicacion</option>
                            <option value="1">Sede A</option>
                            <option value="2">Sede B</option>
                            <option value="3">Sede C</option>
                            <option value="4">Para todas las sedes</option>
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
                    $cursos_result = $conexion->query("SELECT ID_CURSO FROM curso ORDER BY ID_CURSO ASC");
    
                    while ($curso = $cursos_result->fetch_assoc()) {
                    echo '<a href="CURSOREC-' . $curso['ID_CURSO'] . '.php" class="curso_' . $curso['ID_CURSO'] . '">' . $curso['ID_CURSO'] . '</a>';
                    }
                ?>
            </div>
        </aside>
    </div>
    <script src="JS/refrigeriosrec.js"></script>
</body>
</html>
