<?php
session_start();
require_once '../includes/conexion.php';

if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada.";
    header("Location: ../index.php");
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Capturar datos y limpiar
    $ID_Funcionario = $_POST['ID_Funcionario'] ?? null;
    $Titulo_Obtenido = trim($_POST['Titulo_Obtenido'] ?? '');
    $Institucion_Educativa = trim($_POST['Institucion_Educativa'] ?? '');
    $Fecha_Graduacion = !empty($_POST['Fecha_Graduacion']) ? $_POST['Fecha_Graduacion'] : null;
    $Nivel_Educativo = $_POST['Nivel_Educativo'] ?? '';
    $ID_Usuario_Creador = $_SESSION['ID_Usuario'];
    
    // 1. Validaciones de presencia
    if (empty($ID_Funcionario) || empty($Titulo_Obtenido) || empty($Nivel_Educativo)) {
        throw new Exception("Faltan datos obligatorios o no se seleccionó un funcionario.");
    }

    // 2. VERIFICACIÓN CRÍTICA: ¿Existe el funcionario en la tabla 'funcionarios'?
    $check = $pdo->prepare("SELECT Id_funcionario FROM funcionarios WHERE Id_funcionario = ?");
    $check->execute([$ID_Funcionario]);
    if (!$check->fetch()) {
        throw new Exception("El funcionario seleccionado (ID: $ID_Funcionario) no existe en el sistema.");
    }

    // 3. Procesar archivo (Documento_Formacion)
    $nombreArchivoRuta = null;
    if (isset($_FILES['Documento_Formacion']) && $_FILES['Documento_Formacion']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/formacion_academica/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileExt = strtolower(pathinfo($_FILES['Documento_Formacion']['name'], PATHINFO_EXTENSION));
        $newFileName = "form_" . $ID_Funcionario . "_" . time() . "." . $fileExt;
        
        if (move_uploaded_file($_FILES['Documento_Formacion']['tmp_name'], $uploadDir . $newFileName)) {
            $nombreArchivoRuta = $newFileName;
        }
    }

    // 4. Inserción Final
    $sql = "INSERT INTO tbl_formacion_academica 
            (ID_Funcionario, Titulo_Obtenido, Institucion_Educativa, Fecha_Graduacion, Nivel_Educativo, Documento_Formacion, ID_Usuario_Creador) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $ID_Funcionario,
        $Titulo_Obtenido,
        $Institucion_Educativa,
        $Fecha_Graduacion,
        $Nivel_Educativo,
        $nombreArchivoRuta,
        $ID_Usuario_Creador
    ]);

    $_SESSION['exito'] = "Formación académica registrada con éxito.";

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error de Base de Datos: " . $e->getMessage();
}

header("Location: ../administrador/formacion_academica.php");
exit;