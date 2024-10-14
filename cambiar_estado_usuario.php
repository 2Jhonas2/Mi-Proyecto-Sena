<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['loggedin']) || $_SESSION['ID_ROL'] != 1) { // Asegura que el rol sea coordinador (ID_ROL = 1)
    header('Location: index.html');
    exit();
}

// Imprime los parámetros GET para depuración
echo '<pre>';
print_r($_GET);
echo '</pre>';

// Verifica que los parámetros están definidos
if (isset($_GET['id'], $_GET['estado'])) {
    $id_usuario = intval($_GET['id']);
    $estado = intval($_GET['estado']);

    // Conexión a la base de datos
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'refrigerios';
    $conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

    if (mysqli_connect_error()) {
        exit('Fallo en la conexión de MySQL: ' . mysqli_connect_error());
    }

    // Verificar si el usuario a modificar es un auxiliar
    $resultado = $conexion->query("SELECT ID_ROL FROM usuarios WHERE ID_USUARIOS = '$id_usuario'");
    if ($resultado) {
        $usuario = $resultado->fetch_assoc();
        if ($usuario['ID_ROL'] != 2) { // Asegúrate que sea auxiliar (ID_ROL = 2)
            echo 'No puedes modificar el estado de un usuario que no es auxiliar.';
            exit();
        }
    } else {
        echo 'Error al consultar el usuario.';
        exit();
    }

    // Actualizar el estado del usuario en la tabla de usuarios
    $stmt = $conexion->prepare("UPDATE usuarios SET ACTIVO = ? WHERE ID_USUARIOS = ?");
    if ($stmt === false) {
        die('Error en la preparación de la consulta: ' . $conexion->error);
    }
    $stmt->bind_param('ii', $estado, $id_usuario);
    if (!$stmt->execute()) {
        die('Error en la ejecución de la consulta: ' . $stmt->error);
    }
    $stmt->close();

    // Actualizar el estado del usuario en la tabla de coordinadores (si aplica)
    $stmt = $conexion->prepare("UPDATE coordinador SET ACTIVO = ? WHERE ID_USUARIOS = ?");
    if ($stmt === false) {
        die('Error en la preparación de la consulta: ' . $conexion->error);
    }
    $stmt->bind_param('ii', $estado, $id_usuario);
    if (!$stmt->execute()) {
        die('Error en la ejecución de la consulta: ' . $stmt->error);
    }
    $stmt->close();

    // Actualizar el estado del usuario en la tabla de auxiliares (si aplica)
    $stmt = $conexion->prepare("UPDATE auxiliar SET ACTIVO = ? WHERE ID_USUARIOS = ?");
    if ($stmt === false) {
        die('Error en la preparación de la consulta: ' . $conexion->error);
    }
    $stmt->bind_param('ii', $estado, $id_usuario);
    if (!$stmt->execute()) {
        die('Error en la ejecución de la consulta: ' . $stmt->error);
    }
    $stmt->close();

    // Cierra la conexión
    $conexion->close();

    // Redirige de vuelta a la página de usuarios
    header('Location: usuarios.php');
    exit();
} else {
    // Si los parámetros no están definidos, muestra un mensaje de error
    echo 'Parámetros no válidos.';
}
?>
