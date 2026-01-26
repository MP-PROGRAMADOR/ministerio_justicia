<?php
// 1. CONFIGURACIÓN DE ERRORES Y LIMPIEZA
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../includes/conexion.php';
if (!isset($pdo)) {
    $pdo = new PDO($dsn, $user, $pass, $options);
}

// 2. VALIDACIÓN DE ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("Error: No se proporcionó un ID válido.");
}

try {
    // Consulta Principal con Distrito y Ubicación
    $sql = "SELECT f.*, s.nombre as seccion_nom, d.nombre as direccion_nom, 
            d.distrito, d.ubicacion,
            c.Nombre as cargo_nom,
            TIMESTAMPDIFF(YEAR, f.Fecha_nacimiento, CURDATE()) AS edad
            FROM funcionarios f
            LEFT JOIN secciones s ON f.Id_seccion = s.Id_seccion
            LEFT JOIN direcciones d ON s.Id_direccion = d.Id_direccion
            LEFT JOIN cargos c ON f.Id_cargo = c.Id_cargo
            WHERE f.Id_funcionario = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $f = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$f) {
        die("Error: El funcionario no existe.");
    }

    // Formación Académica
    $stmtEst = $pdo->prepare("SELECT * FROM tbl_formacion_academica WHERE ID_Funcionario = ? ORDER BY Fecha_Graduacion DESC");
    $stmtEst->execute([$id]);
    $listaEstudios = $stmtEst->fetchAll(PDO::FETCH_ASSOC);

    // Capacitaciones
    $stmtCap = $pdo->prepare("SELECT * FROM tbl_capacitaciones WHERE ID_Funcionario = ? ORDER BY Fecha_Fin_Curso DESC");
    $stmtCap->execute([$id]);
    $listaCapacitaciones = $stmtCap->fetchAll(PDO::FETCH_ASSOC);

    // Permisos
    $stmtPerm = $pdo->prepare("SELECT * FROM tbl_permisos WHERE ID_Funcionario = ? AND Estado_Permiso = 'Aprobado'");
    $stmtPerm->execute([$id]);
    $listaPermisos = $stmtPerm->fetchAll(PDO::FETCH_ASSOC);

    // Historial de Nombramientos (Vigentes primero)
    $stmtNom = $pdo->prepare("SELECT n.*, 
    c.Nombre as cargo_nom, 
    s.nombre as seccion_nom,
    d.nombre as direccion_nom,
    d.ubicacion as dir_ubicacion 
    FROM nombramientos n 
    LEFT JOIN cargos c ON n.Id_cargo = c.Id_cargo 
    LEFT JOIN secciones s ON n.Id_seccion = s.Id_seccion
    LEFT JOIN direcciones d ON s.Id_direccion = d.Id_direccion
    WHERE n.Id_funcionario = ? 
    ORDER BY (n.Fecha_finalizacion_nombramiento IS NULL) DESC, n.Fecha_nombramiento DESC");
    $stmtNom->execute([$id]);
    $listaNombramientos = $stmtNom->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error en la Base de Datos: " . $e->getMessage());
}

// 3. GENERACIÓN DE PDF
require_once 'fpdf.php';
if (ob_get_length()) ob_end_clean();

function txt($texto)
{
    return utf8_decode($texto ?? 'No registrado');
}

class PDF extends FPDF
{
    protected $colBlue = [26, 64, 126];
    protected $colGold = [184, 134, 11];
    protected $colGray = [245, 245, 245];

    function Header()
    {
        if ($this->PageNo() == 1) {
            $this->SetFillColor($this->colBlue[0], $this->colBlue[1], $this->colBlue[2]);
            $this->Rect(0, 0, 210, 3, 'F');

            if (file_exists('../img/logo.png')) {
                $this->Image('../img/logo.png', 12, 10, 26);
            }

            $this->SetTextColor($this->colBlue[0], $this->colBlue[1], $this->colBlue[2]);
            $this->SetFont('Arial', 'B', 10);
            $this->SetX(44);
            $this->Cell(0, 5, txt('REPÚBLICA DE GUINEA ECUATORIAL'), 0, 1);

            $this->SetFont('Arial', '', 8);
            $this->SetTextColor(100);
            $this->SetX(44);
            $this->Cell(0, 4, txt('MINISTERIO DE JUSTICIA, CULTO Y DERECHOS HUMANOS'), 0, 1);

            // Espaciado y SISTEMA THEMIS (Tamaño 16)
            $this->Ln(4);
            $this->SetFont('Arial', 'B', 16);
            $this->SetTextColor($this->colBlue[0], $this->colBlue[1], $this->colBlue[2]);
            $this->SetX(44);
            $this->Cell(0, 7, txt('SISTEMA THEMIS'), 0, 1);

            // Subtítulo
            $this->SetFont('Arial', 'B', 11);
            $this->SetTextColor($this->colGold[0], $this->colGold[1], $this->colGold[2]);
            $this->SetX(44);
            $this->Cell(0, 6, txt('EXPEDIENTE DEL FUNCIONARIO'), 0, 1);

            // Línea Divisoria bajada para no tocar el logo
            $this->SetDrawColor($this->colGold[0], $this->colGold[1], $this->colGold[2]);
            $this->SetLineWidth(0.8);
            $this->Line(12, 46, 198, 46);

            $this->Ln(12);
        } else {
            $this->Ln(5);
        }
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150);
        $this->Cell(0, 10, txt('Sistema THEMIS - Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function Titulo($txt)
    {
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor($this->colGray[0], $this->colGray[1], $this->colGray[2]);
        $this->SetTextColor($this->colBlue[0], $this->colBlue[1], $this->colBlue[2]);
        $this->Cell(0, 8, "  " . txt(strtoupper($txt)), 'B', 1, 'L', true);
        $this->Ln(2);
        $this->SetTextColor(0);
    }

    function InfoPair($label1, $value1, $label2, $value2)
    {
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(85);
        $this->Cell(35, 6, txt($label1 . ":"), 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(30);
        $this->Cell(60, 6, txt($value1), 0, 0);

        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(85);
        $this->Cell(35, 6, txt($label2 . ":"), 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(30);
        $this->Cell(0, 6, txt($value2), 0, 1);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// CABECERA DE DATOS (FOTO Y NOMBRE)
$yStart = $pdf->GetY() + 5;
$fotoW = 35;
$fotoH = 42;
$fotoX = 163;
$fotoY = $yStart;

$rutaFoto = '../api/' . $f['Foto'];
if (!empty($f['Foto']) && file_exists($rutaFoto)) {
    $pdf->Image($rutaFoto, $fotoX, $fotoY, $fotoW, $fotoH);
} else {
    $pdf->SetDrawColor(200);
    $pdf->Rect($fotoX, $fotoY, $fotoW, $fotoH);
    $pdf->SetXY($fotoX, $fotoY + 18);
    $pdf->SetFont('Arial', 'I', 7);
    $pdf->Cell($fotoW, 5, txt('SIN FOTO'), 0, 0, 'C');
}

$pdf->SetXY(12, $yStart + 5);
$pdf->SetFont('Arial', 'B', 18);
$pdf->SetTextColor(26, 64, 126);
$pdf->Cell(145, 10, txt($f['Nombre'] . " " . $f['Apellidos']), 0, 1);
$pdf->SetX(12);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(184, 134, 11);
$pdf->Cell(145, 7, txt($f['cargo_nom'] ?? 'CARGO NO ASIGNADO'), 0, 1);
$pdf->SetX(12);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(100);
$pdf->Cell(145, 7, txt('ID FUNCIONARIO: ' . $f['CODIGO']), 0, 1);

$pdf->SetY($yStart + 45);



// 1. DATOS PERSONALES
$val = function ($campo) {
    return (!empty($campo) && $campo !== '0000-00-00') ? $campo : 'No registrado';
};

// 1. DATOS PERSONALES
$pdf->Titulo('1. Datos Personales');

// Bloque A: Identificación Oficial
$pdf->SetFont('Arial', 'BI', 8);
$pdf->SetTextColor(150);
$pdf->Cell(0, 5, txt("Identificación Oficial"), 0, 1);

$pdf->InfoPair('DIP / Pasaporte', $val($f['Dip_Pasaporte']), 'Carnet Funcionario', $val($f['Num_carnet_fun']));
$pdf->InfoPair('Fecha Nacimiento', $val($f['Fecha_nacimiento']) . " (" . $f['edad'] . " años)", 'Sexo / Nacionalidad', $val($f['Sexo']) . " - " . $val($f['Nacionalidad']));

$pdf->Ln(3);

// Bloque B: Origen y Etnografía (Ajustado para altura simétrica)
$pdf->SetFont('Arial', 'BI', 8);
$pdf->SetTextColor(150);
$pdf->Cell(0, 5, txt("Origen y Etnografía"), 0, 1);

// Combinamos para que siempre ocupen 2 filas exactas
$etnia = ($f['Tribu'] || $f['Pueblo']) ? $f['Tribu'] . " / " . $f['Pueblo'] : "No registrado";
$procedencia = ($f['Provincia'] || $f['Distrito']) ? $f['Provincia'] . " - " . $f['Distrito'] : "No registrado";

$pdf->InfoPair('Etnia (Tribu/Pueblo)', $etnia, 'Lugar de Nacimiento', $val($f['Lugar_nacimiento']));
$pdf->InfoPair('Provincia / Distrito', $procedencia, 'Referencia Origen', 'G. Ecuatorial'); // Relleno para mantener altura simétrica

$pdf->Ln(3);

// Bloque C: Contacto y Localización
$pdf->SetFont('Arial', 'BI', 8);
$pdf->SetTextColor(150);
$pdf->Cell(0, 5, txt("Contacto y Localización"), 0, 1);

$pdf->InfoPair('Teléfono', $val($f['Telefono']), 'Correo Electrónico', $val($f['Correo']));

// Domicilio con control de ancho para no romper la estética
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetTextColor(100);
$pdf->Cell(35, 5, txt("Domicilio Actual:"), 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(30);
$pdf->Cell(0, 5, txt($val($f['Domicilio'])), 0, 1);




// 2. SITUACIÓN LABORAL Y DESTINO
$pdf->Titulo('2. Situación Laboral Actual');
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(85);
$pdf->Cell(45, 6, txt("Estado Laboral:"), 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(26, 64, 126);
$pdf->Cell(0, 6, txt($f['Estado_Laboral']), 0, 1);

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(85);
$pdf->Cell(45, 6, txt("Dirección:"), 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(30);
$pdf->Cell(0, 6, txt($f['direccion_nom']), 0, 1);

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(85);
$pdf->Cell(45, 6, txt("Sección:"), 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(30);
$pdf->Cell(0, 6, txt($f['seccion_nom']), 0, 1);

$destino = ($f['ubicacion'] ?? 'N/R') . " - " . ($f['distrito'] ?? 'N/R');
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(85);
$pdf->Cell(45, 6, txt("Destino Actual:"), 0, 0);
$pdf->SetFont('Arial', 'I', 10);
$pdf->SetTextColor(30);
$pdf->Cell(0, 6, txt($destino), 0, 1);

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(85);
$pdf->Cell(45, 6, txt("Fecha de Nombramiento:"), 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(30);
$pdf->Cell(0, 6, $f['Fecha_nombramiento'], 0, 1);

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(85);
$pdf->Cell(45, 6, txt("Fecha Posesión:"), 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(30);
$pdf->Cell(0, 6, $f['Fecha_posesion'], 0, 1);



// 3. HISTORIAL DE NOMBRAMIENTOS
$pdf->Titulo('3. Historial de Nombramientos');

if ($listaNombramientos) {
    $wCargo = 42;
    $wDir   = 63; 
    $wSec   = 35;
    $wFini  = 25;
    $wFfin  = 25;

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(235, 235, 235);
    $pdf->SetTextColor(0);

    // Encabezados
    $pdf->Cell($wCargo, 7, txt('CARGO'), 1, 0, 'L', true);
    $pdf->Cell($wDir,   7, txt('DIRECCIÓN / UBICACIÓN'), 1, 0, 'L', true);
    $pdf->Cell($wSec,   7, txt('SECCIÓN'), 1, 0, 'L', true);
    $pdf->Cell($wFini,  7, txt('F. INICIO'), 1, 0, 'C', true);
    $pdf->Cell($wFfin,  7, txt('ESTADO / FIN'), 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 7);

    foreach ($listaNombramientos as $n) {
        $vigente = empty($n['Fecha_finalizacion_nombramiento']);

        if ($vigente) {
            $pdf->SetFillColor(240, 250, 240);
            $pdf->SetTextColor(20, 80, 20);
            $pdf->SetFont('Arial', 'B', 7);
        } else {
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(50);
            $pdf->SetFont('Arial', '', 7);
        }


        $dirText = ($n['direccion_nom'] ?? 'N/R') . " (" . ($n['dir_ubicacion'] ?? 'N/R') . ")";

        $pdf->Cell($wCargo, 6, txt(substr($n['cargo_nom'] ?? 'N/R', 0, 28)), 1, 0, 'L', true);
        $pdf->Cell($wDir,   6, txt(substr($dirText, 0, 48)), 1, 0, 'L', true);
        $pdf->Cell($wSec,   6, txt(substr($n['seccion_nom'] ?? 'N/R', 0, 22)), 1, 0, 'L', true);
        $pdf->Cell($wFini,  6, $val($n['Fecha_nombramiento']), 1, 0, 'C', true);

        $txtFin = $vigente ? 'VIGENTE' : $val($n['Fecha_finalizacion_nombramiento']);
        $pdf->Cell($wFfin,  6, txt($txtFin), 1, 1, 'C', true);
    }
    $pdf->SetTextColor(0); 
} else {
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Cell(0, 8, txt('No existen registros históricos de nombramientos.'), 0, 1);
}

// 4. FORMACIÓN ACADÉMICA
$pdf->Titulo('4. Formación Académica');
foreach ($listaEstudios as $e) {
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetX(15);
    $pdf->Cell(0, 5, txt($e['Titulo_Obtenido'] . " (" . $e['Nivel_Educativo'] . ")"), 0, 1);
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetX(15);
    $pdf->Cell(0, 5, txt($e['Institucion_Educativa'] . " - " . $e['Fecha_Graduacion']), 0, 1);
}

// 5. CAPACITACIONES
$pdf->Titulo('5. Capacitaciones');
foreach ($listaCapacitaciones as $c) {
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetX(15);
    $pdf->Cell(0, 5, txt($c['Nombre_Curso']), 0, 1);
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetX(15);
    $pdf->Cell(0, 5, txt($c['Institucion_Organizadora'] . " - Fin: " . $c['Fecha_Fin_Curso']), 0, 1);
}

// 6. PERMISOS
$pdf->Titulo('6. Permisos Aprobados');
if ($listaPermisos) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(235, 235, 235);
    $pdf->Cell(45, 7, txt('TIPO'), 1, 0, 'C', true);
    $pdf->Cell(85, 7, txt('MOTIVO'), 1, 0, 'C', true);
    $pdf->Cell(30, 7, txt('F. INICIO'), 1, 0, 'C', true);
    $pdf->Cell(30, 7, txt('F. FIN'), 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 8);
    foreach ($listaPermisos as $p) {
        $pdf->Cell(45, 6, txt($p['Tipo_Permiso']), 1);
        $pdf->Cell(85, 6, txt(substr($p['Motivo'], 0, 50)), 1);
        $pdf->Cell(30, 6, $p['Fecha_Inicio_Permiso'], 1, 0, 'C');
        $pdf->Cell(30, 6, ($p['Fecha_Fin_Permiso'] ?? 'N/R'), 1, 1, 'C');
    }
} else {
    $pdf->Cell(0, 6, txt('Sin permisos registrados.'), 0, 1);
}

$pdf->Output('I', 'Expediente_' . $f['CODIGO'] . '.pdf');
