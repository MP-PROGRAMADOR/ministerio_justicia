<?php
session_start();

// 1. VERIFICACIÓN DE SESIÓN
if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Vuelve a iniciar sesión.";
    header("Location: ../index.php");
    exit;
}

include_once '../includes/conexion.php';

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // 2. DATOS DEL FORMULARIO
    $ID_Funcionario  = $_POST['ID_Funcionario'] ?? null;
    $Tipo_Permiso    = $_POST['Tipo_Permiso'] ?? null;
    $Fecha_Inicio    = $_POST['Fecha_Inicio_Permiso'] ?? null;
    $Fecha_Fin       = $_POST['Fecha_Fin_Permiso'] ?? null;
    $Motivo          = $_POST['Motivo'] ?? null;
    $Usuario_creador = $_SESSION['ID_Usuario'];

    // CAMPOS ADICIONALES DE TU TABLA
    $token = 1; // O la lógica que manejes para este campo int(1)

    // 3. VALIDACIONES
    if (empty($ID_Funcionario) || empty($Tipo_Permiso) || empty($Motivo) ) {
        throw new Exception("Datos obligatorios incompletos.");
    }

    // 4. GESTIÓN DE ARCHIVOS (Para Documento_Soporte_URL y documento_permiso)
    $documento_URL = null;
    $nombre_original_archivo = null;

    if (isset($_FILES['Documento_Soporte_URL']) && $_FILES['Documento_Soporte_URL']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/permisos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $nombre_original_archivo = basename($_FILES['Documento_Soporte_URL']['name']);
        $fileExt = strtolower(pathinfo($nombre_original_archivo, PATHINFO_EXTENSION));

        // Generar nombre único para la URL
        $newFileName = 'permiso_' . $ID_Funcionario . '_' . time() . '.' . $fileExt;
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['Documento_Soporte_URL']['tmp_name'], $destPath)) {
            $documento_URL = 'uploads/permisos/' . $newFileName;
        } else {
            throw new Exception("Error al mover el archivo al servidor.");
        }
    } else {
        throw new Exception("El documento soporte es obligatorio.");
    }

    // 5. INSERCIÓN COMPLETA (Sincronizada con todos los campos de tu CREATE TABLE)
    $sql = "INSERT INTO tbl_permisos (
                ID_Funcionario, 
                Tipo_Permiso, 
                Fecha_Inicio_Permiso, 
                Fecha_Fin_Permiso, 
                Motivo, 
                token, 
                documento_permiso, 
                Documento_Soporte_URL, 
                Usuario_creador,
                Estado_Permiso,
                Fecha_Solicitud
            ) VALUES (
                :ID_Funcionario, 
                :Tipo_Permiso, 
                :Fecha_Inicio_Permiso, 
                :Fecha_Fin_Permiso, 
                :Motivo, 
                :token, 
                :documento_permiso, 
                :Documento_Soporte_URL, 
                :Usuario_creador,
                'Pendiente',
                CURDATE()
            )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ID_Funcionario'        => $ID_Funcionario,
        ':Tipo_Permiso'          => $Tipo_Permiso,
        ':Fecha_Inicio_Permiso'  => $Fecha_Inicio,
        ':Fecha_Fin_Permiso'     => $Fecha_Fin,
        ':Motivo'                => $Motivo,
        ':token'                 => $token,
        ':documento_permiso'     => $nombre_original_archivo, // Guardamos el nombre original aquí
        ':Documento_Soporte_URL' => $documento_URL,          // Guardamos la ruta del servidor aquí
        ':Usuario_creador'       => $Usuario_creador
    ]);

    $_SESSION['exito'] = "Solicitud registrada con éxito.";
    header('Location: ../administrador/permisos.php');
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header('Location: ../administrador/permisos.php');
    exit;
}
