<?php
include_once '../includes/conexion.php';
$pdo = new PDO($dsn, $user, $pass, $options);

header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST['ID_Curso']) || !isset($_POST['funcionarios'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

$idCurso = intval($_POST['ID_Curso']);
$funcionarios = json_decode($_POST['funcionarios'], true);

if (!is_array($funcionarios) || count($funcionarios) === 0) {
    echo json_encode(['success' => false, 'message' => 'No se enviaron funcionarios válidos.']);
    exit;
}

try {
    // Obtener el cupo
    $stmtCupo = $pdo->prepare("SELECT Cupo FROM tbl_cursos WHERE ID_Curso = :id");
    $stmtCupo->execute([':id' => $idCurso]);
    $curso = $stmtCupo->fetch(PDO::FETCH_ASSOC);

    if (!$curso) {
        echo json_encode(['success' => false, 'message' => 'Curso no encontrado.']);
        exit;
    }

    $cupoMax = intval($curso['Cupo']);

    // Contar inscritos actuales
    $stmtContar = $pdo->prepare("SELECT COUNT(*) as total FROM tbl_cursos_funcionarios WHERE ID_Curso = :id");
    $stmtContar->execute([':id' => $idCurso]);
    $inscritosActuales = intval($stmtContar->fetch(PDO::FETCH_ASSOC)['total']);

    $pdo->beginTransaction();

    $sqlInsert = "INSERT INTO tbl_cursos_funcionarios (ID_Curso, ID_Funcionario) VALUES (:ID_Curso, :ID_Funcionario)";
    $stmtInsert = $pdo->prepare($sqlInsert);

    // CORRECCIÓN AQUÍ: Se cambió ID_Funcionario por Id_funcionario según el error de SQL
    $stmtNombre = $pdo->prepare("SELECT Nombre, Apellidos FROM funcionarios WHERE Id_funcionario = :id");

    $inscritos = [];
    $noInscritos = [];

    foreach ($funcionarios as $idFuncionario) {
        $idFuncionario = intval($idFuncionario);

        // Obtener nombre del funcionario y VERIFICAR EXISTENCIA
        $stmtNombre->execute([':id' => $idFuncionario]);
        $funcionarioData = $stmtNombre->fetch(PDO::FETCH_ASSOC);
        
        // Si el funcionario no existe en la tabla maestra, saltamos el registro
        if (!$funcionarioData) {
            $noInscritos[] = [
                'ID_Funcionario' => $idFuncionario,
                'nombre' => "ID $idFuncionario",
                'motivo' => 'El funcionario no existe en la base de datos'
            ];
            continue; 
        }

        $nombreCompleto = $funcionarioData['Nombre'] . ' ' . $funcionarioData['Apellidos'];

        // Verificar duplicado
        $stmtVerificar = $pdo->prepare("SELECT 1 FROM tbl_cursos_funcionarios WHERE ID_Curso = :curso AND ID_Funcionario = :funcionario");
        $stmtVerificar->execute([
            ':curso' => $idCurso,
            ':funcionario' => $idFuncionario
        ]);

        if ($stmtVerificar->fetch()) {
            $noInscritos[] = [
                'ID_Funcionario' => $idFuncionario,
                'nombre' => $nombreCompleto,
                'motivo' => 'Ya inscrito en el curso'
            ];
            continue;
        }

        // Verificar cupo
        if ($inscritosActuales >= $cupoMax) {
            $noInscritos[] = [
                'ID_Funcionario' => $idFuncionario,
                'nombre' => $nombreCompleto,
                'motivo' => 'Cupo lleno'
            ];
            continue;
        }

        // Insertar
        $stmtInsert->execute([
            ':ID_Curso' => $idCurso,
            ':ID_Funcionario' => $idFuncionario
        ]);
        $inscritosActuales++;
        $inscritos[] = $nombreCompleto;
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'inscritos' => $inscritos,
        'noInscritos' => $noInscritos
    ]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}