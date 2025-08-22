<?php
// obtener_funcionarios.php
header('Content-Type: application/json; charset=utf-8');

include_once '../includes/conexion.php';

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Consultar todos los funcionarios activos (puedes agregar filtros si quieres)
    $stmt = $pdo->query("SELECT ID_Funcionario, Nombres, Apellidos FROM tbl_funcionarios ORDER BY Nombres ASC");
    $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($funcionarios, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
