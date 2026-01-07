<?php
session_start();
include_once '../includes/conexion.php';

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    $_SESSION['error'] = "Error de conexión a la base de datos.";
    header("Location: ../funcionario/panel_funcionario.php");
    exit;
}

/* ===============================
   VALIDAR DATOS POST
================================= */
if (!isset($_POST['tipo'], $_POST['message'], $_POST['id_funcionario'])) {
    $_SESSION['error'] = "Datos incompletos.";
    header("Location: ../funcionario/panel_funcionario.php");
    exit;
}

$tipo = $_POST['tipo']; // queja | sugerencia
$mensaje = trim($_POST['message']);
$idFuncionario = (int) $_POST['id_funcionario'];
$anonimo = (isset($_POST['anonimo']) && $_POST['anonimo'] == '1') ? 1 : 0;

/* ===============================
   VALIDACIONES
================================= */
if (!in_array($tipo, ['queja', 'sugerencia'])) {
    $_SESSION['error'] = "Tipo de mensaje inválido.";
    header("Location: ../funcionario/panel_funcionario.php");
    exit;
}

if ($mensaje === '') {
    $_SESSION['error'] = "El mensaje no puede estar vacío.";
    header("Location: ../funcionario/panel_funcionario.php");
    exit;
}

if ($idFuncionario <= 0) {
    $_SESSION['error'] = "Funcionario no válido.";
    header("Location: ../funcionario/panel_funcionario.php");
    exit;
}

try {
    /* ===============================
       INSERTAR QUEJA / SUGERENCIA
    ================================= */
    $stmtInsert = $pdo->prepare("
        INSERT INTO tbl_quejas_sugerencias 
        (ID_Funcionario, Tipo, Mensaje, Anonimo)
        VALUES (:id_funcionario, :tipo, :mensaje, :anonimo)
    ");

    $stmtInsert->execute([
        ':id_funcionario' => $idFuncionario,
        ':tipo' => $tipo,
        ':mensaje' => $mensaje,
        ':anonimo' => $anonimo
    ]);

    /* ===============================
       REDIRECCIÓN FINAL
    ================================= */
    $_SESSION['exito'] = "Mensaje enviado correctamente.";
    header("Location: ../funcionario/panel_funcionario.php");
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = "Error en la base de datos: " . $e->getMessage();
    header("Location: ../funcionario/panel_funcionario.php");
    exit;
}
