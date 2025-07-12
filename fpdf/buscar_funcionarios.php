<?php
require_once 'fpdf.php';

// Conexión PDO
require '../includes/conexion.php';

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión PDO: ' . $e->getMessage()]);
    exit;
}

// Recoger filtros
$estado_laboral = $_POST['estado_laboral'] ?? '';
$id_departamento = isset($_POST['id_departamento']) ? (int)$_POST['id_departamento'] : 0;
$id_cargo = isset($_POST['id_cargo']) ? (int)$_POST['id_cargo'] : 0;
$id_destino = isset($_POST['id_destino']) ? (int)$_POST['id_destino'] : 0;
$fecha_inicio = $_POST['fecha_inicio'] ?? '';
$fecha_fin = $_POST['fecha_fin'] ?? '';
$reporte_general = isset($_POST['reporte_general']) && $_POST['reporte_general'] == '1';
$export = $_POST['export'] ?? '';

$where = [];
$params = [];

if (!$reporte_general) {
    if ($estado_laboral !== '') {
        $where[] = "f.Estado_Laboral = :estado_laboral";
        $params[':estado_laboral'] = $estado_laboral;
    }
    if ($id_departamento > 0) {
        $where[] = "a.ID_Departamento = :id_departamento";
        $params[':id_departamento'] = $id_departamento;
    }
    if ($id_cargo > 0) {
        $where[] = "a.ID_Cargo = :id_cargo";
        $params[':id_cargo'] = $id_cargo;
    }
    if ($id_destino > 0) {
        $where[] = "a.ID_Destino = :id_destino";
        $params[':id_destino'] = $id_destino;
    }
    if ($fecha_inicio !== '' && $fecha_fin !== '') {
        $where[] = "(a.Fecha_Inicio_Asignacion >= :fecha_inicio AND a.Fecha_Fin_Asignacion <= :fecha_fin)";
        $params[':fecha_inicio'] = $fecha_inicio;
        $params[':fecha_fin'] = $fecha_fin;
    } elseif ($fecha_inicio !== '') {
        $where[] = "a.Fecha_Inicio_Asignacion >= :fecha_inicio";
        $params[':fecha_inicio'] = $fecha_inicio;
    } elseif ($fecha_fin !== '') {
        $where[] = "a.Fecha_Fin_Asignacion <= :fecha_fin";
        $params[':fecha_fin'] = $fecha_fin;
    }
}

$whereSQL = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "
    SELECT 
        f.ID_Funcionario, f.Codigo_Funcionario, f.Nombres, f.Apellidos, f.Estado_Laboral,
        d.Nombre_Departamento,
        c.Nombre_Cargo,
        dest.Nombre_Destino,
        a.Fecha_Inicio_Asignacion,
        a.Fecha_Fin_Asignacion
    FROM tbl_funcionarios f
    LEFT JOIN tbl_asignaciones a ON f.ID_Funcionario = a.ID_Funcionario
    LEFT JOIN tbl_departamentos d ON a.ID_Departamento = d.ID_Departamento
    LEFT JOIN tbl_cargos c ON a.ID_Cargo = c.ID_Cargo
    LEFT JOIN tbl_destinos dest ON a.ID_Destino = dest.ID_Destino
    $whereSQL
    ORDER BY f.Nombres, f.Apellidos
";

if (!$export) {
    $sql .= " LIMIT 100";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$funcionarios = $stmt->fetchAll();

if (!$export) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'data' => $funcionarios]);
    exit;
}

// Exportar a PDF
if ($export === 'pdf') {

    class PDF extends FPDF
    {
        function Header()
        {
            // Logo centrado
            $logoWidth = 20;
            $pageWidth = $this->GetPageWidth();
            $x = ($pageWidth - $logoWidth) / 2;
            if (file_exists('../img/logo.png')) {
                $this->Image('../img/logo.png', $x, 8, $logoWidth);
            }

            // Espaciado
            $this->Ln(22);

            // Títulos institucionales
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(0, 6, utf8_decode('REPÚBLICA DE GUINEA ECUATORIAL'), 0, 1, 'C');
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(0, 6, utf8_decode('MINISTERIO DE JUSTICIA'), 0, 1, 'C');

            // Título del reporte
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 8, utf8_decode('REPORTE DE FUNCIONARIOS'), 0, 1, 'C');

            $this->Ln(3);

            // Encabezado tabla
            $this->SetFont('Arial', 'B', 9);
            $this->SetFillColor(230, 230, 230);
            $this->Cell(8, 7, '#', 1, 0, 'C', true);
            $this->Cell(50, 7, 'Nombre', 1, 0, 'C', true);
            $this->Cell(25, 7, 'Codigo', 1, 0, 'C', true);
            $this->Cell(20, 7, 'Estado', 1, 0, 'C', true);
            $this->Cell(40, 7, 'Departamento', 1, 0, 'C', true);
            $this->Cell(30, 7, 'Cargo', 1, 0, 'C', true);
            $this->Cell(40, 7, 'Destino', 1, 0, 'C', true);
            $this->Cell(25, 7, 'Inicio', 1, 0, 'C', true);
            $this->Cell(25, 7, 'Fin', 1, 1, 'C', true);
        }

        function Footer()
        {
            $this->SetY(-30);
            $this->SetFont('Arial', '', 9);
            $this->Ln(5);
            $this->Cell(0, 6, utf8_decode('Malabo, ' . date('d-m-Y')), 0, 1, 'C');
            $this->Cell(0, 6, utf8_decode('POR UNA GUINEA MEJOR...'), 0, 0, 'C');

            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
        }
    }

    $pdf = new PDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 9);

    foreach ($funcionarios as $i => $f) {
        $pdf->Cell(8, 7, $i + 1, 1);
        $pdf->Cell(50, 7, utf8_decode($f['Nombres'] . ' ' . $f['Apellidos']), 1);
        $pdf->Cell(25, 7, $f['Codigo_Funcionario'], 1);
        $pdf->Cell(20, 7, utf8_decode($f['Estado_Laboral']), 1);
        $pdf->Cell(40, 7, utf8_decode($f['Nombre_Departamento'] ?? '-'), 1);
        $pdf->Cell(30, 7, utf8_decode($f['Nombre_Cargo'] ?? '-'), 1);
        $pdf->Cell(40, 7, utf8_decode($f['Nombre_Destino'] ?? '-'), 1);
        $pdf->Cell(25, 7, $f['Fecha_Inicio_Asignacion'] ?? '-', 1);
        $pdf->Cell(25, 7, $f['Fecha_Fin_Asignacion'] ?? '-', 1);
        $pdf->Ln();
    }

    $pdf->Output('I', 'funcionarios.pdf');
    exit;
}
