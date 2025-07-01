<?php
header('Content-Type: application/json');

// Configuración de la base de datos
$host = 'localhost';
$db   = 'Themis_MinisterioJusticia';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$data = [];
$status = 'success';
$message = '';

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $estado_laboral = $_GET['estado_laboral'] ?? ''; // Obtener el filtro del GET

    $sql = "
        SELECT
            f.ID_Funcionario,
            f.Nombres,
            f.Apellidos,
            f.DNI_Pasaporte,
            f.Estado_Laboral,
            f.Fecha_Ingreso,
            tc.Nombre_Cargo AS Cargo_Actual,
            td.Nombre_Departamento AS Departamento_Actual
        FROM
            tbl_funcionarios f
        LEFT JOIN
            tbl_asignaciones ap ON f.ID_Funcionario = ap.ID_Funcionario AND ap.Fecha_Fin_Asignacion IS NULL
        LEFT JOIN
            tbl_cargos tc ON ap.ID_Cargo = tc.ID_Cargo
        LEFT JOIN
            tbl_departamentos td ON ap.ID_Departamento = td.ID_Departamento
    ";
    $params = [];

    if (!empty($estado_laboral)) {
        $sql .= " WHERE f.Estado_Laboral = :estado_laboral";
        $params[':estado_laboral'] = $estado_laboral;
    }

    $sql .= " ORDER BY f.Apellidos, f.Nombres";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll();

} catch (\PDOException $e) {
    $status = 'error';
    $message = "Error en la base de datos: " . $e->getMessage();
}

echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
?>