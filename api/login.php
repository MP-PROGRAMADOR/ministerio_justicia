<?php
session_start();
include_once("../includes/conexion.php"); // Asegúrate que esta ruta es correcta

// Inicializar conexión (por si no está en conexion.php)
$pdo = new PDO($dsn, $user, $pass, $options);

// Verificamos el envío del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"] ?? '');
    $contrasena = $_POST["password"] ?? '';
    $error = '';

    // Validación de campos vacíos
    if (empty($usuario) || empty($contrasena)) {
        $error = "Por favor, complete todos los campos.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM tbl_usuarios WHERE Nombre_Usuario = :usuario AND Activo = 1 LIMIT 1");
            $stmt->execute(['usuario' => $usuario]);
            $usuarioData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuarioData && password_verify($contrasena, $usuarioData['Contrasena_Hash'])) {
                // Inicio de sesión correcto
                $_SESSION['ID_Usuario'] = $usuarioData['ID_Usuario'];
                $_SESSION['Nombre_Usuario'] = $usuarioData['Nombre_Usuario'];
                $_SESSION['Rol_Usuario'] = $usuarioData['Rol_Usuario'];

                // Registrar último acceso
                $pdo->prepare("UPDATE tbl_usuarios SET Ultimo_Acceso = NOW() WHERE ID_Usuario = :id")
                    ->execute(['id' => $usuarioData['ID_Usuario']]);

                header("Location: ../administrador/");
                exit();
            } else {
                session_start();
                $_SESSION['error'] = "Nombre de usuario o contraseña incorrectos.";
                header("Location: ../index.php");
                exit();
            }
        } catch (PDOException $e) {
            $error = "Error de conexión a la base de datos.";
        }
    }

    // Redirige con mensaje de error si algo falló
    if (!empty($error)) {
        $_SESSION['login_error'] = $error;
        header("Location: ../index.php");
        exit();
    }
}
