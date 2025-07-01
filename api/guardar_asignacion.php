<?php
session_start();
require_once '../includes/conexion.php';

// Verificar si hay sesión activa
if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Inicia sesión nuevamente.";
    header("Location: ../index.php");
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Recibir datos del formulario
    $ID_Funcionario = $_POST['ID_Funcionario'] ?? null;
    $ID_Cargo = $_POST['ID_Cargo'] ?? null;
    $ID_Departamento = $_POST['ID_Departamento'] ?? null;
    $ID_Destino = $_POST['ID_Destino'] ?? null;
    $Fecha_Inicio_Asignacion = $_POST['Fecha_Inicio_Asignacion'] ?? null;
    $Fecha_Fin_Asignacion = $_POST['Fecha_Fin_Asignacion'] ?? null;
    $ID_Usuario_Creador = $_SESSION['ID_Usuario'];

    // Validar campos requeridos
    if (!$ID_Funcionario || !$ID_Cargo || !$ID_Departamento || !$ID_Destino || !$Fecha_Inicio_Asignacion) {
        $_SESSION['error'] = "Todos los campos obligatorios deben completarse.";
        header("Location: ../administrador/asignaciones.php");
        exit;
    }

    // Insertar en la base de datos
    $sql = "INSERT INTO tbl_asignaciones (
                ID_Funcionario, ID_Cargo, ID_Departamento, ID_Destino,
                Fecha_Inicio_Asignacion, Fecha_Fin_Asignacion, ID_Usuario_Creador
            ) VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $ID_Funcionario,
        $ID_Cargo,
        $ID_Departamento,
        $ID_Destino,
        $Fecha_Inicio_Asignacion,
        $Fecha_Fin_Asignacion ?: null,
        $ID_Usuario_Creador
    ]);

    $_SESSION['exito'] = "Asignación registrada correctamente.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al guardar la asignación: " . $e->getMessage();
}

// Redirigir a la página de asignaciones
header("Location: ../administrador/asignaciones.php");
exit;
