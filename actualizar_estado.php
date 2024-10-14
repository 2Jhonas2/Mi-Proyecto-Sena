<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "refrigerios";

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consulta para actualizar el estado de los refrigerios
$sql = "UPDATE refrigerio 
        SET ESTADO_REFRIGERIO = 'agotado' 
        WHERE ESTADO_REFRIGERIO = 'disponible' 
        AND TIMESTAMPDIFF(MINUTE, HORA_REFRIGERIO, NOW()) >= 5"; // Usar HORA_REFRIGERIO

if ($conexion->query($sql) === TRUE) {
    echo "Estado de refrigerios actualizado correctamente.";
} else {
    echo "Error al actualizar el estado: " . $conexion->error;
}

$conexion->close();
?>
