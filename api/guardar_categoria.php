<?php
session_start();
require_once '../includes/conexion.php';

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
    $descripcion = trim($_POST['descripcion'] ?? '');

    // Validación básica
    if ($nombre === '') {
        $_SESSION['error'] = "El nombre de la categoría es obligatorio.";
        header("Location: ../administrador/categorias.php");
        exit;
    }

    // Verificar si la categoría ya existe
    $checkSql = "SELECT COUNT(*) FROM categorias WHERE nombre = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$nombre]);

    if ($checkStmt->fetchColumn() > 0) {
        $_SESSION['error'] = "Ya existe una categoría con ese nombre.";
        header("Location: ../administrador/categorias.php");
        exit;
    }

    // Insertar categoría
    $sql = "INSERT INTO categorias (nombre, descripcion)
            VALUES (:nombre, :descripcion)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre' => $nombre,
        ':descripcion' => $descripcion ?: null
    ]);

    $_SESSION['exito'] = "Categoría registrada correctamente.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al guardar la categoría: " . $e->getMessage();
}

// Redirección
header("Location: ../administrador/categorias.php");
exit;
