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

    /* ================= FUNCIONARIO ================= */
    $stmt = $pdo->prepare("
        SELECT 
            f.*,
            s.nombre AS nombre_seccion,
            c.nombre AS nombre_categoria
        FROM funcionarios f
        LEFT JOIN secciones s ON f.Id_seccion = s.Id_seccion
        LEFT JOIN categorias c ON f.Id_categoria = c.Id_categoria
        WHERE f.Id_funcionario = ?
    ");
    $stmt->execute([$id]);
    $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$funcionario) {
        echo json_encode(['error' => 'Funcionario no encontrado']);
        exit;
    }

    /* ========= RUTA ABSOLUTA FOTO ========= */
    if (!empty($funcionario['Foto'])) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $funcionario['Foto'] = $protocol . $_SERVER['HTTP_HOST'] . '/ministerio_justicia/api/' . ltrim($funcionario['Foto'], '/');
    }

    /* ================= RESPUESTA ================= */
    echo json_encode([
        'funcionario' => $funcionario,
        'asignaciones' => [],
        'formacion_academica' => [],
        'capacitaciones' => [],
        'permisos' => []
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
