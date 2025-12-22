<?php
session_start();

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Cursos - Panel del Funcionario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .table-custom {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .modal-header {
            border-radius: 15px 15px 0 0;
        }
    </style>
</head>

<body>

    <?php require('header_funcionario.php') ?>

    <div class="container mt-4">
        <?php
        if (isset($_SESSION['exito'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><strong>¡Éxito!</strong> ' . htmlspecialchars($_SESSION['exito']) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
            unset($_SESSION['exito']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-times-circle me-2"></i><strong>¡Error!</strong> ' . htmlspecialchars($_SESSION['error']) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
            unset($_SESSION['error']);
        }
        ?>
    </div>

    <div class="my-5">
        <?php
        $ID_Funcionario_Session = $_SESSION['ID_Funcionario'] ?? null;

        if (!$ID_Funcionario_Session) {
            echo '<div class="container"><div class="alert alert-danger">Error: Sesión no iniciada.</div></div>';
            $cursos = [];
        } else {
            try {
                $pdo = new PDO($dsn, $user, $pass, $options);

                // CONSULTA PARA TRAER CURSOS INSCRITOS
                $sql = "SELECT c.*, cf.Fecha_Matricula 
                        FROM tbl_cursos c
                        JOIN tbl_cursos_funcionarios cf ON c.ID_Curso = cf.ID_Curso
                        WHERE cf.ID_Funcionario = :ID_Funcionario
                        ORDER BY c.Fecha_Inicio DESC";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([':ID_Funcionario' => $ID_Funcionario_Session]);
                $cursos = $stmt->fetchAll();
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Error de conexión: ' . $e->getMessage() . '</div>';
                $cursos = [];
            }
        }
        ?>

        <div class="container-fluid px-4">
            <div class="table-custom mb-4 p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-semibold"><i class="bi bi-journal-bookmark-fill me-2 text-warning"></i>Mis Cursos Inscritos</h5>
                    <a href="panel_funcionario.php" class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-house-door-fill me-1"></i> Volver al Inicio
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre del Curso</th>
                                <th>Fecha de Inscripción</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($cursos)): ?>
                                <?php foreach ($cursos as $curso):
                                    $hoy = date('Y-m-d');
                                    if ($hoy < $curso['Fecha_Inicio']) {
                                        $estado_txt = "Próximamente";
                                        $clase_badge = "bg-info";
                                    } elseif ($hoy >= $curso['Fecha_Inicio'] && $hoy <= $curso['Fecha_Fin']) {
                                        $estado_txt = "En curso";
                                        $clase_badge = "bg-success";
                                    } else {
                                        $estado_txt = "Finalizado";
                                        $clase_badge = "bg-secondary";
                                    }
                                ?>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars($curso['Nombre_Curso']) ?></td>
                                        <td><?= htmlspecialchars($curso['Fecha_Matricula']) ?></td>
                                        <td><?= htmlspecialchars($curso['Fecha_Inicio']) ?></td>
                                        <td><?= htmlspecialchars($curso['Fecha_Fin']) ?></td>
                                        <td><span class="badge <?= $clase_badge ?>"><?= $estado_txt ?></span></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-warning btn-detalles-curso" title="Ver Detalles"
                                                data-nombre="<?= htmlspecialchars($curso['Nombre_Curso']) ?>"
                                                data-descripcion="<?= htmlspecialchars($curso['Descripcion']) ?>"
                                                data-inicio="<?= htmlspecialchars($curso['Fecha_Inicio']) ?>"
                                                data-fin="<?= htmlspecialchars($curso['Fecha_Fin']) ?>"
                                                data-matricula="<?= htmlspecialchars($curso['Fecha_Matricula']) ?>">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No estás inscrito en ningún curso actualmente.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Información del Curso -->
    <div class="modal fade" id="detallesCursoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Información del Curso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h4 id="modalNombreCurso" class="text-primary mb-3"></h4>
                    <p><strong>Descripción:</strong></p>
                    <p id="modalDescripcionCurso" class="text-muted"></p>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <p class="mb-1 small text-uppercase">Fecha Inicio</p>
                            <p id="modalFechaInicio" class="fw-bold"></p>
                        </div>
                        <div class="col-6">
                            <p class="mb-1 small text-uppercase">Fecha Fin</p>
                            <p id="modalFechaFin" class="fw-bold"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.btn-detalles-curso').forEach(button => {
            button.addEventListener('click', () => {
                const modal = new bootstrap.Modal(document.getElementById('detallesCursoModal'));
                document.getElementById('modalNombreCurso').textContent = button.dataset.nombre;
                document.getElementById('modalDescripcionCurso').textContent = button.dataset.descripcion || 'Sin descripción disponible.';
                document.getElementById('modalFechaInicio').textContent = button.dataset.inicio;
                document.getElementById('modalFechaFin').textContent = button.dataset.fin;
                modal.show();
            });
        });
    </script>
</body>

</html>