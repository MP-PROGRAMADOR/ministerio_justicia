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
    header("Location: ../administrador/categorias.php");
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Recibir datos del formulario
    $Id_categoria = $_POST['Id_categoria'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $descripcion = $_POST['descripcion'] ?? null;
    $Usuario_id = $_SESSION['ID_Usuario'];

    // Validar campos
    if (!$Id_categoria || !$nombre) {
        $_SESSION['error'] = "Faltan campos obligatorios para actualizar la categoría.";
        header("Location: ../administrador/categorias.php");
        exit;
    }

    // Actualizar la categoría
    $sql = "UPDATE categorias
            SET nombre = ?, descripcion = ?
            WHERE Id_categoria = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $nombre,
        $descripcion ?: null,
        $Id_categoria
    ]);

    // Registrar en logs
    $sqlLog = "INSERT INTO logs (Usuario_id, Tabla_afectada, Accion, Registro_id, IP, Dispositivo)
               VALUES (?, ?, 'UPDATE', ?, ?, ?)";
    $stmtLog = $pdo->prepare($sqlLog);
    $stmtLog->execute([
        $Usuario_id,
        'categorias',
        $Id_categoria,
        getIP(),
        getDispositivo()
    ]);

    $_SESSION['exito'] = "Categoría actualizada correctamente.";

} catch (PDOException $e) {
    $_SESSION['error'] = "Error al actualizar la categoría: " . $e->getMessage();
}

// Redirigir a la página principal
header("Location: ../administrador/categorias.php");
exit;
