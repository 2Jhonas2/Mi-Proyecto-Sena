<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "refrigerios";

// Crear conexión
$conexion = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// ----------------------------------------
// MANEJO DE AGREGAR USUARIO
// ----------------------------------------

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si es el formulario de agregar usuario
    if (isset($_POST['accion']) && $_POST['accion'] == 'agregar_usuario') {
        // Verificar que las entradas POST sean válidas y contengan datos
        $nombres = isset($_POST['NOMBRE_USUARIO']) && is_array($_POST['NOMBRE_USUARIO']) ? $_POST['NOMBRE_USUARIO'] : [];
        $correos = isset($_POST['CORREO_USUARIO']) && is_array($_POST['CORREO_USUARIO']) ? $_POST['CORREO_USUARIO'] : [];
        $telefonos = isset($_POST['TELEFONO_USUARIO']) && is_array($_POST['TELEFONO_USUARIO']) ? $_POST['TELEFONO_USUARIO'] : [];
        $ubicacion = isset($_POST['ID_UBICACION']) && is_array($_POST['ID_UBICACION']) ? $_POST['ID_UBICACION'] : [];
        $contrasenas = isset($_POST['CONTRASENA_USUARIO']) && is_array($_POST['CONTRASENA_USUARIO']) ? $_POST['CONTRASENA_USUARIO'] : [];
        $roles = isset($_POST['ID_ROL']) && is_array($_POST['ID_ROL']) ? $_POST['ID_ROL'] : [];

        // Validar si todos los arrays tienen la misma longitud
        $totalUsuarios = count($nombres);
        if ($totalUsuarios == 0 || count($correos) != $totalUsuarios || count($telefonos) != $totalUsuarios || count($ubicacion) != $totalUsuarios || count($contrasenas) != $totalUsuarios || count($roles) != $totalUsuarios) {
            echo "Los datos del formulario no coinciden.";
            exit();
        }

        // Iterar sobre cada usuario
        for ($i = 0; $i < $totalUsuarios; $i++) {
            $NOMBRE_USUARIO = $nombres[$i];
            $CORREO_USUARIO = $correos[$i];
            $TELEFONO_USUARIO = $telefonos[$i];
            $ID_UBICACION = $ubicacion[$i];
            $CONTRASENA_USUARIO = password_hash($contrasenas[$i], PASSWORD_DEFAULT); // Hashear la contraseña
            $ID_ROL = $roles[$i];

            // Validar que los campos requeridos no estén vacíos
            if (empty($NOMBRE_USUARIO) || empty($CORREO_USUARIO) || empty($CONTRASENA_USUARIO) || empty($ID_ROL)) {
                echo "Por favor, complete todos los campos obligatorios para el usuario " . ($i + 1) . ".";
                continue; // Salta a la siguiente iteración
            }

            // Verificar si el rol existe en la tabla rol_usuario
            $checkRoleSql = "SELECT COUNT(*) FROM rol_usuario WHERE ID_ROL = ?";
            $stmtCheck = $conexion->prepare($checkRoleSql);
            if (!$stmtCheck) {
                echo "Error al preparar la consulta de verificación de rol: " . $conexion->error;
                continue;
            }
            $stmtCheck->bind_param("i", $ID_ROL);
            $stmtCheck->execute();
            $stmtCheck->bind_result($count);
            $stmtCheck->fetch();
            $stmtCheck->close();

            if ($count == 0) {
                echo "El rol seleccionado no es válido para el usuario " . ($i + 1) . ".";
                continue;
            }

            // Preparar la consulta para insertar en la tabla `usuarios`
            $sql = "INSERT INTO usuarios (NOMBRE_USUARIO, CORREO_USUARIO, TELEFONO_USUARIO, ID_UBICACION, CONTRASENA_USUARIO, ID_ROL) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $conexion->prepare($sql);
            if (!$stmt) {
                echo "Error al preparar la consulta de inserción de usuario: " . $conexion->error;
                continue;
            }

            $stmt->bind_param("sssssi", $NOMBRE_USUARIO, $CORREO_USUARIO, $TELEFONO_USUARIO, $ID_UBICACION, $CONTRASENA_USUARIO, $ID_ROL);

            if ($stmt->execute()) {
                $ID_USUARIOS = $conexion->insert_id; // Obtener el ID del usuario recién creado

                // Verificar el rol y hacer la inserción en la tabla correspondiente
                if ($ID_ROL == 1) {
                    $sql_coordinador = "INSERT INTO coordinador (ID_USUARIOS) VALUES (?)";
                    $stmt_coordinador = $conexion->prepare($sql_coordinador);
                    if ($stmt_coordinador) {
                        $stmt_coordinador->bind_param("i", $ID_USUARIOS);
                        if (!$stmt_coordinador->execute()) {
                            echo "Error al agregar coordinador para el usuario " . ($i + 1) . ": " . $conexion->error;
                        }
                        $stmt_coordinador->close();
                    }
                } elseif ($ID_ROL == 2) {
                    $sql_auxiliar = "INSERT INTO auxiliar (ID_USUARIOS) VALUES (?)";
                    $stmt_auxiliar = $conexion->prepare($sql_auxiliar);
                    if ($stmt_auxiliar) {
                        $stmt_auxiliar->bind_param("i", $ID_USUARIOS);
                        if (!$stmt_auxiliar->execute()) {
                            echo "Error al agregar auxiliar para el usuario " . ($i + 1) . ": " . $conexion->error;
                        }
                        $stmt_auxiliar->close();
                    }
                } elseif ($ID_ROL == 3) {
                    $sql_rector = "INSERT INTO rector (ID_USUARIOS) VALUES (?)";
                    $stmt_rector = $conexion->prepare($sql_rector);
                    if ($stmt_rector) {
                        $stmt_rector->bind_param("i", $ID_USUARIOS);
                        if (!$stmt_rector->execute()) {
                            echo "Error al agregar rector para el usuario " . ($i + 1) . ": " . $conexion->error;
                        }
                        $stmt_rector->close();
                    }
                }

            } else {
                echo "Error al agregar usuario " . ($i + 1) . ": " . $conexion->error;
            }

            $stmt->close();
        }

        // Redirigir a la página de usuarios después de la inserción exitosa
        header("Location: usuariosrec.php");
        exit(); // Detener la ejecución del script tras la redirección
    }
}



// ----------------------------------------
// MANEJO DE ELIMINAR USUARIO
// ----------------------------------------
if (isset($_GET['accion']) && $_GET['accion'] == 'eliminar' && isset($_GET['id'])) {
    $ID_USUARIOS = $_GET['id'];

    // Iniciar transacción
    $conexion->begin_transaction();

    try {
        // Eliminar de la tabla auxiliar
        $sql_auxiliar = "DELETE FROM auxiliar WHERE ID_USUARIOS = ?";
        $stmt_auxiliar = $conexion->prepare($sql_auxiliar);
        $stmt_auxiliar->bind_param("i", $ID_USUARIOS);
        $stmt_auxiliar->execute();

        // Eliminar de la tabla coordinador
        $sql_coordinador = "DELETE FROM coordinador WHERE ID_USUARIOS = ?";
        $stmt_coordinador = $conexion->prepare($sql_coordinador);
        $stmt_coordinador->bind_param("i", $ID_USUARIOS);
        $stmt_coordinador->execute();

        // Eliminar de la tabla rector
        $sql_rector = "DELETE FROM rector WHERE ID_USUARIOS = ?";
        $stmt_rector = $conexion->prepare($sql_rector);
        $stmt_rector->bind_param("i", $ID_USUARIOS);
        $stmt_rector->execute();

        // Finalmente, eliminar de la tabla usuarios
        $sql_usuarios = "DELETE FROM usuarios WHERE ID_USUARIOS = ?";
        $stmt_usuarios = $conexion->prepare($sql_usuarios);
        $stmt_usuarios->bind_param("i", $ID_USUARIOS);

        if ($stmt_usuarios->execute()) {
            // Confirmar la transacción si todo fue bien
            $conexion->commit();
            header("Location: usuariosrec.php");
            exit();
        } else {
            throw new Exception("Error al eliminar usuario: " . $conexion->error);
        }
    } catch (Exception $e) {
        // Revertir la transacción si hubo algún error
        $conexion->rollback();
        echo $e->getMessage();
    }

    // Validar y cerrar las sentencias preparadas
    if (isset($stmt_auxiliar)) {
        $stmt_auxiliar->close();
    }
    if (isset($stmt_coordinador)) {
        $stmt_coordinador->close();
    }
    if (isset($stmt_rector)) {
        $stmt_rector->close();
    }
    if (isset($stmt_usuarios)) {
        $stmt_usuarios->close();
    }
}

// Cerrar la conexión
$conexion->close();
