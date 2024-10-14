<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Credenciales de acceso a la base de datos
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'refrigerios';

// Conexión a la base de datos
$conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

if (mysqli_connect_error()) {
    exit('Fallo en la conexión de MySQL: ' . mysqli_connect_error());
}

// Verificar si el usuario está autenticado y tiene su ID en la sesión
if (!isset($_SESSION['ID_USUARIOS'])) {
    exit('Error: Usuario no autenticado.');
}

// Obtener el ID del usuario desde la sesión
$id_usuario = $_SESSION['ID_USUARIOS'];

// Obtener el rol del usuario
$result_rol = $conexion->query("SELECT ID_ROL FROM usuarios WHERE ID_USUARIOS = $id_usuario");
$rol_usuario = $result_rol->fetch_assoc()['ID_ROL'];

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['ID_USUARIOS'])) { // Verifica si el campo ID_USUARIOS existe
        $id = $_POST['ID_USUARIOS']; // Obtener el ID del usuario
        $nombre = $_POST['NOMBRE_USUARIO'];
        $correo = $_POST['CORREO_USUARIO'];
        $telefono = $_POST['TELEFONO_USUARIO'];

        // Actualizar los datos en la base de datos
        $sql = "UPDATE usuarios SET NOMBRE_USUARIO = ?, CORREO_USUARIO = ?, TELEFONO_USUARIO = ? WHERE ID_USUARIOS = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssi", $nombre, $correo, $telefono, $id_usuario);

        if ($stmt->execute()) {
            // Redirigir según el rol del usuario
            if ($rol_usuario == 1) {
                // Si es administrador (ID_ROL = 1)
                header("Location: Perfil.php");
            } elseif ($rol_usuario == 2) {
                // Si es auxiliar (ID_ROL = 2)
                header("Location: PerfilAux.php");
            }
            exit(); // Terminar el script después de la redirección
        } else {
            echo "Error al actualizar los datos: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error: El campo ID_USUARIOS no está definido.";
    }
}

// Obtener los datos actuales del usuario
$result = $conexion->query("SELECT NOMBRE_USUARIO, CORREO_USUARIO, TELEFONO_USUARIO FROM usuarios WHERE ID_USUARIOS = $id_usuario");
$usuario = $result->fetch_assoc();
?>
