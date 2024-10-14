<?php
// Conexión a la base de datos
$conexion = new mysqli('localhost', 'usuario', 'contraseña', 'nombre_bd');

// Verificar si la conexión es correcta
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener los últimos 3 refrigerios agregados
$sql = "SELECT descripcion, fecha, hora, tipo, estado, imagen FROM refrigerios ORDER BY id DESC LIMIT 3";
$result = $conexion->query($sql);

$refrigerios = [];

if ($result->num_rows > 0) {
    // Almacenar los resultados en un array
    while ($row = $result->fetch_assoc()) {
        $refrigerios[] = $row;
    }
}

$conexion->close();

// Enviar los datos al cliente en formato JSON
echo json_encode($refrigerios);
?>
