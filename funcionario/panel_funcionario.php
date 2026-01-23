<?php
session_start();
// Asegúrate de incluir la conexión antes de usar $pdo
require_once '../includes/conexion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Funcionario | Gestión Interna</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
    <style>
        :root {
            --primary-dark: #1a237e;
            --accent-color: #3f51b5;
        }

        body {
            background-color: #f0f2f5;
            font-family: 'Roboto', sans-serif;
            color: #333;
        }

        /* Navbar estilo Header */
        .navbar {
            background: linear-gradient(135deg, var(--primary-dark), var(--accent-color));
        }

        /* Tarjetas generales */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
        }

        /* Ajuste de Acordeones */
        .accordion-item {
            border: none;
            margin-bottom: 1rem;
            border-radius: 12px !important;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .accordion-button {
            font-weight: 500;
            background-color: #ffffff;
            color: var(--primary-dark);
        }

        .accordion-button:not(.collapsed) {
            background-color: #f8f9ff;
            color: var(--accent-color);
            box-shadow: none;
        }

        /* Hover en quejas */
        .queja-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .queja-card:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
            border-left-color: var(--accent-color);
        }

        /* Estilo de la tabla */
        .table thead th {
            background-color: #f8f9fa;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            font-weight: 700;
            color: #666;
            border: none;
        }

        /* Centrado de dropdown móvil */
        @media (max-width: 575.98px) {
            .dropdown-menu-mobile-center {
                left: 50% !important;
                transform: translateX(-50%);
            }
        }
    </style>
</head>

<body>
    <?php require('header_funcionario.php') ?>

    <div class="container mt-5">
        <?php
        $pdo = new PDO($dsn, $user, $pass, $options);

        // Alertas
        if (isset($_SESSION['exito'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>¡Éxito!</strong> <?= htmlspecialchars($_SESSION['exito']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            <?php unset($_SESSION['exito']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="fas fa-times-circle me-2"></i>
                <strong>¡Error!</strong> <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-12">
                <div class="accordion shadow-sm" id="accordionPanel">

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                <i class="fas fa-address-card fa-lg text-primary me-3"></i>
                                Perfil del Funcionario
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show">
                            <div class="accordion-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center mb-3 mb-md-0">
                                        <img src="<?= htmlspecialchars($fotoURL) ?>" alt="Avatar" class="rounded-circle shadow-sm border border-3 border-light" style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                    <div class="col-md-5">
                                        <h4 class="fw-bold mb-1">
                                            <?= htmlspecialchars($funcionario['Nombre'] . ' ' . $funcionario['Apellidos']) ?>
                                        </h4>

                                        <span class="badge bg-primary-soft text-primary mb-2" style="background: #e8eaff;">
                                            <?= htmlspecialchars($funcionario['cargo'] ?? 'Cargo no asignado') ?>
                                        </span>

                                        <p class="text-muted small mb-0">
                                            <i class="fas fa-hashtag me-1"></i>
                                            Código: <?= htmlspecialchars($funcionario['CODIGO']) ?>
                                        </p>

                                        <p class="text-muted small mb-0">
                                            <i class="fas fa-id-card me-1"></i>
                                            Nº Funcionario: <?= htmlspecialchars($funcionario['Num_carnet_fun'] ?? 'No asignado') ?>
                                        </p>
                                    </div>

                                    <div class="col-md-5 border-start-md">
                                        <div class="row g-2">
                                            <div class="col-8 small"><i class="fas fa-envelope text-muted me-2"></i><?= htmlspecialchars($funcionario['Correo'] ?? '-') ?></div>
                                            <div class="col-6 small"><i class="fas fa-phone text-muted me-2"></i><?= htmlspecialchars($funcionario['Telefono_Contacto'] ?? '-') ?></div>
                                            <div class="col-8 small"><i class="fas fa-id-badge text-muted me-2"></i><?= htmlspecialchars($funcionario['Dip_Pasaporte'] ?? '-') ?></div>
                                            <div class="col-6 small"><i class="fas fa-globe text-muted me-2"></i><?= htmlspecialchars($funcionario['Nacionalidad'] ?? '-') ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                <i class="fas fa-history fa-lg text-primary me-3"></i>
                                Historial de Actividad Reciente
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse">
                            <div class="accordion-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th class="ps-4">Evento</th>
                                                <th>Detalle</th>
                                                <th>Fecha Registro</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $idFuncionario = $_SESSION['ID_Funcionario'];
                                            try {
                                                // Último nombramiento
                                                $stmtNombramiento = $pdo->prepare("
                                                SELECT n.Id_nombramiento, n.Fecha_nombramiento, n.Fecha_toma_posesion,
                                                    c.Nombre AS Nombre_Cargo,
                                                    s.nombre AS Nombre_Seccion,
                                                    d.nombre AS Nombre_Direccion
                                                FROM nombramientos n
                                                LEFT JOIN cargos c ON c.Id_cargo = n.Id_cargo
                                                LEFT JOIN secciones s ON s.Id_seccion = n.Id_seccion
                                                LEFT JOIN direcciones d ON d.Id_direccion = n.Id_direccion
                                                WHERE n.Id_funcionario = :id
                                                ORDER BY n.Fecha_nombramiento DESC
                                                LIMIT 1
                                            ");
                                                $stmtNombramiento->execute(['id' => $idFuncionario]);
                                                if ($nombramiento = $stmtNombramiento->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<tr>
                    <td class='ps-4'><span class='badge bg-light text-dark border'>Nombramiento</span></td>
                    <td>{$nombramiento['Nombre_Cargo']}</td>
                    <td class='text-muted small'>" . date('d/m/Y', strtotime($nombramiento['Fecha_nombramiento'])) . "</td>
                  </tr>";

                                                    // Puedes mostrar sección y dirección si quieres
                                                    echo "<tr>
                                                            <td class='ps-4'><span class='badge bg-light text-dark border'>Sección</span></td>
                                                            <td>{$nombramiento['Nombre_Seccion']}</td>
                                                            <td class='text-muted small'>-</td>
                                                        </tr>";

                                                    echo "<tr>
                                                            <td class='ps-4'><span class='badge bg-light text-dark border'>Dirección</span></td>
                                                            <td>{$nombramiento['Nombre_Direccion']}</td>
                                                            <td class='text-muted small'>-</td>
                                                        </tr>";
                                                }

                                                // Cursos
                                                $stmtCurso = $pdo->prepare("
                                                    SELECT cr.Nombre_Curso, cf.Fecha_Matricula 
                                                    FROM tbl_cursos_funcionarios cf 
                                                    INNER JOIN tbl_cursos cr ON cf.ID_Curso = cr.ID_Curso 
                                                    WHERE cf.ID_Funcionario = :id 
                                                    LIMIT 3
                                                ");
                                                $stmtCurso->execute(['id' => $idFuncionario]);
                                                foreach ($stmtCurso->fetchAll() as $c) {
                                                    echo "<tr>
                                                            <td class='ps-4'><span class='badge bg-light text-dark border'>Curso</span></td>
                                                            <td>Inscrito en: <strong>{$c['Nombre_Curso']}</strong></td>
                                                            <td class='text-muted small'>" . date('d/m/Y', strtotime($c['Fecha_Matricula'])) . "</td>
                                                        </tr>";
                                                }
                                            } catch (PDOException $e) {
                                                echo "<tr><td colspan='3'>Error al cargar datos.</td></tr>";
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
        </div>

        <div class="row g-4 d-flex align-items-stretch mb-5">
            <?php
            $ID_Funcionario_Sesion = $_SESSION['ID_Funcionario'] ?? 0;
            // 1. CONSULTA INSTRUCCIONES
            $stmtInstrucciones = $pdo->prepare("SELECT ID_Instruccion, Titulo, Mensaje, Fecha_Envio FROM tbl_instrucciones WHERE ID_Funcionario = :id ORDER BY Fecha_Envio DESC LIMIT 5");
            $stmtInstrucciones->execute(['id' => $ID_Funcionario_Sesion]);
            $instrucciones = $stmtInstrucciones->fetchAll(PDO::FETCH_ASSOC);

            // 2. CONSULTA PERMISOS
            $stmtPermisos = $pdo->prepare("SELECT Motivo, Estado_Permiso, Fecha_Inicio_Permiso, Fecha_Fin_Permiso FROM tbl_permisos WHERE ID_Funcionario = :id ORDER BY Fecha_registro DESC LIMIT 1");
            $stmtPermisos->execute(['id' => $ID_Funcionario_Sesion]);
            $permisos = $stmtPermisos->fetchAll(PDO::FETCH_ASSOC);

            // 3. CONSULTA CURSOS
            $hoy = date('Y-m-d');
            $stmtCursos = $pdo->prepare("SELECT c.Nombre_Curso, c.Fecha_Inicio, c.Fecha_Fin FROM tbl_cursos_funcionarios cf INNER JOIN tbl_cursos c ON cf.ID_Curso = c.ID_Curso WHERE cf.ID_Funcionario = :id AND c.Fecha_Fin >= :hoy ORDER BY cf.Fecha_Matricula DESC LIMIT 3");
            $stmtCursos->execute(['id' => $ID_Funcionario_Sesion, 'hoy' => $hoy]);
            $cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <div class="col-lg-4 col-md-6 d-flex">
                <div class="card p-4 w-100 shadow-sm border-top border-4 border-success">
                    <h5 class="fw-bold mb-4 d-flex align-items-center text-success">
                        <i class="fas fa-envelope-open-text fa-lg me-3"></i> Instrucciones
                    </h5>
                    <ul class="list-group list-group-flush flex-grow-1">
                        <?php if ($instrucciones): ?>
                            <?php foreach ($instrucciones as $instr): ?>
                                <li class="list-group-item d-flex align-items-start px-0 border-0 mb-2 instruccion-item" style="cursor:pointer;" data-id="<?= $instr['ID_Instruccion'] ?>"
                                    data-titulo="<?= htmlspecialchars($instr['Titulo']) ?>" data-mensaje="<?= htmlspecialchars($instr['Mensaje']) ?>">
                                    <i class="fas fa-chevron-right text-success mt-1 me-2 small"></i>
                                    <div>
                                        <p class="mb-0 fw-bold small text-dark"><?= htmlspecialchars($instr['Titulo']) ?></p>
                                        <p class="text-muted mb-0" style="font-size: 0.75rem;"><?= mb_strimwidth($instr['Mensaje'], 0, 45, "...") ?></p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item px-0 text-muted border-0 small">Sin instrucciones pendientes.</li>
                        <?php endif; ?>
                    </ul>
                    <div class="mt-auto pt-3">
                        <a href="./historial_instrucciones.php" class="btn btn-sm btn-success w-100 rounded-pill shadow-0">Ver Historial</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 d-flex">
                <div class="card p-4 w-100 shadow-sm border-top border-4 border-primary">
                    <h5 class="fw-bold mb-4 d-flex align-items-center text-primary">
                        <i class="fas fa-user-clock fa-lg me-3"></i> Mis Permisos
                    </h5>
                    <div class="flex-grow-1">
                        <ul class="list-group list-group-flush">
                            <?php if ($permisos): ?>
                                <?php foreach ($permisos as $perm):
                                    $badge = match ($perm['Estado_Permiso']) {
                                        'pendiente' => 'bg-warning',
                                        'aprobado' => 'bg-success',
                                        'rechazado' => 'bg-danger',
                                        default => 'bg-info'
                                    };
                                ?>
                                    <li class="list-group-item px-0 border-0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold small text-truncate" style="max-width: 140px;"><?= htmlspecialchars($perm['Motivo']) ?></span>
                                            <span class="badge <?= $badge ?> rounded-pill" style="font-size: 0.65rem;"><?= strtoupper($perm['Estado_Permiso']) ?></span>
                                        </div>
                                        <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> <?= date('d/m/Y', strtotime($perm['Fecha_Inicio_Permiso'])) ?></small>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item px-0 text-muted border-0 small">No hay solicitudes recientes.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="mt-auto pt-3">
                        <a href="./historial_permisos.php" class="btn btn-sm btn-primary w-100 rounded-pill shadow-0">Gestionar Permisos</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12 d-flex">
                <div class="card p-4 w-100 shadow-sm border-top border-4 border-warning">
                    <h5 class="fw-bold mb-4 d-flex align-items-center text-warning">
                        <i class="fas fa-graduation-cap fa-lg me-3"></i> Cursos Activos
                    </h5>
                    <div class="flex-grow-1">
                        <ul class="list-group list-group-flush">
                            <?php if ($cursos): ?>
                                <?php foreach ($cursos as $curso): ?>
                                    <li class="list-group-item px-0 border-0 mb-3">
                                        <span class="fw-bold d-block small mb-1 text-dark"><?= htmlspecialchars($curso['Nombre_Curso']) ?></span>
                                        <div class="progress" style="height: 6px; border-radius: 10px;">
                                            <div class="progress-bar bg-warning" style="width: 100%"></div>
                                        </div>
                                        <small class="text-muted mt-1 d-block" style="font-size: 0.7rem;">Finaliza el <?= date('d/m/Y', strtotime($curso['Fecha_Fin'])) ?></small>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item px-0 text-muted border-0 small">No hay cursos inscritos.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="mt-auto pt-3">
                        <a href="./historial_cursos.php" class="btn btn-sm btn-warning w-100 text-white rounded-pill shadow-0">Ver Mis Cursos</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="accordion" id="accordionQuejas">
            <div class="accordion-item shadow-sm border-0">
                <h2 class="accordion-header" id="headingQuejas">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQuejas" aria-expanded="false" aria-controls="collapseQuejas">
                        <i class="fas fa-comments text-primary me-2"></i> Comunicación Interna (Quejas y Sugerencias)
                    </button>
                </h2>

                <div id="collapseQuejas" class="accordion-collapse collapse" aria-labelledby="headingQuejas" data-bs-parent="#accordionQuejas">
                    <div class="accordion-body p-3">
                        <?php
                        try {
                            $pagina = isset($_GET['pagina_qs']) ? max(1, intval($_GET['pagina_qs'])) : 1;
                            $porPagina = 4;
                            $inicio = ($pagina - 1) * $porPagina;

                            $totalStmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_quejas_sugerencias");
                            $totalStmt->execute();
                            $totalRegistros = $totalStmt->fetchColumn();
                            $totalPaginas = ceil($totalRegistros / $porPagina);

                            $stmt = $pdo->prepare("SELECT qs.*, f.Nombre, f.Apellidos FROM tbl_quejas_sugerencias qs LEFT JOIN funcionarios f ON qs.ID_Funcionario = f.ID_Funcionario ORDER BY qs.Fecha_Envio DESC LIMIT :inicio, :porPagina");
                            $stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
                            $stmt->bindValue(':porPagina', $porPagina, PDO::PARAM_INT);
                            $stmt->execute();
                            $quejas = $stmt->fetchAll();

                            if ($quejas):
                                foreach ($quejas as $qs):
                                    $badgeClass = match ($qs['Estado']) {
                                        'pendiente' => 'bg-warning',
                                        'revisado' => 'bg-info',
                                        'resuelto' => 'bg-success',
                                        default => 'bg-secondary'
                                    };
                                    $icon = ($qs['Tipo'] == 'queja') ? 'fa-exclamation-triangle text-danger' : 'fa-lightbulb text-warning';
                                    $nombreDisplay = ($qs['Anonimo'] == 1) ? 'Anónimo' : htmlspecialchars($qs['Nombre'] . ' ' . $qs['Apellidos']);
                        ?>
                                    <div class="queja-card p-3 mb-2 rounded-3 border border-light shadow-0" data-bs-toggle="modal" data-bs-target="#mensajeModal<?= $qs['ID_QS'] ?>" style="cursor:pointer; background-color: #fafafa;">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <i class="fas <?= $icon ?> fa-lg"></i>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex justify-content-between">
                                                    <span class="fw-bold small text-uppercase text-muted"><?= $qs['Tipo'] ?></span>
                                                    <span class="badge <?= $badgeClass ?> rounded-pill" style="font-size: 0.65rem;"><?= $qs['Estado'] ?></span>
                                                </div>
                                                <p class="mb-0 text-truncate fw-500" style="max-width: 90%;"><?= htmlspecialchars($qs['Mensaje']) ?></p>
                                                <small class="text-muted" style="font-size: 0.7rem;">De: <?= $nombreDisplay ?> • <?= date('d/m/Y', strtotime($qs['Fecha_Envio'])) ?></small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="mensajeModal<?= $qs['ID_QS'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <div class="modal-header border-0">
                                                    <h5 class="fw-bold mb-0"><?= ucfirst($qs['Tipo']) ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4 pt-0 text-center">
                                                    <div class="bg-light p-3 rounded-3 mb-3 text-start">
                                                        <p class="mb-0 text-dark"><?= nl2br(htmlspecialchars($qs['Mensaje'])) ?></p>
                                                    </div>
                                                    <small class="text-muted d-block">Enviado por: <?= $nombreDisplay ?></small>
                                                    <small class="text-muted d-block"><?= date('d/m/Y H:i', strtotime($qs['Fecha_Envio'])) ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                endforeach;
                            else:
                                ?>
                                <div class="text-center py-4 text-muted">No hay comunicaciones registradas.</div>
                        <?php
                            endif;
                        } catch (PDOException $e) {
                            echo "<div class='alert alert-danger'>Error al cargar datos.</div>";
                        }
                        ?>

                        <?php if ($totalPaginas > 1): ?>
                            <nav class="mt-4">
                                <ul class="pagination pagination-sm justify-content-center">
                                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                        <li class="page-item <?= ($i == $pagina) ? 'active' : '' ?>">
                                            <a class="page-link shadow-0" href="?pagina_qs=<?= $i ?>#headingQuejas"><?= $i ?></a>
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



    <!-- Modal de detalle de instruccion al hacerle click sobre una de ellas -->
    <div class="modal fade" id="instruccionModal" tabindex="-1" aria-labelledby="instruccionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="instruccionModalLabel">Detalle</h5>
                    <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <p id="instr-mensaje" class="lead mb-0"></p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill" data-mdb-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


     <!-- Script de detalle de instruccion al hacerle click sobre una de ellas -->
    <script>
        // Inicializamos el modal una sola vez al cargar la página
        const elModal = document.getElementById('instruccionModal');
        let modalInstancia = null;

        document.addEventListener('DOMContentLoaded', () => {
            modalInstancia = new mdb.Modal(elModal);
        });

        document.querySelectorAll('.instruccion-item').forEach(item => {
            item.addEventListener('click', () => {
                const id = item.getAttribute('data-id');
                const titulo = item.getAttribute('data-titulo');
                const mensaje = item.getAttribute('data-mensaje');

                // Rellenar datos
                document.getElementById('instruccionModalLabel').textContent = titulo;
                document.getElementById('instr-mensaje').innerHTML = mensaje.replace(/\n/g, '<br>');

                // Mostrar modal
                if (modalInstancia) modalInstancia.show();

                // Llamada a la API (Marcamos como leído y grabamos la HORA)
                fetch('../api/marcar_leido.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'id=' + id
                    })
                    .then(() => {
                        // Cambiamos el icono a color gris para indicar que ya se procesó
                        const icono = item.querySelector('i');
                        icono.classList.remove('text-success');
                        icono.classList.add('text-muted');
                    })
                    .catch(err => console.error("Error al marcar lectura:", err));
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mensaje = document.getElementById('mensajeFlash');
            if (mensaje) {
                setTimeout(() => {
                    mensaje.style.transition = 'opacity 0.5s ease';
                    mensaje.style.opacity = '0';
                    setTimeout(() => mensaje.remove(), 500); // elimina después del desvanecimiento
                }, 5000);
            }
        });
    </script>
</body>

</html>