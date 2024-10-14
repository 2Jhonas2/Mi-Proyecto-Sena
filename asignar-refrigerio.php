<?php
session_start();

// Credenciales de acceso a la base de datos
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'refrigerios';

// Conexión a la base de datos
$conexion = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

// Verificar la conexión
if ($conexion->connect_error) {
    die('Error de conexión: ' . $conexion->connect_error);
}

// Verificar si se ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== TRUE) {
    header('Location: index.html');
    exit();
}

// Comprobar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $cantidad_asignada = $_POST['CANTIDAD_ASIGNADO'];
    $id_curso = 303; // ID_CURSO estático
    $fecha_asignacion = date('Y-m-d H:i:s'); // Fecha y hora de asignación actual

    // Consultar la ID_UBICACION del curso
    $sql_ubicacion = "SELECT ID_UBICACION FROM curso WHERE ID_CURSO = ?";
    $stmt_ubicacion = $conexion->prepare($sql_ubicacion);
    $stmt_ubicacion->bind_param("i", $id_curso);
    $stmt_ubicacion->execute();
    $resultado_ubicacion = $stmt_ubicacion->get_result();
    
    if ($resultado_ubicacion->num_rows > 0) {
        $row_ubicacion = $resultado_ubicacion->fetch_assoc();
        $id_ubicacion = $row_ubicacion['ID_UBICACION'];
    } else {
        echo "No se encontró la ubicación del curso.";
        exit();
    }

    // Consultar el ID_REFRIGERIO para la fecha actual
    $sql_refrigerio = "SELECT ID_REFRIGERIO FROM refrigerio WHERE FECHA_REFRIGERIO = CURDATE() ORDER BY ID_REFRIGERIO DESC LIMIT 1";
    $resultado_refrigerio = $conexion->query($sql_refrigerio);

    if ($resultado_refrigerio->num_rows > 0) {
        // Si hay un refrigerio para la fecha actual, obtener el ID_REFRIGERIO más reciente
        $row = $resultado_refrigerio->fetch_assoc();
        $id_refrigerio = $row['ID_REFRIGERIO'];
    } else {
        // Si no hay refrigerio para la fecha actual, obtener el último refrigerio agregado
        $sql_refrigerio = "SELECT ID_REFRIGERIO FROM refrigerio ORDER BY ID_REFRIGERIO DESC LIMIT 1";
        $resultado_refrigerio = $conexion->query($sql_refrigerio);

        if ($resultado_refrigerio->num_rows > 0) {
            $row = $resultado_refrigerio->fetch_assoc();
            $id_refrigerio = $row['ID_REFRIGERIO'];
        } else {
            echo "No se encontró ningún refrigerio.";
            exit();
        }
    }

    // Verificar el ID_UBICACION del refrigerio
    $sql_refrigerio_ubicacion = "SELECT ID_UBICACION FROM refrigerio WHERE ID_REFRIGERIO = ?";
    $stmt_refrigerio_ubicacion = $conexion->prepare($sql_refrigerio_ubicacion);
    $stmt_refrigerio_ubicacion->bind_param("i", $id_refrigerio);
    $stmt_refrigerio_ubicacion->execute();
    $resultado_refrigerio_ubicacion = $stmt_refrigerio_ubicacion->get_result();

    if ($resultado_refrigerio_ubicacion->num_rows > 0) {
        $row_refrigerio_ubicacion = $resultado_refrigerio_ubicacion->fetch_assoc();
        $id_refrigerio_ubicacion = $row_refrigerio_ubicacion['ID_UBICACION'];
    } else {
        echo "No se encontró la ubicación del refrigerio.";
        exit();
    }

    // Lógica de asignación según ID_UBICACION
    // Verificamos que el ID_UBICACION del refrigerio coincida con el del curso o sea 4 (todas las sedes)
    if ($id_refrigerio_ubicacion == 4 || $id_ubicacion == 4 || $id_refrigerio_ubicacion == $id_ubicacion) {
        // Insertar en la tabla refrigerios_curso
        $sql_insert = "INSERT INTO refrigerios_curso (ID_REFRIGERIO, ID_CURSO, FECHA_ASIGNACION, CANTIDAD_ASIGNADO) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql_insert);
        $stmt->bind_param("iisi", $id_refrigerio, $id_curso, $fecha_asignacion, $cantidad_asignada);

        if ($stmt->execute()) {
            // Redirigir a una página de éxito o confirmación
            header("Location: CURSO-303.php"); // Cambia esto según lo que desees
            exit();
        } else {
            echo "Error al insertar los datos: " . $stmt->error;
        }
    } else {
        echo "El refrigerio no es válido para esta sede. Asegúrese de que el refrigerio seleccionado coincida con la sede del curso.";
    }

    $stmt->close(); // Cerrar la consulta después de ejecutar
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>
