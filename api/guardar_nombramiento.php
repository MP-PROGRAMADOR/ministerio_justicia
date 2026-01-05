<?php
// GUARDAR Y ACTUALIZAR NOMBRAMIENTOS
session_start();
require_once '../includes/conexion.php';

$accion = $_GET['accion'] ?? '';
$redirectTo = "../administrador/nombramientos.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        $ruta_uploads = "../uploads/";
        if (!is_dir($ruta_uploads)) mkdir($ruta_uploads, 0777, true);

        // --- OPERACIÓN: CREAR ---
        if ($accion == 'crear') {
            if (empty($_POST['id_funcionario']) || empty($_POST['id_cargo']) || empty($_POST['fecha_nombramiento'])) {
                throw new Exception("Funcionario, Cargo y Fecha de Nombramiento son obligatorios.");
            }

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

            $sql = "INSERT INTO nombramientos (
                        Id_funcionario, Id_cargo, Fecha_nombramiento, Fecha_toma_posesion, 
                        Id_direccion, Id_seccion, Id_categoria, 
                        Copia_doc_nomb, Copia_doc_tom_posesion, Usuario_creador
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_POST['id_funcionario'],
                $_POST['id_cargo'],
                $_POST['fecha_nombramiento'],
                !empty($_POST['fecha_toma_posesion']) ? $_POST['fecha_toma_posesion'] : null,
                !empty($_POST['id_direccion']) ? $_POST['id_direccion'] : null,
                !empty($_POST['id_seccion']) ? $_POST['id_seccion'] : null,
                !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : null,
                $nombre_doc_nomb,
                $nombre_doc_posesion,
                $usuario_creador
            ]);

            $_SESSION['exito'] = "Nombramiento registrado con éxito.";

        // --- OPERACIÓN: ACTUALIZAR ---
        } elseif ($accion == 'actualizar') {
            if (empty($_POST['id_nombramiento'])) {
                throw new Exception("ID de nombramiento no proporcionado.");
            }

            $id = $_POST['id_nombramiento'];

            // 1. Obtener nombres de archivos actuales
            $stmt_files = $pdo->prepare("SELECT Copia_doc_nomb, Copia_doc_tom_posesion FROM nombramientos WHERE Id_nombramiento = ?");
            $stmt_files->execute([$id]);
            $current_data = $stmt_files->fetch(PDO::FETCH_ASSOC);

            if (!$current_data) throw new Exception("El registro no existe.");

            $nombre_doc_nomb = $current_data['Copia_doc_nomb'];
            $nombre_doc_posesion = $current_data['Copia_doc_tom_posesion'];
            
            $actualizo_nomb = false;
            $actualizo_pos = false;

            // 2. Procesar nuevo archivo de NOMBRAMIENTO
            if (isset($_FILES['doc_nombramiento']) && $_FILES['doc_nombramiento']['error'] === UPLOAD_ERR_OK) {
                if ($nombre_doc_nomb && file_exists($ruta_uploads . $nombre_doc_nomb)) {
                    unlink($ruta_uploads . $nombre_doc_nomb);
                }
                $ext = pathinfo($_FILES['doc_nombramiento']['name'], PATHINFO_EXTENSION);
                $nombre_doc_nomb = "nomb_" . uniqid() . "." . $ext;
                move_uploaded_file($_FILES['doc_nombramiento']['tmp_name'], $ruta_uploads . $nombre_doc_nomb);
                $actualizo_nomb = true;
            }

            // 3. Procesar nuevo archivo de POSESIÓN
            if (isset($_FILES['doc_posesion']) && $_FILES['doc_posesion']['error'] === UPLOAD_ERR_OK) {
                if ($nombre_doc_posesion && file_exists($ruta_uploads . $nombre_doc_posesion)) {
                    unlink($ruta_uploads . $nombre_doc_posesion);
                }
                $ext = pathinfo($_FILES['doc_posesion']['name'], PATHINFO_EXTENSION);
                $nombre_doc_posesion = "pos_" . uniqid() . "." . $ext;
                move_uploaded_file($_FILES['doc_posesion']['tmp_name'], $ruta_uploads . $nombre_doc_posesion);
                $actualizo_pos = true;
            }

            // 4. Actualizar Base de Datos
            $sql = "UPDATE nombramientos SET 
                        Id_funcionario = ?, Id_cargo = ?, Fecha_nombramiento = ?, 
                        Fecha_toma_posesion = ?, Id_direccion = ?, Id_seccion = ?, 
                        Id_categoria = ?, Copia_doc_nomb = ?, Copia_doc_tom_posesion = ?
                    WHERE Id_nombramiento = ?";

            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([
                $_POST['id_funcionario'],
                $_POST['id_cargo'],
                $_POST['fecha_nombramiento'],
                !empty($_POST['fecha_toma_posesion']) ? $_POST['fecha_toma_posesion'] : null,
                !empty($_POST['id_direccion']) ? $_POST['id_direccion'] : null,
                !empty($_POST['id_seccion']) ? $_POST['id_seccion'] : null,
                !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : null,
                $nombre_doc_nomb,
                $nombre_doc_posesion,
                $id
            ]);

            if ($resultado) {
                // Mensaje dinámico según lo solicitado
                $msg = "Datos actualizados correctamente.";
                
                if ($actualizo_nomb && $actualizo_pos) {
                    $msg .= " Se reemplazaron ambos documentos.";
                } elseif ($actualizo_nomb) {
                    $msg .= " Se reemplazó el documento de Nombramiento.";
                } elseif ($actualizo_pos) {
                    $msg .= " Se reemplazó el documento de Posesión.";
                } else {
                    $msg .= " No se realizaron cambios en los documentos.";
                }
                
                $_SESSION['exito'] = $msg;
            } else {
                throw new Exception("Error al ejecutar la actualización en la base de datos.");
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

header("Location: $redirectTo");
exit();