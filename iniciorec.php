<?php


// confirmar sesion

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

?>

<!DOCTYPE html>
<html>
<head>
	<title>Patio De Refrigerios</title>
	<link rel="icon" href="IMG/logo-pag.png">
	<html lang="en">
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="CSS/inicio.css">
</head>
<body class="loggedin">
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
						<a href="Perfilrec.php"><span>👤</span>Perfil</a>
						<a href="cerrar_sesion.php" class="C-S"><span>🔒</span>Cerrar sesión</a>
					</li>
				</ul>
			</div>
		</nav>
	</div>
	<div>
		<article>
			<div class="nutricion">
				<section>
					<h2 class="Ah2">
						Nutrición
					</h2>
				</section>
				<section>
					<p>
						La nutrición es el proceso biológico que ocurre en un ser vivo cuando su organismo absorbe, de los alimentos y líquidos, los nutrientes que necesita para su crecimiento y el desarrollo de las funciones vitales. A través de la alimentación, el organismo incorpora hidratos de carbono, vitaminas, minerales, proteínas y grasas
					</p>
				</section>
				<section>
					<h2 class="Ah2">
						Pirámide nutricional
					</h2>
					<p>
						Base. Se sitúan los alimentos hechos a base de cereales, como la pasta, el pan, el arroz, las harinas y también los tubérculos. Estos alimentos tienen un gran contenido de hidratos de carbono y son fundamentales porque brindan energía al organismo.
					</p>
					<p>
						Segundo nivel. Se sitúan las frutas y verduras, que son una gran fuente de hidratos, vitaminas y fibra. Se recomienda consumir, por lo menos, cinco raciones de este grupo por día.
					</p>
					<p>
						Tercer nivel. Se sitúan los lácteos, los frutos secos, los huevos, las legumbres y las carnes blancas como pollo y pescado. Se recomienda su consumo diario y, de forma más ocasional, las carnes rojas. Estos alimentos son ricos en nutrientes como vitaminas, proteínas, minerales y grasas.
					</p>
					<p>
						Punta. Se sitúan las azúcares que están presentes en productos que es recomendable consumir de forma moderada, porque contienen grasas trans, pocos nutrientes y tienen un alto contenido calórico. Aquí se sitúan los embutidos, las tortas, los dulces, la mantequilla, entre otros.
					</p>
				</section>
			</div>
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
</body>
</html>