<?php
// 1. ACTIVAR REPORTES DE ERROR Y BUFFER
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start(); // Fundamental para evitar que espacios en blanco rompan el PDF

require_once 'fpdf.php';
require '../includes/conexion.php';

class PDF extends FPDF
{
    function Header()
    {
        // Fondo azul suave para el encabezado
        $this->SetFillColor(230, 240, 255);
        $this->Rect(0, 0, 210, 30, 'F');

   

        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(50, 50, 90);
        $this->Cell(0, 10, utf8_decode('FICHA DE FUNCIONARIO'), 0, 1, 'C');
        
        $this->SetDrawColor(180, 180, 180);
        $this->Line(10, 28, 200, 28);
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 9);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 10, utf8_decode('Sistema THEMIS - Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function SectionTitle($title)
    {
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(245, 245, 245);
        $this->SetTextColor(30, 30, 120);
        $this->Cell(0, 10, utf8_decode("  " . strtoupper($title)), 0, 1, 'L', true);
        $this->SetTextColor(0, 0, 0);
        $this->Ln(2);
    }
}

// 2. CONEXIÓN Y DATOS
try {
    // Asegúrate de que $dsn, $user, $pass, $options vengan de conexion.php
    if (!isset($pdo)) {
        $pdo = new PDO($dsn, $user, $pass, $options);
    }
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$id = isset($_GET['id_funcionario']) ? (int) $_GET['id_funcionario'] : 0;
if ($id === 0) die("Error: ID de funcionario no válido.");

// Consulta datos básicos
$stmt = $pdo->prepare("SELECT * FROM tbl_funcionarios WHERE ID_Funcionario = ?");
$stmt->execute([$id]);
$f = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$f) die("Error: Funcionario no encontrado en la base de datos.");

// 3. INICIO DE GENERACIÓN PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// MANEJO DE IMAGEN (Ruta corregida: api/funcionarios/)
$nombre_foto = $f['Fotografia'];
$ruta_foto = '../api/funcionarios/' . $nombre_foto;

if (!empty($nombre_foto) && file_exists($ruta_foto)) {
    // FPDF necesita la ruta física relativa o absoluta
    $pdf->Image($ruta_foto, 155, 45, 40, 50); 
} else {
    // Si no hay foto, dibujamos un marco con texto
    $pdf->Rect(155, 45, 40, 50, 'D');
    $pdf->SetXY(155, 65);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(40, 10, 'Sin Foto', 0, 0, 'C');
}

$pdf->SetY(40); // Ajustar inicio de texto para que no choque con el header
$pdf->SectionTitle('Datos Personales');
$pdf->SetFont('Arial', '', 11);

$pdf->Cell(45, 8, utf8_decode('Código:'), 0, 0);
$pdf->Cell(0, 8, utf8_decode($f['Codigo_Funcionario']), 0, 1);

$pdf->Cell(45, 8, 'Nombre:', 0, 0);
$pdf->Cell(0, 8, utf8_decode($f['Nombres'] . ' ' . $f['Apellidos']), 0, 1);

$pdf->Cell(45, 8, 'DNI / Pasaporte:', 0, 0);
$pdf->Cell(0, 8, utf8_decode($f['DNI_Pasaporte']), 0, 1);

$pdf->Cell(45, 8, 'Fecha de ingreso:', 0, 0);
$pdf->Cell(0, 8, date('d/m/Y', strtotime($f['Fecha_Ingreso'])), 0, 1);

// ASIGNACIONES (Historial Laboral)
$pdf->SectionTitle('Historial de Asignaciones');
$stmt = $pdo->prepare("SELECT a.*, c.Nombre_Cargo, d.Nombre_Departamento, t.Nombre_Destino 
                       FROM tbl_asignaciones a 
                       JOIN tbl_cargos c ON a.ID_Cargo = c.ID_Cargo 
                       JOIN tbl_departamentos d ON a.ID_Departamento = d.ID_Departamento 
                       JOIN tbl_destinos t ON a.ID_Destino = t.ID_Destino 
                       WHERE a.ID_Funcionario = ? 
                       ORDER BY a.Fecha_Inicio_Asignacion DESC");
$stmt->execute([$id]);

$pdf->SetFont('Arial', '', 10);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $periodo = date('d/m/Y', strtotime($row['Fecha_Inicio_Asignacion'])) . ' - ' . ($row['Fecha_Fin_Asignacion'] ? date('d/m/Y', strtotime($row['Fecha_Fin_Asignacion'])) : 'Actualidad');
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, utf8_decode($row['Nombre_Cargo']), 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(0, 5, utf8_decode($row['Nombre_Departamento'] . ' | ' . $row['Nombre_Destino'] . " (" . $periodo . ")"), 0);
    $pdf->Ln(2);
}

// FORMACIÓN ACADÉMICA
$pdf->SectionTitle('Formación Académica');
$stmt = $pdo->prepare("SELECT * FROM tbl_formacion_academica WHERE ID_Funcionario = ? ORDER BY Fecha_Graduacion DESC");
$stmt->execute([$id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pdf->Cell(0, 6, utf8_decode('* ' . $row['Titulo_Obtenido'] . ' - ' . $row['Institucion_Educativa'] . ' (' . date('Y', strtotime($row['Fecha_Graduacion'])) . ')'), 0, 1);
}

// CAPACITACIONES
$pdf->SectionTitle('Capacitaciones');
$stmt = $pdo->prepare("SELECT * FROM tbl_capacitaciones WHERE ID_Funcionario = ? ORDER BY Fecha_Inicio_Curso DESC");
$stmt->execute([$id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pdf->MultiCell(0, 6, utf8_decode('- ' . $row['Nombre_Curso'] . ' (' . $row['Institucion_Organizadora'] . ')'), 0);
}

// 4. LIMPIEZA FINAL Y SALIDA
// Esto borra cualquier eco accidental o espacio en blanco antes de generar el binario del PDF
if (ob_get_length()) ob_end_clean(); 

// I = Abrir en navegador para imprimir/descargar
$pdf->Output('I', 'Ficha_Funcionario_' . $id . '.pdf');