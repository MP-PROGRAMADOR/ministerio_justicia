<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include_once '../includes/conexion.php';

$pdo = new PDO($dsn, $user, $pass, $options);

// Validar que se reciban los datos necesarios
if (
    !isset($_POST['ID_Instruccion']) || empty($_POST['ID_Instruccion']) ||
    !isset($_POST['Titulo']) || trim($_POST['Titulo']) === '' ||
    !isset($_POST['Mensaje']) || trim($_POST['Mensaje']) === '' ||
    !isset($_POST['Leido']) || ($_POST['Leido'] !== '0' && $_POST['Leido'] !== '1')
) {
    $_SESSION['error'] = "Debe completar todos los campos correctamente.";
    header("Location: ../administrador/instrucciones_diarias.php");
    exit;
}

$idInstruccion = intval($_POST['ID_Instruccion']);
$titulo = trim($_POST['Titulo']);
$mensaje = trim($_POST['Mensaje']);
$leido = intval($_POST['Leido']);

try {
    $sql = "UPDATE tbl_instrucciones
            SET Titulo = :Titulo,
                Mensaje = :Mensaje,
                Leido = :Leido
            WHERE ID_Instruccion = :ID_Instruccion";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':Titulo' => $titulo,
        ':Mensaje' => $mensaje,
        ':Leido' => $leido,
        ':ID_Instruccion' => $idInstruccion
    ]);

    $_SESSION['exito'] = "Instrucción actualizada correctamente.";
    header("Location: ../administrador/instrucciones_diarias.php");
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = "Error al actualizar la instrucción: " . $e->getMessage();
    header("Location: ../administrador/instrucciones_diarias.php");
    exit;
}
