<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit();
}

// Conexión a la base de datos
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'refrigerios';
$conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

if (mysqli_connect_error()) {
    exit('Fallo en la conexión de MySQL: ' . mysqli_connect_error());
}

// Procesar entrega
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y sanitizar los datos del formulario
    $curso = isset($_POST['curso']) ? (int)$_POST['curso'] : 0;
    $jornada = isset($_POST['jornada']) ? (int)$_POST['jornada'] : 0;
    $entrega_a_curso = isset($_POST['entrega_a_curso']) ? htmlspecialchars($_POST['entrega_a_curso']) : '';
    $ubicacion = isset($_POST['ubicacion']) ? htmlspecialchars($_POST['ubicacion']) : '';

    if (empty($curso) || empty($jornada) || empty($entrega_a_curso) || empty($ubicacion)) {
        // Redirigir con un mensaje de error
        header('Location: auxiliar.php?error=Datos incompletos');
        exit();
    }

    // Ejemplo de inserción en la base de datos (ajustar según tu estructura)
    $stmt = $conexion->prepare("INSERT INTO entregas (ID_CURSO, ID_JORNADA, ENTREGA_A_CURSO, UBICACION) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('iiss', $curso, $jornada, $entrega_a_curso, $ubicacion);

    if ($stmt->execute()) {
        // Redirigir con un mensaje de éxito
        header('Location: auxiliar.php?success=Entrega asignada correctamente');
    } else {
        // Redirigir con un mensaje de error
        header('Location: auxiliar.php?error=Error al asignar la entrega');
    }

    $stmt->close();
    $conexion->close();
    exit();
}
?>
