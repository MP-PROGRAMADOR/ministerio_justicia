<?php
include_once '../includes/conexion.php';
 $pdo = new PDO($dsn, $user, $pass, $options);




header('Content-Type: application/json; charset=utf-8');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($q === '') {
    echo json_encode([]);
    exit;
}

$sql = "SELECT ID_Funcionario, Nombres, Apellidos 
        FROM tbl_funcionarios
        WHERE Nombres LIKE :q OR Apellidos LIKE :q
        LIMIT 10";

$stmt = $pdo->prepare($sql);
$stmt->execute(['q' => "%$q%"]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($resultados);

