<?php
header('Content-Type: application/json');



try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit();
}

$data = [];

// 1. Total Funcionarios
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM tbl_funcionarios");
    $data['totalFuncionarios'] = $stmt->fetch()['total'];
} catch (PDOException $e) {
    $data['totalFuncionarios'] = 0;
    error_log("Error fetching totalFuncionarios: " . $e->getMessage());
}

// 2. Funcionarios Activos
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM tbl_funcionarios WHERE Estado_Laboral = 'Activo'");
    $data['funcionariosActivos'] = $stmt->fetch()['total'];
} catch (PDOException $e) {
    $data['funcionariosActivos'] = 0;
    error_log("Error fetching funcionariosActivos: " . $e->getMessage());
}

// 3. Permisos Este Mes (ejemplo: permisos con Fecha_Solicitud en el mes actual)
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM tbl_permisos WHERE MONTH(Fecha_Solicitud) = MONTH(CURDATE()) AND YEAR(Fecha_Solicitud) = YEAR(CURDATE())");
    $data['permisosEsteMes'] = $stmt->fetch()['total'];
} catch (PDOException $e) {
    $data['permisosEsteMes'] = 0;
    error_log("Error fetching permisosEsteMes: " . $e->getMessage());
}

// 4. Permisos Pendientes
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM tbl_permisos WHERE Estado_Permiso = 'Pendiente'");
    $data['permisosPendientes'] = $stmt->fetch()['total'];
} catch (PDOException $e) {
    $data['permisosPendientes'] = 0;
    error_log("Error fetching permisosPendientes: " . $e->getMessage());
}

// 5. Destinos Activos (suponiendo que un destino es 'activo' si tiene una Fecha_Fin_Destino nula o en el futuro)
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM tbl_destinos WHERE Fecha_Fin_Destino IS NULL OR Fecha_Fin_Destino >= CURDATE()");
    $data['destinosActivos'] = $stmt->fetch()['total'];
} catch (PDOException $e) {
    $data['destinosActivos'] = 0;
    error_log("Error fetching destinosActivos: " . $e->getMessage());
}

// 6. Conteo de Juzgados (de tbl_destinos)
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM tbl_destinos WHERE Tipo_Destino = 'Juzgado'");
    $data['totalJuzgados'] = $stmt->fetch()['total'];
} catch (PDOException $e) {
    $data['totalJuzgados'] = 0;
    error_log("Error fetching totalJuzgados: " . $e->getMessage());
}

// 7. Distribución de Funcionarios por Estado Laboral (para el Doughnut Chart)
try {
    $stmt = $pdo->query("SELECT Estado_Laboral, COUNT(*) AS count FROM tbl_funcionarios GROUP BY Estado_Laboral");
    $estadoLaboralData = $stmt->fetchAll();
    $data['funcionarioDistribution'] = [
        'labels' => array_column($estadoLaboralData, 'Estado_Laboral'),
        'data' => array_column($estadoLaboralData, 'count')
    ];
} catch (PDOException $e) {
    $data['funcionarioDistribution'] = ['labels' => [], 'data' => []];
    error_log("Error fetching funcionarioDistribution: " . $e->getMessage());
}

// 8. Funcionarios por Género (para un Bar Chart o Pie Chart)
try {
    $stmt = $pdo->query("SELECT Genero, COUNT(*) AS count FROM tbl_funcionarios GROUP BY Genero");
    $generoData = $stmt->fetchAll();
    $data['generoDistribution'] = [
        'labels' => array_column($generoData, 'Genero'),
        'data' => array_column($generoData, 'count')
    ];
} catch (PDOException $e) {
    $data['generoDistribution'] = ['labels' => [], 'data' => []];
    error_log("Error fetching generoDistribution: " . $e->getMessage());
}

// 9. Permisos por Tipo (para un Bar Chart)
try {
    $stmt = $pdo->query("SELECT Tipo_Permiso, COUNT(*) AS count FROM tbl_permisos GROUP BY Tipo_Permiso ORDER BY count DESC");
    $permisosTipoData = $stmt->fetchAll();
    $data['permisosTipoDistribution'] = [
        'labels' => array_column($permisosTipoData, 'Tipo_Permiso'),
        'data' => array_column($permisosTipoData, 'count')
    ];
} catch (PDOException $e) {
    $data['permisosTipoDistribution'] = ['labels' => [], 'data' => []];
    error_log("Error fetching permisosTipoDistribution: " . $e->getMessage());
}

// 10. Top 5 Departamentos con más funcionarios (para un Bar Chart o Table)
try {
    $stmt = $pdo->query("
        SELECT d.Nombre_Departamento, COUNT(a.ID_Funcionario) AS num_funcionarios
        FROM tbl_asignaciones a
        JOIN tbl_departamentos d ON a.ID_Departamento = d.ID_Departamento
        GROUP BY d.Nombre_Departamento
        ORDER BY num_funcionarios DESC
        LIMIT 5
    ");
    $topDepartamentos = $stmt->fetchAll();
    $data['topDepartamentos'] = [
        'labels' => array_column($topDepartamentos, 'Nombre_Departamento'),
        'data' => array_column($topDepartamentos, 'num_funcionarios')
    ];
} catch (PDOException $e) {
    $data['topDepartamentos'] = ['labels' => [], 'data' => []];
    error_log("Error fetching topDepartamentos: " . $e->getMessage());
}

// 11. Funcionarios por Nivel Educativo (para un Pie Chart o Bar Chart)
try {
    $stmt = $pdo->query("SELECT Nivel_Educativo, COUNT(*) AS count FROM tbl_formacion_academica GROUP BY Nivel_Educativo");
    $nivelEducativoData = $stmt->fetchAll();
    $data['nivelEducativoDistribution'] = [
        'labels' => array_column($nivelEducativoData, 'Nivel_Educativo'),
        'data' => array_column($nivelEducativoData, 'count')
    ];
} catch (PDOException $e) {
    $data['nivelEducativoDistribution'] = ['labels' => [], 'data' => []];
    error_log("Error fetching nivelEducativoDistribution: " . $e->getMessage());
}


echo json_encode(['status' => 'success', 'data' => $data]);
?>