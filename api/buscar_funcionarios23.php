<?php
require '../includes/conexion.php';

header('Content-Type: application/json');

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $estadoLaboral = $_POST['estado_laboral'] ?? '';
    $idDepartamento = $_POST['id_departamento'] ?? '';
    $idCargo = $_POST['id_cargo'] ?? '';
    $idDestino = $_POST['id_destino'] ?? '';
    $fechaInicio = $_POST['fecha_inicio'] ?? '';
    $fechaFin = $_POST['fecha_fin'] ?? '';
    $reporteGeneral = $_POST['reporte_general'] ?? '';

    $sql = "
        SELECT f.ID_Funcionario, f.Codigo_Funcionario, f.Nombres, f.Apellidos, f.Estado_Laboral, 
               d.Nombre_Departamento, c.Nombre_Cargo, de.Nombre_Destino,
               a.Fecha_Inicio_Asignacion, a.Fecha_Fin_Asignacion
        FROM tbl_funcionarios f
        LEFT JOIN tbl_asignaciones a ON f.ID_Funcionario = a.ID_Funcionario
        LEFT JOIN tbl_departamentos d ON a.ID_Departamento = d.ID_Departamento
        LEFT JOIN tbl_cargos c ON a.ID_Cargo = c.ID_Cargo
        LEFT JOIN tbl_destinos de ON a.ID_Destino = de.ID_Destino
        WHERE 1 = 1
    ";

    $params = [];

    if (!$reporteGeneral) {
        if (!empty($estadoLaboral)) {
            $sql .= " AND f.Estado_Laboral = ?";
            $params[] = $estadoLaboral;
        }

        if (!empty($idDepartamento)) {
            $sql .= " AND a.ID_Departamento = ?";
            $params[] = $idDepartamento;
        }

        if (!empty($idCargo)) {
            $sql .= " AND a.ID_Cargo = ?";
            $params[] = $idCargo;
        }

        if (!empty($idDestino)) {
            $sql .= " AND a.ID_Destino = ?";
            $params[] = $idDestino;
        }

        if (!empty($fechaInicio) && !empty($fechaFin)) {
            $sql .= " AND a.Fecha_Inicio_Asignacion BETWEEN ? AND ?";
            $params[] = $fechaInicio;
            $params[] = $fechaFin;
        }
    }

    $sql .= " ORDER BY f.Apellidos ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $resultados]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}





