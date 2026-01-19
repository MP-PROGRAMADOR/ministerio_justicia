<?php
session_start();
require_once '../includes/conexion.php';

// Verificación de sesión
if (!isset($_SESSION['ID_Usuario'])) {
    header("Content-Type: application/json"); // Opcional si es llamada AJAX
    $_SESSION['error'] = "Sesión expirada.";
    header("Location: ../index.php");
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // 1. Recibir y limpiar datos
    $idFuncionario = isset($_POST['ID_Funcionario']) ? intval($_POST['ID_Funcionario']) : 0;
    $nombreCurso   = trim($_POST['Nombre_Curso'] ?? '');
    $institucion   = trim($_POST['Institucion_Organizadora'] ?? '');
    $fechaInicio   = !empty($_POST['Fecha_Inicio_Curso']) ? $_POST['Fecha_Inicio_Curso'] : null;
    $fechaFin      = !empty($_POST['Fecha_Fin_Curso']) ? $_POST['Fecha_Fin_Curso'] : null;
    $idUsuarioCreador = $_SESSION['ID_Usuario'];

    // 2. Validaciones de servidor
    if ($idFuncionario <= 0) {
        throw new Exception("Error: No se ha seleccionado un funcionario válido de la lista.");
    }
    if (empty($nombreCurso) || empty($institucion)) {
        throw new Exception("El nombre del curso y la institución son obligatorios.");
    }

    // 3. Verificación de existencia (Nombre de columna: Id_funcionario)
    $check = $pdo->prepare("SELECT Id_funcionario FROM funcionarios WHERE Id_funcionario = ?");
    $check->execute([$idFuncionario]);
    if (!$check->fetch()) {
        throw new Exception("El funcionario seleccionado ya no existe en la base de datos.");
    }

    // 4. Subida de Certificado
    $certificadoURL = null;
    if (!empty($_FILES['Certificado_URL']['name']) && $_FILES['Certificado_URL']['error'] === 0) {
        $directorio = '../certificados/';
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES['Certificado_URL']['name'], PATHINFO_EXTENSION));
        $nombreArchivo = 'cert_' . uniqid() . '.' . $extension;
        $rutaDestino = $directorio . $nombreArchivo;

        if (move_uploaded_file($_FILES['Certificado_URL']['tmp_name'], $rutaDestino)) {
            $certificadoURL = 'certificados/' . $nombreArchivo;
        }
    }

    // 5. Inserción en tbl_capacitaciones
    $sql = "INSERT INTO tbl_capacitaciones (
                ID_Funcionario, 
                Nombre_Curso, 
                Institucion_Organizadora, 
                Fecha_Inicio_Curso, 
                Fecha_Fin_Curso, 
                Certificado_URL, 
                ID_Usuario_Creador
            ) VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $idFuncionario,
        $nombreCurso,
        $institucion,
        $fechaInicio,
        $fechaFin,
        $certificadoURL,
        $idUsuarioCreador
    ]);

    $_SESSION['exito'] = "Capacitación guardada correctamente.";

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error técnico de base de datos.";
    // En desarrollo puedes usar: $_SESSION['error'] = $e->getMessage();
}

header("Location: ../administrador/capacitaciones.php");
exit;