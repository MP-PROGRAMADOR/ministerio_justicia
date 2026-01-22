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
$pdo = new PDO($dsn, $user, $pass, $options);