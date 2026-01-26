<?php
session_start();
require_once '../includes/conexion.php';

// Verificar que se haya enviado el ID del curso
$pdo = new PDO($dsn, $user, $pass, $options);
if (!isset($_POST['ID_Curso'], $_POST['Nombre_Curso'], $_POST['Descripcion'], $_POST['Fecha_Inicio'], $_POST['Fecha_Fin'], $_POST['Cupo'])) {
    $_SESSION['error'] = "Todos los campos obligatorios deben completarse.";
    header("Location: ../administrador/cursos_ministerio.php");
    exit;
}

// Obtener datos del formulario
$idCurso      = $_POST['ID_Curso'];
$nombreCurso  = trim($_POST['Nombre_Curso']);
$descripcion  = trim($_POST['Descripcion']);
$fechaInicio  = $_POST['Fecha_Inicio'];
$fechaFin     = $_POST['Fecha_Fin'];
$cupo         = intval($_POST['Cupo']);

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Validación simple: la fecha de inicio no puede ser mayor a la fecha fin
    if ($fechaInicio > $fechaFin) {
        $_SESSION['error'] = "La fecha de inicio no puede ser mayor que la fecha de fin.";
        header("Location: ../administrador/cursos_ministerio.php");
        exit;
    }

    // Actualizar curso
    $sql = "UPDATE tbl_cursos SET 
                Nombre_Curso = :nombre,
                Descripcion = :descripcion,
                Fecha_Inicio = :inicio,
                Fecha_Fin = :fin,
                Cupo = :cupo
            WHERE ID_Curso = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre'      => $nombreCurso,
        ':descripcion' => $descripcion,
        ':inicio'      => $fechaInicio,
        ':fin'         => $fechaFin,
        ':cupo'        => $cupo,
        ':id'          => $idCurso
    ]);

    $_SESSION['exito'] = "Curso actualizado correctamente.";
    header("Location: ../administrador/cursos_ministerio.php");
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = "Error al actualizar el curso: " . $e->getMessage();
    header("Location: ../administrador/cursos_ministerio.php");
    exit;
}
?>
