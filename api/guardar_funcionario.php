<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../includes/conexion.php';
$pdo = new PDO($dsn, $user, $pass, $options);

if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Vuelve a iniciar sesión.";
    header("Location: ../index.php");
    exit;
}

/* ===============================
   FUNCIÓN GENERAR CÓDIGO
================================ */
function generarCodigo($nombre) {
    $prefijo = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $nombre), 0, 3));
    $sufijo = date('Y') . substr(md5(uniqid()), 0, 4);
    return $prefijo . '-' . $sufijo;
}

/* ===============================
   CAMPOS OBLIGATORIOS
================================ */
$camposObligatorios = [
    'Nombre','Apellidos','Dip_Pasaporte','Sexo','Fecha_nacimiento',
    'Lugar_nacimiento','Nacionalidad','Telefono','Domicilio',
    'Fecha_nombramiento','Fecha_posesion',
    'Id_seccion','Id_categoria','Id_cargo',
    'Profesion','Maximo_nivel_estudios','Titulacion_academica',
    'Universidad_centro_formacion','Fecha_graduacion','Estado_Laboral'
];

foreach ($camposObligatorios as $campo) {
    if (empty($_POST[$campo])) {
        $_SESSION['error'] = "El campo '$campo' es obligatorio.";
        header("Location: ../administrador/funcionarios.php");
        exit;
    }
}

/* ===============================
   VALIDAR EDAD
================================ */
$edad = date_diff(date_create($_POST['Fecha_nacimiento']), date_create('today'))->y;
if ($edad < 18) {
    $_SESSION['error'] = "El funcionario debe tener al menos 18 años.";
    header("Location: ../administrador/funcionarios.php");
    exit;
}

/* ===============================
   SUBIDA DE ARCHIVOS
================================ */
$archivosObligatorios = [
    'Foto','Dip_pass_copia','Copia_doc_nomb',
    'Copia_carnet_func','Copia_doc_tom_posesion','Copia_doc_academicos'
];

$archivosOpcionales = ['Doc_Estado_Adjunto'];
$archivosGuardados = [];

$directorio = __DIR__ . '/funcionarios';
if (!is_dir($directorio)) mkdir($directorio, 0777, true);

function subirArchivo($campo, $directorio) {
    if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $ext = strtolower(pathinfo($_FILES[$campo]['name'], PATHINFO_EXTENSION));
    $nombre = uniqid($campo.'_') . '.' . $ext;
    move_uploaded_file($_FILES[$campo]['tmp_name'], "$directorio/$nombre");
    return 'funcionarios/' . $nombre;
}

foreach ($archivosObligatorios as $file) {
    $ruta = subirArchivo($file, $directorio);
    if (!$ruta) {
        $_SESSION['error'] = "El archivo '$file' es obligatorio.";
        header("Location: ../administrador/funcionarios.php");
        exit;
    }
    $archivosGuardados[$file] = $ruta;
}

foreach ($archivosOpcionales as $file) {
    $archivosGuardados[$file] = subirArchivo($file, $directorio);
}

/* ===============================
   INSERTAR EN BD
================================ */
try {
    $codigo = generarCodigo($_POST['Nombre']);

    $sql = "INSERT INTO funcionarios (
        CODIGO, Nombre, Apellidos, Dip_Pasaporte, Sexo, Fecha_nacimiento,
        Lugar_nacimiento, Tribu, Pueblo, Distrito, Provincia,
        Nacionalidad, Telefono, Correo, Domicilio, Num_carnet_fun,
        Fecha_nombramiento, Fecha_posesion,
        Id_seccion, Id_categoria, Id_cargo,
        Profesion, Maximo_nivel_estudios, Titulacion_academica,
        Universidad_centro_formacion, Fecha_graduacion,
        Estado_Laboral, Doc_Estado_Adjunto,
        Foto, Dip_pass_copia, Copia_doc_nomb,
        Copia_carnet_func, Copia_doc_tom_posesion, Copia_doc_academicos,
        Usuario_creador
    ) VALUES (
        :CODIGO, :Nombre, :Apellidos, :Dip_Pasaporte, :Sexo, :Fecha_nacimiento,
        :Lugar_nacimiento, :Tribu, :Pueblo, :Distrito, :Provincia,
        :Nacionalidad, :Telefono, :Correo, :Domicilio, :Num_carnet_fun,
        :Fecha_nombramiento, :Fecha_posesion,
        :Id_seccion, :Id_categoria, :Id_cargo,
        :Profesion, :Maximo_nivel_estudios, :Titulacion_academica,
        :Universidad_centro_formacion, :Fecha_graduacion,
        :Estado_Laboral, :Doc_Estado_Adjunto,
        :Foto, :Dip_pass_copia, :Copia_doc_nomb,
        :Copia_carnet_func, :Copia_doc_tom_posesion, :Copia_doc_academicos,
        :Usuario_creador
    )";

    $stmt = $pdo->prepare($sql);

    $stmt->execute(array_merge(
        $_POST,
        $archivosGuardados,
        [
            'CODIGO' => $codigo,
            'Usuario_creador' => $_SESSION['ID_Usuario']
        ]
    ));

    $_SESSION['exito'] = "Funcionario registrado correctamente.";
    header("Location: ../administrador/funcionarios.php");
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = "Error en la base de datos: " . $e->getMessage();
    header("Location: ../administrador/funcionarios.php");
    exit;
}
