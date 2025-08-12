<?php
session_start();
require_once '../includes/conexion.php';

if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Vuelve a iniciar sesión.";
    header("Location: ../index.php");
    exit;
}

$usuarioModificador = $_SESSION['ID_Usuario'];
$rolUsuario = $_SESSION['Rol_Usuario'] ?? ''; // Aseguramos que se obtenga el rol

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
$estadoPermiso = $_POST['Estado_Permiso'];
$motivo = $_POST['Motivo'] ?? null;
$observaciones = $_POST['Observaciones'] ?? null;


try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Obtener documentos actuales
    $stmt = $pdo->prepare("SELECT Documento_Soporte_URL, documento_permiso FROM tbl_permisos WHERE ID_Permiso = ?");
    $stmt->execute([$idPermiso]);
    $permisoActual = $stmt->fetch(PDO::FETCH_ASSOC);

    $documentoActual = $permisoActual['Documento_Soporte_URL'];
    $documentoPermisoActual = $permisoActual['documento_permiso'];



    // === Manejo de Documento_Soporte_URL ===
    $documentoFinal = $documentoActual; // Por defecto mantiene el actual
    if (isset($_FILES['Documento_Soporte_URL']) && $_FILES['Documento_Soporte_URL']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = 'perm_' . uniqid() . '_' . basename($_FILES['Documento_Soporte_URL']['name']);
        $rutaDestino = '../api/uploads/permisos/' . $nombreArchivo;

        if (move_uploaded_file($_FILES['Documento_Soporte_URL']['tmp_name'], $rutaDestino)) {
            $documentoFinal = 'uploads/permisos/' . $nombreArchivo;
        } else {
            $_SESSION['error'] = "Error al subir el documento de soporte.";
            header("Location: ../administrador/permisos.php");
            exit;
        }
    }

    // === Manejo de documento_permiso (columna aparte) ===
    $documentoPermisoFinal = $documentoPermisoActual; // Por defecto mantiene el actual
    if (isset($_FILES['documento_permiso']) && $_FILES['documento_permiso']['error'] === UPLOAD_ERR_OK) {
        $nombreDocPermiso = 'docperm_' . uniqid() . '_' . basename($_FILES['documento_permiso']['name']);
        $rutaDocPermiso = '../api/uploads/documentos_permiso/' . $nombreDocPermiso;

        if (move_uploaded_file($_FILES['documento_permiso']['tmp_name'], $rutaDocPermiso)) {
            $documentoPermisoFinal = 'uploads/documentos_permiso/' . $nombreDocPermiso;
        } else {
            $_SESSION['error'] = "Error al subir el documento de permiso.";
            header("Location: ../administrador/permisos.php");
            exit;
        }
    }


    // === Lógica especial para Jefe Personal ===
    if ($rolUsuario === 'Jefe Personal') {
        $estadoPermiso = 'Pendiente';
        $token = 0;
    }

    // Actualizar permiso
    $sql = "UPDATE tbl_permisos SET 
                Tipo_Permiso = :tipo,
                Fecha_Inicio_Permiso = :inicio,
                Fecha_Fin_Permiso = :fin,
                Estado_Permiso = :estado,
                Motivo = :motivo,
                Observaciones = :obs,
                Documento_Soporte_URL = :doc_soporte,
                documento_permiso = :doc_permiso,
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
        ':doc_soporte' => $documentoFinal,
        ':doc_permiso' => $documentoPermisoFinal,
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
