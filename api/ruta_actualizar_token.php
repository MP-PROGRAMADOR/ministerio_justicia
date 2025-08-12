<?php
session_start();
require_once '../includes/conexion.php';

if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada.";
    header("Location: ../index.php");
    exit;
}

if (!isset($_POST['ID_Permiso'])) {
    $_SESSION['error'] = "Faltan datos.";
    header("Location: ../administrador/permisos.php");
    exit;
}

$idPermiso = $_POST['ID_Permiso'];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $sql = "UPDATE tbl_permisos SET token = 1 WHERE ID_Permiso = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $idPermiso]);

    $_SESSION['exito'] = "Token actualizado correctamente.";
    header("Location: ../administrador/permisos.php");
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = "Error al actualizar token: " . $e->getMessage();
    header("Location: ../administrador/permisos.php");
    exit;
}
?>
