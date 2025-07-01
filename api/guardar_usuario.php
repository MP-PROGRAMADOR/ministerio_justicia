<?php


include_once '../conexion/conexion.php';

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    exit("Error al conectar con la base de datos: " . $e->getMessage());
}

function cleanInput($data) {
    return trim(htmlspecialchars($data, ENT_QUOTES, 'UTF-8'));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Método HTTP no permitido.");
}

// Recoger y sanitizar entradas
$nombreUsuario = isset($_POST['Nombre_Usuario']) ? cleanInput($_POST['Nombre_Usuario']) : '';
$contrasena    = $_POST['Contrasena_Hash'] ?? '';
$emailContacto = isset($_POST['Email_Contacto']) ? cleanInput($_POST['Email_Contacto']) : '';
$rolUsuario    = isset($_POST['Rol_Usuario']) ? cleanInput($_POST['Rol_Usuario']) : '';
$activo        = isset($_POST['Activo']) ? 1 : 0;

$errors = [];

// Validaciones
if (empty($nombreUsuario)) {
    $errors[] = "El nombre de usuario es obligatorio.";
} elseif (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $nombreUsuario)) {
    $errors[] = "El nombre de usuario solo puede contener letras, números y guiones bajos, entre 3 y 50 caracteres.";
}

if (empty($contrasena)) {
    $errors[] = "La contraseña es obligatoria.";
} elseif (strlen($contrasena) < 8) {
    $errors[] = "La contraseña debe tener al menos 8 caracteres.";
}

if (empty($emailContacto)) {
    $errors[] = "El email de contacto es obligatorio.";
} elseif (!filter_var($emailContacto, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "El email no tiene un formato válido.";
}

$rolesValidos = ['Administrador', 'Recursos Humanos', 'Consulta', 'Auditor'];
if (!in_array($rolUsuario, $rolesValidos, true)) {
    $errors[] = "El rol de usuario no es válido.";
}

if ($errors) {
    // Guardar errores y datos para repoblar el formulario
    $_SESSION['errores_formulario'] = $errors;
    $_SESSION['datos_formulario'] = [
        'Nombre_Usuario' => $nombreUsuario,
        'Email_Contacto' => $emailContacto,
        'Rol_Usuario' => $rolUsuario,
        'Activo' => $activo,
    ];

    // Cambia esta URL por la de tu formulario
    header("Location: ../administrador/formulario_usuario.php");
    exit;
}

// Verificar que no exista usuario ni email duplicado
$stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM tbl_Usuarios WHERE Nombre_Usuario = :nombre OR Email_Contacto = :email");
$stmtCheck->execute(['nombre' => $nombreUsuario, 'email' => $emailContacto]);
$existe = $stmtCheck->fetchColumn();

if ($existe > 0) {
    $_SESSION['errores_formulario'] = ["El nombre de usuario o el email ya están registrados."];
    $_SESSION['datos_formulario'] = [
        'Nombre_Usuario' => $nombreUsuario,
        'Email_Contacto' => $emailContacto,
        'Rol_Usuario' => $rolUsuario,
        'Activo' => $activo,
    ];
    header("Location: ../administrador/formulario_usuario.php");
    exit;
}

// Hashear contraseña
$hashContrasena = password_hash($contrasena, PASSWORD_DEFAULT);

// Insertar nuevo usuario
$stmtInsert = $pdo->prepare("INSERT INTO tbl_Usuarios (Nombre_Usuario, Contrasena_Hash, Rol_Usuario, Email_Contacto, Activo) VALUES (:nombre, :contrasena, :rol, :email, :activo)");

try {
    $stmtInsert->execute([
        ':nombre' => $nombreUsuario,
        ':contrasena' => $hashContrasena,
        ':rol' => $rolUsuario,
        ':email' => $emailContacto,
        ':activo' => $activo,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit("Error al guardar el usuario: " . $e->getMessage());
}

// Éxito: guardar mensaje y redirigir a lista
$_SESSION['mensaje_exito'] = "Usuario guardado correctamente.";
header("Location: ../administrador/usuarios.php");
exit;



?>