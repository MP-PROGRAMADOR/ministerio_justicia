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

    // Obtener datos del formulario
    $ID_Funcionario = $_POST['ID_Funcionario'] ?? null;
    $Titulo_Obtenido = trim($_POST['Titulo_Obtenido'] ?? '');
    $Institucion_Educativa = trim($_POST['Institucion_Educativa'] ?? '');
    $Fecha_Graduacion = $_POST['Fecha_Graduacion'] ?? null;
    $Nivel_Educativo = $_POST['Nivel_Educativo'] ?? '';
    $ID_Usuario_Creador = $_SESSION['ID_Usuario'];

    // Validaciones mínimas
    if (!$ID_Funcionario || !$Titulo_Obtenido || !$Institucion_Educativa || !$Nivel_Educativo) {
        $_SESSION['error'] = "Todos los campos obligatorios deben completarse.";
        header("Location: ../administrador/formacion_academica.php");
        exit;
    }

    // Insertar en la base de datos
    $sql = "INSERT INTO tbl_formacion_academica 
            (ID_Funcionario, Titulo_Obtenido, Institucion_Educativa, Fecha_Graduacion, Nivel_Educativo, ID_Usuario_Creador) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $ID_Funcionario,
        $Titulo_Obtenido,
        $Institucion_Educativa,
        $Fecha_Graduacion ?: null,
        $Nivel_Educativo,
        $ID_Usuario_Creador
    ]);

    $_SESSION['exito'] = "Formación académica registrada correctamente.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al guardar la formación: " . $e->getMessage();
}

header("Location: ../administrador/formacion_academica.php");
exit;
