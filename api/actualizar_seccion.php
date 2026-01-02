<?php
session_start();
require_once '../includes/conexion.php';

// Funciones para IP y dispositivo
function getIP() {
    return $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
}

function getDispositivo() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
}

// Verificar sesión activa
if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Inicia sesión nuevamente.";
    header("Location: ../administrador/secciones.php");
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Recibir datos del formulario
    $Id_seccion = $_POST['Id_seccion'] ?? null;
    $Id_direccion = $_POST['Id_direccion'] ?? null;
    $nombre = trim($_POST['nombre'] ?? '');
    $Usuario_id = $_SESSION['ID_Usuario'];

    // Validación básica
    if (!$Id_seccion || !$Id_direccion || $nombre === '') {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: ../administrador/secciones.php");
        exit;
    }

    // Verificar si ya existe otra sección con el mismo nombre para la misma dirección
    $checkSql = "SELECT COUNT(*) FROM secciones WHERE Id_direccion = ? AND nombre = ? AND Id_seccion != ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$Id_direccion, $nombre, $Id_seccion]);

    if ($checkStmt->fetchColumn() > 0) {
        $_SESSION['error'] = "Ya existe otra sección con ese nombre para esta dirección.";
        header("Location: ../administrador/secciones.php");
        exit;
    }

    // Actualizar sección
    $sql = "UPDATE secciones
            SET Id_direccion = ?, nombre = ?
            WHERE Id_seccion = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$Id_direccion, $nombre, $Id_seccion]);

    // Registrar acción en logs
    $sqlLog = "INSERT INTO logs (Usuario_id, Tabla_afectada, Accion, Registro_id, IP, Dispositivo)
               VALUES (?, 'secciones', 'UPDATE', ?, ?, ?)";
    $stmtLog = $pdo->prepare($sqlLog);
    $stmtLog->execute([
        $Usuario_id,
        $Id_seccion,
        getIP(),
        getDispositivo()
    ]);

    $_SESSION['exito'] = "Sección actualizada correctamente.";

} catch (PDOException $e) {
    $_SESSION['error'] = "Error al actualizar la sección: " . $e->getMessage();
}

// Redirigir a la página de secciones
header("Location: ../administrador/secciones.php");
exit;
