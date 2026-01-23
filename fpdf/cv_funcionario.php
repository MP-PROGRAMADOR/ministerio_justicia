<?php
// 1. CONFIGURACIÓN DE ERRORES Y LIMPIEZA
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../includes/conexion.php'; 
if (!isset($pdo)) {
    $pdo = new PDO($dsn, $user, $pass, $options);
}

// 2. VALIDACIÓN DE ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("Error: No se proporcionó un ID válido.");
}

try {
    // Consulta Principal
    $sql = "SELECT f.*, s.nombre as seccion_nom, d.nombre as direccion_nom, c.Nombre as cargo_nom,
            TIMESTAMPDIFF(YEAR, f.Fecha_nacimiento, CURDATE()) AS edad
            FROM funcionarios f
            LEFT JOIN secciones s ON f.Id_seccion = s.Id_seccion
            LEFT JOIN direcciones d ON s.Id_direccion = d.Id_direccion
            LEFT JOIN cargos c ON f.Id_cargo = c.Id_cargo
            WHERE f.Id_funcionario = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $f = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$f) { die("Error: El funcionario no existe."); }

    // Formación Académica
    $stmtEst = $pdo->prepare("SELECT * FROM tbl_formacion_academica WHERE ID_Funcionario = ? ORDER BY Fecha_Graduacion DESC");
    $stmtEst->execute([$id]);
    $listaEstudios = $stmtEst->fetchAll(PDO::FETCH_ASSOC);

    // Capacitaciones
    $stmtCap = $pdo->prepare("SELECT * FROM tbl_capacitaciones WHERE ID_Funcionario = ? ORDER BY Fecha_Fin_Curso DESC");
    $stmtCap->execute([$id]);
    $listaCapacitaciones = $stmtCap->fetchAll(PDO::FETCH_ASSOC);

    // Permisos
    $stmtPerm = $pdo->prepare("SELECT * FROM tbl_permisos WHERE ID_Funcionario = ? AND Estado_Permiso = 'Aprobado'");
    $stmtPerm->execute([$id]);
    $listaPermisos = $stmtPerm->fetchAll(PDO::FETCH_ASSOC);

    // NUEVO: Historial de Nombramientos
    $stmtNom = $pdo->prepare("SELECT n.*, c.Nombre as cargo_nom, s.nombre as seccion_nom 
                             FROM nombramientos n 
                             LEFT JOIN cargos c ON n.Id_cargo = c.Id_cargo 
                             LEFT JOIN secciones s ON n.Id_seccion = s.Id_seccion
                             WHERE n.Id_funcionario = ? ORDER BY n.Fecha_nombramiento DESC");
    $stmtNom->execute([$id]);
    $listaNombramientos = $stmtNom->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error en la Base de Datos: " . $e->getMessage());
}

// 3. GENERACIÓN DE PDF
require_once 'fpdf.php';
if (ob_get_length()) ob_end_clean();

function txt($texto) {
    return utf8_decode($texto ?? 'No registrado');
}

class PDF extends FPDF {
    protected $colBlue = [26, 64, 126];
    protected $colGold = [184, 134, 11];
    protected $colGray = [245, 245, 245];

    function Header() {
        $this->SetFillColor($this->colBlue[0], $this->colBlue[1], $this->colBlue[2]);
        $this->Rect(0, 0, 210, 3, 'F');
        if (file_exists('../img/logo.png')) { $this->Image('../img/logo.png', 12, 12, 22); }
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor($this->colBlue[0], $this->colBlue[1], $this->colBlue[2]);
        $this->SetX(40);
        $this->Cell(0, 6, txt('REPÚBLICA DE GUINEA ECUATORIAL'), 0, 1);
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100);
        $this->SetX(40);
        $this->Cell(0, 5, txt('MINISTERIO DE JUSTICIA, CULTO Y DERECHOS HUMANOS'), 0, 1);
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor($this->colGold[0], $this->colGold[1], $this->colGold[2]);
        $this->SetXY(40, 24);
        $this->Cell(0, 10, txt('EXPEDIENTE TÉCNICO INDIVIDUAL'), 0, 1);
        $this->SetDrawColor($this->colGold[0], $this->colGold[1], $this->colGold[2]);
        $this->Line(12, 36, 198, 36);
        $this->Ln(15);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150);
        $this->Cell(0, 10, txt('Sistema THEMIS - Página ').$this->PageNo().'/{nb}', 0, 0, 'C');
    }

    function Titulo($txt) {
        $this->Ln(4);
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor($this->colGray[0], $this->colGray[1], $this->colGray[2]);
        $this->SetTextColor($this->colBlue[0], $this->colBlue[1], $this->colBlue[2]);
        $this->Cell(0, 8, "  " . txt(strtoupper($txt)), 'B', 1, 'L', true);
        $this->Ln(3);
        $this->SetTextColor(0);
    }

    function InfoRow($label, $value, $bold = false) {
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(80);
        $this->Cell(45, 6, txt($label . ":"), 0, 0);
        $this->SetTextColor(30);
        $this->SetFont('Arial', ($bold ? 'B' : ''), 10);
        $this->Cell(0, 6, txt($value), 0, 1);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// BLOQUE DE CABECERA (Centrado de Foto y Datos)
$yStart = $pdf->GetY();
$fotoW = 35; $fotoH = 42; $fotoX = 163; $fotoY = $yStart + 2;

$rutaFoto = '../api/funcionarios/' . $f['Foto'];
if (!empty($f['Foto']) && file_exists($rutaFoto)) {
    $pdf->Image($rutaFoto, $fotoX, $fotoY, $fotoW, $fotoH);
} else {
    $pdf->SetDrawColor(200);
    $pdf->Rect($fotoX, $fotoY, $fotoW, $fotoH);
    $pdf->SetXY($fotoX, $fotoY + 18);
    $pdf->SetFont('Arial', 'I', 7);
    $pdf->Cell($fotoW, 5, txt('SIN FOTO'), 0, 0, 'C');
}

$pdf->SetXY(12, $yStart + 8);
$pdf->SetFont('Arial', 'B', 18);
$pdf->SetTextColor(26, 64, 126);
$pdf->Cell(145, 10, txt($f['Nombre'] . " " . $f['Apellidos']), 0, 1);
$pdf->SetX(12);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(184, 134, 11);
$pdf->Cell(145, 7, txt($f['cargo_nom'] ?? 'CARGO NO ASIGNADO'), 0, 1);
$pdf->SetX(12);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(100);
$pdf->Cell(145, 7, txt('ID FUNCIONARIO: ' . $f['CODIGO']), 0, 1);

$pdf->SetY($yStart + 48);

// 1. DATOS PERSONALES
$pdf->Titulo('1. Datos Personales');
$pdf->InfoRow('DIP/Pasaporte', $f['Dip_Pasaporte']);
$pdf->InfoRow('Edad / Sexo', $f['edad'] . " años / " . $f['Sexo']);
$pdf->InfoRow('Tribu / Pueblo', $f['Tribu'] . " / " . $f['Pueblo']);
$pdf->InfoRow('Lugar Nac.', $f['Lugar_nacimiento'] . " (Prov: ".$f['Provincia'].")");

// 2. DATOS LABORALES ACTUALES
$pdf->Titulo('2. Situación Laboral Actual');
$pdf->InfoRow('Estado', $f['Estado_Laboral'], true);
$pdf->InfoRow('Dirección', $f['direccion_nom']);
$pdf->InfoRow('Sección', $f['seccion_nom']);
$pdf->InfoRow('Fecha Posesión', $f['Fecha_posesion']);

// 3. HISTORIAL DE NOMBRAMIENTOS (NUEVO)
$pdf->Titulo('3. Historial de Nombramientos');
if($listaNombramientos) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(235, 235, 235);
    $pdf->Cell(75, 7, txt('CARGO'), 1, 0, 'L', true);
    $pdf->Cell(55, 7, txt('SECCIÓN'), 1, 0, 'L', true);
    $pdf->Cell(30, 7, txt('FECHA NOMB.'), 1, 0, 'C', true);
    $pdf->Cell(30, 7, txt('FECHA FIN'), 1, 1, 'C', true);
    
    $pdf->SetFont('Arial', '', 8);
    foreach($listaNombramientos as $n) {
        $pdf->Cell(75, 6, txt($n['cargo_nom']), 1);
        $pdf->Cell(55, 6, txt($n['seccion_nom']), 1);
        $pdf->Cell(30, 6, $n['Fecha_nombramiento'], 1, 0, 'C');
        $pdf->Cell(30, 6, ($n['Fecha_finalizacion_nombramiento'] ?? 'Vigente'), 1, 1, 'C');
    }
} else {
    $pdf->Cell(0, 6, txt('No hay registros en el historial de nombramientos.'), 0, 1);
}

// 4. FORMACIÓN ACADÉMICA
$pdf->Titulo('4. Formación Académica');
foreach($listaEstudios as $e) {
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetX(15);
    $pdf->Cell(0, 5, txt($e['Titulo_Obtenido'] . " (" . $e['Nivel_Educativo'] . ")"), 0, 1);
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetX(15);
    $pdf->Cell(0, 5, txt($e['Institucion_Educativa'] . " - " . $e['Fecha_Graduacion']), 0, 1);
}

// 5. CAPACITACIONES
$pdf->Titulo('5. Capacitaciones');
foreach($listaCapacitaciones as $c) {
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetX(15);
    $pdf->Cell(0, 5, txt($c['Nombre_Curso']), 0, 1);
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetX(15);
    $pdf->Cell(0, 5, txt($c['Institucion_Organizadora'] . " - Fin: " . $c['Fecha_Fin_Curso']), 0, 1);
}

// 6. PERMISOS
$pdf->Titulo('6. Permisos Aprobados');
if($listaPermisos) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(50, 7, txt('TIPO'), 1, 0, 'C', true);
    $pdf->Cell(100, 7, txt('MOTIVO'), 1, 0, 'C', true);
    $pdf->Cell(40, 7, txt('INICIO'), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 8);
    foreach($listaPermisos as $p) {
        $pdf->Cell(50, 6, txt($p['Tipo_Permiso']), 1);
        $pdf->Cell(100, 6, txt(substr($p['Motivo'], 0, 60)), 1);
        $pdf->Cell(40, 6, $p['Fecha_Inicio_Permiso'], 1, 1, 'C');
    }
} else {
    $pdf->Cell(0, 6, txt('Sin permisos registrados.'), 0, 1);
}

$pdf->Output('I', 'Expediente_' . $f['CODIGO'] . '.pdf');