<?php


// 1. Iniciar la sesión para usar $_SESSION (mensajes flash)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/conexion.php'; 

// La URL a la que redirigiremos después de la eliminación del departamento
$redireccion_url = '../administrador/departamentos.php'; 

// 3. Verificar el método de solicitud (debe ser POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método de solicitud no permitido.';
    header('Location: ' . $redireccion_url);
    exit;
}

// 4. Obtener y sanear el ID del departamento
$departamentoId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$departamentoId) {
    $_SESSION['error'] = 'ID de departamento no válido. No se pudo procesar la solicitud.';
    header('Location: ' . $redireccion_url);
    exit;
}

// Inicializar la variable para el nombre del departamento
$nombreDepartamento = null;

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    $sql_select = "SELECT Nombre_Departamento FROM tbl_departamentos WHERE ID_Departamento = :id";
    $stmt_select = $pdo->prepare($sql_select);
    $stmt_select->bindParam(':id', $departamentoId, PDO::PARAM_INT);
    $stmt_select->execute();
    
    // Capturar el nombre
    $resultado = $stmt_select->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        $nombreDepartamento = $resultado['Nombre_Departamento'];
    } else {
        // El departamento ya no existe o el ID es incorrecto (salimos aquí)
        $_SESSION['error'] = "No se encontró el departamento con ID " . htmlspecialchars($departamentoId) . ".";
        header('Location: ' . $redireccion_url);
        exit;
    }
    

    $sql_delete = " DELETE FROM tbl_departamentos WHERE ID_Departamento = :id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->bindParam(':id', $departamentoId, PDO::PARAM_INT);
    $stmt_delete->execute();

    // 7. Verificar si se eliminó alguna fila
    if ($stmt_delete->rowCount() > 0) {
        // ÉXITO: Usar la variable $nombreDepartamento en el mensaje flash
        $_SESSION['exito'] = "El departamento de " . htmlspecialchars($nombreDepartamento) . " ha sido eliminado con éxito.";
    } else {
        // Esto es poco probable si el SELECT fue exitoso, pero lo mantenemos por si acaso
        $_SESSION['error'] = "No se pudo eliminar el departamento" . htmlspecialchars($nombreDepartamento) . " (ID: " . htmlspecialchars($departamentoId) . ").";
    }
    
} catch (PDOException $e) {
    
    // Si $nombreDepartamento se pudo obtener, lo usamos en el mensaje de error.
    $displayNombre = $nombreDepartamento ? htmlspecialchars($nombreDepartamento) : "el departamento con ID " . htmlspecialchars($departamentoId);

    // El error 23000 (o 1451 en MySQL) indica una violación de Clave Foránea (FK).
    if ($e->getCode() == '23000' || strpos($e->getMessage(), '1451') !== false) {
        $_SESSION['error'] = 'No se puede eliminar ' . $displayNombre . '. Está asignado a funcionarios en la tabla **tbl_asignaciones**
         y la acción fue bloqueada por la restricción de Clave Foránea. Primero debe reasignar a esos funcionarios.';
    } else {
        // Error genérico
        $_SESSION['error'] = 'Error interno del servidor al intentar eliminar ' . $displayNombre . '. Por favor, inténtelo de nuevo.';
    }

} finally {
    // 9. Redirigir siempre al finalizar la operación
    header('Location: ' . $redireccion_url);
    exit;
}
?>