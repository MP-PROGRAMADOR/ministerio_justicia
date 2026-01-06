<?php
include_once '../includes/conexion.php';
$pdo = new PDO($dsn, $user, $pass, $options);

if (!isset($_GET['ID_Curso'])) {
    echo json_encode([]);
    exit;
}

$idCurso = intval($_GET['ID_Curso']);

$stmt = $pdo->prepare("
    SELECT f.ID_Funcionario, f.Nombres
    , f.Apellidos, f.CODIGO, f.Dip_Pasaporte
    FROM tbl_cursos_funcionarios cf
    JOIN funcionarios f ON cf.ID_Funcionario = f.ID_Funcionario
    WHERE cf.ID_Curso = :idCurso
    ORDER BY f.Nombre, f.Apellidos
");
$stmt->execute([':idCurso' => $idCurso]);
$funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($funcionarios);
