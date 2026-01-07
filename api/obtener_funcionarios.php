<?php
// obtener_funcionarios.php
header('Content-Type: application/json; charset=utf-8');

// Iniciamos sesión para asegurar que el Usuario_creador sea válido si fuera necesario
session_start(); 

include_once '../includes/conexion.php';

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Capturamos el término de búsqueda si existe
    $busqueda = isset($_GET['q']) ? $_GET['q'] : '';

    if (!empty($busqueda)) {
        // Búsqueda filtrada usando los nuevos nombres de columna de tu tabla
        $sql = "SELECT Id_funcionario, Nombre, Apellidos, CODIGO 
                FROM funcionarios 
                WHERE Nombre LIKE :query 
                OR Apellidos LIKE :query 
                OR CODIGO LIKE :query 
                ORDER BY Nombre ASC 
                LIMIT 20";
        
        $stmt = $pdo->prepare($sql);
        $searchTerm = "%$busqueda%";
        $stmt->execute(['query' => $searchTerm]);
    } else {
        // Consulta general con los nuevos nombres de columna
        $sql = "SELECT Id_funcionario, Nombre, Apellidos, CODIGO 
                FROM funcionarios 
                ORDER BY Nombre ASC 
                LIMIT 50";
        $stmt = $pdo->query($sql);
    }

    $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retornamos los resultados
    echo json_encode($funcionarios, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    // Si el error 1452 persiste, es porque hay un proceso de escritura fallido
    http_response_code(500);
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
}