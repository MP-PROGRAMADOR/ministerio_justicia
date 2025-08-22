<?php
session_start();
require_once '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Recibir y limpiar datos
    $nombre = trim($_POST['Nombre_Curso'] ?? '');
    $descripcion = trim($_POST['Descripcion'] ?? '');
    $fecha_inicio = $_POST['Fecha_Inicio'] ?? '';
    $fecha_fin = $_POST['Fecha_Fin'] ?? '';
    $cupo = (int) ($_POST['Cupo'] ?? 0);

    // Calcular estado según las fechas
    $hoy = date('Y-m-d');
    if ($fecha_fin < $hoy) {
        $estado = 'Finalizado';
    } elseif ($fecha_inicio > $hoy) {
        $estado = 'Próximo';
    } else {
        $estado = 'En curso';
    }

    // Validar campos obligatorios
    if (empty($nombre) || empty($descripcion) || empty($fecha_inicio) || empty($fecha_fin)) {
        $_SESSION['error'] = "Todos los campos obligatorios deben completarse.";
        header("Location: ../administrador/cursos_ministerio.php");
        exit;
    }

    try {
        // Insertar en la BD
        $sql = "INSERT INTO tbl_cursos (Nombre_Curso, Descripcion, Fecha_Inicio, Fecha_Fin, Cupo)
                VALUES (:nombre, :descripcion, :fecha_inicio, :fecha_fin, :cupo)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin,
            ':cupo' => $cupo
        ]);

        $_SESSION['exito'] = "Curso registrado correctamente. Estado: $estado";
        header("Location: ../administrador/cursos_ministerio.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al registrar el curso: " . $e->getMessage();
        header("Location: ../administrador/cursos_ministerio.php");
        exit;
    }

} else {
    $_SESSION['error'] = "Solicitud inválida.";
    header("Location: ../administrador/cursos_ministerio.php");
    exit;
}
