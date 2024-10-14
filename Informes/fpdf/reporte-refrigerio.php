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
      $this->Cell(100, 10, utf8_decode("REPORTE DE REFRIGERIOS"), 0, 1, 'C', 0);
      $this->Ln(7);

      /* Encabezado de la tabla */
      $this->SetFillColor(212, 15, 15); //color de fondo
      $this->SetTextColor(255, 255, 255); //color de texto
      $this->SetDrawColor(163, 163, 163); //color de borde
      $this->SetFont('Arial', 'B', 11);
      $this->Cell(12, 10, utf8_decode('N°'), 1, 0, 'C', 1);
      $this->Cell(72, 10, utf8_decode('DESCRIPCION'), 1, 0, 'C', 1);
      $this->Cell(36, 10, utf8_decode('UBICACION'), 1, 0, 'C', 1);
      $this->Cell(35, 10, utf8_decode('TIPO'), 1, 0, 'C', 1);
      $this->Cell(36, 10, utf8_decode('DIA'), 1, 0, 'C', 1);
      $this->Cell(30, 10, utf8_decode('FECHA'), 1, 0, 'C', 1);
      $this->Cell(30, 10, utf8_decode('HORA'), 1, 0, 'C', 1);
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
$consulta_reporte_usuarios = $conexion->query("SELECT * FROM refrigerio ORDER BY FECHA_REFRIGERIO DESC");

// Validar si hay resultados
while ($datos_reporte = $consulta_reporte_usuarios->fetch_object()) {
    $i++;
    // Verificar que la propiedad NOMBRE_USUARIO exista y no sea null
    $descripcion = isset($datos_reporte->DESCRIPCION_REFRIGERIO) ? $datos_reporte->DESCRIPCION_REFRIGERIO : 'Desconocido';
    $fecha = isset($datos_reporte->FECHA_REFRIGERIO) ? $datos_reporte->FECHA_REFRIGERIO : 'Desconocido';
    $tipo = isset($datos_reporte->TIPO_REFRIGERIO) ? $datos_reporte->TIPO_REFRIGERIO : 'Desconocido';
    $hora = isset($datos_reporte->HORA_REFRIGERIO) ? $datos_reporte->HORA_REFRIGERIO : 'Desconocido';
    $dia = isset($datos_reporte->DIA_REFRIGERIO) ? $datos_reporte->DIA_REFRIGERIO : 'Desconocido';
    $ubicacion = isset($datos_reporte->ID_UBICACION) ? $datos_reporte->ID_UBICACION : 0; // Asumiendo que ID_SEDE determina la sede
switch ($ubicacion) {
    case 1:
        $ubicacion = 'Sede A';
        break;
    case 2:
        $ubicacion = 'Sede B';
        break;
    case 3:
        $ubicacion = 'Sede C';
        break;
    case 4:
        $ubicacion = 'Todas las sedes';
        break;
    default:
        $ubicacion = 'Desconocido'; // Valor por defecto si no se encuentra la sede
}
$estado = isset($datos_reporte->ESTADO_REFRIGERIO) ? $datos_reporte->ESTADO_REFRIGERIO : 0; // Asumiendo que ID_SEDE determina la sede
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
    $pdf->Cell(72, 10, utf8_decode("$descripcion"), 1, 0, 'C', 0);
    $pdf->Cell(36, 10, utf8_decode("$ubicacion"), 1, 0, 'C', 0);
    $pdf->Cell(35, 10, utf8_decode("$tipo"), 1, 0, 'C', 0);
    $pdf->Cell(36, 10, utf8_decode("$dia"), 1, 0, 'C', 0);
    $pdf->Cell(30, 10, utf8_decode("$fecha"), 1, 0, 'C', 0);
    $pdf->Cell(30, 10, utf8_decode("$hora"), 1, 0, 'C', 0);
    $pdf->Cell(25, 10, utf8_decode("$estado"), 1, 1, 'C', 0);
}

// Evitar que se genere cualquier salida antes de la generación del PDF
$pdf->Output('Informe De Refrigerios DMC.pdf', 'I');
?>
