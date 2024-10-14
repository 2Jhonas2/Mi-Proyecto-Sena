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

// Verificar si se ha enviado información para autenticarse
if (!isset($_POST['NOMBRE_USUARIO'], $_POST['CONTRASENA_USUARIO'])) {
    header('Location: index.html'); // Redirige si no se han enviado las credenciales
    exit();
}

// Evitar inyección SQL
if ($stmt = $conexion->prepare('SELECT ID_USUARIOS, CONTRASENA_USUARIO, ID_ROL, ACTIVO, ID_UBICACION FROM usuarios WHERE NOMBRE_USUARIO = ?')) {
    $stmt->bind_param('s', $_POST['NOMBRE_USUARIO']);
    $stmt->execute();
    $stmt->store_result();

    // Validar si el usuario existe
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($ID_USUARIOS, $CONTRASENA_USUARIO, $ID_ROL, $ACTIVO, $ID_UBICACION);
        $stmt->fetch();

        // Verificar la contraseña usando password_verify
        if (password_verify($_POST['CONTRASENA_USUARIO'], $CONTRASENA_USUARIO)) {
            if ($ACTIVO) {
                // Si el usuario está activo, iniciar sesión
                session_regenerate_id(); // Regenera el ID de la sesión para mayor seguridad
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $_POST['NOMBRE_USUARIO'];
                $_SESSION['ID_USUARIOS'] = $ID_USUARIOS;
                $_SESSION['ID_ROL'] = $ID_ROL;
                $_SESSION['ID_UBICACION'] = $ID_UBICACION; // Guardar ID_UBICACION en la sesión

                // Redirigir según el rol del usuario
                switch ($ID_ROL) {
                    case '1': // Rol coordinador
                        header('Location: inicio.php');
                        break;
                    case '2': // Rol auxiliar
                        header('Location: inicioaux.php');
                        break;
                    case '3': // Rol Rector
                        header('location: iniciorec.php');
                        break;
                    default:
                        header('Location: index.html'); // Rol desconocido
                        break;
                }
                exit();
            } else {
                // Cuenta inactiva
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
    // Error al preparar la consulta SQL
    header('Location: index.html?error=Error en la consulta.');
}

// Cerrar la conexión
$conexion->close();
?>
