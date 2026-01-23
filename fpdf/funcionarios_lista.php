<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
ob_start();

require_once 'fpdf.php';
require '../includes/conexion.php';

function txt($texto, $default = 'No registrado')
{
    $val = ($texto !== null && trim($texto) !== '') ? $texto : $default;
    return utf8_decode($val);
}

class PDF extends FPDF
{
    protected $blueDark = [26, 64, 126];
    protected $blueGold = [184, 134, 11];
    protected $grayLight = [245, 245, 245];

    function Header()
    {
        // Línea superior
        $this->SetFillColor(26, 64, 126);
        $this->Rect(0, 0, 297, 2, 'F');

        // Logo
        if (file_exists('../img/logo.png')) {
            $this->Image('../img/logo.png', 10, 10, 22);
        }

        // Encabezado institucional
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

        // Línea divisoria
        $this->SetDrawColor(26, 64, 126);
        $this->SetLineWidth(0.8);
        $this->Line(35, 32, 285, 32);


        // Caja derecha
        $this->SetXY(140, 15);
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(255, 255, 255);
        $this->SetFillColor(26, 64, 126);
        $this->Cell(60, 6, txt('LISTADO GENERAL DE FUNCIONARIOS'), 0, 1, 'C', true);

        $this->SetTextColor(100);
        $this->SetFont('Arial', 'I', 7);
        $this->SetX(140);
        $this->Cell(60, 5, txt('Emisión: ') . date('d/m/Y H:i'), 0, 1, 'R');

        $this->Ln(15);
    }

    function Footer()
    {
        $this->SetY(-20);
        $this->SetDrawColor(200);
        $this->Line(10, $this->GetY(), 287, $this->GetY());


        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120);
        $this->Cell(0, 10, txt('Sistema THEMIS - Guinea Ecuatorial'), 0, 0, 'L');
        $this->Cell(0, 10, txt('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    // Cabecera de tabla
    function TableHeader()
    {
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(245, 245, 245);
        $this->SetTextColor(26, 64, 126);

        $this->Cell(10, 7, '#', 1, 0, 'C', true);
        $this->Cell(45, 7, txt('NOMBRE COMPLETO'), 1, 0, 'L', true);
        $this->Cell(25, 7, txt('DIP/PAS'), 1, 0, 'L', true);
        $this->Cell(18, 7, txt('SEXO'), 1, 0, 'C', true);
        $this->Cell(30, 7, txt('TELÉFONO'), 1, 0, 'L', true);
        $this->Cell(38, 7, txt('CARGO'), 1, 0, 'L', true);
        $this->Cell(38, 7, txt('SECCIÓN'), 1, 0, 'L', true);
        $this->Cell(38, 7, txt('CATEGORÍA'), 1, 0, 'L', true);
        $this->Cell(30, 7, txt('ESTADO'), 1, 1, 'C', true);
    }

    // Fila de tabla
    function TableRow($i, $nombre, $doc, $sexo, $tel, $cargo,$seccion,$categoria, $estado)
    {
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(0);

        $this->Cell(10, 7, $i, 1, 0, 'C');
        $this->Cell(45, 7, txt($nombre), 1, 0, 'L');
        $this->Cell(25, 7, txt($doc), 1, 0, 'L');
        $this->Cell(18, 7, txt($sexo), 1, 0, 'C');
        $this->Cell(30, 7, txt($tel), 1, 0, 'L');
        $this->Cell(38, 7, txt($cargo), 1, 0, 'L');
        $this->Cell(38, 7, txt($seccion), 1, 0, 'L');
        $this->Cell(38, 7, txt($categoria), 1, 0, 'L');
        $this->Cell(30, 7, txt($estado), 1, 1, 'C');
    }
}

$pdo = new PDO($dsn, $user, $pass, $options);

// CONSULTA DE FUNCIONARIOS
$sql = "SELECT 
    f.Id_funcionario,
    f.Nombre,
    f.Apellidos,
    f.Dip_Pasaporte,
    f.Sexo,
    f.Telefono,
    f.Estado_Laboral,

    c.nombre  AS Cargo,
    s.nombre  AS Seccion,
    cat.nombre AS Categoria

FROM funcionarios f
LEFT JOIN cargos c 
    ON f.Id_cargo = c.Id_cargo

LEFT JOIN secciones s 
    ON f.Id_seccion = s.Id_seccion

LEFT JOIN categorias cat 
    ON f.Id_categoria = cat.Id_categoria

ORDER BY f.Apellidos ASC, f.Nombre ASC";

$stmt = $pdo->query($sql);
$funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// PDF
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 25);

// Cabecera tabla
$pdf->TableHeader();

$i = 1;
foreach ($funcionarios as $f) {

    // Salto de página automático con cabecera repetida
    if ($pdf->GetY() > 185) {

        $pdf->AddPage();
        $pdf->TableHeader();
    }

    $pdf->TableRow(
        $i++,
        $f['Nombre'] . ' ' . $f['Apellidos'],
        $f['Dip_Pasaporte'],
        $f['Sexo'],
        $f['Telefono'],
        $f['Cargo'],
                $f['Seccion'],
                $f['Categoria'],
                $f['Estado_Laboral']
    );
}

$pdf->Output('I', 'listado_funcionarios_themis.pdf');
ob_end_flush();
