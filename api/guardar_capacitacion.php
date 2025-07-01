<?php
session_start();
require_once '../includes/conexion.php';

// Verificar sesión activa
if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Inicia sesión nuevamente.";
    header("Location: ../index.php");
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Recibir datos del formulario
    $ID_Funcionario = $_POST['ID_Funcionario'] ?? null;
    $Nombre_Curso = trim($_POST['Nombre_Curso'] ?? '');
    $Institucion_Organizadora = trim($_POST['Institucion_Organizadora'] ?? '');
    $Fecha_Inicio_Curso = $_POST['Fecha_Inicio_Curso'] ?? null;
    $Fecha_Fin_Curso = $_POST['Fecha_Fin_Curso'] ?? null;
    $ID_Usuario_Creador = $_SESSION['ID_Usuario'];

    // Validar campos obligatorios
    if (!$ID_Funcionario || !$Nombre_Curso || !$Institucion_Organizadora) {
        $_SESSION['error'] = "Todos los campos obligatorios deben completarse.";
        header("Location: ../administrador/capacitaciones.php");
        exit;
    }

    // Subida del archivo si existe
    $Certificado_URL = null;
    if (!empty($_FILES['Certificado_URL']['name'])) {
        $archivo = $_FILES['Certificado_URL'];
        $nombreArchivo = 'certificados/cert_' . uniqid() . '_' . basename($archivo['name']);
        $rutaDestino = '../' . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            $Certificado_URL = $nombreArchivo;
        } else {
            $_SESSION['error'] = "No se pudo subir el archivo del certificado.";
            header("Location: ../administrador/capacitaciones.php");
            exit;
        }
    }

    // Insertar en la base de datos
    $sql = "INSERT INTO tbl_capacitaciones (
                ID_Funcionario,
                Nombre_Curso,
                Institucion_Organizadora,
                Fecha_Inicio_Curso,
                Fecha_Fin_Curso,
                Certificado_URL,
                ID_Usuario_Creador
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $ID_Funcionario,
        $Nombre_Curso,
        $Institucion_Organizadora,
        $Fecha_Inicio_Curso ?: null,
        $Fecha_Fin_Curso ?: null,
        $Certificado_URL,
        $ID_Usuario_Creador
    ]);

    $_SESSION['exito'] = "Capacitación registrada correctamente.";

} catch (PDOException $e) {
    $_SESSION['error'] = "Error al registrar capacitación: " . $e->getMessage();
}

header("Location: ../administrador/capacitaciones.php");
exit;
