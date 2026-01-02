<?php
// 1. Iniciar la sesión para usar $_SESSION (mensajes flash)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


require_once '../includes/conexion.php'; 

// La URL a la que redirigiremos (ajusta si la página principal de asignaciones cambia)
$redireccion_url = '../administrador/asignaciones.php';

// 3. Verificar el método de solicitud (debe ser POST, enviado por el formulario JS)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método de solicitud no permitido.';
    header('Location: ' . $redireccion_url);
    exit;
}

// 4. Obtener y sanear el ID enviado por el formulario
$asignacionId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$asignacionId) {
    $_SESSION['error'] = 'ID de asignación no válido. No se pudo procesar la solicitud.';
    header('Location: ' . $redireccion_url);
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);// 5. Preparar y ejecutar la consulta DELETE usando $pdo (ya definido por el require_once)
    $sql = "DELETE FROM tbl_asignaciones WHERE ID_Asignacion = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $asignacionId, PDO::PARAM_INT);
    $stmt->execute();

    // 6. Verificar si se eliminó alguna fila
    if ($stmt->rowCount() > 0) {
        // ÉXITO: Establecer mensaje de éxito en la sesión
        $_SESSION['exito'] = "La asignación (ID: " . htmlspecialchars($asignacionId) . ") ha sido eliminada con éxito.";
    } else {
        // No se encontró el registro
        $_SESSION['error'] = "No se encontró la asignación o ya fue eliminada.";
    }
    
} catch (PDOException $e) {
    // 7. Manejo de errores de la base de datos
 
    if ($e->getCode() == '23000' || strpos($e->getMessage(), '1451') !== false) {
        $_SESSION['error'] = 'No se puede eliminar esta asignación porque está siendo referenciada por otros registros.';
    } else {
        // Error genérico
        $_SESSION['error'] = 'Error interno del servidor al intentar eliminar. Por favor, inténtelo de nuevo.';
    }

} finally {
    // 8. Redirigir siempre al finalizar la operación (éxito o error)
    header('Location: ' . $redireccion_url);
    exit;
}
?>