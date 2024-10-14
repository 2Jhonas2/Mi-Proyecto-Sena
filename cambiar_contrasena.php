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

if (isset($_POST['cambiar_contrasena'])) {
    // Verificar si el usuario está autenticado
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.html');
        exit();
    }

    $usuario_id = $_SESSION['ID_USUARIOS'];
    $contrasena_actual = trim($_POST['contrasena_actual']); // Usar trim para evitar espacios
    $nueva_contrasena = trim($_POST['nueva_contrasena']);
    $confirmar_contrasena = trim($_POST['confirmar_contrasena']);

    // Validar que la nueva contraseña y la confirmación coincidan
    if ($nueva_contrasena !== $confirmar_contrasena) {
        echo "Las contraseñas no coinciden.<br>";
    } else {
        // Consulta para obtener la contraseña actual
        $sql = "SELECT CONTRASENA_USUARIO, ID_ROL FROM usuarios WHERE ID_USUARIOS = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Depuración
            echo "Contraseña ingresada: " . htmlspecialchars($contrasena_actual) . "<br>"; // Imprime la contraseña ingresada
            echo "Hash almacenado: " . htmlspecialchars($row['CONTRASENA_USUARIO']) . "<br>"; // Imprime el hash almacenado

            // Verificar que la contraseña actual sea correcta
            $verificacion = password_verify($contrasena_actual, $row['CONTRASENA_USUARIO']);
            var_dump($verificacion); // Mostrar el resultado de la verificación

            if ($verificacion) {
                // Actualizar la nueva contraseña
                $nueva_contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
                $sql_update = "UPDATE usuarios SET CONTRASENA_USUARIO = ? WHERE ID_USUARIOS = ?";
                $stmt_update = $conexion->prepare($sql_update);
                $stmt_update->bind_param("si", $nueva_contrasena_hash, $usuario_id);
                $stmt_update->execute();

                if ($stmt_update->affected_rows > 0) {
                    echo "Contraseña actualizada exitosamente.<br>";

                    // Redirigir según el rol del usuario
                    if ($row['ID_ROL'] == 1) {
                        header("Location: Perfil.php"); // Administrador
                    } elseif ($row['ID_ROL'] == 2) {
                        header("Location: PerfilAux.php"); // Auxiliar
                    } elseif ($row['ID_ROL'] == 3) {
                        header("Location: Perfilrec.php"); // Auxiliar
                    }
                    exit(); // Terminar el script después de la redirección
                } else {
                    echo "Error al actualizar la contraseña. Asegúrate de que la nueva contraseña sea diferente a la anterior.<br>";
                }
            } else {
                echo "La contraseña actual es incorrecta.<br>";
            }
        } else {
            // Redirigir según el rol si el usuario no es encontrado
            echo "Usuario no encontrado.<br>";

            // Realizar otra consulta para obtener el rol del usuario
            $rol_sql = "SELECT ID_ROL FROM usuarios WHERE ID_USUARIOS = ?";
            $stmt_rol = $conexion->prepare($rol_sql);
            $stmt_rol->bind_param("i", $usuario_id);
            $stmt_rol->execute();
            $rol_result = $stmt_rol->get_result();

            if ($rol_result->num_rows > 0) {
                $rol_row = $rol_result->fetch_assoc();
                if ($rol_row['ID_ROL'] == 1) {
                    header("Location: Perfil.php"); // Administrador
                } elseif ($rol_row['ID_ROL'] == 2) {
                    header("Location: PerfilAux.php"); // Auxiliar
                } elseif ($rol_row['ID_ROL'] == 3) {
                    header("Location: perfilrec.php"); // Auxiliar
                }
            } else {
                // Si no se puede determinar el rol, redirigir a una página por defecto
                header("Location: index.html");
            }
        }
    }
}
?>
