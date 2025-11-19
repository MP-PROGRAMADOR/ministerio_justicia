<?php
ini_set('display_errors', 1); // Muestra errores en pantalla (desactivar en producción)
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}

require_once '../includes/conexion.php'; 

// 3. RECIBIR DATOS DEL FRONTEND
$funcionario_id = $_POST['id'] ?? null;
header('Content-Type: application/json');

if (!$funcionario_id) {
    echo json_encode(['success' => false, 'message' => 'ID de funcionario no proporcionado.']);
    exit;
}

$nuevo_estado = 'Activo'; 

try {
    // 4. CONEXIÓN A LA BASE DE DATOS
    // Si la conexión falla aquí, el error lo capturará el 'catch'.
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // 5. PREPARAR Y EJECUTAR UPDATE
    $sql_update = "UPDATE tbl_funcionarios 
                   SET Estado_Laboral = ?, 
                       ID_Usuario_Ultima_Modificacion = ?, 
                       Fecha_Ultima_Modificacion = CURRENT_TIMESTAMP() 
                   WHERE ID_Funcionario = ?"; 
    
    $stmt_update = $pdo->prepare($sql_update);
    $usuario_modificador_id = $_SESSION['ID_Usuario'] ?? 2; 
    
    $stmt_update->execute([$nuevo_estado, $usuario_modificador_id, $funcionario_id]);

    // 6. RESPONDER AL FRONTEND
    if ($stmt_update->rowCount() > 0) {
        // Establecer un mensaje de éxito para la siguiente página (si recargas)
        $_SESSION['exito'] = 'El funcionario ha sido ACTIVADO exitosamente.'; 
        echo json_encode(['success' => true, 'message' => 'Funcionario activado correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontró el funcionario o ya estaba activo.']);
    }

} catch (PDOException $e) {
    // 7. CAPTURA ERRORES DE CONEXIÓN O SENTENCIA SQL
    $error_message = "Error de base de datos: " . $e->getMessage();
    error_log("Error al activar funcionario: " . $error_message);
    
    // Devolver el error real (pero NO en producción)
    echo json_encode(['success' => false, 'message' => 'Error de conexión o SQL. Detalles: ' . $e->getMessage()]);
}
?>