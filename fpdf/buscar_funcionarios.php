<?php
// Evitar cualquier salida de texto antes del PDF
if (ob_get_length()) ob_clean();

require_once 'fpdf.php';
require '../includes/conexion.php';

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Recoger filtros del FormData
$id_direccion = $_POST['id_direccion'] ?? '';
$id_seccion   = $_POST['id_seccion'] ?? '';
$id_cargo     = $_POST['id_cargo'] ?? '';
$q             = $_POST['q'] ?? '';
$estado_laboral = $_POST['estado_laboral'] ?? '';
$export        = $_POST['export'] ?? '';

$params = [];
$sql = "SELECT 
            f.Id_funcionario, 
            f.CODIGO, 
            f.Nombre, 
            f.Apellidos, 
            f.Estado_Laboral, 
            COALESCE(n.Fecha_nombramiento, f.Fecha_nombramiento) AS Fecha_nombramiento, 
            s.nombre AS nombre_seccion, 
            d.nombre AS nombre_direccion, 
            c.Nombre AS nombre_cargo
        FROM funcionarios f
        LEFT JOIN nombramientos n ON f.Id_funcionario = n.Id_funcionario
        LEFT JOIN secciones s ON COALESCE(n.Id_seccion, f.Id_seccion) = s.Id_seccion
        LEFT JOIN direcciones d ON s.Id_direccion = d.Id_direccion
        LEFT JOIN cargos c ON n.Id_cargo = c.Id_cargo
        WHERE 1=1";

// Aplicar los mismos filtros que en la tabla visual
if (!empty($id_direccion)) {
    $sql .= " AND d.Id_direccion = :id_dir";
    $params[':id_dir'] = $id_direccion;
}
if (!empty($id_seccion)) {
    $sql .= " AND (n.Id_seccion = :id_sec OR f.Id_seccion = :id_sec)";
    $params[':id_sec'] = $id_seccion;
}
if (!empty($id_cargo)) {
    $sql .= " AND n.Id_cargo = :id_cargo";
    $params[':id_cargo'] = $id_cargo;
}
if (!empty($estado_laboral)) {
    $sql .= " AND f.Estado_Laboral = :estado";
    $params[':estado'] = $estado_laboral;
}
if (!empty($q)) {
    $sql .= " AND (f.Nombre LIKE :q OR f.Apellidos LIKE :q OR f.CODIGO LIKE :q)";
    $params[':q'] = "%$q%";
}

$sql .= " ORDER BY f.Apellidos ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($export === 'pdf') {
    class PDF extends FPDF {
        function Header() {
            if (file_exists('../img/logo.png')) {
                $this->Image('../img/logo.png', 10, 8, 20);
            }
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 5, utf8_decode('REPÚBLICA DE GUINEA ECUATORIAL'), 0, 1, 'C');
            $this->Cell(0, 5, utf8_decode('MINISTERIO DE JUSTICIA, CULTO E INSTITUCIONES PENITENCIARIAS'), 0, 1, 'C');
            $this->Ln(5);
            $this->SetFont('Arial', 'B', 12);
            $this->SetFillColor(200, 220, 255);
            $this->Cell(0, 10, utf8_decode('LISTADO GENERAL DE FUNCIONARIOS'), 0, 1, 'C', true);
            $this->Ln(5);

            // Encabezados de tabla
            $this->SetFont('Arial', 'B', 8);
            $this->SetFillColor(230, 230, 230);
            $this->Cell(7, 7, '#', 1, 0, 'C', true);
            $this->Cell(55, 7, 'Nombre Completo', 1, 0, 'C', true);
            $this->Cell(25, 7, utf8_decode('Código'), 1, 0, 'C', true);
            $this->Cell(25, 7, 'Estado', 1, 0, 'C', true);
            $this->Cell(45, 7, utf8_decode('Dirección'), 1, 0, 'C', true);
            $this->Cell(45, 7, 'Seccion', 1, 0, 'C', true);
            $this->Cell(35, 7, 'Cargo', 1, 1, 'C', true);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        }
    }

    $pdf = new PDF('L', 'mm', 'A4'); // Horizontal
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 8);

    foreach ($funcionarios as $i => $row) {
        $pdf->Cell(7, 6, $i + 1, 1, 0, 'C');
        $pdf->Cell(55, 6, utf8_decode($row['Nombre'] . ' ' . $row['Apellidos']), 1);
        $pdf->Cell(25, 6, $row['CODIGO'], 1, 0, 'C');
        $pdf->Cell(25, 6, utf8_decode($row['Estado_Laboral']), 1, 0, 'C');
        $pdf->Cell(45, 6, utf8_decode($row['nombre_direccion'] ?? 'N/A'), 1);
        $pdf->Cell(45, 6, utf8_decode($row['nombre_seccion'] ?? 'N/A'), 1);
        $pdf->Cell(35, 6, utf8_decode($row['nombre_cargo'] ?? 'N/A'), 1);
        $pdf->Ln();
    }

    $pdf->Output('I', 'Reporte_Funcionarios.pdf');
    exit;
}