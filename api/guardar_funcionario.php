<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../includes/conexion.php'; // Debe definir $pdo correctamente

$pdo = new PDO($dsn, $user, $pass, $options);

if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Vuelve a iniciar sesión.";
    header("Location: ../index.php");
    exit;
}

// Función para generar código
function generarCodigo($nombre) {
    $prefijo = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $nombre), 0, 3));
    $sufijo = strtoupper(substr(md5(uniqid()), 0, 5));
    return $prefijo . $sufijo;
}

// Capturar POST
$campos = [
    'Nombre','Apellidos','Dip_Pasaporte','Sexo','Fecha_nacimiento','Lugar_nacimiento',
    'Nacionalidad','Telefono','Correo','Domicilio','Num_carnet_fun','Fecha_nombramiento',
    'Fecha_posesion','Id_seccion','Funcion','Id_categoria','Profesion','Maximo_nivel_estudios',
    'Titulacion_academica','Universidad_centro_formacion','Fecha_graduacion','Estado_Laboral'
];



foreach ($campos as $campo) {
    if (empty($_POST[$campo])) {
        $_SESSION['error'] = "El campo '$campo' es obligatorio.";
        header("Location: ../administrador/funcionarios.php");
        exit;
    }
}

// Validar fecha de nacimiento
$fechaNacimiento = $_POST['Fecha_nacimiento'];
if ($fechaNacimiento) {
    $edad = date_diff(date_create($fechaNacimiento), date_create('today'))->y;
    if ($edad < 18) {
        $_SESSION['error'] = "El funcionario debe tener al menos 18 años.";
        header("Location: ../administrador/funcionarios.php");
        exit;
    }
}

// Archivos
$archivos = ['Foto','Dip_pass_copia','Copia_doc_nomb','Copia_carnet_func','Copia_doc_tom_posesion','Copia_doc_academicos'];
$archivosGuardados = [];

foreach ($archivos as $file) {
    if (!isset($_FILES[$file]) || $_FILES[$file]['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = "El archivo '$file' es obligatorio.";
        header("Location: ../administrador/funcionarios.php");
        exit;
    }
    $dir = __DIR__ . '/funcionarios';
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    $ext = pathinfo($_FILES[$file]['name'], PATHINFO_EXTENSION);
    $nombreArchivo = uniqid($file.'_') . '.' . strtolower($ext);
    $rutaCompleta = $dir . '/' . $nombreArchivo;

    if (!move_uploaded_file($_FILES[$file]['tmp_name'], $rutaCompleta)) {
        $_SESSION['error'] = "Error al subir el archivo '$file'.";
        header("Location: ../administrador/funcionarios.php");
        exit;
    }
    $archivosGuardados[$file] = 'funcionarios/' . $nombreArchivo;
}

// Generar código
$codigoFuncionario = generarCodigo($_POST['Nombre']);

// Insert en BD
try {
    $sql = "INSERT INTO funcionarios (
        CODIGO, Nombre, Apellidos, Dip_Pasaporte, Sexo, Fecha_nacimiento, Lugar_nacimiento,
        Nacionalidad, Telefono, Correo, Domicilio, Num_carnet_fun,
        Fecha_nombramiento, Fecha_posesion, Id_seccion, Funcion, Id_categoria,
        Profesion, Maximo_nivel_estudios, Titulacion_academica, Universidad_centro_formacion,
        Fecha_graduacion, Estado_Laboral, Foto, Dip_pass_copia, Copia_doc_nomb,
        Copia_carnet_func, Copia_doc_tom_posesion, Copia_doc_academicos,
        Usuario_creador
    ) VALUES (
        :CODIGO, :Nombre, :Apellidos, :Dip_Pasaporte, :Sexo, :Fecha_nacimiento, :Lugar_nacimiento,
        :Nacionalidad, :Telefono, :Correo, :Domicilio, :Num_carnet_fun,
        :Fecha_nombramiento, :Fecha_posesion, :Id_seccion, :Funcion, :Id_categoria,
        :Profesion, :Maximo_nivel_estudios, :Titulacion_academica, :Universidad_centro_formacion,
        :Fecha_graduacion, :Estado_Laboral, :Foto, :Dip_pass_copia, :Copia_doc_nomb,
        :Copia_carnet_func, :Copia_doc_tom_posesion, :Copia_doc_academicos,
        :Usuario_creador
    )";

    $stmt = $pdo->prepare($sql);

    $stmt->execute(array_merge($_POST, $archivosGuardados, ['CODIGO'=>$codigoFuncionario, 'Usuario_creador'=>$_SESSION['ID_Usuario']]));

    $_SESSION['exito'] = "Funcionario registrado correctamente.";
    header("Location: ../administrador/funcionarios.php");
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = "Error en la base de datos: ".$e->getMessage();
    header("Location: ../administrador/funcionarios.php");
    exit;
}
