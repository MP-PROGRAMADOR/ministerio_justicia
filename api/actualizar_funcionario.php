<?php
session_start();
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

// Validar edad
if ($fechaNacimiento) {
    $edad = date_diff(date_create($fechaNacimiento), date_create('today'))->y;
    if ($edad < 18) {
        $_SESSION['error'] = "El funcionario debe tener al menos 18 años.";
        header("Location: ../administrador/funcionarios.php");
        exit;
    }
}

// Obtener foto actual
$sqlFoto = "SELECT Fotografia FROM tbl_funcionarios WHERE ID_Funcionario = ?";
$stmtFoto = $pdo->prepare($sqlFoto);
$stmtFoto->execute([$idFuncionario]);
$fotoActual = $stmtFoto->fetchColumn();
$fotoNombre = $fotoActual;

// Si se sube una nueva foto
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
        $_SESSION['error'] = "Error al subir la nueva fotografía.";
        header("Location: ../administrador/funcionarios.php");
        exit;
    }
}

try {
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

    // Solo añadir la foto si se subió nueva
    if ($fotoNombre !== $fotoActual) {
        $sql .= ", Fotografia = :foto";
    }

    $sql .= " WHERE ID_Funcionario = :id";

    $stmt = $pdo->prepare($sql);

    $params = [
        ':nombres'            => $nombres,
        ':apellidos'          => $apellidos,
        ':dni'                => $dni,
        ':fecha_nac'          => $fechaNacimiento ?: null,
        ':genero'             => $genero ?: null,
        ':nacionalidad'       => $nacionalidad ?: null,
        ':direccion'          => $direccion ?: null,
        ':telefono'           => $telefono ?: null,
        ':email'              => $email ?: null,
        ':fecha_ingreso'      => $fechaIngreso,
        ':estado'             => $estadoLaboral,
        ':usuario_modificador'=> $_SESSION['ID_Usuario'],
        ':id'                 => $idFuncionario
    ];

    if ($fotoNombre !== $fotoActual) {
        $params[':foto'] = $fotoNombre;
    }

    $stmt->execute($params);

    $_SESSION['exito'] = "Funcionario actualizado correctamente.";
    header("Location: ../administrador/funcionarios.php");
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = "Error al actualizar: " . $e->getMessage();
    header("Location: ../administrador/funcionarios.php");
    exit;
}
