<?php

// 1. Iniciar la sesión para usar $_SESSION (mensajes flash)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Asegúrate de que este archivo incluye la configuración de conexión a la base de datos
require_once '../includes/conexion.php';

// URL de redirección a la página de gestión de capacitaciones
$redireccion_url = '../administrador/capacitaciones.php'; // Cambia esto a la URL correcta de gestión de capacitaciones

// 3. Verificar el método de solicitud (debe ser POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método de solicitud no permitido.';
    header('Location: ' . $redireccion_url);
    exit;
}

// 4. Obtener y sanear el ID de la capacitación (esperamos 'id_capacitacion' por POST)
$capacitacionId = filter_input(INPUT_POST, 'id_capacitacion', FILTER_VALIDATE_INT);

if (!$capacitacionId) {
    $_SESSION['error'] = 'ID de capacitación no válido. No se pudo procesar la solicitud.';
    header('Location: ' . $redireccion_url);
    exit;
}

$nombreCapacitacion = null;

try {
    // 5. Establecer conexión PDO
    $pdo = new PDO($dsn, $user, $pass, $options);

    // 6. Seleccionar el nombre de la capacitación para el mensaje de éxito/error
    $sql_select_nombre = "SELECT Nombre_Curso FROM tbl_capacitaciones WHERE ID_Capacitacion = :id";
    $stmt_nombre = $pdo->prepare($sql_select_nombre);
    $stmt_nombre->bindParam(':id', $capacitacionId, PDO::PARAM_INT);
    $stmt_nombre->execute();

    $resultado = $stmt_nombre->fetch(PDO::FETCH_ASSOC);

    if (!$resultado) {
        $_SESSION['error'] = "No se encontró la capacitación con ID " . htmlspecialchars($capacitacionId) . ".";
        header('Location: ' . $redireccion_url);
        exit;
    }

    $nombreCapacitacion = $resultado['Nombre_Curso'];

    // 7. Proceder con la eliminación de la capacitación
    $sql_delete = "DELETE FROM tbl_capacitaciones WHERE ID_Capacitacion = :id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->bindParam(':id', $capacitacionId, PDO::PARAM_INT);
    $stmt_delete->execute();

    // 8. Verificar si se eliminó alguna fila
    if ($stmt_delete->rowCount() > 0) {
        // ÉXITO
        $_SESSION['exito'] = "La capacitación " . htmlspecialchars($nombreCapacitacion) . " ha sido eliminada con éxito.";
    } else {
        // Fallo de eliminación (si se verificó la existencia antes, esto es poco probable)
        $_SESSION['error'] = "No se pudo eliminar la capacitación " . htmlspecialchars($nombreCapacitacion) . " Inténtelo de nuevo.";
    }

} catch (PDOException $e) {
    // Mensaje de error genérico en caso de fallo de conexión o consulta SQL
    $displayInfo = $nombreCapacitacion ? "la capacitación " . htmlspecialchars($nombreCapacitacion) . "" : "la capacitación con ID " . htmlspecialchars($capacitacionId);

    $_SESSION['error'] = 'Error interno del servidor al intentar eliminar ' . $displayInfo . '. Por favor, inténtelo de nuevo.';

} finally {
    // 9. Redirigir siempre al finalizar la operación
    header('Location: ' . $redireccion_url);
    exit;
}
?>