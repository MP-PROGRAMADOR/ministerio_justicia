<?php
// 1. Iniciar la sesión para usar $_SESSION (mensajes flash)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Incluir el archivo de conexión. Asume que este archivo define la variable $pdo.
require_once '../includes/conexion.php'; 

// La URL a la que redirigiremos después de la eliminación del permiso
$redireccion_url = '../funcionario/histrorial_permis.php'; // Cambia esto a tu vista de permisos

// 3. Verificar el método de solicitud (debe ser POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método de solicitud no permitido.';
    header('Location: ' . $redireccion_url);
    exit;
}

// 4. Obtener y sanear el ID del permiso
$permisoId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$permisoId) {
    $_SESSION['error'] = 'ID de permiso no válido. No se pudo procesar la solicitud.';
    header('Location: ' . $redireccion_url);
    exit;
}

try {
    // 5. Conexión a la BBDD
    // Asumiendo que $dsn, $user, $pass, $options están definidos en conexion.php
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // 6. Preparar la consulta DELETE con la restricción de estado 'Pendiente'
    $sql = "DELETE FROM tbl_permisos 
            WHERE ID_Permiso = :id AND Estado_Permiso = 'Pendiente'"; // <-- RESTRICCIÓN CLAVE
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $permisoId, PDO::PARAM_INT);
    // El valor 'Pendiente' es literal en la consulta, no necesita bindParam si es fijo.
    
    $stmt->execute();

    // 7. Verificar si se eliminó alguna fila
    if ($stmt->rowCount() > 0) {
        // ÉXITO: Establecer mensaje de éxito en la sesión
        $_SESSION['exito'] = "El permiso (ID: " . htmlspecialchars($permisoId) . ") ha sido eliminado con éxito.";
    } else {
        // No se encontró el registro o NO CUMPLÍA la condición 'Estado_Permiso = Pendiente'
        
        // OPCIONAL: Podrías hacer una consulta SELECT aquí para ver si el permiso existe pero no está Pendiente
        // Esto permite un mensaje de error más específico al usuario.
        $check_sql = "SELECT Estado_Permiso FROM tbl_permisos WHERE ID_Permiso = :id";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->bindParam(':id', $permisoId, PDO::PARAM_INT);
        $check_stmt->execute();
        $permiso = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if ($permiso) {
             // El permiso existe, pero su estado impide la eliminación
             $_SESSION['error'] = "No se puede eliminar el permiso. Su estado actual es: **" . htmlspecialchars($permiso['Estado_Permiso']) . "**. Solo se pueden eliminar permisos con estado **Pendiente**.";
        } else {
             // El permiso no existe
             $_SESSION['error'] = "No se encontró el permiso o ya fue eliminado.";
        }
    }
    
} catch (PDOException $e) {
    // 8. Manejo de errores de la base de datos (No se espera FK aquí, pero se mantiene el manejo general)
    
    $_SESSION['error'] = 'Error interno del servidor al intentar eliminar el permiso. Por favor, inténtelo de nuevo.';
    // Si quieres guardar logs de errores, lo harías aquí: error_log($e->getMessage());

} finally {
    // 9. Redirigir siempre al finalizar la operación (éxito o error)
    header('Location: ' . $redireccion_url);
    exit;
}
?>