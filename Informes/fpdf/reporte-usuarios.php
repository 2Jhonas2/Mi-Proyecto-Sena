<?php
error_reporting(0);
require('./fpdf.php');

class PDF extends FPDF
{
   // Cabecera de página
   function Header()
   {
      $this->Image('logo.png', 267, 5, 20); //logo
      $this->SetFont('Arial', 'B', 19); //tipo fuente
      $this->Cell(80); // Movernos a la derecha
      $this->SetTextColor(0, 0, 0); //color
      $this->Cell(110, 15, utf8_decode('DIEGO MONTAÑA CUELLAR I.E.D'), 1, 1, 'C', 0); 
      $this->Ln(3); // Salto de línea
      $this->SetTextColor(103); //color

      /* Detalles de contacto */
      /*$this->Cell(110);  // mover a la derecha
      $this->SetFont('Arial', 'B', 10);
      $this->Cell(96, 10, utf8_decode("Ubicación : "), 0, 0, '', 0);
      $this->Ln(5);

      $this->Cell(110);  // mover a la derecha
      $this->Cell(59, 10, utf8_decode("Teléfono : "), 0, 0, '', 0);
      $this->Ln(5);

      $this->Cell(110);  // mover a la derecha
      $this->Cell(85, 10, utf8_decode("Correo : "), 0, 0, '', 0);
      $this->Ln(5);

      $this->Cell(110);  // mover a la derecha
      $this->Cell(85, 10, utf8_decode("Sucursal : "), 0, 0, '', 0);
      $this->Ln(10);*/

      /* Título de la tabla */
      $this->SetTextColor(212, 15, 15);
      $this->Cell(85); // mover a la derecha
      $this->SetFont('Arial', 'B', 15);
      $this->Cell(100, 10, utf8_decode("REPORTE DE USUARIOS"), 0, 1, 'C', 0);
      $this->Ln(7);

      /* Encabezado de la tabla */
      $this->SetFillColor(212, 15, 15); //color de fondo
      $this->SetTextColor(255, 255, 255); //color de texto
      $this->SetDrawColor(163, 163, 163); //color de borde
      $this->SetFont('Arial', 'B', 11);
      $this->Cell(12, 10, utf8_decode('N°'), 1, 0, 'C', 1);
      $this->Cell(70, 10, utf8_decode('NOMBRE'), 1, 0, 'C', 1);
      $this->Cell(69, 10, utf8_decode('CORREO'), 1, 0, 'C', 1);
      $this->Cell(35, 10, utf8_decode('TELEFONO'), 1, 0, 'C', 1);
      $this->Cell(36, 10, utf8_decode('UBICACION'), 1, 0, 'C', 1);
      $this->Cell(30, 10, utf8_decode('ROL'), 1, 0, 'C', 1);
      $this->Cell(25, 10, utf8_decode('ESTADO'), 1, 1, 'C', 1);
   }

   // Pie de página
   function Footer()
   {
      $this->SetY(-15);
      $this->SetFont('Arial', 'I', 8);
      $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
      $this->SetY(-15);
      $this->SetFont('Arial', 'I', 8);
      $hoy = date('d/m/Y');
      $this->Cell(355, 10, utf8_decode($hoy), 0, 0, 'C');
   }
}

include '../../conexion.php';

$pdf = new PDF();
$pdf->AddPage("landscape");
$pdf->AliasNbPages();

$i = 0;
$pdf->SetFont('Arial', '', 12);
$pdf->SetDrawColor(163, 163, 163); //color de borde

// Consulta a la base de datos
$consulta_reporte_usuarios = $conexion->query("SELECT * FROM usuarios ORDER BY NOMBRE_USUARIO ASC");

// Validar si hay resultados
while ($datos_reporte = $consulta_reporte_usuarios->fetch_object()) {
    $i++;
    // Verificar que la propiedad NOMBRE_USUARIO exista y no sea null
    $nombre_usuario = isset($datos_reporte->NOMBRE_USUARIO) ? $datos_reporte->NOMBRE_USUARIO : 'Desconocido';
    $correo_usuario = isset($datos_reporte->CORREO_USUARIO) ? $datos_reporte->CORREO_USUARIO : 'Desconocido';
    $telefono_usuario = isset($datos_reporte->TELEFONO_USUARIO) ? $datos_reporte->TELEFONO_USUARIO : 'Desconocido';
    $ubicacion_usuario = isset($datos_reporte->ID_UBICACION) ? $datos_reporte->ID_UBICACION : 0; // Asumiendo que ID_SEDE determina la sede
switch ($ubicacion_usuario) {
    case 1:
        $ubicacion_usuario = 'Sede A';
        break;
    case 2:
        $ubicacion_usuario = 'Sede B';
        break;
    case 3:
        $ubicacion_usuario = 'Sede C';
        break;
    case 4:
        $ubicacion_usuario = 'Todas las sedes';
        break;
    default:
        $ubicacion_usuario = 'Desconocido'; // Valor por defecto si no se encuentra la sede
}
$rol = isset($datos_reporte->ID_ROL) ? $datos_reporte->ID_ROL : 0; // Asumiendo que ID_SEDE determina la sede
switch ($rol) {
    case 1:
        $rol = 'Coordinador';
        break;
    case 2:
        $rol = 'Auxiliar';
        break;
    case 3:
        $rol = 'Rector';
        break;
    default:
        $rol = 'Desconocido'; // Valor por defecto si no se encuentra la sede
}
$estado = isset($datos_reporte->ACTIVO) ? $datos_reporte->ACTIVO : 0; // Asumiendo que ID_SEDE determina la sede
switch ($estado) {
    case 0:
        $estado = 'Inactivo';
        break;
    case 1:
        $estado = 'Activo';
        break;
    default:
        $rol = 'Desconocido'; // Valor por defecto si no se encuentra la sede
}

    // Generar las filas de la tabla
    $pdf->Cell(12, 10, utf8_decode("$i"), 1, 0, 'C', 0);
    $pdf->Cell(70, 10, utf8_decode("$nombre_usuario"), 1, 0, 'C', 0);
    $pdf->Cell(69, 10, utf8_decode("$correo_usuario"), 1, 0, 'C', 0);
    $pdf->Cell(35, 10, utf8_decode("$telefono_usuario"), 1, 0, 'C', 0);
    $pdf->Cell(36, 10, utf8_decode("$ubicacion_usuario"), 1, 0, 'C', 0);
    $pdf->Cell(30, 10, utf8_decode("$rol"), 1, 0, 'C', 0);
    $pdf->Cell(25, 10, utf8_decode("$estado"), 1, 1, 'C', 0);
}

// Evitar que se genere cualquier salida antes de la generación del PDF
$pdf->Output('Informe De Usuarios DMC.pdf', 'I');
?>
