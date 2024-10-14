<?php 
session_start();

date_default_timezone_set('America/Bogota');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "refrigerios";

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Asegurarse de que el usuario esté autenticado
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header('Location: index.html');
    exit();
}

// Obtener el ID del usuario, rol y ubicación desde la sesión
$id_usuario = $_SESSION['ID_USUARIOS'];
$id_rol = $_SESSION['ID_ROL'];
$id_ubicacion = $_SESSION['ID_UBICACION']; // Ubicación del usuario

// Inicializar ID_AUXILIAR si es necesario
$id_auxiliar = null;
if ($id_rol == '2') { // Auxiliar
    $sql_auxiliar = "SELECT ID_AUXILIAR FROM auxiliar WHERE ID_USUARIOS = ?";
    $stmt_aux = $conexion->prepare($sql_auxiliar);
    $stmt_aux->bind_param("i", $id_usuario);
    $stmt_aux->execute();
    $stmt_aux->bind_result($id_auxiliar);
    $stmt_aux->fetch();
    $stmt_aux->close();
}

// Verificar que se ha obtenido un ID_AUXILIAR válido
if ($id_rol == '2' && !$id_auxiliar) {
    echo "Error: No se encontró el ID_AUXILIAR correspondiente.";
    exit();
}

if (isset($_POST['DESCRIPCION_REFRIGERIO']) && isset($_FILES['IMAGEN_REFRIGERIO']) && isset($_POST['ID_UBICACION'])) {
    $DESCRIPCION_REFRIGERIO = $_POST['DESCRIPCION_REFRIGERIO'];
    $TIPO_REFRIGERIO = $_POST['TIPO_REFRIGERIO'];
    $ESTADO_REFRIGERIO = 'Disponible';
    $ID_UBICACION_REFRIGERIO = $_POST['ID_UBICACION']; // Capturar la ubicación desde el formulario

    $IMAGEN_REFRIGERIO = $_FILES['IMAGEN_REFRIGERIO']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($IMAGEN_REFRIGERIO);

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    if (move_uploaded_file($_FILES['IMAGEN_REFRIGERIO']['tmp_name'], $target_file)) {
        // Obtener la fecha actual y el día de la semana
        $fecha = date('Y-m-d'); // Formato de fecha: YYYY-MM-DD
        $diaNumero = date('w'); // Día de la semana (0=domingo, 6=sábado)

        // Convertir el número del día a nombre
        switch ($diaNumero) {
            case 0: $diaNombre = 'Domingo'; break;
            case 1: $diaNombre = 'Lunes'; break;
            case 2: $diaNombre = 'Martes'; break;
            case 3: $diaNombre = 'Miércoles'; break;
            case 4: $diaNombre = 'Jueves'; break;
            case 5: $diaNombre = 'Viernes'; break;
            case 6: $diaNombre = 'Sábado'; break;
        }

        // Preparar la consulta SQL para insertar el refrigerio con la ubicación
        $sql = "INSERT INTO refrigerio (IMAGEN_REFRIGERIO, DIA_REFRIGERIO, TIPO_REFRIGERIO, DESCRIPCION_REFRIGERIO, ESTADO_REFRIGERIO, ID_UBICACION) 
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssssi", $target_file, $diaNombre, $TIPO_REFRIGERIO, $DESCRIPCION_REFRIGERIO, $ESTADO_REFRIGERIO, $ID_UBICACION_REFRIGERIO);

        if ($stmt->execute() === TRUE) {
            // Redirigir según el rol del usuario
            if ($id_rol == '1') { // Coordinador
                header("Location: Refrigerios.php");
            } elseif ($id_rol == '2') { // Auxiliar
                header("Location: Refrigeriosaux.php");
            } elseif ($id_rol == '3') { // Auxiliar
                header("Location: Refrigeriosrec.php");
            }
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error al cargar la imagen.";
    }
} else {
    echo "Error: No se subió ningún archivo o hubo un problema con la subida.";
}

$conexion->close();
?>
