<?php
// Iniciar sesión
session_start();



// 1. Verificar la sesión y el rol del usuario
// Redirigir si el usuario no está logueado o tiene un rol no autorizado
if (!isset($_SESSION['ID_Usuario']) || $_SESSION['Rol_Usuario'] === 'Usuario') {
    // Redirigir al index.php (asumiendo que es la página de login o inicio)
    header('Location: ../index.php'); 
    exit;
}


require_once '../includes/conexion.php'; 

// Variable para la redirección final
$redirect_page = '../administrador/cargo.php'; // Cambia esto si tu archivo de listado se llama diferente

// Verificar si se ha enviado el ID_Cargo por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && !empty($_POST['id'])) {
    
    // Obtener el ID del cargo a eliminar y limpiarlo
    $id_cargo = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);

    // 3. Ejecutar la lógica de eliminación
    if ($id_cargo > 0) {
        
        try {
            // Conexión PDO, utilizando las variables definidas en includes/conexion.php
            $pdo = new PDO($dsn, $user, $pass, $options);
            
            // La tabla a eliminar es 'tbl_cargos' y su clave primaria es 'ID_Cargo'.
            $sql = "DELETE FROM tbl_cargos WHERE ID_Cargo = :id_cargo";
            $stmt = $pdo->prepare($sql);
            
            // Vincular el parámetro
            $stmt->bindParam(':id_cargo', $id_cargo, PDO::PARAM_INT);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Verificar si se eliminó alguna fila
                if ($stmt->rowCount() > 0) {
                    // Éxito: Se eliminó el cargo
                    $_SESSION['exito'] = "El cargo ha sido eliminado exitosamente.";
                } else {
                    // Advertencia: El ID no existe o ya fue eliminado
                    $_SESSION['error'] = "No se pudo encontrar o eliminar el cargo con ID " . $id_cargo . ".";
                }
            } 

        } catch (PDOException $e) {
            // Manejo de fallos por integridad referencial (FOREIGN KEY)
            // Error code 23000 (Integrity constraint violation)
            if ($e->getCode() === '23000') {
                $_SESSION['error'] = "Error: No puedes eliminar este cargo porque está asignado a uno o más funcionarios. Elimina primero las asignaciones o cambia el cargo de los funcionarios afectados.";
            } else {
                $_SESSION['error'] = "Error inesperado al intentar eliminar el cargo: " . $e->getMessage();
            }
        }
    } else {
        // ID no válido
        $_SESSION['error'] = "ID de cargo no válido.";
    }
} else {
    // Si no se recibió el ID por POST
    $_SESSION['error'] = "Solicitud no válida para eliminar un cargo.";
}

// 4. Redirigir de vuelta a la página de listado
header('Location: ' . $redirect_page); 
exit;

?>