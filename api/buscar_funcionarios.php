<?php
include_once '../includes/conexion.php';

header('Content-Type: application/json'); // Muy importante para que fetch interprete correctamente

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $q = $_GET['q'] ?? '';
    $sql = "SELECT Id_funcionario, Nombre, Apellidos, Dip_Pasaporte 
            FROM funcionarios 
            WHERE Nombre LIKE ? OR Apellidos LIKE ? 
            LIMIT 10";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$q%", "%$q%"]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
