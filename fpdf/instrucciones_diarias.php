<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once 'fpdf.php';
require '../includes/conexion.php'; 

/**
 * Función para limpiar textos y evitar errores de codificación y campos vacíos
 */
function txt($texto) {
    // Si el texto es nulo, vacío o no definido, devolvemos guiones
    if (!isset($texto) || trim($texto) === '' || $texto === null) {
        return '---';
    }
    return utf8_decode($texto);
}

class PDF extends FPDF {
    protected $filtroInfo;

    public function setFiltros($funcionario) {
        $this->filtroInfo = $funcionario;
    }

    function Header() {
        $this->SetFillColor(26, 64, 126);
        $this->Rect(0, 0, 297, 2, 'F'); 

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
        $this->Line(35, 32, 285, 32);
        
        $this->Ln(15);
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0);
        $this->Cell(0, 7, txt("REPORTE ADMINISTRATIVO DE INSTRUCCIONES"), 0, 1, 'C');
        
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, txt("Funcionario: " . $this->filtroInfo . " | Generado: " . date('d/m/Y H:i')), 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-30);
        $this->SetFont('Arial', '', 8);
        $this->Cell(90, 0, '', 'T', 0, 'C');
        $this->Cell(100, 0, '', 0, 0, 'C');
        $this->Cell(90, 0, '', 'T', 1, 'C');
        
        $this->Cell(90, 5, txt('Firma Responsable'), 0, 0, 'C');
        $this->Cell(100, 5, '', 0, 0, 'C');
        $this->Cell(90, 5, txt('Sello de Recibido'), 0, 1, 'C');

        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120);
        $this->Cell(0, 10, txt('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function TableHeader() {
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(26, 64, 126);
        
        $this->Cell(15, 8, txt('ID'), 1, 0, 'C', true);
        $this->Cell(50, 8, txt('FUNCIONARIO'), 1, 0, 'L', true);
        $this->Cell(50, 8, txt('TÍTULO'), 1, 0, 'L', true);
        $this->Cell(85, 8, txt('MENSAJE / INSTRUCCIÓN'), 1, 0, 'L', true);
        $this->Cell(30, 8, txt('FECHA ENVÍO'), 1, 0, 'C', true);
        $this->Cell(47, 8, txt('ESTADO'), 1, 1, 'C', true);
    }
}

// Filtros
$idFuncionario = isset($_GET['funcionario']) ? $_GET['funcionario'] : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Ajuste de nombres de columnas según tu CREATE TABLE
    $query = "SELECT i.ID_Instruccion, i.Titulo, i.Mensaje, i.Fecha_Envio, i.Estado, 
                     f.Nombre, f.Apellidos 
              FROM tbl_instrucciones i 
              LEFT JOIN funcionarios f ON i.ID_Funcionario = f.Id_funcionario 
              WHERE 1=1";
    $params = [];

    if (!empty($idFuncionario)) {
        $query .= " AND i.ID_Funcionario = ?";
        $params[] = $idFuncionario;
    }
    if (!empty($estado)) {
        $query .= " AND i.Estado = ?";
        $params[] = $estado;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $datos = $stmt->fetchAll();

    $nombreFiltro = "Todos";
    if (!empty($idFuncionario) && count($datos) > 0) {
        $nombreFiltro = $datos[0]['Nombre'] . ' ' . $datos[0]['Apellidos'];
    }

} catch (Exception $e) {
    if (ob_get_length()) ob_end_clean();
    die("Error: " . $e->getMessage());
}

$pdf = new PDF('L', 'mm', 'A4'); 
$pdf->setFiltros($nombreFiltro);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->TableHeader();

$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(0);

foreach ($datos as $row) {
    // Verificación de campos obligatorios para que nunca salgan vacíos
    $id = txt($row['ID_Instruccion']);
    $funcionario = txt($row['Nombre'] . ' ' . $row['Apellidos']);
    $titulo = txt($row['Titulo']);
    $mensaje = txt($row['Mensaje']);
    $fecha = (!empty($row['Fecha_Envio'])) ? date('d/m/Y H:i', strtotime($row['Fecha_Envio'])) : '---';
    $estado = txt($row['Estado']);

    // Truncar textos largos para que no se desborde la celda
    if (strlen($titulo) > 30) $titulo = substr($titulo, 0, 27) . '...';
    if (strlen($mensaje) > 60) $mensaje = substr($mensaje, 0, 57) . '...';

    $pdf->Cell(15, 8, $id, 1, 0, 'C');
    $pdf->Cell(50, 8, $funcionario, 1, 0, 'L');
    $pdf->Cell(50, 8, $titulo, 1, 0, 'L');
    $pdf->Cell(85, 8, $mensaje, 1, 0, 'L');
    $pdf->Cell(30, 8, $fecha, 1, 0, 'C');
    $pdf->Cell(47, 8, $estado, 1, 1, 'C');
}

if (ob_get_length()) ob_end_clean();
$pdf->Output('I', 'Reporte_Instrucciones.pdf');