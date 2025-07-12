<?php
// Activar errores solo para desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de la base de datos
require '../includes/conexion.php';

// Conexión PDO
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

// Consultas
try {
    $departamentos = $pdo->query("SELECT ID_Departamento, Nombre_Departamento FROM tbl_departamentos ORDER BY Nombre_Departamento")->fetchAll();
    $cargos = $pdo->query("SELECT ID_Cargo, Nombre_Cargo FROM tbl_cargos ORDER BY Nombre_Cargo")->fetchAll();
    $destinos = $pdo->query("SELECT ID_Destino, Nombre_Destino FROM tbl_destinos ORDER BY Nombre_Destino")->fetchAll();

    echo json_encode([
        'success' => true,
        'departamentos' => $departamentos,
        'cargos' => $cargos,
        'destinos' => $destinos
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al consultar filtros: ' . $e->getMessage()]);
}
