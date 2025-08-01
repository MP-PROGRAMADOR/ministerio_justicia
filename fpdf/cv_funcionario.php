<?php
require_once 'fpdf.php';
require '../includes/conexion.php';

class PDF extends FPDF
{
    function Header()
    {
        // Colores suaves
        $this->SetFillColor(230, 240, 255);
        $this->Rect(0, 0, 210, 30, 'F');

        // Logo institucional
        $this->Image('../img/logo.png', 10, 5, 20); // Ajusta la ruta y tamaño
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(50, 50, 90);
        $this->Cell(0, 10, utf8_decode('Ficha de Funcionario'), 0, 1, 'C');
        $this->Ln(5);
        $this->SetDrawColor(180, 180, 180);
        $this->Line(10, 28, 200, 28);
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 9);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function SectionTitle($title)
    {
        $this->SetFont('Arial', 'B', 13);
        $this->SetTextColor(30, 30, 120);
        $this->Cell(0, 10, utf8_decode($title), 0, 1, 'L');
        $this->SetTextColor(0, 0, 0);
    }
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$id = isset($_GET['id_funcionario']) ? (int) $_GET['id_funcionario'] : exit('ID inválido');

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// DATOS BÁSICOS
$stmt = $pdo->prepare("SELECT * FROM tbl_funcionarios WHERE ID_Funcionario = ?");
$stmt->execute([$id]);
$f = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$f) exit('Funcionario no encontrado');

// Imagen del funcionario (ruta en tu base de datos, por ejemplo 'Foto_Funcionario')
if (!empty($f['Fotografia']) && file_exists('../api/' . $f['Fotografia'])) {
    $pdf->Image('../api/' . $f['Fotografia'], 150, 40, 40); // Posición y tamaño ajustables
}

$pdf->SectionTitle('Datos Personales');
$pdf->Cell(50, 8, utf8_decode('Código:'), 0, 0);
$pdf->Cell(0, 8, utf8_decode($f['Codigo_Funcionario']), 0, 1);
$pdf->Cell(50, 8, 'Nombre:', 0, 0);
$pdf->Cell(0, 8, utf8_decode($f['Nombres'] . ' ' . $f['Apellidos']), 0, 1);
$pdf->Cell(50, 8, 'DNI / Pasaporte:', 0, 0);
$pdf->Cell(0, 8, utf8_decode($f['DNI_Pasaporte']), 0, 1);
$pdf->Cell(50, 8, 'Fecha de ingreso:', 0, 0);
$pdf->Cell(0, 8, date('d/m/Y', strtotime($f['Fecha_Ingreso'])), 0, 1);
$pdf->Ln(4);


// ASIGNACIONES
$pdf->SectionTitle('Asignaciones');
$pdf->SetFont('Arial', '', 11);
$stmt = $pdo->prepare("SELECT a.Fecha_Inicio_Asignacion,a.Fecha_Fin_Asignacion,c.Nombre_Cargo,d.Nombre_Departamento,t.Nombre_Destino FROM tbl_asignaciones a JOIN tbl_cargos c ON a.ID_Cargo = c.ID_Cargo JOIN tbl_departamentos d ON a.ID_Departamento = d.ID_Departamento JOIN tbl_destinos t ON a.ID_Destino = t.ID_Destino WHERE a.ID_Funcionario = ? ORDER BY a.Fecha_Inicio_Asignacion DESC");
$stmt->execute([$id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $line = utf8_decode($row['Nombre_Cargo']) . ' | ' . utf8_decode($row['Nombre_Departamento']) . ' | ' . utf8_decode($row['Nombre_Destino']);
    $pdf->MultiCell(0, 6, $line, 0);
}
$pdf->Ln(4);

// FORMACIÓN ACADÉMICA
$pdf->SectionTitle('Formación Académica');
$stmt = $pdo->prepare("SELECT Titulo_Obtenido, Institucion_Educativa, Fecha_Graduacion FROM tbl_formacion_academica WHERE ID_Funcionario = ? ORDER BY Fecha_Graduacion DESC");
$stmt->execute([$id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $line = utf8_decode('*'. ' '. $row['Titulo_Obtenido']  .' - '. $row['Institucion_Educativa']) . ' (' . date('d/m/Y', strtotime($row['Fecha_Graduacion'])) . ')';
    $pdf->MultiCell(0, 6, $line, 0);
}
$pdf->Ln(4);

// CAPACITACIONES
$pdf->SectionTitle('Capacitaciones');
$stmt = $pdo->prepare("SELECT Nombre_Curso, Institucion_Organizadora, Fecha_Inicio_Curso, Fecha_Fin_Curso FROM tbl_capacitaciones WHERE ID_Funcionario = ? ORDER BY Fecha_Inicio_Curso DESC");
$stmt->execute([$id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $line = utf8_decode($row['Nombre_Curso'] .' - '. $row['Institucion_Organizadora']) . ' (' . date('d/m/Y', strtotime($row['Fecha_Inicio_Curso'])) .' - '. date('d/m/Y', strtotime($row['Fecha_Fin_Curso'])) . ')';
    $pdf->MultiCell(0, 6, $line, 0);
}

$pdf->Output('I', 'Ficha_Funcionario_' . $id . '.pdf');
