<?php
session_start();
require_once '../includes/conexion.php';

// Verificar sesión
if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Inicia sesión nuevamente.";
    header("Location: ../index.php");
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Recibir datos POST
    $ID_Capacitacion = $_POST['ID_Capacitacion'] ?? null;
    $Nombre_Curso = trim($_POST['Nombre_Curso'] ?? '');
    $Institucion_Organizadora = trim($_POST['Institucion_Organizadora'] ?? '');
    $Fecha_Inicio_Curso = $_POST['Fecha_Inicio_Curso'] ?? null;
    $Fecha_Fin_Curso = $_POST['Fecha_Fin_Curso'] ?? null;
    $ID_Usuario_Ultima_Modificacion = $_SESSION['ID_Usuario'];

    // Validar datos mínimos
    if (!$ID_Capacitacion || !$Nombre_Curso || !$Institucion_Organizadora) {
        $_SESSION['error'] = "Faltan datos obligatorios para actualizar la capacitación.";
        header("Location: ../administrador/capacitaciones.php");
        exit;
    }

    // Procesar archivo certificado (opcional)
    $certificado_url = null;
    if (isset($_FILES['Certificado_URL']) && $_FILES['Certificado_URL']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['Certificado_URL']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/certificados/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = uniqid('cert_') . '_' . basename($_FILES['Certificado_URL']['name']);
            $targetFile = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['Certificado_URL']['tmp_name'], $targetFile)) {
                $certificado_url = 'uploads/certificados/' . $filename;
            } else {
                $_SESSION['error'] = "No se pudo subir el archivo del certificado.";
                header("Location: ../administrador/capacitaciones.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Error al subir el archivo del certificado.";
            header("Location: ../administrador/capacitaciones.php");
            exit;
        }
    }

    // Construir SQL
    $sql = "UPDATE tbl_capacitaciones SET
                Nombre_Curso = :nombre_curso,
                Institucion_Organizadora = :institucion,
                Fecha_Inicio_Curso = :fecha_inicio,
                Fecha_Fin_Curso = :fecha_fin,
                ID_Usuario_Ultima_Modificacion = :usuario_modificacion,
                Fecha_Ultima_Modificacion = NOW()";

    if ($certificado_url !== null) {
        $sql .= ", Certificado_URL = :certificado_url";
    }

    $sql .= " WHERE ID_Capacitacion = :id_capacitacion";

    $stmt = $pdo->prepare($sql);

    // Bind parámetros
    $stmt->bindParam(':nombre_curso', $Nombre_Curso);
    $stmt->bindParam(':institucion', $Institucion_Organizadora);
    $stmt->bindParam(':fecha_inicio', $Fecha_Inicio_Curso);
    $stmt->bindParam(':fecha_fin', $Fecha_Fin_Curso);
    $stmt->bindParam(':usuario_modificacion', $ID_Usuario_Ultima_Modificacion);
    $stmt->bindParam(':id_capacitacion', $ID_Capacitacion);

    if ($certificado_url !== null) {
        $stmt->bindParam(':certificado_url', $certificado_url);
    }

    $stmt->execute();

    $_SESSION['exito'] = "Capacitación actualizada correctamente.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al actualizar la capacitación: " . $e->getMessage();
}

header("Location: ../administrador/capacitaciones.php");
exit;
