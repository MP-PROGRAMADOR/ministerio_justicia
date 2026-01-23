<?php
session_start();
require_once '../includes/conexion.php';

// Verificamos sesión y datos recibidos
if(isset($_POST['id']) && isset($_SESSION['ID_Funcionario'])){
    $id = intval($_POST['id']);
    $ID_Funcionario = $_SESSION['ID_Funcionario'];

    try {
        // Asegurar que usamos la conexión PDO
        if (!isset($pdo)) {
            $pdo = new PDO($dsn, $user, $pass, $options);
        }

        // ACTUALIZACIÓN: Marcamos leído, grabamos la hora y cambiamos el estado
        $stmt = $pdo->prepare("UPDATE tbl_instrucciones 
                               SET Leido = 1, 
                                   Fecha_Lectura = NOW(), 
                                   Estado = IF(Estado = 'PENDIENTE', 'EN-PROCESO', Estado)
                               WHERE ID_Instruccion = :id 
                               AND ID_Funcionario = :funcionario 
                               AND Leido = 0");
        
        $stmt->execute(['id' => $id, 'funcionario' => $ID_Funcionario]);
        
        echo json_encode(["status" => "success", "message" => "Lectura registrada"]);

    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>