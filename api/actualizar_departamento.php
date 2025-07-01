<?php
session_start();
require_once '../includes/conexion.php';

// Validar sesión
if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Vuelve a iniciar sesión.";
    header("Location: ../index.php");
    exit;
}

$usuario_id = $_SESSION['ID_Usuario'];

// Validar datos POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['ID_Departamento'] ?? null;
    $nombre = trim($_POST['Nombre_Departamento'] ?? '');
    $ubicacion = trim($_POST['Ubicacion'] ?? '');
    $telefono = trim($_POST['Telefono_Departamento'] ?? '');
    $provincia = trim($_POST['Provincia'] ?? '');
    $distrito = trim($_POST['Distrito'] ?? '');
    $ciudad = trim($_POST['Ciudad'] ?? '');

    // Validación básica
    if (!$id || !$nombre || !$provincia || !$distrito || !$ciudad) {
        $_SESSION['error'] = "Todos los campos obligatorios deben completarse.";
        header("Location: ../administrador/departamentos.php");
        exit;
    }

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        $sql = "UPDATE tbl_departamentos 
                SET Nombre_Departamento = :nombre,
                    Ubicacion = :ubicacion,
                    Telefono_Departamento = :telefono,
                    Provincia = :provincia,
                    Distrito = :distrito,
                    Ciudad = :ciudad,
                    ID_Usuario_Ultima_Modificacion = :usuario,
                    Fecha_Ultima_Modificacion = CURRENT_TIMESTAMP
                WHERE ID_Departamento = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':ubicacion' => $ubicacion,
            ':telefono' => $telefono,
            ':provincia' => $provincia,
            ':distrito' => $distrito,
            ':ciudad' => $ciudad,
            ':usuario' => $usuario_id,
            ':id' => $id
        ]);

        $_SESSION['exito'] = "Departamento actualizado correctamente.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar: " . $e->getMessage();
    }

    header("Location: ../administrador/departamentos.php");
    exit;
} else {
    $_SESSION['error'] = "Acceso inválido al formulario.";
    header("Location: ../administrador/departamentos.php");
    exit;
}
