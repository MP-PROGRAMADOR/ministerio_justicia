<?php
session_start();
require_once '../includes/conexion.php';

$accion = $_GET['accion'] ?? '';

$redirectTo = "../administrador/cargo.php";

// Validar que se haya enviado información por POST si es crear o actualizar
if (($_SERVER['REQUEST_METHOD'] === 'POST') || $accion === 'eliminar') {

    try {
    $pdo = new PDO($dsn, $user, $pass, $options);

        if ($accion == 'crear') {
            // Validar campos obligatorios
            if (empty($_POST['nombre']) || empty($_POST['nivel'])) {
                throw new Exception("El nombre y el nivel son obligatorios.");
            }

            // AJUSTE: Basado en tu CREATE TABLE cargos (Id_cargo, Nombre, Nivel_jerarquico)
            $sql = "INSERT INTO cargos (Nombre, Nivel_jerarquico) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                trim($_POST['nombre']), 
                trim($_POST['nivel'])
            ]);
            
            $_SESSION['exito'] = "Cargo registrado con éxito.";

        } elseif ($accion == 'actualizar') {
            if (empty($_POST['id']) || empty($_POST['nombre'])) {
                throw new Exception("Faltan datos para actualizar el cargo.");
            }

            $sql = "UPDATE cargos SET Nombre = ?, Nivel_jerarquico = ? WHERE Id_cargo = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                trim($_POST['nombre']), 
                trim($_POST['nivel']), 
                $_POST['id']
            ]);

            $_SESSION['exito'] = "Cargo actualizado correctamente.";

        } elseif ($accion == 'eliminar') {
            if (empty($_GET['id'])) {
                throw new Exception("ID no válido para eliminar.");
            }

            $sql = "DELETE FROM cargos WHERE Id_cargo = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_GET['id']]);

            $_SESSION['exito'] = "Cargo eliminado correctamente.";
        }

    } catch (PDOException $e) {
        // Error específico de Base de Datos
        $_SESSION['error'] = "Error en la base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        // Error general (validaciones)
        $_SESSION['error'] = $e->getMessage();
    }
}

header("Location: $redirectTo");
exit();