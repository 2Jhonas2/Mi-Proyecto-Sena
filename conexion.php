<?php
// Configuración de la conexión a la base de datos
$servidor = "localhost";     // Servidor de la base de datos (normalmente "localhost")
$usuario = "root";     // Usuario de la base de datos
$password = "";   // Contraseña de la base de datos
$base_datos = "refrigerios";   // Nombre de la base de datos

// Crear la conexión
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer el conjunto de caracteres a utf8 para evitar problemas con caracteres especiales
$conexion->set_charset("utf8");
?>
