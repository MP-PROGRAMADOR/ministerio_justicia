<?php
session_start();
require_once '../includes/conexion.php';

if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Vuelve a iniciar sesión.";
    header("Location: ../index.php");
    exit;
}

$usuarioModificador = $_SESSION['ID_Usuario'];
$rolUsuario = $_SESSION['Rol_Usuario'] ?? '';

// 1. Validar datos requeridos
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
// El token lo manejaremos dinámicamente según el rol o cambio
$token = $_POST['token'] ?? 1; 

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // 2. Obtener documentos actuales para no perder las rutas si no se suben archivos nuevos
    $stmt = $pdo->prepare("SELECT Documento_Soporte_URL, documento_permiso, token FROM tbl_permisos WHERE ID_Permiso = ?");
    $stmt->execute([$idPermiso]);
    $permisoActual = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$permisoActual) {
        throw new Exception("El permiso no existe.");
    }

    $documentoSoporteFinal = $permisoActual['Documento_Soporte_URL'];
    $documentoPermisoFinal = $permisoActual['documento_permiso'];

    // Directorios de subida (Asegúrate de que existan)
    $baseDir = __DIR__ . '/../uploads/permisos/';
    if (!is_dir($baseDir)) mkdir($baseDir, 0755, true);

    // 3. Manejo de Documento_Soporte_URL (Justificante del funcionario)
    if (isset($_FILES['Documento_Soporte_URL']) && $_FILES['Documento_Soporte_URL']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = 'soporte_' . $idPermiso . '_' . time() . '_' . basename($_FILES['Documento_Soporte_URL']['name']);
        if (move_uploaded_file($_FILES['Documento_Soporte_URL']['tmp_name'], $baseDir . $nombreArchivo)) {
            $documentoSoporteFinal = 'uploads/permisos/' . $nombreArchivo;
        }
    }

    // 4. Manejo de documento_permiso (Respuesta oficial del Admin)
    if (isset($_FILES['documento_permiso']) && $_FILES['documento_permiso']['error'] === UPLOAD_ERR_OK) {
        $nombreDocRes = 'respuesta_' . $idPermiso . '_' . time() . '_' . basename($_FILES['documento_permiso']['name']);
        if (move_uploaded_file($_FILES['documento_permiso']['tmp_name'], $baseDir . $nombreDocRes)) {
            $documentoPermisoFinal = 'uploads/permisos/' . $nombreDocRes;
        }
    }

    // 5. Lógica de roles y token
    if ($rolUsuario === 'Jefe Personal') {
        $estadoPermiso = 'Pendiente';
        $token = 0; // Ejemplo: 0 para revisión pendiente
    }

    // 6. Actualizar permiso (Sincronizado con las columnas de tu CREATE TABLE)
    // Nota: Eliminé ID_Usuario_Ultima_Modificacion porque no está en tu tabla
    $sql = "UPDATE tbl_permisos SET 
                Tipo_Permiso = :tipo,
                Fecha_Inicio_Permiso = :inicio,
                Fecha_Fin_Permiso = :fin,
                Estado_Permiso = :estado,
                Motivo = :motivo,
                token = :token,
                Documento_Soporte_URL = :doc_soporte,
                documento_permiso = :doc_permiso
            WHERE ID_Permiso = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':tipo'         => $tipoPermiso,
        ':inicio'       => $fechaInicio,
        ':fin'          => $fechaFin,
        ':estado'       => $estadoPermiso,
        ':motivo'       => $motivo,
        ':token'        => $token,
        ':doc_soporte'  => $documentoSoporteFinal,
        ':doc_permiso'  => $documentoPermisoFinal,
        ':id'           => $idPermiso
    ]);

    $_SESSION['exito'] = "Permiso actualizado correctamente.";
    header("Location: ../administrador/permisos.php");
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = "Error al actualizar: " . $e->getMessage();
    header("Location: ../administrador/permisos.php");
    exit;
}