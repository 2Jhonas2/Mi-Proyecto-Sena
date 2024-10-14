<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

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

// Se valida si se ha enviado información
if (!isset($_POST['NOMBRE_USUARIO'], $_POST['CONTRASENA_USUARIO'])) {
    header('Location: index.html');
    exit();
}

// Evitar inyección SQL
if ($stmt = $conexion->prepare('SELECT ID_USUARIOS, CONTRASENA_USUARIO, ID_ROL, ACTIVO FROM usuarios WHERE NOMBRE_USUARIO = ?')) {
    $stmt->bind_param('s', $_POST['NOMBRE_USUARIO']);
    $stmt->execute();
    $stmt->store_result();

    // Validar si el usuario existe
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($ID_USUARIOS, $CONTRASENA_USUARIO, $ID_ROL, $ACTIVO);
        $stmt->fetch();

        // Validar la contraseña usando password_verify
        if (password_verify($_POST['CONTRASENA_USUARIO'], $CONTRASENA_USUARIO)) {
            if ($ACTIVO) {
                // Crear la sesión
                session_regenerate_id();
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $_POST['NOMBRE_USUARIO'];
                $_SESSION['ID_USUARIOS'] = $ID_USUARIOS;
                $_SESSION['ID_ROL'] = $ID_ROL;

                // Redirigir según el rol del usuario
                switch ($ID_ROL) {
                    case '1':
                        header('Location: inicio.php');
                        break;
                    case '2':
                        header('Location: auxiliar.php');
                        break;
                    default:
                        header('Location: index.html');
                        break;
                }
                exit();
            } else {
                header('Location: index.html?error=Cuenta inactiva. Contacte al administrador.');
                exit();
            }
        } else {
            // Contraseña incorrecta
            header('Location: index.html?error=Contraseña incorrecta.');
            exit();
        }
    } else {
        // Usuario no encontrado
        header('Location: index.html?error=Usuario no encontrado.');
        exit();
    }

} else {
    // Error en la consulta
    header('Location: index.html?error=Error en la consulta.');
}

// Cerrar la conexión
$conexion->close();
?>
