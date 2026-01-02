<?php

// 1. Iniciar la sesión para usar $_SESSION (mensajes flash)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Asegúrate de que esta ruta sea correcta para tu conexión a la BBDD
require_once '../includes/conexion.php'; 

// La URL a la que redirigiremos después de la eliminación. 
// A JUZGAR POR LA BBDD, debería ser la página de perfil del funcionario o la lista de formaciones.
// Asumiremos una página de lista de funcionarios por defecto, ajústala si tienes una específica.
$redireccion_url = '../administrador/funcionarios.php'; 

// 3. Verificar el método de solicitud (debe ser POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método de solicitud no permitido.';
    header('Location: ' . $redireccion_url);
    exit;
}

// 4. Obtener y sanear el ID de la Formación Académica
// El JavaScript envía el ID bajo la clave 'id_formacion'
$formacionId = filter_input(INPUT_POST, 'id_formacion', FILTER_VALIDATE_INT);

if (!$formacionId) {
    $_SESSION['error'] = 'ID de Formación Académica no válido. No se pudo procesar la solicitud.';
    header('Location: ' . $redireccion_url);
    exit;
}

// Inicializar la variable para el título de la formación
$tituloFormacion = null;

try {
    // 5. Conexión y Preparación para el SELECT
    // Suponemos que $dsn, $user, $pass, y $options están definidos en 'conexion.php'
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // a) Primero, obtenemos el título para el mensaje de éxito/error
    $sql_select = "SELECT Titulo_Obtenido FROM tbl_formacion_academica WHERE ID_Formacion = :id";
    $stmt_select = $pdo->prepare($sql_select);
    $stmt_select->bindParam(':id', $formacionId, PDO::PARAM_INT);
    $stmt_select->execute();
    
    // Capturar el título
    $resultado = $stmt_select->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        $tituloFormacion = $resultado['Titulo_Obtenido'];
    } else {
        // La formación ya no existe o el ID es incorrecto (salimos aquí)
        $_SESSION['error'] = "No se encontró la formación con ID " . htmlspecialchars($formacionId) . ".";
        header('Location: ' . $redireccion_url);
        exit;
    }
    
    // 6. Ejecución de la eliminación
    $sql_delete = " DELETE FROM tbl_formacion_academica WHERE ID_Formacion = :id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->bindParam(':id', $formacionId, PDO::PARAM_INT);
    $stmt_delete->execute();

    // 7. Verificar si se eliminó alguna fila
    if ($stmt_delete->rowCount() > 0) {
        // ÉXITO: Usar la variable $tituloFormacion en el mensaje flash
        $_SESSION['exito'] = "La formación '" . htmlspecialchars($tituloFormacion) . "' ha sido eliminada con éxito.";
    } else {
        // Esto es poco probable si el SELECT fue exitoso
        $_SESSION['error'] = "No se pudo eliminar la formación '" . htmlspecialchars($tituloFormacion) . "' (ID: " . htmlspecialchars($formacionId) . ").";
    }
    
} catch (PDOException $e) {
    
    // Si $tituloFormacion se pudo obtener, lo usamos en el mensaje de error.
    $displayNombre = $tituloFormacion ? "'" . htmlspecialchars($tituloFormacion) . "'" : "la formación con ID " . htmlspecialchars($formacionId);

    // En la tabla de formación no hay Claves Foráneas que dependan de ella,
    // por lo que este bloque capturará errores de conexión u otros errores SQL genéricos.
    
    // Error genérico
    $_SESSION['error'] = 'Error interno del servidor al intentar eliminar ' . $displayNombre . '. Por favor, inténtelo de nuevo.';
    // Opcional: Para depuración:
    // $_SESSION['error'] .= ' DEBUG: ' . $e->getMessage();


} finally {
    // 9. Redirigir siempre al finalizar la operación
    header('Location: ' . $redireccion_url);
    exit;
}
?>