<?php
/* =========================================================
   CONFIGURACIÓN Y CONEXIÓN
   ========================================================= */
ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start();

require_once 'fpdf.php';
require '../includes/conexion.php'; // Asegúrate que aquí estén $dsn, $user, $pass

/* =========================================================
   CLASE PDF PERSONALIZADA
   ========================================================= */
class PDF extends FPDF {
    function Header() {
        $this->SetFillColor(40, 60, 110);
        $this->Rect(0, 0, 210, 35, 'F');
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(0, 15, utf8_decode('EXPEDIENTE UNIFICADO DEL FUNCIONARIO'), 0, 1, 'C');
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 5, utf8_decode('Sistema THEMIS - Ministerio de Justicia'), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->Cell(0, 10, date('d/m/Y H:i'), 0, 0, 'R');
    }

    function SectionTitle($title) {
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(230, 230, 230);
        $this->SetTextColor(40, 60, 110);
        $this->Cell(0, 8, utf8_decode("  " . strtoupper($title)), 0, 1, 'L', true);
        $this->Ln(3);
    }
}

/* =========================================================
   PROCESAMIENTO DE DATOS
   ========================================================= */
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) die('ID de funcionario no proporcionado.');

$pdo = new PDO($dsn, $user, $pass, $options);

// 1. Datos del Funcionario y su Categoría actual
$stmt = $pdo->prepare("SELECT f.*, c.nombre as categoria_nom 
                       FROM funcionarios f 
                       LEFT JOIN categorias c ON f.Id_categoria = c.Id_categoria 
                       WHERE f.Id_funcionario = ?");
$stmt->execute([$id]);
$f = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$f) die('Funcionario no encontrado.');

// 2. Historial de Nombramientos
$stmtN = $pdo->prepare("SELECT n.*, c.Nombre as cargo_nom, d.nombre as direccion_nom 
                        FROM nombramientos n
                        LEFT JOIN cargos c ON n.Id_cargo = c.Id_cargo
                        LEFT JOIN direcciones d ON n.Id_direccion = d.Id_direccion
                        WHERE n.Id_funcionario = ? ORDER BY n.Fecha_nombramiento DESC");
$stmtN->execute([$id]);
$nombramientos = $stmtN->fetchAll(PDO::FETCH_ASSOC);

// 3. Formación Académica
$stmtF = $pdo->prepare("SELECT * FROM tbl_formacion_academica WHERE ID_Funcionario = ? ORDER BY Fecha_Graduacion DESC");
$stmtF->execute([$id]);
$estudios = $stmtF->fetchAll(PDO::FETCH_ASSOC);

// 4. Capacitaciones/Cursos
$stmtC = $pdo->prepare("SELECT * FROM tbl_capacitaciones WHERE ID_Funcionario = ? ORDER BY Fecha_Inicio_Curso DESC");
$stmtC->execute([$id]);
$capacitaciones = $stmtC->fetchAll(PDO::FETCH_ASSOC);

/* =========================================================
   CONSTRUCCIÓN DEL PDF
   ========================================================= */
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

// Foto del Funcionario
$rutaFoto = !empty($f['Foto']) ? '../api/' . $f['Foto'] : '';
if ($rutaFoto && file_exists($rutaFoto)) {
    $pdf->Image($rutaFoto, 160, 45, 35, 45);
} else {
    $pdf->Rect(160, 45, 35, 45);
    $pdf->SetXY(160, 62);
    $pdf->SetFont('Arial', 'I', 7);
    $pdf->Cell(35, 10, 'SIN FOTO', 0, 0, 'C');
}

// SECCIÓN: DATOS PERSONALES
$pdf->SetY(40);
$pdf->SectionTitle('Datos Personales');
$pdf->SetFont('Arial', '', 10);

$col1 = 40; 
$h = 6;

$pdf->SetFont('Arial', 'B', 10); $pdf->Cell($col1, $h, 'Codigo:', 0); 
$pdf->SetFont('Arial', '', 10); $pdf->Cell(0, $h, utf8_decode($f['CODIGO']), 0, 1);

$pdf->SetFont('Arial', 'B', 10); $pdf->Cell($col1, $h, 'Nombre:', 0); 
$pdf->SetFont('Arial', '', 10); $pdf->Cell(0, $h, utf8_decode($f['Nombre'] . ' ' . $f['Apellidos']), 0, 1);

$pdf->SetFont('Arial', 'B', 10); $pdf->Cell($col1, $h, 'DIP/Pasaporte:', 0); 
$pdf->SetFont('Arial', '', 10); $pdf->Cell(0, $h, utf8_decode($f['Dip_Pasaporte']), 0, 1);

$pdf->SetFont('Arial', 'B', 10); $pdf->Cell($col1, $h, 'Estado Laboral:', 0); 
$pdf->SetFont('Arial', 'B', 10); 
$pdf->SetTextColor(0, 100, 0); // Verde para estado
$pdf->Cell(0, $h, utf8_decode($f['Estado_Laboral']), 0, 1);
$pdf->SetTextColor(0);

$pdf->SetFont('Arial', 'B', 10); $pdf->Cell($col1, $h, utf8_decode('Teléfono/Email:'), 0); 
$pdf->SetFont('Arial', '', 10); $pdf->Cell(0, $h, utf8_decode($f['Telefono'] . ' / ' . $f['Correo']), 0, 1);

// SECCIÓN: HISTORIAL DE NOMBRAMIENTOS
$pdf->SectionTitle('Historial de Cargos y Nombramientos');
if($nombramientos) {
    $pdf->SetFillColor(240, 240, 255);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(60, 7, 'Cargo', 1, 0, 'C', true);
    $pdf->Cell(70, 7, utf8_decode('Dirección/Unidad'), 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Nombramiento', 1, 0, 'C', true);
    $pdf->Cell(30, 7, utf8_decode('Posesión'), 1, 1, 'C', true);
    
    $pdf->SetFont('Arial', '', 8);
    foreach($nombramientos as $n) {
        $pdf->Cell(60, 7, utf8_decode($n['cargo_nom']), 1);
        $pdf->Cell(70, 7, utf8_decode($n['direccion_nom']), 1);
        $pdf->Cell(30, 7, $n['Fecha_nombramiento'], 1, 0, 'C');
        $pdf->Cell(30, 7, $n['Fecha_toma_posesion'], 1, 1, 'C');
    }
} else {
    $pdf->Cell(0, 7, utf8_decode('No hay historial de nombramientos registrado.'), 0, 1);
}

// SECCIÓN: FORMACIÓN ACADÉMICA (Tabla tbl_formacion_academica)
$pdf->SectionTitle(utf8_decode('Formación Académica'));
if($estudios) {
    foreach($estudios as $e) {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 6, utf8_decode($e['Titulo_Obtenido'] . " (" . $e['Nivel_Educativo'] . ")"), 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 5, utf8_decode($e['Institucion_Educativa'] . " | Graduado el: " . ($e['Fecha_Graduacion'] ?? 'N/A')), 0, 1);
        $pdf->Ln(2);
    }
} else {
    $pdf->Cell(0, 7, utf8_decode('No se dispone de información académica detallada.'), 0, 1);
}

// SECCIÓN: CAPACITACIONES (Tabla tbl_capacitaciones)
$pdf->SectionTitle('Capacitaciones y Cursos');
if($capacitaciones) {
    $pdf->SetFont('Arial', '', 9);
    foreach($capacitaciones as $cap) {
        $fecha = $cap['Fecha_Inicio_Curso'] . " a " . $cap['Fecha_Fin_Curso'];
        $pdf->MultiCell(0, 5, utf8_decode("• " . $cap['Nombre_Curso'] . " - " . $cap['Institucion_Organizadora'] . " (Periodo: $fecha)"), 0, 'L');
    }
} else {
    $pdf->Cell(0, 7, 'No hay capacitaciones registradas.', 0, 1);
}

// SECCIÓN: DOCUMENTACIÓN ADJUNTA
$pdf->SectionTitle(utf8_decode('Verificación de Documentación Digital'));
$pdf->SetFont('Arial', '', 9);
$docs = [
    'Copia DIP/Pasaporte' => $f['Dip_pass_copia'],
    'Documento Nombramiento' => $f['Copia_doc_nomb'],
    'Carnet Funcionario' => $f['Copia_carnet_func'],
    'Títulos Académicos' => $f['Copia_doc_academicos']
];

foreach($docs as $label => $val) {
    $status = !empty($val) ? '[ DISPONIBLE ]' : '[ NO CARGADO ]';
    $pdf->Cell(50, 6, utf8_decode($label . ":"), 0);
    $pdf->Cell(0, 6, $status, 0, 1);
}

/* =========================================================
   SALIDA
   ========================================================= */
if (ob_get_length()) ob_end_clean();
$pdf->Output('I', 'CV_' . $f['CODIGO'] . '.pdf');