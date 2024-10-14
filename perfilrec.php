<?php
session_start();


// Verifica si el usuario está autenticado y tiene el rol adecuado
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['ID_ROL']) || $_SESSION['ID_ROL'] != 3) { // Asumiendo rol '1' es para administrador
    header('Location: index.html');
    exit();
}

// Conexión a la base de datos
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'refrigerios';

date_default_timezone_set('America/Bogota');

$conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

if (mysqli_connect_error()) {
    exit('Fallo en la conexión de MySQL: ' . mysqli_connect_error());
}

// Obtener el ID del usuario actual
$id_usuario_actual = $_SESSION['ID_USUARIOS'] ?? null; // Usa null si no existe

// Obtener la dirección del coordinador actual
$direccion_usuario_actual = '';
$query_direccion = $conexion->query("SELECT ID_UBICACION FROM usuarios WHERE ID_USUARIOS = '$id_usuario_actual' LIMIT 1");

if ($query_direccion->num_rows > 0) {
    $direccion_usuario_actual = $query_direccion->fetch_assoc()['ID_UBICACION'];
}

// Obtener el ID del usuario de la sesión
$ID_USUARIOS = $_SESSION['ID_USUARIOS'];

// Consulta para obtener los datos del usuario
$sql = "SELECT NOMBRE_USUARIO, CORREO_USUARIO, TELEFONO_USUARIO, ID_UBICACION, ID_ROL FROM usuarios WHERE ID_USUARIOS = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('i', $ID_USUARIOS);
$stmt->execute();
$stmt->bind_result($NOMBRE_USUARIO, $CORREO_USUARIO, $TELEFONO_USUARIO, $ID_UBICACION, $ID_ROL);
$stmt->fetch();
$stmt->close();

$UBICACION = '';
if ($ID_UBICACION == 4){
	$UBICACION = 'Todas Las Sedes';
}

$ROL = '';
if ($ID_ROL == 1) {
    $ROL = 'Coordinador';
} elseif ($ID_ROL == 2) {
    $ROL = 'Auxiliar';
} elseif  ($ID_ROL == 3){
    $ROL = 'Rector';
} else {
    $ROL = 'Desconocido'; // En caso de que el rol no sea 1 o 2
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="CSS/perfiles.css">
    <title>Patio De Refrigerios</title>
	<link rel="icon" href="IMG/logo-pag.png">
</head>
<body>
<div>
		<header>
			<h1 id="nombre">
				DIEGO MONTANA CUELLAR IED
			</h1>
		</header>
	</div>
	<div>
		<nav>
			<div>
				<ul>
					<li>
						<a href="iniciorec.php"><span>🏡</span>Inicio</a>
						<a href="usuariosrec.php"><span>👥</span>Usuarios</a>
						<a href="Refrigeriosrec.php"><span>🍔</span>Refrigerios</a>
						<a href="perfilrec.php"><span>👤</span>Perfil</a>
						<a href="#" class="C-S" onclick="confirmarCerrarSesion(event)"><span>🔒</span>Cerrar sesión</a>
					</li>
				</ul>
			</div>
		</nav>
	</div>
	<div>
		<article>
			<section>
				<h2 class="Ah2">Mi Perfil</h2>
				<div class="perfil">
					<h3>Nombre</h3>
   					<p><?php echo htmlspecialchars($NOMBRE_USUARIO); ?></p>
					<h3>Correo</h3>
    				<p><?php echo htmlspecialchars($CORREO_USUARIO); ?></p>
					<h3>Teléfono</h3>
    				<p><?php echo htmlspecialchars($TELEFONO_USUARIO); ?></p>
					<h3>Dirección</h3>
    				<p><?php echo htmlspecialchars($UBICACION); ?></p>
					<h3>Rol</h3>
    				<p><?php echo htmlspecialchars($ROL); ?></p> <!-- Mostrar el rol -->
					<button id="abrirModal" class="boton">Cambiar Contraseña</button>

					<dialog id="modal">
						<h2 class="Ah1">Cambiar Contrasena</h2>
							<form id="form-contrasena" enctype="multipart/form-data" action="cambiar_contrasena.php" method="post">
                   				<input type="password" name="contrasena_actual" placeholder="Contrasena Actual:" required>
                    			<br><br>
                    			<input type="password" name="nueva_contrasena" placeholder="Nueva Contraseña:" required pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}" 
								title="La contraseña debe tener al menos 8 caracteres, incluyendo al menos una letra mayúscula, una letra minúscula y un número.">
                    			<br><br>
                    			<input type="password" name="confirmar_contrasena" placeholder="Confirmar Contraseña:" required pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}" 
								title="La contraseña debe tener al menos 8 caracteres, incluyendo al menos una letra mayúscula, una letra minúscula y un número." >
                    			<br><br>
                    			<input type="submit" name="cambiar_contrasena" value="Cambiar Contraseña" class="btn">

							<div class="btn-container">
								<button id="cerrarModal"><span>❌</span></button>
							</div>
						</form>
					</dialog>
					<button id="abrirModal1" class="boton">actualizar Datos</button>

					<dialog id="modal1">
						<h2 class="Ah1">Actualizar Datos</h2>
						<form action="actualizar_datos.php" method="post" enctype="multipart/form-data">
						<!-- Cambio en el campo oculto para el ID -->
						<input type="hidden" name="ID_USUARIOS" value="<?php echo $ID_USUARIOS; ?>">

						<!-- Cambio en los valores del formulario -->
						<label for="NOMBRE_USUARIO">Nombre:</label><br>
						<input type="text" name="NOMBRE_USUARIO" value="<?php echo htmlspecialchars($NOMBRE_USUARIO); ?>" required>
						<br>

						<label for="CORREO_USUARIO">Correo:</label><br>
						<input type="email" name="CORREO_USUARIO" value="<?php echo htmlspecialchars ($CORREO_USUARIO); ?>" required>
						<br>

						<label for="TELEFONO_USUARIO">Teléfono:</label><br>
						<input type="tel" name="TELEFONO_USUARIO" value="<?php echo htmlspecialchars($TELEFONO_USUARIO); ?>" required>

                    	<br><br>
                    	<button type="submit" class="btn">Actualizar</button>
						</form>
						<button id="cerrarModal1"><span>❌</span></button>
					</dialog>
				</div>
			</section>
		</article>
	</div>
	<div>
		<aside>
		<div id="cursos">
                <?php 
                    $cursos_result = $conexion->query("SELECT ID_CURSO FROM curso ORDER BY ID_CURSO ASC");
    
                    while ($curso = $cursos_result->fetch_assoc()) {
                    echo '<a href="CURSOREC-' . $curso['ID_CURSO'] . '.php" class="curso_' . $curso['ID_CURSO'] . '">' . $curso['ID_CURSO'] . '</a>';
                    }
                ?>
            </div>
		</aside>
	</div>
	<script src="JS/perfil.js"></script>
</body>
</html>
