<?php
// 1. Iniciar la sesión para usar $_SESSION (mensajes flash)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Incluir el archivo de conexión. Asume que este archivo define la variable $pdo.
require_once '../includes/conexion.php'; 

// La URL a la que redirigiremos después de la eliminación de un destino
$redireccion_url = '../administrador/destinos.php'; // Cambiar si la ruta a tu vista de destinos es diferente

// 3. Verificar el método de solicitud (debe ser POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método de solicitud no permitido.';
    header('Location: ' . $redireccion_url);
    exit;
}

// 4. Obtener y sanear el ID del destino enviado por el formulario
$destinoId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$destinoId) {
    $_SESSION['error'] = 'ID de destino no válido. No se pudo procesar la solicitud.';
    header('Location: ' . $redireccion_url);
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // 5. Preparar y ejecutar la consulta DELETE en la tabla tbl_destinos
    // Usamos ID_Destino como clave primaria para la eliminación.
    $sql = "DELETE FROM tbl_destinos WHERE ID_Destino = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $destinoId, PDO::PARAM_INT);
    $stmt->execute();

    // 6. Verificar si se eliminó alguna fila
    if ($stmt->rowCount() > 0) {
        // ÉXITO: Establecer mensaje de éxito en la sesión
        $_SESSION['exito'] = "El destino (ID: " . htmlspecialchars($destinoId) . ") ha sido eliminado con éxito.";
    } else {
        // No se encontró el registro
        $_SESSION['error'] = "No se encontró el destino o ya fue eliminado.";
    }
    
} catch (PDOException $e) {
    // 7. Manejo de errores de la base de datos
    
    // El error 23000 (o 1451 en MySQL) indica una violación de Clave Foránea (FK).
    // Esto es común si el destino está asignado a un funcionario (e.g., en tbl_asignaciones).
    if ($e->getCode() == '23000' || strpos($e->getMessage(), '1451') !== false) {
        $_SESSION['error'] = 'No se puede eliminar este destino porque está siendo referenciado por otros registros (ej. por funcionarios en asignaciones).';
    } else {
        // Error genérico
        $_SESSION['error'] = 'Error interno del servidor al intentar eliminar el destino. Por favor, inténtelo de nuevo.';
    }

} finally {
    // 8. Redirigir siempre al finalizar la operación (éxito o error)
    header('Location: ' . $redireccion_url);
    exit;
}
?>