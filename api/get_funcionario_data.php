<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permite peticiones desde cualquier origen (ajusta en producción)

include_once '../includes/conexion.php';



try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Obtener el ID del funcionario de la URL
    $funcionario_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($funcionario_id === 0) {
        $response_data['error'] = 'ID de funcionario no proporcionado o inválido.';
        echo json_encode($response_data);
        exit();
    }

    // 1. Obtener datos del funcionario
    $stmt = $pdo->prepare("SELECT * FROM tbl_funcionarios WHERE ID_Funcionario = ?");
    $stmt->execute([$funcionario_id]);
    $funcionario = $stmt->fetch();

    if (!$funcionario) {
        $response_data['error'] = 'Funcionario no encontrado.';
        echo json_encode($response_data);
        exit();
    }

    // Obtener protocolo y host
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];

// Generar URL absoluta de la fotografía
if (!empty($funcionario['Fotografia'])) {
    $fotoRuta = ltrim($funcionario['Fotografia'], '/'); // Eliminar barra inicial si la hay
    $funcionario['Fotografia'] = $protocol . $host . '/ministerio/api/' . $fotoRuta;
}



    // 2. Obtener asignaciones del funcionario
    $stmt = $pdo->prepare("
        SELECT
            a.*,
            c.Nombre_Cargo,
            d.Nombre_Departamento,
            de.Nombre_Destino
        FROM tbl_asignaciones a
        JOIN tbl_cargos c ON a.ID_Cargo = c.ID_Cargo
        JOIN tbl_departamentos d ON a.ID_Departamento = d.ID_Departamento
        JOIN tbl_destinos de ON a.ID_Destino = de.ID_Destino
        WHERE a.ID_Funcionario = ?
        ORDER BY a.Fecha_Inicio_Asignacion DESC LIMIT 1
    ");
    $stmt->execute([$funcionario_id]);
    $asignaciones = $stmt->fetchAll();

    // 3. Obtener formación académica del funcionario
    $stmt = $pdo->prepare("
        SELECT *
        FROM tbl_formacion_academica
        WHERE ID_Funcionario = ?
        ORDER BY Fecha_Graduacion DESC LIMIT 2
    ");
    $stmt->execute([$funcionario_id]);
    $formacion_academica = $stmt->fetchAll();

    // 4. Obtener capacitaciones del funcionario
    $stmt = $pdo->prepare("
        SELECT *
        FROM tbl_capacitaciones
        WHERE ID_Funcionario = ?
        ORDER BY Fecha_Inicio_Curso DESC LIMIT 1
    ");
    $stmt->execute([$funcionario_id]);
    $capacitaciones = $stmt->fetchAll();



    // Obtener protocolo y host
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];

// Ajustar la ruta del certificado si es necesario
foreach ($capacitaciones as &$cap) {
    if (!empty($cap['Certificado_URL']) && !filter_var($cap['Certificado_URL'], FILTER_VALIDATE_URL)) {
        $certificadoRuta = ltrim($cap['Certificado_URL'], '/'); // Quita barra inicial si la hay
        $cap['Certificado_URL'] = $protocol . $host . '/ministerio/certificados/' . basename($certificadoRuta);
    }
}
unset($cap);

    // 5. Obtener permisos del funcionario
    $stmt = $pdo->prepare("
        SELECT *
        FROM tbl_permisos
        WHERE ID_Funcionario = ?
        ORDER BY Fecha_Inicio_Permiso DESC LIMIT 2
    ");
    $stmt->execute([$funcionario_id]);
    $permisos = $stmt->fetchAll();

    // Detectar protocolo y host dinámicamente para formar la URL completa
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];

foreach ($permisos as &$perm) {
    if ($perm['Documento_Soporte_URL'] && !filter_var($perm['Documento_Soporte_URL'], FILTER_VALIDATE_URL)) {
        // Solo el nombre del archivo, asegurando no tener rutas problemáticas
        $filename = basename($perm['Documento_Soporte_URL']);
        // Construir la URL pública accesible desde el navegador
        $perm['Documento_Soporte_URL'] = $protocol . $host . '/ministerio/uploads/permisos/' . $filename;
    }
}
unset($perm);



    // Preparar la respuesta JSON
    $response_data = [
        'funcionario' => $funcionario,
        'asignaciones' => $asignaciones,
        'formacion_academica' => $formacion_academica,
        'capacitaciones' => $capacitaciones,
        'permisos' => $permisos
    ];

    echo json_encode($response_data);

} catch (PDOException $e) {
    $response_data['error'] = 'Error de conexión a la base de datos: ' . $e->getMessage();
    echo json_encode($response_data);
} catch (Exception $e) {
    $response_data['error'] = 'Error inesperado: ' . $e->getMessage();
    echo json_encode($response_data);
} finally {
    $pdo = null; // Cerrar la conexión
}
?>
