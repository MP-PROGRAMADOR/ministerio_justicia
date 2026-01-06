<?php
session_start();
require_once '../includes/conexion.php';

// 1. Verificar sesión activa (Seguridad)
if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Inicia sesión nuevamente.";
    header("Location: ../index.php");
    exit;
}

// 2. Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recibir y limpiar datos del formulario
    $id_direccion = $_POST['id_direccion'] ?? null;
    $nombre = trim($_POST['nombre'] ?? '');
    $ubicacion = trim($_POST['ubicacion'] ?? '');
    $distrito = $_POST['distrito'] ?? '';
    $provincia = $_POST['provincia'] ?? '';
    $region = $_POST['region'] ?? '';

    // 3. Validaciones básicas
    if (!$id_direccion || empty($nombre) || empty($distrito) || empty($provincia) || empty($region)) {
        $_SESSION['error'] = "Por favor, complete todos los campos obligatorios.";
        header("Location: ../administrador/direcciones.php"); // Ajusta a tu nombre de archivo
        exit;
    }

    try {
        // Conexión PDO
        $pdo = new PDO($dsn, $user, $pass, $options);

        // 4. Preparar la sentencia SQL de actualización
        $sql = "UPDATE direcciones 
                SET nombre = ?, 
                    ubicacion = ?, 
                    distrito = ?, 
                    provincia = ?, 
                    region = ?
                WHERE Id_direccion = ?";

        $stmt = $pdo->prepare($sql);

        // 5. Ejecutar con los valores recibidos
        $resultado = $stmt->execute([
            $nombre,
            $ubicacion,
            $distrito,
            $provincia,
            $region,
            $id_direccion
        ]);

        if ($resultado) {
            $_SESSION['exito'] = "Dirección actualizada correctamente.";
        } else {
            $_SESSION['error'] = "No se realizaron cambios en el registro.";
        }
    } catch (PDOException $e) {
        // Manejo de errores de base de datos
        $_SESSION['error'] = "Error en la base de datos: " . $e->getMessage();
    }

    // Redirigir de vuelta a la lista
    header("Location: ../administrador/direcciones.php");
    exit;
} else {
    // Si intentan entrar al archivo por URL (GET)
    $_SESSION['error'] = "Acceso no autorizado.";
    header("Location: ../administrador/direcciones.php");
    exit;
}
