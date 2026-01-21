<?php
session_start();
include_once '../includes/conexion.php';
$pdo = new PDO($dsn, $user, $pass, $options);

if(isset($_POST['id']) && isset($_SESSION['ID_Funcionario'])){
    $id = intval($_POST['id']);
    $ID_Funcionario = $_SESSION['ID_Funcionario'];

    // Se añade Fecha_Lectura = NOW() para que el sistema grabe la hora exacta de la apertura
    $stmt = $pdo->prepare("UPDATE tbl_instrucciones 
                           SET Leido = 1, Fecha_Lectura = NOW() 
                           WHERE ID_Instruccion = :id 
                           AND ID_Funcionario = :funcionario 
                           AND Leido = 0"); // Solo actualiza si no estaba leída
    $stmt->execute(['id' => $id, 'funcionario' => $ID_Funcionario]);
}
?>