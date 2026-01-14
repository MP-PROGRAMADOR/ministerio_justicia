<?php
session_start();

// 1. VERIFICACIÓN DE SESIÓN (CORREGIDO: Usar ID_Funcionario de la sesión de login)
if (!isset($_SESSION['ID_Usuario'])) { 
    $_SESSION['error'] = "Sesión expirada. Vuelve a iniciar sesión.";
    // Redirige al login principal
    header("Location: ../index.php"); 
    exit;
}

include_once '../includes/conexion.php';

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // ID del Funcionario que solicita el permiso (tomado de la variable correcta de la sesión)
    $ID_Funcionario = $_SESSION['ID_Usuario']; 

    // 2. RECUPERACIÓN DE DATOS DEL FORMULARIO
    $Tipo_Permiso = $_POST['Tipo_Permiso'] ?? null;
    $Fecha_Inicio_Permiso = $_POST['Fecha_Inicio_Permiso'] ?? null;
    $Fecha_Fin_Permiso = $_POST['Fecha_Fin_Permiso'] ?? null;
    $Motivo = $_POST['Motivo'] ?? ''; // Asignar cadena vacía si no se proporciona
    $Observaciones = $_POST['Observaciones'] ?? ''; // Asignar cadena vacía si no se proporciona

    // 3. ESTADO POR DEFECTO Y CREADOR
    $Estado_Permiso = 'Pendiente'; 
    $ID_Usuario_Creador = $ID_Funcionario; // El mismo funcionario es el creador

    // 4. VALIDACIÓN DE DATOS OBLIGATORIOS
    if (empty($ID_Funcionario) || empty($Tipo_Permiso) || empty($Fecha_Inicio_Permiso) || empty($Fecha_Fin_Permiso)) {
        throw new Exception("Faltan datos obligatorios (Funcionario ID, Tipo de Permiso o Fechas).");
    }
    
    // Validar que la fecha de inicio no sea posterior a la de fin
    if ($Fecha_Inicio_Permiso > $Fecha_Fin_Permiso) {
        throw new Exception("La fecha de inicio no puede ser posterior a la fecha de fin.");
    }
    
    // 5. GESTIÓN DE SUBIDA DE ARCHIVOS
    $documentoURL = null;
    
    // El campo de documento es 'required' en el HTML. Verificamos su carga.
    if (!isset($_FILES['Documento_Soporte_URL']) || $_FILES['Documento_Soporte_URL']['error'] === UPLOAD_ERR_NO_FILE) {
         // Si el documento es obligatorio, lanzar una excepción.
         throw new Exception("El Documento Soporte es obligatorio y no fue subido.");
    }

    if ($_FILES['Documento_Soporte_URL']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/permisos/';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception("Error al crear el directorio de subida.");
            }
        }
        
        $fileTmpPath = $_FILES['Documento_Soporte_URL']['tmp_name'];
        $fileName = basename($_FILES['Documento_Soporte_URL']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExt = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

        if (!in_array($fileExtension, $allowedExt)) {
            throw new Exception("Tipo de archivo no permitido. Solo se permiten: " . implode(', ', $allowedExt));
        }

        $newFileName = 'permiso_' . $ID_Funcionario . '_' . time() . '.' . $fileExtension;
        $destPath = $uploadDir . $newFileName;

        if (!move_uploaded_file($fileTmpPath, $destPath)) {
            throw new Exception("Error al subir el archivo. Revisa permisos.");
        }

        $documentoURL = 'uploads/permisos/' . $newFileName;
    } else {
        // Manejo de otros errores de subida de PHP
        throw new Exception("Error al subir el archivo. Código de error: " . $_FILES['Documento_Soporte_URL']['error']);
    }
    
    // 6. INSERCIÓN EN LA BASE DE DATOS
    $sql = "INSERT INTO tbl_permisos 
        (ID_Funcionario, Tipo_Permiso, Fecha_Inicio_Permiso, Fecha_Fin_Permiso, Motivo, Observaciones, Documento_Soporte_URL, ID_Usuario_Creador, Estado_Permiso)
        VALUES (:ID_Funcionario, :Tipo_Permiso, :Fecha_Inicio_Permiso, :Fecha_Fin_Permiso, :Motivo, :Observaciones, :Documento_Soporte_URL, :ID_Usuario_Creador, :Estado_Permiso)";
        
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ID_Funcionario' => $ID_Funcionario,
        ':Tipo_Permiso' => $Tipo_Permiso,
        ':Fecha_Inicio_Permiso' => $Fecha_Inicio_Permiso,
        ':Fecha_Fin_Permiso' => $Fecha_Fin_Permiso,
        ':Motivo' => $Motivo,
        ':Observaciones' => $Observaciones,
        ':Documento_Soporte_URL' => $documentoURL,
        ':ID_Usuario_Creador' => $ID_Usuario_Creador,
        ':Estado_Permiso' => $Estado_Permiso,
    ]);

    $_SESSION['exito'] = "Solicitud de permiso enviada correctamente. Estado: Pendiente.";
    header('Location: ../funcionario/panel_funcionario.php'); 
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = "Error al solicitar el permiso: " . $e->getMessage();
    // Redirige a la página anterior o al dashboard si no hay REFERER
    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '../funcionario/panel_funcionario.php');
    exit;
}