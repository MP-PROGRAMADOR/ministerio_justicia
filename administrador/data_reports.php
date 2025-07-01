<?php
// Configuración de la base de datos (copia de tu index.php)
$host = 'localhost';
$db   = 'themis_ministeriojusticia';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}

$reportType = $_GET['report'] ?? '';
$data = [];
$status = 'success';
$message = 'Data fetched successfully.';

header('Content-Type: application/json');

switch ($reportType) {
    case 'generalFuncionarios':
        // Listado General de Funcionarios
        $stmt = $pdo->query("SELECT Codigo_Funcionario, Nombres, Apellidos, DNI_Pasaporte, Fecha_Ingreso, Estado_Laboral FROM tbl_funcionarios ORDER BY Nombres, Apellidos");
        $data = $stmt->fetchAll();
        break;

    case 'funcionariosPorEstadoLaboral':
        // Funcionarios por Estado Laboral
        $stmt = $pdo->query("SELECT Estado_Laboral, COUNT(*) as total FROM tbl_funcionarios GROUP BY Estado_Laboral");
        $data = $stmt->fetchAll();
        break;

    case 'funcionariosPorAntiguedad':
        // Funcionarios por Antigüedad (Ej. categorizados por años de servicio)
        $stmt = $pdo->query("
            SELECT
                Nombres,
                Apellidos,
                Fecha_Ingreso,
                TIMESTAMPDIFF(YEAR, Fecha_Ingreso, CURDATE()) as Anios_Servicio
            FROM tbl_funcionarios
            ORDER BY Anios_Servicio DESC, Nombres, Apellidos
        ");
        $data = $stmt->fetchAll();
        break;

    case 'historialAsignacionesPorFuncionario':
        // Historial de Asignaciones por Funcionario
        $funcionarioId = $_GET['funcionarioId'] ?? null;
        if ($funcionarioId) {
            $stmt = $pdo->prepare("
                SELECT
                    f.Nombres,
                    f.Apellidos,
                    c.Nombre_Cargo,
                    dpto.Nombre_Departamento,
                    dest.Nombre_Destino,
                    a.Fecha_Inicio_Asignacion,
                    a.Fecha_Fin_Asignacion
                FROM tbl_asignaciones a
                JOIN tbl_funcionarios f ON a.ID_Funcionario = f.ID_Funcionario
                JOIN tbl_cargos c ON a.ID_Cargo = c.ID_Cargo
                JOIN tbl_departamentos dpto ON a.ID_Departamento = dpto.ID_Departamento
                JOIN tbl_destinos dest ON a.ID_Destino = dest.ID_Destino
                WHERE a.ID_Funcionario = :funcionarioId
                ORDER BY a.Fecha_Inicio_Asignacion DESC
            ");
            $stmt->execute([':funcionarioId' => $funcionarioId]);
            $data = $stmt->fetchAll();
        } else {
            // Si no se especifica funcionarioId, obtener una lista básica de funcionarios para selección
            $stmt = $pdo->query("SELECT ID_Funcionario, Nombres, Apellidos FROM tbl_funcionarios ORDER BY Nombres, Apellidos");
            $data = $stmt->fetchAll();
            $status = 'info';
            $message = 'Select a funcionario to view assignments history.';
        }
        break;

    case 'funcionariosPorCargoActual':
        // Funcionarios por Cargo Actual
        $stmt = $pdo->query("
            SELECT
                c.Nombre_Cargo,
                f.Nombres,
                f.Apellidos,
                f.DNI_Pasaporte,
                dpto.Nombre_Departamento,
                dest.Nombre_Destino
            FROM tbl_asignaciones a
            JOIN tbl_funcionarios f ON a.ID_Funcionario = f.ID_Funcionario
            JOIN tbl_cargos c ON a.ID_Cargo = c.ID_Cargo
            JOIN tbl_departamentos dpto ON a.ID_Departamento = dpto.ID_Departamento
            JOIN tbl_destinos dest ON a.ID_Destino = dest.ID_Destino
            WHERE a.Fecha_Fin_Asignacion IS NULL OR a.Fecha_Fin_Asignacion >= CURDATE()
            ORDER BY c.Nombre_Cargo, f.Nombres, f.Apellidos
        ");
        $data = $stmt->fetchAll();
        break;

    case 'funcionariosPorDepartamento':
        // Funcionarios por Departamento
        $stmt = $pdo->query("
            SELECT
                dpto.Nombre_Departamento,
                f.Nombres,
                f.Apellidos,
                f.DNI_Pasaporte,
                c.Nombre_Cargo
            FROM tbl_asignaciones a
            JOIN tbl_funcionarios f ON a.ID_Funcionario = f.ID_Funcionario
            JOIN tbl_departamentos dpto ON a.ID_Departamento = dpto.ID_Departamento
            JOIN tbl_cargos c ON a.ID_Cargo = c.ID_Cargo
            WHERE a.Fecha_Fin_Asignacion IS NULL OR a.Fecha_Fin_Asignacion >= CURDATE()
            ORDER BY dpto.Nombre_Departamento, f.Nombres, f.Apellidos
        ");
        $data = $stmt->fetchAll();
        break;

    case 'funcionariosPorDestino':
        // Funcionarios por Destino (Juzgado/Tribunal/Oficina)
        $stmt = $pdo->query("
            SELECT
                dest.Nombre_Destino,
                dest.Tipo_Destino,
                f.Nombres,
                f.Apellidos,
                f.DNI_Pasaporte,
                c.Nombre_Cargo
            FROM tbl_asignaciones a
            JOIN tbl_funcionarios f ON a.ID_Funcionario = f.ID_Funcionario
            JOIN tbl_destinos dest ON a.ID_Destino = dest.ID_Destino
            JOIN tbl_cargos c ON a.ID_Cargo = c.ID_Cargo
            WHERE a.Fecha_Fin_Asignacion IS NULL OR a.Fecha_Fin_Asignacion >= CURDATE()
            ORDER BY dest.Nombre_Destino, f.Nombres, f.Apellidos
        ");
        $data = $stmt->fetchAll();
        break;

    case 'permisosSolicitadosPorFuncionario':
        // Permisos Solicitados por Funcionario
        $funcionarioId = $_GET['funcionarioId'] ?? null;
        if ($funcionarioId) {
            $stmt = $pdo->prepare("
                SELECT
                    p.Tipo_Permiso,
                    p.Fecha_Solicitud,
                    p.Fecha_Inicio_Permiso,
                    p.Fecha_Fin_Permiso,
                    p.Estado_Permiso,
                    p.Motivo
                FROM tbl_permisos p
                WHERE p.ID_Funcionario = :funcionarioId
                ORDER BY p.Fecha_Solicitud DESC
            ");
            $stmt->execute([':funcionarioId' => $funcionarioId]);
            $data = $stmt->fetchAll();
        } else {
            // Si no se especifica funcionarioId, obtener una lista básica de funcionarios para selección
            $stmt = $pdo->query("SELECT ID_Funcionario, Nombres, Apellidos FROM tbl_funcionarios ORDER BY Nombres, Apellidos");
            $data = $stmt->fetchAll();
            $status = 'info';
            $message = 'Select a funcionario to view permit history.';
        }
        break;

    case 'estadoDePermisos':
        // Estado de Permisos (Pendientes, Aprobados, Denegados)
        $estado = $_GET['estado'] ?? null;
        $sql = "
            SELECT
                f.Nombres,
                f.Apellidos,
                p.Tipo_Permiso,
                p.Fecha_Inicio_Permiso,
                p.Fecha_Fin_Permiso,
                p.Estado_Permiso,
                p.Motivo
            FROM tbl_permisos p
            JOIN tbl_funcionarios f ON p.ID_Funcionario = f.ID_Funcionario
        ";
        $params = [];
        if ($estado) {
            $sql .= " WHERE p.Estado_Permiso = :estado";
            $params[':estado'] = $estado;
        }
        $sql .= " ORDER BY p.Fecha_Solicitud DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        break;

    case 'resumenAusenciasPorTipoPermiso':
        // Resumen de Ausencias por Tipo de Permiso
        $stmt = $pdo->query("SELECT Tipo_Permiso, COUNT(*) as total FROM tbl_permisos GROUP BY Tipo_Permiso");
        $data = $stmt->fetchAll();
        break;

    case 'permisosProximosAVencer':
        // Permisos Próximos a Vencer o Finalizar (Ej. en los próximos 30 días)
        $stmt = $pdo->query("
            SELECT
                f.Nombres,
                f.Apellidos,
                p.Tipo_Permiso,
                p.Fecha_Fin_Permiso,
                p.Estado_Permiso
            FROM tbl_permisos p
            JOIN tbl_funcionarios f ON p.ID_Funcionario = f.ID_Funcionario
            WHERE p.Fecha_Fin_Permiso BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
            AND p.Estado_Permiso = 'Aprobado'
            ORDER BY p.Fecha_Fin_Permiso ASC
        ");
        $data = $stmt->fetchAll();
        break;

    default:
        $status = 'error';
        $message = 'Invalid report type.';
        break;
}

echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
?>