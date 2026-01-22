<?php
// GUARDAR NOMBRAMIENTOS
session_start();
require_once '../includes/conexion.php';

$redirectTo = "../administrador/nombramientos.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        // Carpeta de uploads
        $ruta_uploads = "../uploads/";
        if (!is_dir($ruta_uploads)) mkdir($ruta_uploads, 0777, true);

        // Validación de campos obligatorios
        if (empty($_POST['id_funcionario']) || empty($_POST['id_cargo']) || empty($_POST['fecha_nombramiento'])) {
            throw new Exception("Funcionario, Cargo y Fecha de Nombramiento son obligatorios.");
        }

        $id_funcionario = $_POST['id_funcionario'];

        // --- VALIDACIÓN: Nombramiento activo ---
        $sql_check = "SELECT COUNT(*) FROM nombramientos 
                      WHERE Id_funcionario = ? 
                      AND (Fecha_finalizacion_nombramiento IS NULL OR Fecha_finalizacion_nombramiento > CURDATE())";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$id_funcionario]);
        $activo = $stmt_check->fetchColumn();

        if ($activo > 0) {
            throw new Exception("Este funcionario ya tiene un nombramiento activo. No se puede registrar uno nuevo hasta finalizar el anterior.");
        }

        // Procesar archivos
        $nombre_doc_nomb = null;
        $nombre_doc_posesion = null;

        if (isset($_FILES['doc_nombramiento']) && $_FILES['doc_nombramiento']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['doc_nombramiento']['name'], PATHINFO_EXTENSION);
            $nombre_doc_nomb = "nomb_" . uniqid() . "." . $ext;
            move_uploaded_file($_FILES['doc_nombramiento']['tmp_name'], $ruta_uploads . $nombre_doc_nomb);
        }

        if (isset($_FILES['doc_posesion']) && $_FILES['doc_posesion']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['doc_posesion']['name'], PATHINFO_EXTENSION);
            $nombre_doc_posesion = "pos_" . uniqid() . "." . $ext;
            move_uploaded_file($_FILES['doc_posesion']['tmp_name'], $ruta_uploads . $nombre_doc_posesion);
        }

        $usuario_creador = $_SESSION['ID_Usuario'] ?? 2;

        // INSERT: guardar el nombramiento
        $sql = "INSERT INTO nombramientos (
                    Id_funcionario, Id_cargo, Fecha_nombramiento, Fecha_toma_posesion, 
                    Id_seccion, Id_categoria, 
                    Copia_doc_nomb, Copia_doc_tom_posesion, Usuario_creador
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $id_funcionario,
            $_POST['id_cargo'],
            $_POST['fecha_nombramiento'],
            !empty($_POST['fecha_toma_posesion']) ? $_POST['fecha_toma_posesion'] : null,
            !empty($_POST['id_seccion']) ? $_POST['id_seccion'] : null,
            !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : null,
            $nombre_doc_nomb,
            $nombre_doc_posesion,
            $usuario_creador
        ]);

        $_SESSION['exito'] = "Nombramiento registrado con éxito.";

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

header("Location: $redirectTo");
exit();
