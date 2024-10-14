<?php
session_start();

// Verificar si el administrador está autenticado
if (!isset($_SESSION['loggedin']) || $_SESSION['ID_ROL'] != 1) {
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

$id_usuario = $_POST['id_usuario'];
$nueva_contrasena = password_hash($_POST['nueva_contrasena'], PASSWORD_DEFAULT);

// Actualizar la contraseña del usuario en la base de datos
$stmt = $conexion->prepare("UPDATE usuarios SET CONTRASENA_USUARIO = ? WHERE ID_USUARIOS = ?");
$stmt->bind_param('si', $nueva_contrasena, $id_usuario);

if ($stmt->execute()) {
    echo "<script>alert('Contraseña actualizada correctamente'); window.location.href='usuarios.php';</script>";
} else {
    echo "<script>alert('Error al actualizar la contraseña'); window.history.back();</script>";
}

$stmt->close();
$conexion->close();
?>
