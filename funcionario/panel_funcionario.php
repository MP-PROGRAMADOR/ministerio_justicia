<?php
session_start();
require_once '../includes/conexion.php';
$pdo = new PDO($dsn, $user, $pass, $options);

// Verificar si el funcionario ha iniciado sesión
if (!isset($_SESSION['Codigo_Funcionario'])) {
    header("Location: login.php");
    exit;
}

$codigoFuncionario = $_SESSION['Codigo_Funcionario'];

// Obtener datos personales del funcionario
$stmt = $pdo->prepare("SELECT * FROM tbl_funcionarios WHERE Codigo_Funcionario = ?");
$stmt->execute([$codigoFuncionario]);
$funcionario = $stmt->fetch();

if (!$funcionario) {
    echo "Funcionario no encontrado.";
    exit;
}




$idFuncionario = $_SESSION['ID_Funcionario'];

// Consultar datos del funcionario y su cargo
$sql = "SELECT f.Codigo_Funcionario, f.Nombres, f.Apellidos, f.Email_Oficial, 
               f.Telefono_Contacto, f.Fotografia, c.Nombre_Cargo, f.DNI_Pasaporte, f.Nacionalidad
        FROM tbl_funcionarios f
        LEFT JOIN tbl_cargos c ON c.ID_Cargo = (
            SELECT ID_Cargo 
            FROM tbl_cargos 
            WHERE ID_Funcionario = f.ID_Funcionario 
            LIMIT 1
        )
        WHERE f.ID_Funcionario = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $idFuncionario]);
$funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

// Ruta foto
$fotoURL = !empty($funcionario['Fotografia'])
    ? "../api/" . $funcionario['Fotografia']
    : "https://placehold.co/80x80/3f51b5/ffffff?text=" . strtoupper(substr($funcionario['Nombres'], 0, 1) . substr($funcionario['Apellidos'], 0, 1));







try {
    $stmt = $pdo->prepare("SELECT ID_Instruccion, Titulo, Fecha_Envio, Leido 
                           FROM tbl_instrucciones 
                           WHERE ID_Funcionario = :id_funcionario 
                           ORDER BY Fecha_Envio DESC 
                           LIMIT 3");
    $stmt->execute(['id_funcionario' => $idFuncionario]);
    $instrucciones = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error al traer las instrucciones: " . $e->getMessage();
}




?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Funcionario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Roboto', sans-serif;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .navbar {
            background: linear-gradient(to right, #1a237e, #3f51b5);
        }

        /* Nueva regla CSS para centrar el dropdown en móviles */
        @media (max-width: 575.98px) {
            .dropdown-menu-mobile-center {
                left: 50% !important;
                transform: translateX(-70%);
            }
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark shadow-lg">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center me-3 ms-3" href="#">
                <i class="fas fa-building fa-2x me-2"></i>
                <span class="d-none d-md-block">Panel del Funcionario</span>
            </a>

            <div class="d-flex align-items-center ms-auto">
                <button type="button" class="btn btn-primary rounded-pill me-3" data-bs-toggle="modal" data-bs-target="#quejasModal">
                    <i class="fas fa-comment-dots me-2"></i>
                    Quejas / Sugerencias
                </button>

                <div class="dropdown me-3">
                    <a class="dropdown-toggle d-flex align-items-center hidden-arrow" href="#" id="navbarDropdownMenuAvatar" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?= htmlspecialchars($fotoURL) ?>" class="rounded-circle me-2 border border-white" style="width: 50px; height: 50px; object-fit: cover;" alt="Avatar" loading="lazy" />
                        <strong class="d-none d-sm-block text-white"><?= htmlspecialchars($funcionario['Nombres'] ?? 'Sin Nombre') ?></strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-mobile-center" aria-labelledby="navbarDropdownMenuAvatar">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2 text-muted"></i>Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cogs me-2 text-muted"></i>Configuración</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a></li>



                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="modal fade" id="quejasModal" tabindex="-1" aria-labelledby="quejasModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quejasModalLabel">Enviar Queja o Sugerencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="complaint-form">
                        <div class="mb-4">
                            <label for="type" class="form-label">Tipo</label>
                            <select class="form-select" id="type" name="type">
                                <option value="queja">Queja</option>
                                <option value="sugerencia">Sugerencia</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label">Mensaje</label>
                            <textarea class="form-control" id="message" name="message" rows="5"></textarea>
                        </div>

                        <!-- Interruptor para Anónimo -->
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="anonimo" name="anonimo" value="1">
                            <label class="form-check-label" for="anonimo">Enviar como anónimo</label>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" form="complaint-form" class="btn btn-primary">Enviar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row g-4">

            <div class="col-lg-12">
                <div class="card">
                    <div class="accordion" id="accordionPanel">

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <i class="fas fa-user-circle fa-2x text-primary me-3"></i>
                                    Información Personal
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionPanel">




                                <div class="accordion-body text-center text-md-start">
                                    <div class="d-flex flex-column flex-md-row align-items-center mb-4">
                                        <img src="<?= htmlspecialchars($fotoURL) ?>"
                                            alt="Foto de perfil"
                                            class="rounded-circle border border-5 border-primary me-md-4 mb-3 mb-md-0"
                                            style="width: 80px; height: 80px; object-fit: cover;">
                                        <div>
                                            <h5 class="fw-bold mb-1"><?= htmlspecialchars($funcionario['Nombres'] . ' ' . $funcionario['Apellidos']) ?></h5>
                                            <p class="text-muted mb-0">Código: <?= htmlspecialchars($funcionario['Codigo_Funcionario']) ?></p>
                                        </div>
                                    </div>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2">
                                            <i class="fas fa-envelope text-primary me-2"></i>
                                            <?= htmlspecialchars($funcionario['Email_Oficial'] ?? 'Sin correo') ?>
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-phone-alt text-primary me-2"></i>
                                            <?= htmlspecialchars($funcionario['Telefono_Contacto'] ?? 'Sin teléfono') ?>
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-id-card text-primary me-2"></i>
                                            <?= htmlspecialchars($funcionario['DNI_Pasaporte'] ?? 'Sin D.I./Pasaporte') ?>
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-flag text-primary me-2"></i>
                                            <?= htmlspecialchars($funcionario['Nacionalidad'] ?? 'Sin Nacionalidad') ?>
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-building text-primary me-2"></i>
                                            Departamento de Recursos Humanos
                                        </li>
                                    </ul>

                                    <p class="text-muted mt-2 mb-0">
                                        Cargo actual: <?= htmlspecialchars($funcionario['Nombre_Cargo'] ?? 'No asignado') ?>
                                    </p>
                                </div>



                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    <i class="fas fa-search-dollar fa-2x text-indigo me-3"></i>
                                    Movimientos Recientes
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionPanel">
                                <div class="accordion-body">



                                    <?php
                                    $idFuncionario = $_SESSION['ID_Funcionario'];
                                    ?>

                                    <table class="table table-hover align-middle mb-0 bg-white">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>ID Funcionario</th>
                                                <th>Nombre</th>
                                                <th>Tipo de Evento</th>
                                                <th>Detalle</th>
                                                <th>Fecha</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            try {
                                                // Traer datos del funcionario específico
                                                $stmt = $pdo->prepare("SELECT ID_Funcionario, Nombres, Apellidos FROM tbl_funcionarios WHERE ID_Funcionario = :id");
                                                $stmt->execute(['id' => $idFuncionario]);
                                                $f = $stmt->fetch();

                                                if ($f) {
                                                    $id = $f['ID_Funcionario'];
                                                    $nombre = htmlspecialchars($f['Nombres'] . ' ' . $f['Apellidos']);

                                                    // Cargo actual y fecha de creación
                                                    $stmtCargo = $pdo->prepare("SELECT Nombre_Cargo, Fecha_Creacion_Registro FROM tbl_cargos WHERE ID_Cargo = (SELECT ID_Cargo FROM tbl_funcionarios WHERE ID_Funcionario = :id)");
                                                    $stmtCargo->execute(['id' => $id]);
                                                    $cargo = $stmtCargo->fetch();

                                                    if ($cargo) {
                                                        echo "<tr>
                    <td>{$id}</td>
                    <td>{$nombre}</td>
                    <td>Cargo</td>
                    <td>{$cargo['Nombre_Cargo']}</td>
                    <td>{$cargo['Fecha_Creacion_Registro']}</td>
                  </tr>";
                                                    }

                                                    // Cursos inscritos del funcionario
                                                    $stmtCurso = $pdo->prepare("
            SELECT cr.Nombre_Curso, cf.Fecha_Matricula
            FROM tbl_cursos_funcionarios cf
            INNER JOIN tbl_cursos cr ON cf.ID_Curso = cr.ID_Curso
            WHERE cf.ID_Funcionario = :id
        ");
                                                    $stmtCurso->execute(['id' => $id]);
                                                    $cursos = $stmtCurso->fetchAll();

                                                    foreach ($cursos as $curso) {
                                                        echo "<tr>
                    <td>{$id}</td>
                    <td>{$nombre}</td>
                    <td>Curso</td>
                    <td>{$curso['Nombre_Curso']}</td>
                    <td>{$curso['Fecha_Matricula']}</td>
                  </tr>";
                                                    }

                                                    // Aquí se pueden añadir más tablas (permisos, quejas, etc.) filtradas por $idFuncionario
                                                }
                                            } catch (PDOException $e) {
                                                echo "<tr><td colspan='5'>Error: " . $e->getMessage() . "</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>





                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row g-3 d-flex align-items-stretch">
                <!-- Columna 1: Instrucciones del Superior -->
                <div class="col-lg-4 col-md-12 d-flex">
                    <div class="card p-4 w-100">
                        <h5 class="fw-bold mb-4 d-flex align-items-center">
                            <i class="fas fa-calendar-plus fa-2x text-success me-3"></i>
                            Instrucciones del Superior
                        </h5>
                        <ul class="list-group list-group-flush">
                            <?php if ($instrucciones): ?>
                                <?php foreach ($instrucciones as $instr): ?>
                                    <li class="list-group-item d-flex align-items-start px-0 instruccion-item"
                                        data-id="<?= $instr['ID_Instruccion'] ?>"
                                        data-titulo="<?= htmlspecialchars($instr['Titulo']) ?>"
                                        data-mensaje="<?= htmlspecialchars($instr['Mensaje']) ?>">
                                        <i class="fas fa-flag-checkered text-success mt-1 me-3"></i>
                                        <div>
                                            <p class="mb-0 fw-bold"><?= htmlspecialchars($instr['Titulo']) ?> - <?= date('d/m/Y', strtotime($instr['Fecha_Envio'])) ?></p>
                                            <p class="text-muted small"><?= mb_strimwidth($instr['Mensaje'], 0, 50, "...") ?></p>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item px-0 text-muted">No hay instrucciones recientes.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>



                <?php
                $ID_Funcionario = $_SESSION['ID_Funcionario'] ?? 0;

                // Traer todos los permisos del funcionario
                $stmtPermisos = $pdo->prepare("
    SELECT Motivo, Estado_Permiso, Fecha_Ultima_Modificacion, Fecha_Inicio_Permiso, Fecha_Fin_Permiso
    FROM tbl_permisos
    WHERE ID_Funcionario = :id
    ORDER BY Fecha_Ultima_Modificacion DESC LIMIT 1
");
                $stmtPermisos->execute(['id' => $ID_Funcionario]);
                $permisos = $stmtPermisos->fetchAll(PDO::FETCH_ASSOC);
                ?>



                <!-- Columna 2: Mis Estadísticas -->
                <!-- Columna: Permisos del Usuario -->
                <!-- Columna: Permisos del Usuario -->
                <div class="col-lg-4 col-md-12 d-flex">
                    <div class="card p-4 w-100">
                        <h5 class="fw-bold mb-4 d-flex align-items-center">
                            <i class="fas fa-clipboard-check fa-2x text-primary me-3"></i>
                            Mis Permisos
                        </h5>
                        <ul class="list-group list-group-flush">
                            <?php if ($permisos): ?>
                                <?php foreach ($permisos as $perm): ?>
                                    <?php
                                    // Color según estado
                                    switch ($perm['Estado_Permiso']) {
                                        case 'pendiente':
                                            $badgeClass = 'bg-warning';
                                            break;
                                        case 'revisado':
                                            $badgeClass = 'bg-info';
                                            break;
                                        case 'aprobado':
                                            $badgeClass = 'bg-success';
                                            break;
                                        case 'rechazado':
                                            $badgeClass = 'bg-danger';
                                            break;
                                        default:
                                            $badgeClass = 'bg-secondary';
                                    }
                                    ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fw-bold"><?= htmlspecialchars($perm['Motivo']) ?></span>
                                            <br>
                                            <small class="text-muted">
                                                Inicio: <?= $perm['Fecha_Inicio_Permiso'] ? date('d/m/Y', strtotime($perm['Fecha_Inicio_Permiso'])) : '-' ?> |
                                                Fin: <?= $perm['Fecha_Fin_Permiso'] ? date('d/m/Y', strtotime($perm['Fecha_Fin_Permiso'])) : '-' ?> |
                                                Fecha de Desicion: <?= $perm['Fecha_Ultima_Modificacion'] ? date('d/m/Y', strtotime($perm['Fecha_Ultima_Modificacion'])) : '-' ?>
                                            </small>
                                        </div>
                                        <span class="badge <?= $badgeClass ?> rounded-pill"><?= ucfirst($perm['Estado_Permiso']) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item text-muted">No hay permisos registrados</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>






                <?php
                $ID_Funcionario = $_SESSION['ID_Funcionario'] ?? 0;
                $hoy = date('Y-m-d');

                // Traer cursos activos (fecha_fin >= hoy)
                $stmtCursos = $pdo->prepare("
    SELECT c.Nombre_Curso, c.Fecha_Inicio, c.Fecha_Fin, cf.Fecha_Matricula
    FROM tbl_cursos_funcionarios cf
    INNER JOIN tbl_cursos c ON cf.ID_Curso = c.ID_Curso
    WHERE cf.ID_Funcionario = :id AND c.Fecha_Fin >= :hoy
    ORDER BY cf.Fecha_Matricula DESC
");
                $stmtCursos->execute(['id' => $ID_Funcionario, 'hoy' => $hoy]);
                $cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);
                ?>




                <!-- Columna 3: Columna de prueba -->
                <!-- Columna de Cursos Inscritos -->
                <div class="col-lg-4 col-md-12 d-flex">
                    <div class="card p-4 w-100">
                        <h5 class="fw-bold mb-4 d-flex align-items-center">
                            <i class="fas fa-star fa-2x text-primary me-3"></i>
                            Cursos Donde estas Inscrito
                        </h5>
                        <ul class="list-group list-group-flush">
                            <?php if ($cursos): ?>
                                <?php foreach ($cursos as $curso): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="fw-bold"><?= htmlspecialchars($curso['Nombre_Curso']) ?></span>
                                        <div class="text-end">
                                            <span class="badge bg-success me-1">Inicio: <?= date('d/m/Y', strtotime($curso['Fecha_Inicio'])) ?></span>
                                            <span class="badge bg-danger">Fin: <?= date('d/m/Y', strtotime($curso['Fecha_Fin'])) ?></span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item text-muted">No tienes cursos activos.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>


            </div>



            <div class="modal fade" id="instruccionModal" tabindex="-1" aria-labelledby="instruccionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="instruccionModalLabel">Instrucción</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <p id="instr-mensaje"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>






            <div class="accordion-item shadow p-2 rounder-2">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed rounder-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQuejas" aria-expanded="false" aria-controls="collapseQuejas">
                        <i class="fas fa-comment-dots fa-2x text-indigo me-3"></i>
                        Últimas Quejas y Sugerencias
                    </button>
                </h2>
                <div id="collapseQuejas" class="accordion-collapse collapse" data-bs-parent="#accordionPanel">
                    <div class="accordion-body">
                        <?php
                        try {
                            $pagina = isset($_GET['pagina_qs']) ? max(1, intval($_GET['pagina_qs'])) : 1;
                            $porPagina = 4;
                            $inicio = ($pagina - 1) * $porPagina;

                            $totalStmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_quejas_sugerencias");
                            $totalStmt->execute();
                            $totalRegistros = $totalStmt->fetchColumn();
                            $totalPaginas = ceil($totalRegistros / $porPagina);

                            $stmt = $pdo->prepare("
                    SELECT qs.*, f.Nombres, f.Apellidos
                    FROM tbl_quejas_sugerencias qs
                    LEFT JOIN tbl_funcionarios f ON qs.ID_Funcionario = f.ID_Funcionario
                    ORDER BY qs.Fecha_Envio DESC
                    LIMIT :inicio, :porPagina
                ");
                            $stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
                            $stmt->bindValue(':porPagina', $porPagina, PDO::PARAM_INT);
                            $stmt->execute();
                            $quejas = $stmt->fetchAll();

                            if ($quejas):
                                foreach ($quejas as $qs):
                                    switch ($qs['Estado']) {
                                        case 'pendiente':
                                            $badgeClass = 'bg-warning';
                                            break;
                                        case 'revisado':
                                            $badgeClass = 'bg-info';
                                            break;
                                        case 'resuelto':
                                            $badgeClass = 'bg-success';
                                            break;
                                        default:
                                            $badgeClass = 'bg-secondary';
                                    }

                                    $icon = ($qs['Tipo'] == 'queja') ? 'fa-exclamation-circle text-danger' : 'fa-lightbulb text-success';
                                    $nombreDisplay = ($qs['Anonimo'] == 1) ? 'Anónimo' : htmlspecialchars($qs['Nombres'] . ' ' . $qs['Apellidos']);
                        ?>
                                    <div class="card mb-2 shadow-sm rounded-3 hover-pointer p-2" data-bs-toggle="modal" data-bs-target="#mensajeModal<?= $qs['ID_QS'] ?>" style="cursor:pointer;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center flex-grow-1 me-2">
                                                <i class="fas <?= $icon ?> fa-lg me-2"></i>
                                                <span class="fw-bold me-2"><?= ucfirst($qs['Tipo']) ?> - <?= $nombreDisplay ?>:</span>
                                                <p class="text-truncate mb-0">
                                                    <?= htmlspecialchars($qs['Mensaje']) ?>
                                                    <small class="text-muted ms-2">Enviado:<?= date('d/m/Y', strtotime($qs['Fecha_Envio'])) ?></small>
                                                </p>
                                            </div>
                                            <span class="badge <?= $badgeClass ?> rounded-pill"><?= ucfirst($qs['Estado']) ?></span>
                                        </div>
                                    </div>

                                    <!-- Modal del mensaje -->
                                    <div class="modal fade" id="mensajeModal<?= $qs['ID_QS'] ?>" tabindex="-1" aria-labelledby="mensajeModalLabel<?= $qs['ID_QS'] ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <div class="modal-header border-bottom-0 pb-0">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                </div>
                                                <div class="modal-body pt-0 px-4 pb-4">
                                                    <div class="text-center mb-4">
                                                        <h3 class="modal-title fw-bold text-primary mb-1" id="mensajeModalLabel<?= $qs['ID_QS'] ?>"><?= ucfirst($qs['Tipo']) ?> de <?= $nombreDisplay ?></h3>
                                                        <small class="text-muted">Enviado: <?= date('d/m/Y H:i', strtotime($qs['Fecha_Envio'])) ?></small>
                                                    </div>
                                                    <div class="p-4 bg-light rounded-3">
                                                        <p class="lead mb-0 text-dark"><?= nl2br(htmlspecialchars($qs['Mensaje'])) ?></p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0 justify-content-center">
                                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                        <?php
                                endforeach;
                            else:
                                echo '<div class="text-center text-muted">No hay quejas o sugerencias registradas.</div>';
                            endif;
                        } catch (PDOException $e) {
                            echo '<div class="text-danger">Error al cargar las quejas: ' . $e->getMessage() . '</div>';
                        }
                        ?>

                        <!-- Paginación -->
                        <?php if ($totalPaginas > 1): ?>
                            <nav aria-label="Paginación Quejas/Sugerencias">
                                <ul class="pagination justify-content-center mt-3 mb-0">
                                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                        <li class="page-item <?= ($i == $pagina) ? 'active' : '' ?>">
                                            <a class="page-link" href="?pagina_qs=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>






        </div>
    </div>





    <!-- Modal de Cierre de Sesión -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="logoutModalLabel">Cerrar Sesión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <!-- Icono de advertencia -->
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>

                    <!-- Avatar del usuario -->
                    <div class="mb-3">
                        <img src="<?= htmlspecialchars($fotoURL) ?>" alt="Avatar" class="rounded-circle" width="80" height="80">
                    </div>

                    <?php

                    $nombre = $_SESSION['Nombres'];
                    $apellidos =     $_SESSION['Apellidos'];
                    ?>
                    <!-- Nombre del usuario -->
                    <p class="fw-bold mb-2">¿Deseas cerrar sesión, <span class="text-primary"><?= htmlspecialchars($nombre ." ".$apellidos) ?></span>?</p>
                    <p class="text-muted small">Se cerrará tu sesión actual y se te redirigirá a la página de inicio de sesión.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-secondary btn-lg btn-sm" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <a href="../api/logout2.php" class="btn btn-danger btn-lg btn-sm">
                        <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



    <script>
        document.querySelectorAll('.instruccion-item').forEach(item => {
            item.addEventListener('click', () => {
                const id = item.getAttribute('data-id');
                const titulo = item.getAttribute('data-titulo');
                const mensaje = item.getAttribute('data-mensaje');

                // Mostrar en modal
                document.getElementById('instruccionModalLabel').textContent = titulo;
                document.getElementById('instr-mensaje').innerHTML = mensaje.replace(/\n/g, '<br>');

                // Abrir modal
                const modalEl = document.getElementById('instruccionModal');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();

                // Marcar como leído con fetch (AJAX)
                fetch('../api/marcar_leido.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id=' + id
                });
            });
        });
    </script>

    <script>
        // Lógica para manejar el formulario
        const complaintForm = document.getElementById('complaint-form');
        if (complaintForm) {
            complaintForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const type = document.getElementById('type').value;
                const message = document.getElementById('message').value;

                console.log(`Tipo: ${type}, Mensaje: ${message}`);

                // Simula el envío exitoso y cierra el modal
                alert('Queja/Sugerencia enviada con éxito.');
                const modalElement = document.getElementById('quejasModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
                complaintForm.reset();
            });
        }
    </script>

</body>

</html>