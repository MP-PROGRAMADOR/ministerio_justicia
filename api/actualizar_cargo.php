<?php
session_start();
require_once '../includes/conexion.php';

// Verificar sesión activa
if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Inicia sesión nuevamente.";
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ID_Cargo = $_POST['ID_Cargo'] ?? null;
    $Nombre_Cargo = trim($_POST['Nombre_Cargo'] ?? '');
    $Descripcion_Cargo = trim($_POST['Descripcion_Cargo'] ?? '');
    $Nivel_Jerarquico = $_POST['Nivel_Jerarquico'] ?? null;
    $ID_Usuario_Ultima_Modificacion = $_SESSION['ID_Usuario'];

    // Validaciones básicas
    if (!$ID_Cargo || !$Nombre_Cargo || !$Nivel_Jerarquico) {
        $_SESSION['error'] = "Por favor, complete todos los campos obligatorios.";
        header("Location: ../administrador/cargo.php");
        exit;
    }

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        $sql = "UPDATE tbl_cargos 
                SET Nombre_Cargo = ?, 
                    Descripcion_Cargo = ?, 
                    Nivel_Jerarquico = ?, 
                    ID_Usuario_Ultima_Modificacion = ?,
                    Fecha_Ultima_Modificacion = NOW()
                WHERE ID_Cargo = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $Nombre_Cargo,
            $Descripcion_Cargo,
            $Nivel_Jerarquico,
            $ID_Usuario_Ultima_Modificacion,
            $ID_Cargo
        ]);

        $_SESSION['exito'] = "Cargo actualizado correctamente.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar el cargo: " . $e->getMessage();
    }

    header("Location: ../administrador/cargo.php");
    exit;
} else {
    $_SESSION['error'] = "Acceso inválido.";
    header("Location: ../administrador/cargo.php");
    exit;
}
?>
