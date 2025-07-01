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
    $Nombre_Destino = trim($_POST['Nombre_Destino'] ?? '');
    $Tipo_Destino = $_POST['Tipo_Destino'] ?? null;
    $Direccion_Destino = trim($_POST['Direccion_Destino'] ?? '');
    $Provincia = $_POST['Provincia'] ?? '';
    $Distrito = $_POST['Distrito'] ?? '';
    $Ciudad = $_POST['Ciudad'] ?? '';
    $Fecha_Destino = $_POST['Fecha_Destino'] ?? '';
    $Fecha_Fin_Destino = $_POST['Fecha_Fin_Destino'] ?? null;
    $Telefono_Destino = trim($_POST['Telefono_Destino'] ?? '');
    $ID_Usuario_Creador = $_SESSION['ID_Usuario'];

    // Validación mínima de campos obligatorios
    if (
        !$Nombre_Destino || !$Tipo_Destino || !$Direccion_Destino || !$Provincia ||
        !$Distrito || !$Ciudad || !$Fecha_Destino || !$Telefono_Destino
    ) {
        $_SESSION['error'] = "Todos los campos obligatorios deben completarse.";
        header("Location: ../administrador/destinos.php");
        exit;
    }

    // Insertar en la base de datos
    $sql = "INSERT INTO tbl_destinos (
                Nombre_Destino, Tipo_Destino, Direccion_Destino,
                Ciudad, Telefono_Destino, ID_Usuario_Creador,
                Provincia, Distrito, Fecha_Destino, Fecha_Fin_Destino
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $Nombre_Destino,
        $Tipo_Destino,
        $Direccion_Destino,
        $Ciudad,
        $Telefono_Destino,
        $ID_Usuario_Creador,
        $Provincia,
        $Distrito,
        $Fecha_Destino,
        $Fecha_Fin_Destino ?: null
    ]);

    $_SESSION['exito'] = "Destino registrado correctamente.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al guardar el destino: " . $e->getMessage();
}

// Redirigir a la página de gestión
header("Location: ../administrador/destinos.php");
exit;
