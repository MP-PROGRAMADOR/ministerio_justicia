<?php
session_start();

if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Vuelve a iniciar sesión.";
    header("Location: ../index.php");
    exit;
}

include_once '../includes/conexion.php';

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $ID_Funcionario = $_POST['ID_Funcionario'] ?? null;
    $Tipo_Permiso = $_POST['Tipo_Permiso'] ?? null;
    $Fecha_Inicio_Permiso = $_POST['Fecha_Inicio_Permiso'] ?? null;
    $Fecha_Fin_Permiso = $_POST['Fecha_Fin_Permiso'] ?? null;
    $Motivo = $_POST['Motivo'] ?? null;
    $Observaciones = $_POST['Observaciones'] ?? null;

    if (empty($ID_Funcionario) || empty($Tipo_Permiso) || empty($Fecha_Inicio_Permiso) || empty($Fecha_Fin_Permiso)) {
        throw new Exception("Faltan datos obligatorios.");
    }

    $documentoURL = null;
    if (isset($_FILES['Documento_Soporte_URL']) && $_FILES['Documento_Soporte_URL']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/permisos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $fileTmpPath = $_FILES['Documento_Soporte_URL']['tmp_name'];
        $fileName = basename($_FILES['Documento_Soporte_URL']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExt = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

        if (!in_array($fileExtension, $allowedExt)) {
            throw new Exception("Tipo de archivo no permitido.");
        }

        $newFileName = 'permiso_' . time() . '_' . bin2hex(random_bytes(5)) . '.' . $fileExtension;
        $destPath = $uploadDir . $newFileName;

        if (!move_uploaded_file($fileTmpPath, $destPath)) {
            throw new Exception("Error al subir el archivo.");
        }

        $documentoURL = 'uploads/permisos/' . $newFileName;
    }

    $ID_Usuario_Creador = $_SESSION['ID_Usuario']; // Usuario real desde sesión

    $sql = "INSERT INTO tbl_permisos 
        (ID_Funcionario, Tipo_Permiso, Fecha_Inicio_Permiso, Fecha_Fin_Permiso, Motivo, Observaciones, Documento_Soporte_URL, ID_Usuario_Creador)
        VALUES (:ID_Funcionario, :Tipo_Permiso, :Fecha_Inicio_Permiso, :Fecha_Fin_Permiso, :Motivo, :Observaciones, :Documento_Soporte_URL, :ID_Usuario_Creador)";
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
    ]);

    $_SESSION['exito'] = "Permiso registrado correctamente.";
    header('Location: ../administrador/permisos.php');
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: ../administrador/permisos.php');
    exit;
}
