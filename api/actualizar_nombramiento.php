<?php
// actualizar_nombramiento.php
session_start();
require_once '../includes/conexion.php';

$redirectTo = "../administrador/nombramientos.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        $ruta_uploads = "../uploads/";
        if (!is_dir($ruta_uploads)) mkdir($ruta_uploads, 0777, true);

        // Validamos ID del nombramiento
        if (empty($_POST['id_nombramiento'])) {
            throw new Exception("ID de nombramiento no proporcionado.");
        }
        $id = $_POST['id_nombramiento'];

        // Obtenemos los documentos actuales
        $stmt_files = $pdo->prepare("SELECT Copia_doc_nomb, Copia_doc_tom_posesion FROM nombramientos WHERE Id_nombramiento = ?");
        $stmt_files->execute([$id]);
        $current_data = $stmt_files->fetch(PDO::FETCH_ASSOC);

        if (!$current_data) throw new Exception("El registro no existe.");

        $nombre_doc_nomb = $current_data['Copia_doc_nomb'];
        $nombre_doc_posesion = $current_data['Copia_doc_tom_posesion'];

        // Procesar nuevo archivo de NOMBRAMIENTO si se envía
        if (isset($_FILES['doc_nombramiento']) && $_FILES['doc_nombramiento']['error'] === UPLOAD_ERR_OK) {
            if ($nombre_doc_nomb && file_exists($ruta_uploads . $nombre_doc_nomb)) {
                unlink($ruta_uploads . $nombre_doc_nomb);
            }
            $ext = pathinfo($_FILES['doc_nombramiento']['name'], PATHINFO_EXTENSION);
            $nombre_doc_nomb = "nomb_" . uniqid() . "." . $ext;
            move_uploaded_file($_FILES['doc_nombramiento']['tmp_name'], $ruta_uploads . $nombre_doc_nomb);
        }

        // Procesar nuevo archivo de POSESIÓN si se envía
        if (isset($_FILES['doc_posesion']) && $_FILES['doc_posesion']['error'] === UPLOAD_ERR_OK) {
            if ($nombre_doc_posesion && file_exists($ruta_uploads . $nombre_doc_posesion)) {
                unlink($ruta_uploads . $nombre_doc_posesion);
            }
            $ext = pathinfo($_FILES['doc_posesion']['name'], PATHINFO_EXTENSION);
            $nombre_doc_posesion = "pos_" . uniqid() . "." . $ext;
            move_uploaded_file($_FILES['doc_posesion']['tmp_name'], $ruta_uploads . $nombre_doc_posesion);
        }

        // Actualizamos los datos en la BD
        $sql = "UPDATE nombramientos SET 
                    Id_cargo = ?, 
                    Fecha_nombramiento = ?, 
                    Fecha_toma_posesion = ?, 
                    Fecha_finalizacion_nombramiento = ?, 
                    Id_seccion = ?, 
                    Id_categoria = ?, 
                    Copia_doc_nomb = ?, 
                    Copia_doc_tom_posesion = ?
                WHERE Id_nombramiento = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['id_cargo'],
            $_POST['fecha_nombramiento'],
            !empty($_POST['fecha_toma_posesion']) ? $_POST['fecha_toma_posesion'] : null,
            !empty($_POST['fecha_finalizacion_nombramiento']) ? $_POST['fecha_finalizacion_nombramiento'] : null,
            !empty($_POST['id_seccion']) ? $_POST['id_seccion'] : null,
            !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : null,
            $nombre_doc_nomb,
            $nombre_doc_posesion,
            $id
        ]);

        $_SESSION['exito'] = "Nombramiento actualizado correctamente.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

header("Location: $redirectTo");
exit();
