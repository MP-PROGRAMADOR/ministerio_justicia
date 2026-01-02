<?php
session_start();
require_once '../includes/conexion.php';

// Funciones para obtener IP y dispositivo
function getIP() {
    return $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
}

function getDispositivo() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
}

// Verificar sesión activa
if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Inicia sesión nuevamente.";
    header("Location: ../index.php");
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Recibir datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $ubicacion = trim($_POST['ubicacion'] ?? '');
    $distrito = trim($_POST['distrito'] ?? '');
    $provincia = trim($_POST['provincia'] ?? '');
    $region = trim($_POST['region'] ?? '');
    $Usuario_id = $_SESSION['ID_Usuario'];

    // Validación básica
    if ($nombre === '') {
        $_SESSION['error'] = "El nombre de la dirección es obligatorio.";
        header("Location: ../administrador/direcciones.php");
        exit;
    }

    // Verificar si la dirección ya existe
    $checkSql = "SELECT COUNT(*) FROM direcciones WHERE nombre = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$nombre]);

    if ($checkStmt->fetchColumn() > 0) {
        $_SESSION['error'] = "Ya existe una dirección con ese nombre.";
        header("Location: ../administrador/direcciones.php");
        exit;
    }

    // Insertar dirección
    $sql = "INSERT INTO direcciones (nombre, ubicacion, distrito, provincia, region)
            VALUES (:nombre, :ubicacion, :distrito, :provincia, :region)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre' => $nombre,
        ':ubicacion' => $ubicacion ?: null,
        ':distrito' => $distrito ?: null,
        ':provincia' => $provincia ?: null,
        ':region' => $region ?: null
    ]);

    // Obtener el ID insertado
    $Id_direccion = $pdo->lastInsertId();

    // Registrar en logs
    $sqlLog = "INSERT INTO logs (Usuario_id, Tabla_afectada, Accion, Registro_id, IP, Dispositivo)
               VALUES (?, 'direcciones', 'INSERT', ?, ?, ?)";
    $stmtLog = $pdo->prepare($sqlLog);
    $stmtLog->execute([
        $Usuario_id,
        $Id_direccion,
        getIP(),
        getDispositivo()
    ]);

    $_SESSION['exito'] = "Dirección registrada correctamente.";

} catch (PDOException $e) {
    $_SESSION['error'] = "Error al guardar la dirección: " . $e->getMessage();
}

// Redirección
header("Location: ../administrador/direcciones.php");
exit;
