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
    // Añadimos el JOIN con cargos que agregaste vía ALTER TABLE
    $stmt = $pdo->prepare("
        SELECT 
            f.*, 
            s.nombre AS Nombre_Seccion, 
            d.nombre AS Nombre_Direccion, 
            d.provincia AS Provincia_Direccion, 
            d.distrito AS Distrito_Direccion,
            c.nombre AS Nombre_Categoria,
            cr.Nombre AS Nombre_Cargo_Actual
        FROM funcionarios f
        LEFT JOIN secciones s ON f.Id_seccion = s.Id_seccion
        LEFT JOIN direcciones d ON s.Id_direccion = d.Id_direccion
        LEFT JOIN categorias c ON f.Id_categoria = c.Id_categoria
        LEFT JOIN cargos cr ON f.Id_cargo = cr.Id_cargo
        WHERE f.Id_funcionario = ?;
    ");
    $stmt->execute([$id]);
    $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$funcionario) {
        echo json_encode(['error' => 'Funcionario no encontrado']);
        exit;
    }

    // Procesar ruta de la foto
    if (!empty($funcionario['Foto'])) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $funcionario['Foto'] = $protocol . $_SERVER['HTTP_HOST'] . '/ministerio_justicia/api/' . ltrim($funcionario['Foto'], '/');
    }

    /* ================= 2. FORMACIÓN ACADÉMICA ================= */
    $stmtFormacion = $pdo->prepare("SELECT * FROM tbl_formacion_academica WHERE ID_Funcionario = ? ORDER BY Fecha_Graduacion DESC");
    $stmtFormacion->execute([$id]);
    $formacion = $stmtFormacion->fetchAll(PDO::FETCH_ASSOC);

    /* ================= 3. CAPACITACIONES ================= */
    $stmtCapacitaciones = $pdo->prepare("SELECT * FROM tbl_capacitaciones WHERE ID_Funcionario = ? ORDER BY Fecha_Inicio_Curso DESC");
    $stmtCapacitaciones->execute([$id]);
    $capacitaciones = $stmtCapacitaciones->fetchAll(PDO::FETCH_ASSOC);

    /* ================= 4. PERMISOS ================= */
    // Asegúrate de que los nombres coincidan con el ENUM de tu DB
    $stmtPermisos = $pdo->prepare("SELECT * FROM tbl_permisos WHERE ID_Funcionario = ? ORDER BY Fecha_Inicio_Permiso DESC");
    $stmtPermisos->execute([$id]);
    $permisos = $stmtPermisos->fetchAll(PDO::FETCH_ASSOC);

   /* ================= 5. ASIGNACIONES (NOMBRAMIENTOS) ================= */
    $stmtAsignaciones = $pdo->prepare("
        SELECT 
            n.Id_nombramiento,
            n.Fecha_nombramiento,
            n.Fecha_toma_posesion,
            n.Fecha_finalizacion_nombramiento,
            n.Copia_doc_nomb,
            c.Nombre AS Nombre_Cargo, 
            s.nombre AS Nombre_Seccion,
            d.nombre AS Nombre_Direccion,
            cat.nombre AS Nombre_Categoria
        FROM nombramientos n
        LEFT JOIN cargos c ON n.Id_cargo = c.Id_cargo
        LEFT JOIN secciones s ON n.Id_seccion = s.Id_seccion
        LEFT JOIN direcciones d ON s.Id_direccion = d.Id_direccion
        LEFT JOIN categorias cat ON n.Id_categoria = cat.Id_categoria
        WHERE n.Id_funcionario = ? 
        ORDER BY n.Fecha_nombramiento DESC
    ");
    $stmtAsignaciones->execute([$id]);
    $asignaciones = $stmtAsignaciones->fetchAll(PDO::FETCH_ASSOC);

    /* ================= RESPUESTA FINAL ================= */
    echo json_encode([
        'funcionario' => $funcionario,
        'formacion_academica' => $formacion,
        'capacitaciones' => $capacitaciones,
        'permisos' => $permisos,
        'asignaciones' => $asignaciones
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}