<?php

// 1. Iniciar la sesión para usar $_SESSION (mensajes flash)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/conexion.php'; 

$redireccion_url = '../administrador/instrucciones_diarias.php'; 

// 3. Verificar el método de solicitud (debe ser POST, siguiendo el patrón robusto)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método de solicitud no permitido.';
    header('Location: ' . $redireccion_url);
    exit;
}

// 4. Obtener y sanear el ID de la instrucción (se espera que sea 'id_instruccion' por POST)
$instruccionId = filter_input(INPUT_POST, 'id_instruccion', FILTER_VALIDATE_INT);

if (!$instruccionId) {
    $_SESSION['error'] = 'ID de instrucción no válido. No se pudo procesar la solicitud.';
    header('Location: ' . $redireccion_url);
    exit;
}

$tituloInstruccion = null;
$nombreFuncionario = null;

try {
 
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // 5. SELECT: Obtener el título y el nombre del funcionario para el mensaje de éxito
    $sql_select = "
        SELECT 
            i.Titulo, 
            CONCAT(f.Nombres, ' ', f.Apellidos) AS NombreCompleto
        FROM tbl_instrucciones i
        JOIN tbl_funcionarios f ON i.ID_Funcionario = f.ID_Funcionario
        WHERE i.ID_Instruccion = :id
    ";
    $stmt_select = $pdo->prepare($sql_select);
    $stmt_select->bindParam(':id', $instruccionId, PDO::PARAM_INT);
    $stmt_select->execute();
    
    $resultado = $stmt_select->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        $tituloInstruccion = $resultado['Titulo'];
        $nombreFuncionario = $resultado['NombreCompleto'];
    } else {
        // La instrucción ya no existe
        $_SESSION['error'] = "No se encontró la instrucción con ID " . htmlspecialchars($instruccionId) . ".";
        header('Location: ' . $redireccion_url);
        exit;
    }
    
    // 6. DELETE: Eliminar la instrucción
    $sql_delete = " DELETE FROM tbl_instrucciones WHERE ID_Instruccion = :id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->bindParam(':id', $instruccionId, PDO::PARAM_INT);
    $stmt_delete->execute();

    // 7. Verificar si se eliminó alguna fila
    if ($stmt_delete->rowCount() > 0) {
        // ÉXITO: Usar la información capturada en el mensaje flash
        $_SESSION['exito'] = "La instrucción: " . htmlspecialchars($tituloInstruccion) . " enviada a " . htmlspecialchars($nombreFuncionario) . " ha sido eliminada con éxito.";
    } else {
        // Fallo de eliminación
        $_SESSION['error'] = "No se pudo eliminar la instrucción " . htmlspecialchars($tituloInstruccion) . " (ID: " . htmlspecialchars($instruccionId) . ").";
    }
    
} catch (PDOException $e) {
    
   
    $displayInfo = $tituloInstruccion ? "la instrucción " . htmlspecialchars($tituloInstruccion) . "" : "la instrucción con ID " . htmlspecialchars($instruccionId);

    $_SESSION['error'] = 'Error interno del servidor al intentar eliminar ' . $displayInfo . '. Por favor, inténtelo de nuevo.';
    

} finally {
    // 9. Redirigir siempre al finalizar la operación
    header('Location: ' . $redireccion_url);
    exit;
}
?>