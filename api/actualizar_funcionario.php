<?php
session_start();
// Asegúrate de que este archivo 'conexion.php' contenga la configuración de $dsn, $user, $pass, $options.
require_once '../includes/conexion.php'; 

if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Vuelve a iniciar sesión.";
    header("Location: ../administrador/funcionarios.php");
    exit;
}

$pdo = new PDO($dsn, $user, $pass, $options);

// Recoger datos
$idFuncionario   = $_POST['ID_Funcionario'] ?? null;
$nombres         = trim($_POST['Nombres'] ?? '');
$apellidos       = trim($_POST['Apellidos'] ?? '');
$dni             = trim($_POST['DNI_Pasaporte'] ?? '');
$fechaNacimiento = $_POST['Fecha_Nacimiento'] ?? null;
$genero          = $_POST['Genero'] ?? null;
$nacionalidad    = trim($_POST['Nacionalidad'] ?? '');
$direccion       = trim($_POST['Direccion_Residencia'] ?? '');
$telefono        = trim($_POST['Telefono_Contacto'] ?? '');
$email           = trim($_POST['Email_Oficial'] ?? '');
$fechaIngreso    = $_POST['Fecha_Ingreso'] ?? null;
$estadoLaboral   = $_POST['Estado_Laboral'] ?? 'Activo';

// Validación y Sanitización Básica (se omiten validaciones de campo vacíos para mantener el foco en la foto)
if (empty($idFuncionario) || !is_numeric($idFuncionario)) {
    $_SESSION['error'] = "ID de funcionario inválido.";
    header("Location: ../administrador/funcionarios.php");
    exit;
}

// Validar edad (Bloque sin cambios)
if ($fechaNacimiento) {
    $edad = date_diff(date_create($fechaNacimiento), date_create('today'))->y;
    if ($edad < 18) {
        $_SESSION['error'] = "El funcionario debe tener al menos 18 años.";
        header("Location: ../administrador/funcionarios.php");
        exit;
    }
}

// 1. Obtener foto actual de la base de datos
$sqlFoto = "SELECT Fotografia FROM tbl_funcionarios WHERE ID_Funcionario = ?";
$stmtFoto = $pdo->prepare($sqlFoto);
$stmtFoto->execute([$idFuncionario]);
$fotoActual = $stmtFoto->fetchColumn(); // Ruta relativa almacenada en la DB (e.g., 'funcionarios/func_xyz.png')
$fotoNombre = $fotoActual; // Inicialmente, mantenemos la foto actual

$nuevaFotoSubida = false;
$directorio_base = realpath(__DIR__ . '/..'); // Define la ruta base del proyecto
$directorio_fotos = $directorio_base . '/api/funcionarios'; // Asumiendo que las fotos se guardan en '/api/funcionarios'

// 2. Si se sube una nueva foto
if (isset($_FILES['Fotografia']) && $_FILES['Fotografia']['error'] === UPLOAD_ERR_OK) {
    
    // Crear el directorio si no existe
    if (!is_dir($directorio_fotos)) {
        mkdir($directorio_fotos, 0777, true);
    }

    $extension = pathinfo($_FILES['Fotografia']['name'], PATHINFO_EXTENSION);
    $nombreArchivo = uniqid('func_') . '.' . strtolower($extension);
    $rutaCompletaDestino = $directorio_fotos . '/' . $nombreArchivo;

    if (move_uploaded_file($_FILES['Fotografia']['tmp_name'], $rutaCompletaDestino)) {
        // La subida fue exitosa
        $fotoNombre = 'funcionarios/' . $nombreArchivo; // Ruta relativa para la DB
        $nuevaFotoSubida = true;
        
        // --- LÓGICA DE ELIMINACIÓN DE LA FOTO ANTERIOR ---
        if ($fotoActual && $fotoActual !== $fotoNombre) {
            // Construye la ruta completa de la foto antigua
            $rutaAntiguaCompleta = $directorio_base . '/api/' . $fotoActual; 
            
            // Verifica que el archivo exista y no sea un directorio antes de eliminarlo
            if (file_exists($rutaAntiguaCompleta) && !is_dir($rutaAntiguaCompleta)) {
                if (!unlink($rutaAntiguaCompleta)) {
                    // Opcional: Registrar un error si la eliminación falla, pero permitir la actualización de la DB
                    error_log("Fallo al eliminar la foto antigua: " . $rutaAntiguaCompleta);
                }
            }
        }
        // --- FIN LÓGICA DE ELIMINACIÓN ---

    } else {
        $_SESSION['error'] = "Error al subir la nueva fotografía.";
        header("Location: ../administrador/funcionarios.php");
        exit;
    }
}

try {
    // 3. Preparar la consulta de ACTUALIZACIÓN
    $sql = "UPDATE tbl_funcionarios SET
                Nombres = :nombres,
                Apellidos = :apellidos,
                DNI_Pasaporte = :dni,
                Fecha_Nacimiento = :fecha_nac,
                Genero = :genero,
                Nacionalidad = :nacionalidad,
                Direccion_Residencia = :direccion,
                Telefono_Contacto = :telefono,
                Email_Oficial = :email,
                Fecha_Ingreso = :fecha_ingreso,
                Estado_Laboral = :estado,
                ID_Usuario_Ultima_Modificacion = :usuario_modificador,
                Fecha_Ultima_Modificacion = NOW()";

    // Solo añadir el campo Fotografia si se subió una nueva
    if ($nuevaFotoSubida) {
        $sql .= ", Fotografia = :foto";
    }

    $sql .= " WHERE ID_Funcionario = :id";

    $stmt = $pdo->prepare($sql);

    // 4. Parámetros para la ejecución
    $params = [
        ':nombres'              => $nombres,
        ':apellidos'            => $apellidos,
        ':dni'                  => $dni,
        ':fecha_nac'            => $fechaNacimiento ?: null,
        ':genero'               => $genero ?: null,
        ':nacionalidad'         => $nacionalidad ?: null,
        ':direccion'            => $direccion ?: null,
        ':telefono'             => $telefono ?: null,
        ':email'                => $email ?: null,
        ':fecha_ingreso'        => $fechaIngreso,
        ':estado'               => $estadoLaboral,
        ':usuario_modificador'  => $_SESSION['ID_Usuario'],
        ':id'                   => $idFuncionario
    ];

    if ($nuevaFotoSubida) {
        $params[':foto'] = $fotoNombre;
    }

    $stmt->execute($params);

    // 5. Redirección de éxito
    $_SESSION['exito'] = "Funcionario actualizado correctamente.";
    header("Location: ../administrador/funcionarios.php");
    exit;

} catch (PDOException $e) {
    // En caso de error de DB, intentar eliminar la foto recién subida para limpiar
    if ($nuevaFotoSubida && file_exists($rutaCompletaDestino)) {
        unlink($rutaCompletaDestino);
    }
    
    $_SESSION['error'] = "Error al actualizar: " . $e->getMessage();
    header("Location: ../administrador/funcionarios.php");
    exit;
}
?>
