<?php
session_start();

// Mostrar errores durante desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexión PDO (ajusta tus variables)
require_once '../includes/conexion.php'; // aquí debe definirse $pdo correctamente
 $pdo = new PDO($dsn, $user, $pass, $options);

if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Vuelve a iniciar sesión.";
    header("Location: ../index.php");
    exit;
}

// FUNCIONES
function generarCodigo($nombre) {
    $prefijo = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $nombre), 0, 3));
    $sufijo = strtoupper(substr(md5(uniqid()), 0, 5));
    return $prefijo . $sufijo;
}

// CAPTURAR DATOS
$nombres       = trim($_POST['Nombres'] ?? '');
$apellidos     = trim($_POST['Apellidos'] ?? '');
$dni           = trim($_POST['DNI_Pasaporte'] ?? '');
$fechaNacimiento = $_POST['Fecha_Nacimiento'] ?? null;
$genero        = $_POST['Genero'] ?? null;
$nacionalidad  = trim($_POST['Nacionalidad'] ?? '');
$direccion     = trim($_POST['Direccion_Residencia'] ?? '');
$telefono      = trim($_POST['Telefono_Contacto'] ?? '');
$email         = trim($_POST['Email_Oficial'] ?? '');
$fechaIngreso  = $_POST['Fecha_Ingreso'] ?? null;
$estadoLaboral = $_POST['Estado_Laboral'] ?? 'Activo';
$fotoNombre    = null;

// VALIDACIÓN DE EDAD
if ($fechaNacimiento) {
    $edad = date_diff(date_create($fechaNacimiento), date_create('today'))->y;
    if ($edad < 18) {
        $_SESSION['error'] = "El funcionario debe tener al menos 18 años.";
        header("Location: ../administrador/funcionarios.php");
        exit;
    }
}

// FOTOGRAFÍA
if (isset($_FILES['Fotografia']) && $_FILES['Fotografia']['error'] === UPLOAD_ERR_OK) {
    $directorio = __DIR__ . '/funcionarios';
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $extension = pathinfo($_FILES['Fotografia']['name'], PATHINFO_EXTENSION);
    $nombreArchivo = uniqid('func_') . '.' . strtolower($extension);
    $rutaCompleta = $directorio . '/' . $nombreArchivo;

    if (move_uploaded_file($_FILES['Fotografia']['tmp_name'], $rutaCompleta)) {
        $fotoNombre = 'funcionarios/' . $nombreArchivo;
    } else {
        $_SESSION['error'] = "Error al subir la fotografía.";
        header("Location: ../administrador/funcionarios.php");
        exit;
    }
}

// GENERAR CÓDIGO
$codigoFuncionario = generarCodigo($nombres);

// GUARDAR EN BD
try {
    $sql = "INSERT INTO tbl_funcionarios (
                Codigo_Funcionario, Nombres, Apellidos, DNI_Pasaporte,
                Fecha_Nacimiento, Genero, Nacionalidad, Direccion_Residencia,
                Telefono_Contacto, Email_Oficial, Fecha_Ingreso,
                Estado_Laboral, Fotografia, ID_Usuario_Creador
            ) VALUES (
                :codigo, :nombres, :apellidos, :dni,
                :fecha_nac, :genero, :nacionalidad, :direccion,
                :telefono, :email, :fecha_ingreso,
                :estado, :foto, :usuario_id
            )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'codigo'        => $codigoFuncionario,
        'nombres'       => $nombres,
        'apellidos'     => $apellidos,
        'dni'           => $dni,
        'fecha_nac'     => $fechaNacimiento ?: null,
        'genero'        => $genero ?: null,
        'nacionalidad'  => $nacionalidad ?: null,
        'direccion'     => $direccion ?: null,
        'telefono'      => $telefono ?: null,
        'email'         => $email ?: null,
        'fecha_ingreso' => $fechaIngreso,
        'estado'        => $estadoLaboral,
        'foto'          => $fotoNombre,
        'usuario_id'    => $_SESSION['ID_Usuario']
    ]);

    $_SESSION['exito'] = "Funcionario registrado correctamente.";
    header("Location: ../administrador/funcionarios.php");
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = "Error en la base de datos: " . $e->getMessage();
    header("Location: ../administrador/funcionarios.php");
    exit;
}
