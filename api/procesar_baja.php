<?php
ini_set('display_errors', 0);
header('Content-Type: application/json');
session_start();
require_once '../includes/conexion.php';

$response = ['success' => false, 'message' => ''];

try {
    $usuario_id = $_SESSION['ID_Usuario'] ?? null;
    if (!$usuario_id) throw new Exception('Sesión no válida.');

    $id = $_POST['funcionario_id'] ?? null;
    $tipo = $_POST['tipo_baja'] ?? ''; 
    $file = $_FILES['documento_baja'] ?? null;

    if (!$id) throw new Exception('ID de funcionario faltante.');
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) throw new Exception('El documento justificante es obligatorio.');

    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->beginTransaction();

    // 1. OBTENER EL DOCUMENTO ANTERIOR PARA BORRARLO DESPUÉS
    $stmt_old = $pdo->prepare("SELECT Copia_doc_nomb FROM funcionarios WHERE Id_funcionario = ?");
    $stmt_old->execute([$id]);
    $doc_anterior = $stmt_old->fetchColumn();

    // Determinar nuevo estado
    $nuevo_estado = 'Activo';
    $mensaje_flash = "Funcionario reactivado correctamente.";
    
    if ($tipo === 'temporal') {
        $nuevo_estado = 'Baja Temporal';
        $mensaje_flash = "Baja temporal registrada con éxito.";
    } elseif ($tipo === 'definitivo') {
        $nuevo_estado = 'Cesado';
        $mensaje_flash = "Cese definitivo procesado con éxito.";
    }

    // 2. GESTIONAR NUEVO ARCHIVO
    $nombre_archivo = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($file['name']));
    $directorio = "../uploads/documentos_bajas/";
    if (!is_dir($directorio)) mkdir($directorio, 0777, true);
    
    $ruta_completa = $directorio . $nombre_archivo;
    
    if (!move_uploaded_file($file['tmp_name'], $ruta_completa)) {
        throw new Exception('Error al guardar el nuevo archivo.');
    }

    // 3. ACTUALIZAR BASE DE DATOS
    $sql = "UPDATE funcionarios SET Estado_Laboral = ?, Copia_doc_nomb = ? WHERE Id_funcionario = ?";
    $pdo->prepare($sql)->execute([$nuevo_estado, $ruta_completa, $id]);

    // Registrar en Logs
    $sql_log = "INSERT INTO logs (Usuario_id, Tabla_afectada, Accion, Registro_id, Descripcion) VALUES (?, 'funcionarios', 'UPDATE', ?, ?)";
    $pdo->prepare($sql_log)->execute([$usuario_id, $id, "Cambio a $nuevo_estado. Sustitución de documento."]);

    $pdo->commit();

    // 4. BORRAR EL ARCHIVO FÍSICO ANTERIOR SI EXISTE
    // Lo hacemos después del commit para asegurar que la DB se actualizó primero
    if ($doc_anterior && file_exists($doc_anterior)) {
        unlink($doc_anterior); 
    }
    
    $_SESSION['exito'] = $mensaje_flash;
    session_write_close(); 

    $response = ['success' => true];

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    // Si hubo error y alcanzamos a subir el nuevo, lo borramos para no dejar basura
    if (isset($ruta_completa) && file_exists($ruta_completa)) unlink($ruta_completa);
    $response['message'] = $e->getMessage();
}

echo json_encode($response);