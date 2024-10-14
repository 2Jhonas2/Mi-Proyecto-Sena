<?php
session_start();

// Verificar si el administrador está autenticado
if (!isset($_SESSION['loggedin']) || $_SESSION['ID_ROL'] != 3) {
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

$admin_id = $_SESSION['ID_USUARIOS'];
$admin_password = $_POST['admin_password'];
$id_usuario_modificar = $_POST['id_usuario'];

// Obtener la contraseña del administrador desde la base de datos
$stmt = $conexion->prepare("SELECT CONTRASENA_USUARIO FROM usuarios WHERE ID_USUARIOS = ?");
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($hash_password);
$stmt->fetch();

// Verificar si la contraseña es correcta
if (password_verify($admin_password, $hash_password)) {
    // Si es correcta, redirigir al formulario para cambiar la contraseña del usuario
    header("Location: modificar_contrasenarec.php?id_usuario=$id_usuario_modificar");
} else {
    // Si la contraseña no es válida, mostrar un mensaje de error
    echo "<script>alert('Contraseña del administrador incorrecta'); window.history.back();</script>";
}

$stmt->close();
$conexion->close();
?>
