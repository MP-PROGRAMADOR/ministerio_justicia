<?php
session_start();
include_once '../includes/conexion.php';
$pdo = new PDO($dsn, $user, $pass, $options);

if(isset($_POST['id']) && isset($_SESSION['ID_Funcionario'])){
    $id = intval($_POST['id']);
    $ID_Funcionario = $_SESSION['ID_Funcionario'];

    $stmt = $pdo->prepare("UPDATE tbl_instrucciones SET Leido = 1 WHERE ID_Instruccion = :id AND ID_Funcionario = :funcionario");
    $stmt->execute(['id' => $id, 'funcionario' => $ID_Funcionario]);
}
?>
