<?php
session_start(); // OBLIGATORIO para usar $_SESSION
require '../includes/conexion.php';
$pdo = new PDO($dsn, $user, $pass, $options);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'finalizar_tarea') {
    $id_instruccion = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    // Verificamos que la conexión exista
    if (!$pdo) {
        $_SESSION['error'] = "Error interno: No se pudo establecer la conexión con la base de datos.";
        header("Location: ../administrador/instrucciones_diarias.php");
        exit;
    }

    if ($id_instruccion) {
        try {
            

            $stmt = $pdo->prepare("UPDATE tbl_instrucciones SET Estado = 'FINALIZADO' WHERE ID_Instruccion = ? AND Estado = 'EN-PROCESO'");
            $stmt->execute([$id_instruccion]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['exito'] = "Instrucción finalizada correctamente.";
            } else {
                $_SESSION['error'] = "No se pudo actualizar. Es posible que la tarea ya esté finalizada.";
            }

            header("Location: ../administrador/instrucciones_diarias.php");
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
            header("Location: ../administrador/instrucciones_diarias.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "ID de instrucción no válido.";
        header("Location: ../administrador/instrucciones_diarias.php");
        exit;
    }
}
