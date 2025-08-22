<?php
session_start(); // Iniciar sesión para guardar mensajes
include_once '../includes/conexion.php';

$pdo = new PDO($dsn, $user, $pass, $options);

// Verificar que se hayan recibido los datos
if (
    !isset($_POST['ID_Funcionario']) || empty($_POST['ID_Funcionario']) ||
    !isset($_POST['Titulo']) || trim($_POST['Titulo']) === '' ||
    !isset($_POST['Mensaje']) || trim($_POST['Mensaje']) === ''
) {
    $_SESSION['error'] = "Debe seleccionar un funcionario y completar todos los campos.";
    header("Location: ../administrador/instrucciones_diarias.php");
    exit;
}

$idFuncionario = intval($_POST['ID_Funcionario']);
$titulo = trim($_POST['Titulo']);
$mensaje = trim($_POST['Mensaje']);

try {
    $sql = "INSERT INTO tbl_instrucciones (ID_Funcionario, Titulo, Mensaje) 
            VALUES (:ID_Funcionario, :Titulo, :Mensaje)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ID_Funcionario' => $idFuncionario,
        ':Titulo' => $titulo,
        ':Mensaje' => $mensaje
    ]);

    // Guardar mensaje de éxito en sesión y redirigir
    $_SESSION['exito'] = "Instrucción registrada correctamente.";
    header("Location: ../administrador/instrucciones_diarias.php");
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = "Error en la base de datos: " . $e->getMessage();
    header("Location: ../administrador/instrucciones_diarias.php");
    exit;
}
