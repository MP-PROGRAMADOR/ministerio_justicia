<?php
include("conexion.php");

$idFuncionario = $_GET['id'] ?? null;
if (!$idFuncionario) {
    echo json_encode(['error' => 'ID no proporcionado']);
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Datos personales
    $stmt = $pdo->prepare("SELECT * FROM tbl_funcionarios WHERE ID_Funcionario = ?");
    $stmt->execute([$idFuncionario]);
    $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Asignaciones
    $stmt = $pdo->prepare("SELECT a.*, c.Nombre_Cargo AS Cargo, d.Nombre_Departamento AS Departamento, dest.Nombre_Destino AS Destino
        FROM tbl_asignaciones a
        JOIN tbl_cargos c ON a.ID_Cargo = c.ID_Cargo
        JOIN tbl_departamentos d ON a.ID_Departamento = d.ID_Departamento
        JOIN tbl_destinos dest ON a.ID_Destino = dest.ID_Destino
        WHERE a.ID_Funcionario = ?");
    $stmt->execute([$idFuncionario]);
    $asignaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formación Académica
    $stmt = $pdo->prepare("SELECT * FROM tbl_formaciones WHERE ID_Funcionario = ?");
    $stmt->execute([$idFuncionario]);
    $formaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Capacitaciones
    $stmt = $pdo->prepare("SELECT * FROM tbl_capacitaciones WHERE ID_Funcionario = ?");
    $stmt->execute([$idFuncionario]);
    $capacitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Permisos
    $stmt = $pdo->prepare("SELECT * FROM tbl_permisos WHERE ID_Funcionario = ?");
    $stmt->execute([$idFuncionario]);
    $permisos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'funcionario' => $funcionario,
        'asignaciones' => $asignaciones,
        'formaciones' => $formaciones,
        'capacitaciones' => $capacitaciones,
        'permisos' => $permisos
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>
