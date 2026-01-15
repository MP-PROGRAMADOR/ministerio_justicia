<?php
require_once '../includes/conexion.php';
header('Content-Type: application/json');

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $response = ['success' => true];

    // Obtener Direcciones
    $response['direcciones'] = $pdo->query("SELECT Id_direccion, nombre FROM direcciones ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

    // Obtener Secciones
    $response['secciones'] = $pdo->query("SELECT Id_seccion, nombre FROM secciones ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

    // Obtener Categorías (Cargos)
    $response['categorias'] = $pdo->query("SELECT Id_categoria, nombre FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
