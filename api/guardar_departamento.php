<?php
session_start();
include_once '../includes/conexion.php';

// Validar sesión de usuario
if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Por favor, inicie sesión de nuevo.";
    header("Location: ../index.php");
    exit;
}

$ID_Usuario_Creador = $_SESSION['ID_Usuario'];

// Obtener datos POST con validación básica
$nombre = $_POST['Nombre_Departamento'] ?? '';
$ubicacion = $_POST['Ubicacion'] ?? '';
$telefono = $_POST['Telefono_Departamento'] ?? '';
$ciudad = $_POST['Ciudad'] ?? '';
$distrito = $_POST['Distrito'] ?? '';
$provincia = $_POST['Provincia'] ?? '';

// Validar campos obligatorios
if (empty($nombre) || empty($ciudad) || empty($distrito) || empty($provincia)) {
    $_SESSION['error'] = "Por favor, complete todos los campos obligatorios.";
    header("Location: ../administrador/departamentos.php");
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $sql = "INSERT INTO tbl_departamentos 
        (Nombre_Departamento, Ubicacion, Telefono_Departamento, ID_Usuario_Creador, Ciudad, Distrito, Provincia) 
        VALUES 
        (:nombre, :ubicacion, :telefono, :usuario, :ciudad, :distrito, :provincia)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre' => $nombre,
        ':ubicacion' => $ubicacion,
        ':telefono' => $telefono,
        ':usuario' => $ID_Usuario_Creador,
        ':ciudad' => $ciudad,
        ':distrito' => $distrito,
        ':provincia' => $provincia,
    ]);

    $_SESSION['exito'] = "Departamento registrado correctamente.";
    header("Location: ../administrador/departamentos.php");
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = "Error al guardar el departamento: " . $e->getMessage();
    header("Location: ../administrador/departamentos.php");
    exit;
}
