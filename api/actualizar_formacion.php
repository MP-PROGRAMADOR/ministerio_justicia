<?php
session_start();
require_once '../includes/conexion.php';

if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada. Inicia sesión nuevamente.";
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        $idFormacion = $_POST['ID_Formacion'] ?? null;
        $titulo = trim($_POST['Titulo_Obtenido'] ?? '');
        $institucion = trim($_POST['Institucion_Educativa'] ?? '');
        $fechaGraduacion = $_POST['Fecha_Graduacion'] ?? null;
        $nivel = $_POST['Nivel_Educativo'] ?? '';
        $idUsuario = $_SESSION['ID_Usuario'];

        if (!$idFormacion || !$titulo || !$institucion || !$nivel) {
            $_SESSION['error'] = "Todos los campos obligatorios deben completarse.";
            header("Location: ../administrador/formacion_academica.php");
            exit;
        }

        $sql = "UPDATE tbl_formacion_academica 
                SET Titulo_Obtenido = :titulo,
                    Institucion_Educativa = :institucion,
                    Fecha_Graduacion = :fecha,
                    Nivel_Educativo = :nivel,
                    ID_Usuario_Ultima_Modificacion = :usuario
                WHERE ID_Formacion = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':titulo' => $titulo,
            ':institucion' => $institucion,
            ':fecha' => $fechaGraduacion ?: null,
            ':nivel' => $nivel,
            ':usuario' => $idUsuario,
            ':id' => $idFormacion
        ]);

        $_SESSION['exito'] = "Formación académica actualizada correctamente.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar: " . $e->getMessage();
    }

    header("Location: ../administrador/formacion_academica.php");
    exit;
} else {
    $_SESSION['error'] = "Acceso inválido.";
    header("Location: ../administrador/formacion_academica.php");
    exit;
}
