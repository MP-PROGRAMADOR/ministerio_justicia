<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
ob_start();

require_once 'fpdf.php';
require '../includes/conexion.php'; 

function txt($texto, $default = 'No registrado') {
    $val = ($texto !== null && trim($texto) !== '') ? $texto : $default;
    return utf8_decode($val);
}

class PDF extends FPDF {
    protected $blueDark = [26, 64, 126];
    protected $blueGold = [184, 134, 11]; 
    protected $grayLight = [245, 245, 245];

    function SectionTitle($title) {
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor($this->grayLight[0], $this->grayLight[1], $this->grayLight[2]);
        $this->SetTextColor($this->blueDark[0], $this->blueDark[1], $this->blueDark[2]);
        $this->SetDrawColor(200, 200, 200);
        $this->Cell(0, 8, "  " . strtoupper(txt($title)), 'B', 1, 'L', true); 
        $this->Ln(2);
        $this->SetTextColor(0);
    }

    function Header() {
        $this->SetFillColor(26, 64, 126);
        $this->Rect(0, 0, 210, 2, 'F');
        if (file_exists('../img/logo.png')) {
            $this->Image('../img/logo.png', 10, 10, 22);
        }
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor($this->blueDark[0], $this->blueDark[1], $this->blueDark[2]);
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
        $this->SetXY(140, 15);
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(255, 255, 255);
        $this->SetFillColor(26, 64, 126);
        $this->Cell(60, 6, txt('EXPEDIENTE TÉCNICO INDIVIDUAL'), 0, 1, 'C', true);
        $this->SetTextColor(100);
        $this->SetFont('Arial', 'I', 7);
        $this->SetX(140);
        $this->Cell(60, 5, txt('Emisión: ') . date('d/m/Y H:i'), 0, 1, 'R');
        $this->Ln(15);
    }

    function Footer() {
        $this->SetY(-20);
        $this->SetDrawColor(200);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120);
        $this->Cell(0, 10, txt('Sistema THEMIS - Guinea Ecuatorial'), 0, 0, 'L');
        $this->Cell(0, 10, txt('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    // Nueva función para filas verticales (Etiqueta arriba, valor abajo o al lado)
    function VerticalRow($label, $value, $textColor = [0, 0, 0], $isBold = false) {
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(100);
        $this->Cell(50, 6, txt($label), 0, 0);
        
        $this->SetFont('Arial', ($isBold ? 'B' : ''), 10);
        $this->SetTextColor($textColor[0], $textColor[1], $textColor[2]);
        $this->MultiCell(0, 6, txt($value), 0, 'L');
        $this->SetTextColor(0);
        $this->Ln(1);
    }
}

// ... (Mismas consultas a la base de datos de tu código anterior) ...
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$pdo = new PDO($dsn, $user, $pass, $options);
$stmt = $pdo->prepare("SELECT f.*, s.nombre as seccion_nom, d.nombre as direccion_nom, 
                        d.ubicacion as dir_ciudad, d.distrito as dir_distrito, 
                        d.provincia as dir_provincia, d.region as dir_region,
                        TIMESTAMPDIFF(YEAR, f.Fecha_nacimiento, CURDATE()) AS edad
                        FROM funcionarios f
                        LEFT JOIN secciones s ON f.Id_seccion = s.Id_seccion
                        LEFT JOIN direcciones d ON s.Id_direccion = d.Id_direccion
                        WHERE f.Id_funcionario = ?");
$stmt->execute([$id]);
$f = $stmt->fetch(PDO::FETCH_ASSOC);

// Otras consultas (Formación, Capacitaciones, Permisos, Historial)
$stmtEst = $pdo->prepare("SELECT * FROM tbl_formacion_academica WHERE ID_Funcionario = ? ORDER BY Fecha_Graduacion DESC");
$stmtEst->execute([$id]); $estudios_adicionales = $stmtEst->fetchAll(PDO::FETCH_ASSOC);
$stmtCap = $pdo->prepare("SELECT * FROM tbl_capacitaciones WHERE ID_Funcionario = ? ORDER BY Fecha_Fin_Curso DESC");
$stmtCap->execute([$id]); $capacitaciones = $stmtCap->fetchAll(PDO::FETCH_ASSOC);
$stmtPerm = $pdo->prepare("SELECT * FROM tbl_permisos WHERE ID_Funcionario = ? AND Estado_Permiso = 'Aprobado'");
$stmtPerm->execute([$id]); $permisos = $stmtPerm->fetchAll(PDO::FETCH_ASSOC);
$stmtH = $pdo->prepare("SELECT n.*, c.Nombre as cargo_nom, d.nombre as dir_nom FROM nombramientos n LEFT JOIN cargos c ON n.Id_cargo = c.Id_cargo LEFT JOIN direcciones d ON n.Id_direccion = d.Id_direccion WHERE n.Id_funcionario = ? ORDER BY n.Fecha_nombramiento DESC");
$stmtH->execute([$id]); $historial = $stmtH->fetchAll(PDO::FETCH_ASSOC);

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// 1. FOTO CENTRADA AL INICIO
$fotoW = 35; $fotoH = 42;
$posX = (210 - $fotoW) / 2; // Centrado horizontal
$rutaFoto = !empty($f['Foto']) ? '../api/' . $f['Foto'] : '';

if ($rutaFoto && file_exists($rutaFoto)) {
    $pdf->Image($rutaFoto, $posX, 40, $fotoW, $fotoH);
} else {
    $pdf->SetFillColor(245);
    $pdf->Rect($posX, 40, $fotoW, $fotoH, 'F');
    $pdf->SetXY($posX, 40 + 18);
    $pdf->SetFont('Arial', 'I', 7);
    $pdf->Cell($fotoW, 5, txt('SIN FOTO'), 0, 0, 'C');
}
$pdf->SetY(40 + $fotoH + 5);

// 2. NOMBRE DESTACADO
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(26, 64, 126);
$pdf->Cell(0, 10, txt($f['Nombre'].' '.$f['Apellidos']), 0, 1, 'C');
$pdf->Ln(2);

// 1. INFORMACIÓN PERSONAL (Vertical)
$pdf->SectionTitle('1. Información Personal');
$pdf->VerticalRow('DIP / Pasaporte:', $f['Dip_Pasaporte']);
$pdf->VerticalRow('Edad:', $f['edad'] . ' años');
$pdf->VerticalRow('Fecha de Nacimiento:', $f['Fecha_nacimiento']);
$pdf->VerticalRow('Lugar de Nacimiento:', $f['Lugar_nacimiento']);
$pdf->VerticalRow('Natural de:', $f['Nacionalidad'] . " - Distrito: " . $f['dir_distrito'] . " - Provincia: " . $f['dir_provincia']);

// 2. INFORMACIÓN DE CONTACTO
$pdf->SectionTitle('2. Información de Contacto');
$pdf->VerticalRow('Número de Teléfono:', $f['Telefono']);
$pdf->VerticalRow('Correo Electrónico:', $f['Correo'], [26, 64, 126]);
$pdf->VerticalRow('Ubicación / Barrio:', $f['Domicilio']);

// 3. INFORMACIÓN DE FUNCIONARIO
$pdf->SectionTitle('3. Información de Funcionario');
$cargoActual = !empty($historial) ? $historial[0]['cargo_nom'] : $f['Funcion'];
$pdf->VerticalRow('Función (Cargo) Actual:', $cargoActual, [184, 134, 11], true);
$pdf->VerticalRow('Sección Actual:', $f['seccion_nom']);
$pdf->VerticalRow('Dirección Actual:', $f['direccion_nom']);
$pdf->VerticalRow('Ubicación Laboral:', $f['dir_ciudad'].", ".$f['dir_distrito'].", ".$f['dir_provincia'].", ".$f['dir_region']);
$pdf->VerticalRow('Fecha Nombramiento:', $f['Fecha_nombramiento']);
$pdf->VerticalRow('Fecha Toma Posesión:', $f['Fecha_posesion']);

// 4. INFORMACIÓN DE ESTUDIO
$pdf->SectionTitle('4. Información de Estudio');
$pdf->VerticalRow('Profesión:', $f['Profesion']);
$pdf->VerticalRow('Nivel Maximo de Estudios:', $f['Maximo_nivel_estudios']);
$pdf->VerticalRow('Titulación Académica:', $f['Titulacion_academica'] . " (" . $f['Universidad_centro_formacion'] . ")");

if ($estudios_adicionales || $capacitaciones) {
    $pdf->SetFont('Arial', 'B', 9); $pdf->SetTextColor(100);
    $pdf->Cell(50, 6, txt('Otros Estudios:'), 0, 1);
    $pdf->SetFont('Arial', '', 9); $pdf->SetTextColor(0);
    foreach ($estudios_adicionales as $est) {
        $pdf->Cell(10); $pdf->Cell(0, 5, chr(149)." ".txt($est['Titulo_Obtenido'] . " (" . $est['Institucion_Educativa'] . ")"), 0, 1);
    }
    foreach ($capacitaciones as $cap) {
        $pdf->Cell(10); $pdf->Cell(0, 5, chr(149)." ".txt($cap['Nombre_Curso'] . " - Formación Ministerio"), 0, 1);
    }
}


// 5. HISTORIAL Y TRAYECTORIA
$pdf->SectionTitle('5. Historial y Trayectoria');

// --- TABLA 1: NOMBRAMIENTOS ---
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(80, 80, 80); // Color neutro para subtítulo
$pdf->Cell(0, 7, txt('DETALLE DE CARGOS Y FUNCIONES PREVIAS'), 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(26, 64, 126); // Azul Corporativo para Nombramientos
$pdf->SetTextColor(255); 
$w1 = 55; $w2 = 75; $w3 = 30; $w4 = 30;

$pdf->Cell($w1, 8, txt('CARGO'), 1, 0, 'C', true);
$pdf->Cell($w2, 8, txt('UNIDAD ADMINISTRATIVA'), 1, 0, 'C', true);
$pdf->Cell($w3, 8, txt('F. NOMBRAMIENTO'), 1, 0, 'C', true);
$pdf->Cell($w4, 8, txt('F. POSESIÓN'), 1, 1, 'C', true);

$pdf->SetTextColor(0);
$pdf->SetFont('Arial', '', 7.5);
$fill = false;

if (!empty($historial)) {
    foreach ($historial as $h) {
        $pdf->Cell($w1, 7, txt($h['cargo_nom']), 1, 0, 'L', $fill);
        $pdf->Cell($w2, 7, txt($h['dir_nom']), 1, 0, 'L', $fill);
        $pdf->Cell($w3, 7, txt($h['Fecha_nombramiento']), 1, 0, 'C', $fill);
        $pdf->Cell($w4, 7, txt($h['Fecha_toma_posesion']), 1, 1, 'C', $fill);
        $fill = !$fill;
    }
} else {
    $pdf->Cell(190, 7, txt('No se registran cargos previos en el sistema.'), 1, 1, 'C');
}

$pdf->Ln(6);

// --- TABLA 2: PERMISOS Y LICENCIAS ---
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(80, 80, 80); // Color neutro para subtítulo
$pdf->Cell(0, 7, txt('REGISTRO PERMISOS'), 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(220, 220, 220); 
$pdf->SetTextColor(0);        

// Ajuste de anchos para Permisos (Total 190mm)
$wTipo = 60; $wMot = 100; $wFec = 30;

$pdf->Cell($wTipo, 8, txt('TIPO DE PERMISO'), 1, 0, 'C', true);
$pdf->Cell($wMot, 8, txt('MOTIVO / OBSERVACIÓN'), 1, 0, 'C', true);
$pdf->Cell($wFec, 8, txt('FECHA INICIO'), 1, 1, 'C', true);

$pdf->SetTextColor(0);
$pdf->SetFont('Arial', '', 7.5);
$fill = false;

if (!empty($permisos)) {
    foreach ($permisos as $p) {
        $pdf->Cell($wTipo, 7, txt($p['Tipo_Permiso']), 1, 0, 'L', $fill);
        $pdf->Cell($wMot, 7, txt($p['Motivo']), 1, 0, 'L', $fill);
        $pdf->Cell($wFec, 7, txt($p['Fecha_Inicio_Permiso']), 1, 1, 'C', $fill);
        $fill = !$fill;
    }
} else {
    $pdf->Cell(190, 7, txt('No existen registros de licencias para este funcionario.'), 1, 1, 'C');
}

$pdf->Ln(5);




if (ob_get_length()) ob_end_clean();
$pdf->Output('I', 'Expediente_' . $f['CODIGO'] . '.pdf');