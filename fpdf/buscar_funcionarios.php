<?php
error_reporting(0);
ini_set('display_errors', 0);

// Limpiar cualquier salida previa para evitar errores en el PDF
if (ob_get_length()) ob_clean();

require_once 'fpdf.php';
require '../includes/conexion.php';

/**
 * Función para codificar texto a ISO-8859-1 (necesario para FPDF)
 */
function txt($texto) {
    return utf8_decode($texto ?? '---');
}

class PDF extends FPDF {
    // --- Lógica para filas con MultiCell (Evita que el texto choque) ---
    function Row($data, $widths, $aligns) {
        $nb = 0;
        for($i=0; $i<count($data); $i++)
            $nb = max($nb, $this->NbLines($widths[$i], $data[$i]));
        
        $h = 6 * $nb; // Altura dinámica
        
        // Salto de página automático si la fila no cabe
        if($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);

        for($i=0; $i<count($data); $i++) {
            $w = $widths[$i];
            $a = isset($aligns[$i]) ? $aligns[$i] : 'L';
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $h); // Dibuja el borde de la celda
            $this->MultiCell($w, 6, $data[$i], 0, $a);
            $this->SetXY($x + $w, $y); // Mueve la posición a la siguiente celda
        }
        $this->Ln($h);
    }

    function NbLines($w, $txt) {
        $cw = &$this->CurrentFont['cw'];
        if($w == 0) $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        $nl = 1;
        if($nb > 0 && $s[$nb-1] == "\n") $nb--;
        $sep = -1; $i = 0; $j = 0; $l = 0;
        while($i < $nb) {
            $c = $s[$i];
            if($c == "\n") { $i++; $sep = -1; $j = $i; $l = 0; $nl++; continue; }
            if($c == ' ') $sep = $i;
            $l += $cw[$c];
            if($l > $wmax) {
                if($sep == -1) { if($i == $j) $i++; } else $i = $sep + 1;
                $sep = -1; $j = $i; $l = 0; $nl++;
            } else $i++;
        }
        return $nl;
    }

    // --- Encabezado Estilo THEMIS ---
    function Header() {
        $this->SetFillColor(26, 64, 126); // Azul Institucional
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
        $this->Cell(0, 5, txt('MINISTERIO DE JUSTICIA, CULTO E INSTITUCIONES PENITENCIARIAS'), 0, 1, 'L');

        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(184, 134, 11); // Dorado Sistema Themis
        $this->SetXY(35, 22);
        $this->Cell(0, 10, txt('SISTEMA THEMIS'), 0, 1, 'L');

        $this->SetDrawColor(26, 64, 126);
        $this->SetLineWidth(0.8);
        $this->Line(35, 32, 285, 32);
        
        $this->Ln(15);
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0);
        $this->Cell(0, 7, txt("LISTADO GENERAL DE FUNCIONARIOS"), 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 5, txt("Generado el: " . date('d/m/Y H:i')), 0, 1, 'C');
        $this->Ln(5);

        // Encabezados de tabla
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(245, 245, 245);
        $this->SetTextColor(26, 64, 126);
        $this->SetDrawColor(200);
        $this->SetLineWidth(0.2);

        $this->Cell(10, 8, txt('#'), 1, 0, 'C', true);
        $this->Cell(55, 8, txt('NOMBRE COMPLETO'), 1, 0, 'L', true);
        $this->Cell(22, 8, txt('CÓDIGO'), 1, 0, 'C', true);
        $this->Cell(45, 8, txt('DIRECCIÓN'), 1, 0, 'L', true);
        $this->Cell(45, 8, txt('SECCIÓN'), 1, 0, 'L', true);
        $this->Cell(50, 8, txt('DESTINO (UBICACIÓN -- DISTRITO)'), 1, 0, 'L', true);
        $this->Cell(25, 8, txt('ESTADO'), 1, 1, 'C', true);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120);
        $this->Cell(0, 10, txt('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }
}

// --- Lógica de Base de Datos ---
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $params = [];

    // Recoger filtros (Vienen del FormData enviado por Fetch)
    $id_dir = $_POST['id_direccion'] ?? '';
    $q = $_POST['q'] ?? '';
    $estado = $_POST['estado_laboral'] ?? '';

    $sql = "SELECT 
                f.Id_funcionario, f.CODIGO, f.Nombre, f.Apellidos, f.Estado_Laboral, 
                s.nombre AS nombre_seccion, d.nombre AS nombre_direccion, 
                d.ubicacion, d.distrito
            FROM funcionarios f
            LEFT JOIN (
                SELECT n1.* FROM nombramientos n1
                WHERE n1.Id_nombramiento = (
                    SELECT MAX(n2.Id_nombramiento) FROM nombramientos n2 
                    WHERE n2.Id_funcionario = n1.Id_funcionario
                )
            ) n ON f.Id_funcionario = n.Id_funcionario
            LEFT JOIN secciones s ON COALESCE(n.Id_seccion, f.Id_seccion) = s.Id_seccion
            LEFT JOIN direcciones d ON s.Id_direccion = d.Id_direccion
            WHERE 1=1";

    if (!empty($id_dir)) {
        $sql .= " AND d.Id_direccion = :id_dir";
        $params[':id_dir'] = $id_dir;
    }
    if (!empty($q)) {
        $sql .= " AND (f.Nombre LIKE :q OR f.Apellidos LIKE :q OR f.CODIGO LIKE :q)";
        $params[':q'] = "%$q%";
    }
    if (!empty($estado)) {
        $sql .= " AND f.Estado_Laboral = :estado";
        $params[':estado'] = $estado;
    }

    $sql .= " GROUP BY f.Id_funcionario ORDER BY f.Apellidos ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Error técnico: " . $e->getMessage());
}

// --- Generación del PDF ---
$pdf = new PDF('L', 'mm', 'A4'); // L = Horizontal
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(0);

// Anchos y alineaciones (deben sumar aprox 252mm para dejar márgenes)
$widths = [10, 55, 22, 45, 45, 50, 25];
$aligns = ['C', 'L', 'C', 'L', 'L', 'L', 'C'];

foreach ($funcionarios as $i => $row) {
    $destino = ($row['ubicacion'] ? $row['ubicacion'] : 'N/A') . 
               ($row['distrito'] ? " -- " . $row['distrito'] : '');

    $pdf->Row([
        $i + 1,
        txt($row['Nombre'] . ' ' . $row['Apellidos']),
        txt($row['CODIGO']),
        txt($row['nombre_direccion']),
        txt($row['nombre_seccion']),
        txt($destino),
        txt($row['Estado_Laboral'])
    ], $widths, $aligns);
}

// Asegurar que no hay basura en el búfer antes de enviar el PDF
if (ob_get_length()) ob_end_clean();
$pdf->Output('I', 'Reporte_General_Funcionarios.pdf');