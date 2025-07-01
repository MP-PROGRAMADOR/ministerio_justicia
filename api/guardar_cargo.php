<?php
session_start();
require_once '../includes/conexion.php'; // Ajusta la ruta según tu estructura

// Verificar sesión activa (opcional, si usas sistema de login)
if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Por favor, inicia sesión nuevamente.";
    header("Location: ../index.php");
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Recoger datos POST y limpiar
    $Nombre_Cargo = trim($_POST['Nombre_Cargo'] ?? '');
    $Descripcion_Cargo = trim($_POST['Descripcion_Cargo'] ?? '');
    $Nivel_Jerarquico = $_POST['Nivel_Jerarquico'] ?? null;
    $ID_Usuario_Creador = $_SESSION['ID_Usuario'];

    // Validaciones básicas
    if (empty($Nombre_Cargo) || empty($Nivel_Jerarquico) || !is_numeric($Nivel_Jerarquico)) {
        $_SESSION['error'] = "Por favor, complete todos los campos obligatorios correctamente.";
        header("Location: ../administrador/cargo.php"); // Ajusta la ruta
        exit;
    }

    // Insertar en la BD
    $sql = "INSERT INTO tbl_cargos (Nombre_Cargo, Descripcion_Cargo, Nivel_Jerarquico, ID_Usuario_Creador) 
            VALUES (:nombre, :descripcion, :nivel, :usuario)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre' => $Nombre_Cargo,
        ':descripcion' => $Descripcion_Cargo ?: null,
        ':nivel' => (int)$Nivel_Jerarquico,
        ':usuario' => $ID_Usuario_Creador,
    ]);

    $_SESSION['exito'] = "Cargo registrado correctamente.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al guardar el cargo: " . $e->getMessage();
}

// Redireccionar a la página de cargos (ajusta la ruta según tu proyecto)
header("Location: ../administrador/cargo.php");
exit;
