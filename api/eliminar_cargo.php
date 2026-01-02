<?php
// Iniciar sesión
session_start();

// 1. Verificar la sesión y el rol del usuario
if (!isset($_SESSION['ID_Usuario']) || $_SESSION['Rol_Usuario'] === 'Usuario') {
    header('Location: ../index.php');
    exit;
}

// 2. Importar conexión (Asegúrate de que este archivo define $dsn, $user, $pass)
require_once '../includes/conexion.php';

$redirect_page = '../administrador/cargo.php';

// CAMBIO: Ahora verificamos $_GET['id'] porque lo envías por la URL
if (isset($_GET['id'])) {

    $id_cargo = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    if ($id_cargo > 0) {
        try {
            $pdo = new PDO($dsn, $user, $pass, $options);

            // IMPORTANTE: Asegúrate que la tabla se llama 'cargos' y la columna 'Id_cargo'
            $sql = "DELETE FROM cargos WHERE Id_cargo = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id_cargo, PDO::PARAM_INT);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $_SESSION['exito'] = "Cargo eliminado correctamente.";
            } else {
                $_SESSION['error'] = "El cargo no existe o ya fue eliminado.";
            }
        } catch (PDOException $e) {
            // Manejo de Integridad Referencial (Error 23000: Clave Foránea)
            if ($e->getCode() == '23000') {
                $_SESSION['error'] = "No se puede eliminar: el cargo está asignado a empleados activos.";
            } else {
                $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
            }
        }
    } else {
        $_SESSION['error'] = "ID de cargo no válido.";
    }
} else {
    // Si llegas aquí, es porque no se recibió el parámetro 'id' en la URL
    $_SESSION['error'] = "No se recibió el ID del cargo.";
}

// Redirección final
header('Location: ' . $redirect_page);
exit;