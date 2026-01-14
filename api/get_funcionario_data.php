<?php
header('Content-Type: application/json');
require_once '../includes/conexion.php';

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }

    /* ================= 1. DATOS DEL FUNCIONARIO ================= */
    $stmt = $pdo->prepare("
      SELECT 
    f.*, 
    s.nombre AS Nombre_Seccion, 
    d.nombre AS Nombre_Direccion, 
    d.provincia AS Provincia_Direccion, 
    d.distrito AS Distrito_Direccion,
    c.nombre AS Nombre_Categoria
FROM funcionarios f
LEFT JOIN secciones s ON f.Id_seccion = s.Id_seccion
LEFT JOIN direcciones d ON s.Id_direccion = d.Id_direccion
LEFT JOIN categorias c ON f.Id_categoria = c.Id_categoria
WHERE f.Id_funcionario = ?;
    ");
    $stmt->execute([$id]);
    $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$funcionario) {
        echo json_encode(['error' => 'Funcionario no encontrado']);
        exit;
    }

    // Ruta de la foto
    if (!empty($funcionario['Foto'])) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $funcionario['Foto'] = $protocol . $_SERVER['HTTP_HOST'] . '/ministerio_justicia/api/' . ltrim($funcionario['Foto'], '/');
    }

    /* ================= 2. FORMACIÓN ACADÉMICA ================= */
    // Consultamos la tabla tbl_formacion_academica que definiste en el SQL
    $stmtFormacion = $pdo->prepare("SELECT * FROM tbl_formacion_academica WHERE ID_Funcionario = ? ORDER BY Fecha_Graduacion DESC");
    $stmtFormacion->execute([$id]);
    $formacion = $stmtFormacion->fetchAll(PDO::FETCH_ASSOC);

    /* ================= 3. CAPACITACIONES ================= */
    $stmtCapacitaciones = $pdo->prepare("SELECT * FROM tbl_capacitaciones WHERE ID_Funcionario = ? ORDER BY Fecha_Inicio_Curso DESC");
    $stmtCapacitaciones->execute([$id]);
    $capacitaciones = $stmtCapacitaciones->fetchAll(PDO::FETCH_ASSOC);

    /* ================= 4. PERMISOS ================= */
    $stmtPermisos = $pdo->prepare("SELECT * FROM tbl_permisos WHERE ID_Funcionario = ? ORDER BY Fecha_Inicio_Permiso DESC");
    $stmtPermisos->execute([$id]);
    $permisos = $stmtPermisos->fetchAll(PDO::FETCH_ASSOC);

    /* ================= 5. ASIGNACIONES (NOMBRAMIENTOS) ================= */
    $stmtAsignaciones = $pdo->prepare("
        SELECT n.*, c.Nombre AS Nombre_Cargo, d.nombre AS Nombre_Destino
        FROM nombramientos n
        LEFT JOIN cargos c ON n.Id_cargo = c.Id_cargo
        LEFT JOIN direcciones d ON n.Id_direccion = d.Id_direccion
        WHERE n.Id_funcionario = ? 
        ORDER BY n.Fecha_nombramiento DESC
    ");
    $stmtAsignaciones->execute([$id]);
    $asignaciones = $stmtAsignaciones->fetchAll(PDO::FETCH_ASSOC);

    /* ================= RESPUESTA FINAL ================= */
    echo json_encode([
        'funcionario' => $funcionario,
        'formacion_academica' => $formacion, // Ahora contiene los datos reales
        'capacitaciones' => $capacitaciones,
        'permisos' => $permisos,
        'asignaciones' => $asignaciones
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}