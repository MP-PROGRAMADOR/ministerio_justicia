<?php
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    // Total Funcionarios
    $total = $pdo->query("SELECT COUNT(*) FROM funcionarios")->fetchColumn();
    
    // Funcionarios Activos
    $activos = $pdo->query("SELECT COUNT(*) FROM funcionarios WHERE Estado_Laboral = 'Activo'")->fetchColumn();

    // Distribución por Estado
    $dist = $pdo->query("SELECT Estado_Laboral as label, COUNT(*) as value FROM funcionarios GROUP BY Estado_Laboral")->fetchAll(PDO::FETCH_ASSOC);

    // Top Direcciones (JOIN con secciones porque funcionarios apunta a secciones)
    $direcciones = $pdo->query("SELECT d.nombre as label, COUNT(f.Id_funcionario) as value 
                                FROM direcciones d
                                JOIN secciones s ON d.Id_direccion = s.Id_direccion
                                JOIN funcionarios f ON s.Id_seccion = f.Id_seccion
                                GROUP BY d.nombre LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'data' => [
            'totalFuncionarios' => (int)$total,
            'statFuncionariosActivos' => (int)$activos, // Cambiado para coincidir con el JS
            'funcionarioDistribution' => [
                'labels' => array_column($dist, 'label'),
                'data' => array_column($dist, 'value')
            ],
            'topDirecciones' => [
                'labels' => array_column($direcciones, 'label'),
                'data' => array_column($direcciones, 'value')
            ],
            'recentActivity' => $pdo->query("SELECT * FROM logs ORDER BY Fecha DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC)
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}