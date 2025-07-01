<?php
session_start();
require_once '../includes/conexion.php';

if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Vuelve a iniciar sesión.";
    header("Location: ../index.php");
    exit;
}

$usuarioModificador = $_SESSION['ID_Usuario'];

// Validar si vienen los datos requeridos
if (!isset($_POST['ID_Permiso'], $_POST['Tipo_Permiso'], $_POST['Fecha_Inicio_Permiso'], $_POST['Fecha_Fin_Permiso'])) {
    $_SESSION['error'] = "Faltan datos obligatorios.";
    header("Location: ../administrador/permisos.php");
    exit;
}

$idPermiso = $_POST['ID_Permiso'];
$tipoPermiso = $_POST['Tipo_Permiso'];
$fechaInicio = $_POST['Fecha_Inicio_Permiso'];
$fechaFin = $_POST['Fecha_Fin_Permiso'];
$estadoPermiso = $_POST['Estado_Permiso'] ?? 'Pendiente';
$motivo = $_POST['Motivo'] ?? null;
$observaciones = $_POST['Observaciones'] ?? null;

// Conexión a la base de datos
try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Obtener el documento actual (si lo hay)
    $stmt = $pdo->prepare("SELECT Documento_Soporte_URL FROM tbl_permisos WHERE ID_Permiso = ?");
    $stmt->execute([$idPermiso]);
    $permisoActual = $stmt->fetch(PDO::FETCH_ASSOC);
    $documentoActual = $permisoActual['Documento_Soporte_URL'];

    // Manejo del nuevo documento (si se sube)
    $documentoFinal = $documentoActual;

    if (isset($_FILES['Documento_Soporte_URL']) && $_FILES['Documento_Soporte_URL']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = 'perm_' . uniqid() . '_' . basename($_FILES['Documento_Soporte_URL']['name']);
        $rutaDestino = '../api/soportes/' . $nombreArchivo;

        if (move_uploaded_file($_FILES['Documento_Soporte_URL']['tmp_name'], $rutaDestino)) {
            $documentoFinal = 'soportes/' . $nombreArchivo;
        } else {
            $_SESSION['error'] = "Error al subir el documento.";
            header("Location: ../administrador/permisos.php");
            exit;
        }
    }

    // Actualizar permiso
    $sql = "UPDATE tbl_permisos SET 
                Tipo_Permiso = :tipo,
                Fecha_Inicio_Permiso = :inicio,
                Fecha_Fin_Permiso = :fin,
                Estado_Permiso = :estado,
                Motivo = :motivo,
                Observaciones = :obs,
                Documento_Soporte_URL = :doc,
                ID_Usuario_Ultima_Modificacion = :usuario
            WHERE ID_Permiso = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':tipo' => $tipoPermiso,
        ':inicio' => $fechaInicio,
        ':fin' => $fechaFin,
        ':estado' => $estadoPermiso,
        ':motivo' => $motivo,
        ':obs' => $observaciones,
        ':doc' => $documentoFinal,
        ':usuario' => $usuarioModificador,
        ':id' => $idPermiso
    ]);

    $_SESSION['exito'] = "Permiso actualizado correctamente.";
    header("Location: ../administrador/permisos.php");
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al actualizar: " . $e->getMessage();
    header("Location: ../administrador/permisos.php");
    exit;
}
