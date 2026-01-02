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
    $Id_direccion = trim($_POST['Id_direccion'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $Usuario_id = $_SESSION['ID_Usuario'];

    // Validación básica
    if ($Id_direccion === '' || $nombre === '') {
        $_SESSION['error'] = "La dirección y el nombre de la sección son obligatorios.";
        header("Location: ../administrador/secciones.php");
        exit;
    }

    // Verificar si la sección ya existe para esa dirección
    $checkSql = "SELECT COUNT(*) FROM secciones WHERE nombre = ? AND Id_direccion = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$nombre, $Id_direccion]);

    if ($checkStmt->fetchColumn() > 0) {
        $_SESSION['error'] = "Ya existe una sección con ese nombre en esta dirección.";
        header("Location: ../administrador/secciones.php");
        exit;
    }

    // Insertar sección
    $sql = "INSERT INTO secciones (Id_direccion, nombre)
            VALUES (:Id_direccion, :nombre)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':Id_direccion' => $Id_direccion,
        ':nombre' => $nombre
    ]);

    // Obtener el ID insertado
    $Id_seccion = $pdo->lastInsertId();

    // Registrar en logs
    $sqlLog = "INSERT INTO logs (Usuario_id, Tabla_afectada, Accion, Registro_id, IP, Dispositivo)
               VALUES (?, 'secciones', 'INSERT', ?, ?, ?)";
    $stmtLog = $pdo->prepare($sqlLog);
    $stmtLog->execute([
        $Usuario_id,
        $Id_seccion,
        getIP(),
        getDispositivo()
    ]);

    $_SESSION['exito'] = "Sección registrada correctamente.";

} catch (PDOException $e) {
    $_SESSION['error'] = "Error al guardar la sección: " . $e->getMessage();
}

// Redirección
header("Location: ../administrador/secciones.php");
exit;
