<?php

// 1. Iniciar la sesión para usar $_SESSION (mensajes flash)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Asegúrate de que este archivo incluye la configuración de conexión a la base de datos
require_once '../includes/conexion.php';

// URL de redirección a la página de gestión de cursos
$redireccion_url = '../administrador/cursos_ministerio.php';

// 3. Verificar el método de solicitud (debe ser POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método de solicitud no permitido.';
    header('Location: ' . $redireccion_url);
    exit;
}

// 4. Obtener y sanear el ID del curso (esperamos 'id_curso' por POST)
$cursoId = filter_input(INPUT_POST, 'id_curso', FILTER_VALIDATE_INT);

if (!$cursoId) {
    $_SESSION['error'] = 'ID de curso no válido. No se pudo procesar la solicitud.';
    header('Location: ' . $redireccion_url);
    exit;
}

$nombreCurso = null;

try {

    $pdo = new PDO($dsn, $user, $pass, $options);


    $sql_select_nombre = "SELECT Nombre_Curso FROM tbl_cursos WHERE ID_Curso = :id";
    $stmt_nombre = $pdo->prepare($sql_select_nombre);
    $stmt_nombre->bindParam(':id', $cursoId, PDO::PARAM_INT);
    $stmt_nombre->execute();

    $resultado = $stmt_nombre->fetch(PDO::FETCH_ASSOC);

    if (!$resultado) {
        $_SESSION['error'] = "No se encontró el curso con ID " . htmlspecialchars($cursoId) . ".";
        header('Location: ' . $redireccion_url);
        exit;
    }

    $nombreCurso = $resultado['Nombre_Curso'];

    $sql_check_inscritos = "
        SELECT COUNT(*) AS total_inscritos 
        FROM tbl_cursos_funcionarios 
        WHERE ID_Curso = :id
    ";
    $stmt_check = $pdo->prepare($sql_check_inscritos);
    $stmt_check->bindParam(':id', $cursoId, PDO::PARAM_INT);
    $stmt_check->execute();

    $conteo = $stmt_check->fetch(PDO::FETCH_ASSOC);
    $totalInscritos = (int) $conteo['total_inscritos'];


    if ($totalInscritos > 0) {
        // ERROR: Hay funcionarios inscritos. Mostrar alerta.
        $_SESSION['error'] = "No se puede eliminar un curso con funcionarios inscritos  "
            . " ya hay un TOTAL DE: " . $totalInscritos . " FUNCIONARIOS INSCRITOS.";
    } else {

        $sql_delete = " DELETE FROM tbl_cursos WHERE ID_Curso = :id";
        $stmt_delete = $pdo->prepare($sql_delete);
        $stmt_delete->bindParam(':id', $cursoId, PDO::PARAM_INT);
        $stmt_delete->execute();

        // 8. Verificar si se eliminó alguna fila
        if ($stmt_delete->rowCount() > 0) {
            // ÉXITO
            $_SESSION['exito'] = "El curso " . htmlspecialchars($nombreCurso) . " ha sido eliminado con éxito.";
        } else {
            // Fallo de eliminación (si se verificó la existencia antes, esto es poco probable)
            $_SESSION['error'] = "No se pudo eliminar el curso " . htmlspecialchars($nombreCurso) . ". Inténtelo de nuevo.";
        }
    }
} catch (PDOException $e) {

    // Mensaje de error genérico en caso de fallo de conexión o consulta SQL
    $displayInfo = $nombreCurso ? "el curso " . htmlspecialchars($nombreCurso) . "" : "el curso con ID " . htmlspecialchars($cursoId);

    $_SESSION['error'] = 'Error interno del servidor al intentar eliminar ' . $displayInfo . '. Por favor, inténtelo de nuevo.';
    // En un entorno real, también se debe loguear $e->getMessage();

} finally {
    // 9. Redirigir siempre al finalizar la operación
    header('Location: ' . $redireccion_url);
    exit;
}
