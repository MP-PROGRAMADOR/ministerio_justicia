<?php
header('Content-Type: application/json');

// Configuración de la base de datos (puedes mover esto a un archivo de configuración separado)
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

    // Consulta para el listado general de funcionarios
    $stmt = $pdo->query("
        SELECT
            f.ID_Funcionario,
            f.Nombres,
            f.Apellidos,
            f.DNI_Pasaporte,
            f.Fecha_Nacimiento,
            f.Genero,
            f.Nacionalidad,
            f.Estado_Laboral,
            f.Fecha_Ingreso
        FROM
            tbl_funcionarios f
        ORDER BY f.Apellidos, f.Nombres
    ");
    $data = $stmt->fetchAll();

} catch (\PDOException $e) {
    $status = 'error';
    $message = "Error en la base de datos: " . $e->getMessage();
}

echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
?>