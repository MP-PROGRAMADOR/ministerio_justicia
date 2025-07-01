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

    // Obtener datos del formulario
    $ID_Destino = $_POST['ID_Destino'] ?? null;
    $Nombre_Destino = trim($_POST['Nombre_Destino'] ?? '');
    $Tipo_Destino = $_POST['Tipo_Destino'] ?? '';
    $Direccion_Destino = trim($_POST['Direccion_Destino'] ?? '');
    $Ciudad = trim($_POST['Ciudad'] ?? '');
    $Distrito = trim($_POST['Distrito'] ?? '');
    $Provincia = trim($_POST['Provincia'] ?? '');
    $Fecha_Destino = $_POST['Fecha_Destino'] ?? null;
    $Fecha_Fin_Destino = $_POST['Fecha_Fin_Destino'] ?? null;
    $Telefono_Destino = trim($_POST['Telefono_Destino'] ?? '');
    $ID_Usuario_Ultima_Modificacion = $_SESSION['ID_Usuario'];

    // Validaciones básicas
    if (
        !$ID_Destino ||
        !$Nombre_Destino ||
        !$Tipo_Destino ||
        !$Direccion_Destino ||
        !$Ciudad ||
        !$Distrito ||
        !$Provincia ||
        !$Fecha_Destino ||
        !$Telefono_Destino
    ) {
        $_SESSION['error'] = "Todos los campos obligatorios deben completarse (excepto Fecha Fin).";
        header("Location: ../administrador/destinos.php");
        exit;
    }

    // Fecha fin puede ser NULL
    $Fecha_Fin_Destino = $Fecha_Fin_Destino ?: null;

    // Consulta SQL para actualizar el registro
    $sql = "UPDATE tbl_destinos SET
                Nombre_Destino = ?,
                Tipo_Destino = ?,
                Direccion_Destino = ?,
                Ciudad = ?,
                Distrito = ?,
                Provincia = ?,
                Fecha_Destino = ?,
                Fecha_Fin_Destino = ?,
                Telefono_Destino = ?,
                ID_Usuario_Ultima_Modificacion = ?,
                Fecha_Ultima_Modificacion = NOW()
            WHERE ID_Destino = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $Nombre_Destino,
        $Tipo_Destino,
        $Direccion_Destino,
        $Ciudad,
        $Distrito,
        $Provincia,
        $Fecha_Destino,
        $Fecha_Fin_Destino,
        $Telefono_Destino,
        $ID_Usuario_Ultima_Modificacion,
        $ID_Destino
    ]);

    $_SESSION['exito'] = "Destino actualizado correctamente.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al actualizar destino: " . $e->getMessage();
}

// Redireccionar a la página de destinos
header("Location: ../administrador/destinos.php");
exit;
