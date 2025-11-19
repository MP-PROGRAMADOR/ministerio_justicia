<?php
// 1. INICIALIZACIÓN Y CONEXIÓN
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}
// Asegúrate de que esta ruta a tu archivo de conexión es correcta
require_once '../includes/conexion.php'; 

$funcionario_id = $_POST['id'] ?? null;
// AHORA RECIBIMOS EL NUEVO ESTADO ELEGIDO POR EL USUARIO EN EL FRONTEND
$nuevo_estado = $_POST['new_state'] ?? null; 

header('Content-Type: application/json');

if (!$funcionario_id || !$nuevo_estado) {
    echo json_encode(['success' => false, 'message' => 'Parámetros incompletos (ID de funcionario o nuevo estado).']);
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // 2. EJECUTAR EL UPDATE con el estado dinámico
    $sql_update = "UPDATE tbl_funcionarios 
                   SET Estado_Laboral = ?, 
                       ID_Usuario_Ultima_Modificacion = ?, 
                       Fecha_Ultima_Modificacion = CURRENT_TIMESTAMP() 
                   WHERE ID_Funcionario = ?"; 
    
    $stmt_update = $pdo->prepare($sql_update);
    $usuario_modificador_id = $_SESSION['ID_Usuario'] ?? 2; 

    $stmt_update->execute([$nuevo_estado, $usuario_modificador_id, $funcionario_id]);

    // 3. RESPONDER AL FRONTEND
    if ($stmt_update->rowCount() > 0) {
        // Usa el nuevo estado en el mensaje de éxito
        $_SESSION['exito'] = "El funcionario ha pasado al estado {$nuevo_estado}"; 
        echo json_encode(['success' => true, 'message' => "Funcionario actualizado a {$nuevo_estado}."]);
    } else {
        echo json_encode(['success' => false, 'message' => "No se encontró el funcionario o ya estaba en estado {$nuevo_estado}."]);
    }

} catch (PDOException $e) {
    error_log("Error al desactivar funcionario a {$nuevo_estado}: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error de base de datos. Detalles: ' . $e->getMessage()]);
}
?>