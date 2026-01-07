<?php
session_start();
include_once '../includes/conexion.php';

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // 1. Verificar sesión
    if (!isset($_SESSION['ID_Usuario'])) {
        $_SESSION['error'] = "Sesión no encontrada. Inicie sesión nuevamente.";
        header("Location: ../index.php");
        exit;
    }

    $usuarioCreador = $_SESSION['ID_Usuario'];

    // 2. DEPUREMOS: Verificar si el ID del usuario realmente existe en la DB
    $checkUser = $pdo->prepare("SELECT COUNT(*) FROM tbl_usuarios WHERE ID_Usuario = ?");
    $checkUser->execute([$usuarioCreador]);
    if ($checkUser->fetchColumn() == 0) {
        // Si entra aquí, el ID en la sesión (ej: 5) no existe en tbl_usuarios
        $_SESSION['error'] = "Error crítico: El ID de usuario ($usuarioCreador) no existe en el sistema. Vuelva a loguearse.";
        header("Location: ../administrador/instrucciones_diarias.php");
        exit;
    }

    // 3. Verificar datos del formulario
    if (empty($_POST['ID_Funcionario']) || empty($_POST['Titulo']) || empty($_POST['Mensaje'])) {
        $_SESSION['error'] = "Faltan datos obligatorios.";
        header("Location: ../administrador/instrucciones_diarias.php");
        exit;
    }

    $idFuncionario = intval($_POST['ID_Funcionario']);
    $titulo = trim($_POST['Titulo']);
    $mensaje = trim($_POST['Mensaje']);

    // 4. Inserción Final
    $sql = "INSERT INTO tbl_instrucciones (ID_Funcionario, Titulo, Mensaje, Usuario_creador, Fecha_Envio) 
            VALUES (:ID_Funcionario, :Titulo, :Mensaje, :Usuario_creador, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ID_Funcionario'  => $idFuncionario,
        ':Titulo'          => $titulo,
        ':Mensaje'         => $mensaje,
        ':Usuario_creador' => $usuarioCreador
    ]);

    $_SESSION['exito'] = "Instrucción registrada correctamente.";
    header("Location: ../administrador/instrucciones_diarias.php");
    exit;

} catch (PDOException $e) {
    // Si falla aquí, imprimiremos el error real de SQL para saber qué columna falla
    $_SESSION['error'] = "Error SQL: " . $e->getMessage();
    header("Location: ../administrador/instrucciones_diarias.php");
    exit;
}