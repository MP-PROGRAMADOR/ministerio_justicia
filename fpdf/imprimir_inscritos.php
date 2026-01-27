<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once 'fpdf.php'; // Ajusta la ruta si es necesario
require '../includes/conexion.php'; 

function txt($texto) {
    return utf8_decode($texto ?? '---');
}

class PDF extends FPDF {
    protected $cursoNombre;
    protected $fechas;

    public function setDatosCurso($nombre, $inicio, $fin) {
        $this->cursoNombre = $nombre;
        $this->fechas = "Desde: $inicio | Hasta: $fin";
    }

    function Header() {
        $this->SetFillColor(26, 64, 126);
        $this->Rect(0, 0, 210, 2, 'F'); // Ajustado a A4 Vertical (210mm)

        if (file_exists('../img/logo.png')) {
            $this->Image('../img/logo.png', 10, 10, 22);
        }

        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(26, 64, 126);
        $this->SetX(35);
        $this->Cell(0, 7, txt('REPÚBLICA DE GUINEA ECUATORIAL'), 0, 1, 'L');

        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(80, 80, 80);
        $this->SetX(35);
        $this->Cell(0, 5, txt('MINISTERIO DE JUSTICIA, CULTO Y DERECHOS HUMANOS'), 0, 1, 'L');

        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(184, 134, 11);
        $this->SetXY(35, 22);
        $this->Cell(0, 10, txt('SISTEMA THEMIS'), 0, 1, 'L');

        $this->SetDrawColor(26, 64, 126);
        $this->SetLineWidth(0.8);
        $this->Line(35, 32, 200, 32);
        
        $this->Ln(15);
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(0);
        $this->Cell(0, 7, txt("CURSO: " . $this->cursoNombre), 0, 1, 'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, txt($this->fechas), 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120);
        $this->Cell(0, 10, txt('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function TableHeader() {
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(245, 245, 245);
        $this->SetTextColor(26, 64, 126);
        $this->Cell(20, 8, txt('ID'), 1, 0, 'C', true);
        $this->Cell(25, 8, txt('CÓDIGO'), 1, 0, 'C', true);
        $this->Cell(85, 8, txt('NOMBRE Y APELLIDOS'), 1, 0, 'L', true);
        $this->Cell(60, 8, txt('DIP / PASAPORTE'), 1, 1, 'L', true);
    }
}

// Lógica de obtención de datos
$idCurso = isset($_GET['ID_Curso']) ? intval($_GET['ID_Curso']) : 0;

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Obtener info del curso
    $stmtC = $pdo->prepare("SELECT Nombre_Curso, Fecha_Inicio, Fecha_Fin FROM tbl_cursos WHERE ID_Curso = ?");
    $stmtC->execute([$idCurso]);
    $curso = $stmtC->fetch();

    // Obtener funcionarios inscritos
    $sql = "SELECT f.ID_Funcionario, f.CODIGO, f.Nombre, f.Apellidos, f.Dip_Pasaporte 
            FROM tbl_cursos_funcionarios cf
            INNER JOIN funcionarios f ON cf.ID_Funcionario = f.Id_funcionario
            WHERE cf.ID_Curso = ?
            ORDER BY f.Apellidos ASC";
    $stmtF = $pdo->prepare($sql);
    $stmtF->execute([$idCurso]);
    $inscritos = $stmtF->fetchAll();

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->setDatosCurso($curso['Nombre_Curso'], $curso['Fecha_Inicio'], $curso['Fecha_Fin']);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->TableHeader();

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0);

foreach ($inscritos as $f) {
    $pdf->Cell(20, 7, $f['ID_Funcionario'], 1, 0, 'C');
    $pdf->Cell(25, 7, txt($f['CODIGO']), 1, 0, 'C');
    $pdf->Cell(85, 7, txt($f['Nombre'] . ' ' . $f['Apellidos']), 1, 0, 'L');
    $pdf->Cell(60, 7, txt($f['Dip_Pasaporte']), 1, 1, 'L');
}

if (ob_get_length()) ob_end_clean();
$pdf->Output('I', 'Inscritos_Curso_' . $idCurso . '.pdf');