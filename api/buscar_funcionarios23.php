<?php
if (ob_get_length()) ob_clean();
header('Content-Type: application/json');
require_once '../includes/conexion.php';

$response = ["success" => false, "data" => [], "message" => ""];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $params = [];
    $sql = "SELECT 
                f.Id_funcionario, 
                f.CODIGO, 
                f.Nombre, 
                f.Apellidos, 
                f.Estado_Laboral, 
                COALESCE(n.Fecha_nombramiento, f.Fecha_nombramiento) AS Fecha_nombramiento, 
                COALESCE(n.Fecha_toma_posesion, f.Fecha_posesion) AS Fecha_toma_posesion,
                s.nombre AS nombre_seccion, 
                d.nombre AS nombre_direccion, 
                d.ubicacion, 
                d.distrito,
                c.Nombre AS nombre_cargo,
                cat.nombre AS nombre_categoria
            FROM funcionarios f
            /* Unimos con la subconsulta que trae solo el último nombramiento */
            LEFT JOIN (
                SELECT n1.*
                FROM nombramientos n1
                WHERE n1.Id_nombramiento = (
                    SELECT MAX(n2.Id_nombramiento) 
                    FROM nombramientos n2 
                    WHERE n2.Id_funcionario = n1.Id_funcionario
                )
            ) n ON f.Id_funcionario = n.Id_funcionario
            LEFT JOIN secciones s ON COALESCE(n.Id_seccion, f.Id_seccion) = s.Id_seccion
            LEFT JOIN direcciones d ON s.Id_direccion = d.Id_direccion
            LEFT JOIN cargos c ON n.Id_cargo = c.Id_cargo
            LEFT JOIN categorias cat ON COALESCE(n.Id_categoria, f.Id_categoria) = cat.Id_categoria
            WHERE 1=1";

    // --- FILTROS ---
    if (!empty($_POST['id_direccion'])) {
        $sql .= " AND d.Id_direccion = :id_dir";
        $params[':id_dir'] = $_POST['id_direccion'];
    }

    if (!empty($_POST['id_seccion'])) {
        $sql .= " AND COALESCE(n.Id_seccion, f.Id_seccion) = :id_sec";
        $params[':id_sec'] = $_POST['id_seccion'];
    }

    if (!empty($_POST['id_cargo'])) {
        $sql .= " AND n.Id_cargo = :id_cargo";
        $params[':id_cargo'] = $_POST['id_cargo'];
    }

    if (!empty($_POST['id_categoria'])) {
        $sql .= " AND COALESCE(n.Id_categoria, f.Id_categoria) = :id_cat";
        $params[':id_cat'] = $_POST['id_categoria'];
    }

    if (!empty($_POST['q'])) {
        $sql .= " AND (f.Nombre LIKE :q OR f.Apellidos LIKE :q OR f.CODIGO LIKE :q)";
        $params[':q'] = "%" . $_POST['q'] . "%";
    }

    if (!empty($_POST['estado_laboral'])) {
        $sql .= " AND f.Estado_Laboral = :estado";
        $params[':estado'] = $_POST['estado_laboral'];
    }

    // --- FILTRO POR TIEMPO ---
    if (!empty($_POST['periodo'])) {
        $periodo = $_POST['periodo'];
        if ($periodo == 'mes') {
            $sql .= " AND MONTH(COALESCE(n.Fecha_toma_posesion, f.Fecha_posesion)) = MONTH(CURRENT_DATE()) 
                  AND YEAR(COALESCE(n.Fecha_toma_posesion, f.Fecha_posesion)) = YEAR(CURRENT_DATE())";
        } elseif ($periodo == 'año') {
            $sql .= " AND YEAR(COALESCE(n.Fecha_toma_posesion, f.Fecha_posesion)) = YEAR(CURRENT_DATE())";
        }
        // ... puedes agregar lógica para trimestre aquí
    }

    $periodo = $_POST['periodo'] ?? 'todo';

    if ($periodo !== 'todo') {
        switch ($periodo) {
            case 'mes':
                $sql .= " AND MONTH(f.Fecha_nombramiento) = MONTH(CURRENT_DATE()) 
                      AND YEAR(f.Fecha_nombramiento) = YEAR(CURRENT_DATE())";
                break;
            case 'trimestre':
                $sql .= " AND QUARTER(f.Fecha_nombramiento) = QUARTER(CURRENT_DATE()) 
                      AND YEAR(f.Fecha_nombramiento) = YEAR(CURRENT_DATE())";
                break;
            case 'año':
                $sql .= " AND YEAR(f.Fecha_nombramiento) = YEAR(CURRENT_DATE())";
                break;
        }
    }

    // Agrupamos por ID para asegurar registros únicos y ordenamos
    $sql .= " GROUP BY f.Id_funcionario ORDER BY f.Apellidos ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = [
        "success" => true,
        "data" => $resultados,
        "count" => count($resultados)
    ];
} catch (Exception $e) {
    $response["message"] = "Error: " . $e->getMessage();
}

echo json_encode($response);
exit;
