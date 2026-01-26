<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once 'fpdf.php';
require '../includes/conexion.php'; 

function txt($texto) {
    return utf8_decode($texto ?? '---');
}

class PDF extends FPDF {
    // Función auxiliar para celdas con texto largo
    function AdaptableCell($w, $h, $txt, $border=1, $ln=0, $align='L', $fill=false) {
        $currentX = $this->GetX();
        $currentY = $this->GetY();
        
        // Verificamos el ancho del texto
        $strWidth = $this->GetStringWidth($txt);
        $contentW = $w - 2; // Margen interno
        
        if ($strWidth > $contentW) {
            // Si el texto es muy largo, bajamos la fuente temporalmente
            $this->SetFont('Arial', '', 6);
            // Si aún así no cabe, usamos MultiCell para que se vea en dos líneas o se ajuste
            // Pero para tablas horizontales, lo ideal es recortar o forzar una línea
            $txt = substr($txt, 0, 35) . (strlen($txt) > 35 ? '..' : '');
        }
        
        $this->Cell($w, $h, txt($txt), $border, $ln, $align, $fill);
        
        // Restauramos fuente original de la fila
        $this->SetFont('Arial', '', 7);
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
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120);
        $this->Cell(0, 10, txt('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function TableHeader() {
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(245, 245, 245);
        $this->SetTextColor(26, 64, 126);

        $this->Cell(20, 8, txt('CÓDIGO'), 1, 0, 'C', true);
        $this->Cell(50, 8, txt('NOMBRE Y APELLIDOS'), 1, 0, 'L', true);
        $this->Cell(30, 8, txt('FUNCIÓN'), 1, 0, 'L', true);
        $this->Cell(30, 8, txt('SECCIÓN'), 1, 0, 'L', true);
        $this->Cell(35, 8, txt('DIRECCIÓN'), 1, 0, 'L', true);
        $this->Cell(30, 8, txt('CATEGORÍA'), 1, 0, 'L', true);
        $this->Cell(22, 8, txt('TELÉFONO'), 1, 0, 'C', true);
        $this->Cell(35, 8, txt('DESTINO'), 1, 0, 'L', true);
        $this->Cell(25, 8, txt('ESTADO'), 1, 1, 'C', true);
    }
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $sql = "SELECT 
        f.Id_funcionario, f.CODIGO, f.Nombre, f.Apellidos, f.Telefono, f.Estado_Laboral,
        s.nombre AS nombre_seccion,
        d.nombre AS nombre_direccion,
        d.ubicacion AS ubicacion_direccion,
        d.distrito AS distrito_direccion,
        cat.nombre AS nombre_categoria,
        (
            SELECT car.Nombre
            FROM nombramientos n
            INNER JOIN cargos car ON n.Id_cargo = car.Id_cargo
            WHERE n.Id_funcionario = f.Id_funcionario
            ORDER BY n.Fecha_nombramiento DESC LIMIT 1
        ) AS nombre_cargo
    FROM funcionarios f
    LEFT JOIN secciones s ON f.Id_seccion = s.Id_seccion
    LEFT JOIN direcciones d ON s.Id_direccion = d.Id_direccion
    LEFT JOIN categorias cat ON f.Id_categoria = cat.Id_categoria
    ORDER BY f.Apellidos ASC, f.Nombre ASC";

    $stmt = $pdo->query($sql);
    $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    if (ob_get_length()) ob_end_clean();
    die("Error de base de datos: " . $e->getMessage());
}

$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 25);
$pdf->TableHeader();

$pdf->SetFont('Arial', '', 7);
$pdf->SetTextColor(0);

foreach ($funcionarios as $f) {
    if ($pdf->GetY() > 180) {
        $pdf->AddPage();
        $pdf->TableHeader();
    }

    $nombre_completo = $f['Nombre'] . ' ' . $f['Apellidos'];
    $destino = trim(($f['ubicacion_direccion'] ?? '') . " - " . ($f['distrito_direccion'] ?? ''), " - ");
    if(empty($destino)) $destino = "No asignado";

    // Usamos AdaptableCell para columnas que suelen tener nombres largos
    $pdf->Cell(20, 7, txt($f['CODIGO']), 1, 0, 'C');
    $pdf->AdaptableCell(50, 7, $nombre_completo, 1, 0, 'L');
    $pdf->AdaptableCell(30, 7, $f['nombre_cargo'] ?? 'N/A', 1, 0, 'L');
    $pdf->AdaptableCell(30, 7, $f['nombre_seccion'] ?? 'N/A', 1, 0, 'L');
    $pdf->AdaptableCell(35, 7, $f['nombre_direccion'] ?? '---', 1, 0, 'L');
    $pdf->AdaptableCell(30, 7, $f['nombre_categoria'] ?? 'N/A', 1, 0, 'L');
    $pdf->Cell(22, 7, txt($f['Telefono'] ?? '---'), 1, 0, 'C');
    $pdf->AdaptableCell(35, 7, $destino, 1, 0, 'L');
    $pdf->Cell(25, 7, txt($f['Estado_Laboral'] ?? 'Activo'), 1, 1, 'C');
}

if (ob_get_length()) ob_end_clean();
$pdf->Output('I', 'Listado_Themis.pdf');