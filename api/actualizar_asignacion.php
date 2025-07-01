<?php
session_start();
require_once '../includes/conexion.php';

// Verificar si hay sesión activa
if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Inicia sesión nuevamente.";
    header("Location: ../administrador/asignaciones.php");
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Recibir datos del formulario
    $ID_Asignacion = $_POST['ID_Asignacion'] ?? null;
    $ID_Funcionario = $_POST['ID_Funcionario'] ?? null;
    $ID_Cargo = $_POST['ID_Cargo'] ?? null;
    $ID_Departamento = $_POST['ID_Departamento'] ?? null;
    $ID_Destino = $_POST['ID_Destino'] ?? null;
    $Fecha_Inicio = $_POST['Fecha_Inicio_Asignacion'] ?? null;
    $Fecha_Fin = $_POST['Fecha_Fin_Asignacion'] ?? null;
    $ID_Usuario = $_SESSION['ID_Usuario'];

    // Validar datos mínimos
    if (!$ID_Asignacion || !$ID_Funcionario || !$ID_Cargo || !$ID_Departamento || !$ID_Destino || !$Fecha_Inicio) {
        $_SESSION['error'] = "Faltan campos obligatorios para actualizar la asignación.";
        header("Location: ../administrador/asignaciones.php");
        exit;
    }

    // Actualizar la asignación
    $sql = "UPDATE tbl_asignaciones
            SET ID_Funcionario = ?, 
                ID_Cargo = ?, 
                ID_Departamento = ?, 
                ID_Destino = ?, 
                Fecha_Inicio_Asignacion = ?, 
                Fecha_Fin_Asignacion = ?, 
                ID_Usuario_Ultima_Modificacion = ?, 
                Fecha_Ultima_Modificacion = NOW()
            WHERE ID_Asignacion = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $ID_Funcionario,
        $ID_Cargo,
        $ID_Departamento,
        $ID_Destino,
        $Fecha_Inicio,
        $Fecha_Fin ?: null,
        $ID_Usuario,
        $ID_Asignacion
    ]);

    $_SESSION['exito'] = "Asignación actualizada correctamente.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al actualizar la asignación: " . $e->getMessage();
}

// Redirigir a la página principal
header("Location: ../administrador/asignaciones.php");
exit;
