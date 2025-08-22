<?php
session_start();
require_once '../includes/conexion.php';

header('Content-Type: application/json');

$q = $_GET['q'] ?? '';
$q = trim($q);

if($q === ''){
    echo json_encode([]);
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $sql = "SELECT ID_Funcionario, Nombres, Apellidos, DNI_Pasaporte 
            FROM tbl_funcionarios 
            WHERE CONCAT(Nombres,' ',Apellidos) LIKE :term 
            AND Estado_Laboral='Activo' 
            LIMIT 10";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':term' => "%$q%"]);
    $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($funcionarios);

} catch(PDOException $e){
    echo json_encode([]);
}
