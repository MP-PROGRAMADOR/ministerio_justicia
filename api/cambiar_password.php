<?php
// ¡ERROR CORREGIDO! Es fundamental iniciar la sesión antes de acceder o modificar la variable $_SESSION.
session_start(); 

require_once '../includes/conexion.php'; 



function redirigirConAlerta($mensaje, $tipo = 'danger') {
    
    $_SESSION['alerta_mensaje'] = $mensaje;
    $_SESSION['alerta_tipo'] = $tipo;
    
    // Redirige a la página del administrador donde debe mostrarse la alerta
    header("Location: ../administrador/perfil_admin.php"); 
    exit();
}


// 1. Verificar si el usuario está logueado y si la solicitud es POST
if (!isset($_SESSION['ID_Usuario'])) {
    // Si no hay ID de usuario en la sesión, se considera un acceso no autorizado.
    redirigirConAlerta("Error: Debes iniciar sesión para realizar esta acción.", "danger");
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // Acceso directo o método incorrecto.
    redirigirConAlerta("Error de solicitud: Método no permitido.", "danger");
}

// 2. Obtener y sanear los datos del formulario
$id_usuario = $_SESSION['ID_Usuario'];
$currentPassword = $_POST['currentPassword'] ?? '';
$newPassword = $_POST['newPassword'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

// 3. Validar las contraseñas
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    redirigirConAlerta("Todos los campos de contraseña son obligatorios.", "danger");
}

if ($newPassword !== $confirmPassword) {
    // La nueva contraseña y la confirmación no coinciden.
    redirigirConAlerta("La nueva contraseña y la confirmación no coinciden.", "danger");
}

// Validación de seguridad: longitud mínima de la contraseña
if (strlen($newPassword) < 8) {
    redirigirConAlerta("La nueva contraseña debe tener al menos 8 caracteres.", "warning");
}

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
    // Consulta para obtener el hash de la contraseña actual del usuario
    $stmt = $pdo->prepare("SELECT Contrasena_Hash FROM tbl_usuarios WHERE ID_Usuario = :id");
    $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario existe o si la consulta falló al traer datos
    if (!$usuario) {
        // Esto podría indicar un problema de sesión si el ID de usuario no existe en la DB.
        redirigirConAlerta("Error de seguridad: Usuario de sesión no válido o no encontrado.", "danger");
    }

    $hash_actual = $usuario['Contrasena_Hash'];

    // Verificar la contraseña actual ingresada por el usuario con el hash almacenado
    if (!password_verify($currentPassword, $hash_actual)) {
        // La contraseña actual es incorrecta. ¡Punto de falla común!
        redirigirConAlerta("La contraseña actual introducida es incorrecta. No se pudo cambiar la contraseña.", "danger");
    }


   
    // Se utiliza PASSWORD_DEFAULT (bcrypt) que es el método recomendado.
    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

    // 6. Actualizar la contraseña en la base de datos
    $stmt_update = $pdo->prepare("
        UPDATE tbl_usuarios 
        SET Contrasena_Hash = :new_hash 
        WHERE ID_Usuario = :id
    ");
    $stmt_update->bindParam(':new_hash', $newPasswordHash, PDO::PARAM_STR);
    $stmt_update->bindParam(':id', $id_usuario, PDO::PARAM_INT);
    
    if ($stmt_update->execute()) {
        // Éxito: Contraseña cambiada
        redirigirConAlerta("Contraseña cambiada con éxito. Recuerda usar tu nueva contraseña la próxima vez que inicies sesión.", "success");
    } else {
        // Error en la ejecución de la consulta de actualización (ej. problemas de conexión persistentes)
        redirigirConAlerta("Error al intentar actualizar la contraseña en la base de datos. Inténtalo de nuevo.", "danger");
    }

} catch (PDOException $e) {
    // Error de conexión o consulta grave
    error_log("Error al cambiar contraseña: " . $e->getMessage());
    redirigirConAlerta("Error interno del sistema. Por favor, contacta a soporte. Código: DB01.", "danger"); 
    // Mantenemos el error de PDO genérico para el usuario por seguridad.
}

?>