<?php
session_start();

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instrucciones Recibidas - Panel del Funcionario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .table-custom { background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); }
        .modal-header { border-radius: 15px 15px 0 0; }
        .badge-prioridad { font-size: 0.8rem; padding: 0.4em 0.7em; }
    </style>
</head>

<body>

    <?php require('header_funcionario.php') ?>

    <div class="container mt-4">
        <?php
        $ID_Funcionario_Session = $_SESSION['ID_Funcionario'] ?? null;

        if (!$ID_Funcionario_Session) {
            echo '<div class="alert alert-danger">Error: Sesión no iniciada.</div>';
            $instrucciones = [];
        } else {
            try {
                $pdo = new PDO($dsn, $user, $pass, $options);

                $sql = "SELECT ID_Instruccion, Titulo, Mensaje, Fecha_Envio 
                        FROM tbl_instrucciones 
                        WHERE ID_Funcionario = :id 
                        ORDER BY Fecha_Envio DESC";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([':id' => $ID_Funcionario_Session]);
                $instrucciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Error de conexión: ' . $e->getMessage() . '</div>';
                $instrucciones = [];
            }
        }
        ?>

        <div class="table-custom mb-4 p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0 fw-bold text-success">
                    <i class="bi bi-chat-left-text-fill me-2"></i>Instrucciones del Superior
                </h5>
                <a href="panel_funcionario.php" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-house-door-fill me-1"></i> Volver al Inicio
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Título de la Instrucción</th>
                            <th>Fecha de Recepción</th>
                            <th>Vista Previa</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($instrucciones)): ?>
                            <?php foreach ($instrucciones as $ins): ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($ins['Titulo']) ?></td>
                                    <td>
                                        <i class="bi bi-calendar3 me-2 text-muted"></i>
                                        <?= date('d/m/Y H:i', strtotime($ins['Fecha_Envio'])) ?>
                                    </td>
                                    <td>
                                        <span class="text-muted small">
                                            <?= mb_strimwidth(htmlspecialchars($ins['Mensaje']), 0, 60, "...") ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-success rounded-pill btn-detalles-ins"
                                            data-titulo="<?= htmlspecialchars($ins['Titulo']) ?>"
                                            data-mensaje="<?= htmlspecialchars($ins['Mensaje']) ?>"
                                            data-fecha="<?= date('d/m/Y H:i', strtotime($ins['Fecha_Envio'])) ?>">
                                            <i class="bi bi-envelope-open-fill me-1"></i> Leer Completa
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="bi bi-info-circle display-6 d-block mb-3"></i>
                                    No tienes instrucciones pendientes en este momento.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalInstruccion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-file-earmark-text me-2"></i>Detalle de Instrucción</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <small class="text-uppercase text-muted fw-bold">Asunto:</small>
                        <h4 id="modalTitulo" class="text-success"></h4>
                    </div>
                    <div class="mb-4">
                        <small class="text-uppercase text-muted fw-bold">Mensaje:</small>
                        <div id="modalMensaje" class="p-3 bg-light rounded shadow-sm border-start border-4 border-success mt-2" style="white-space: pre-wrap;"></div>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">
                            <i class="bi bi-clock-history me-1"></i> Recibido: <span id="modalFecha" class="fw-bold"></span>
                        </small>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success px-4" onclick="window.print()"><i class="bi bi-printer me-1"></i> Imprimir</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.btn-detalles-ins').forEach(button => {
            button.addEventListener('click', () => {
                const modalElement = document.getElementById('modalInstruccion');
                const modal = new bootstrap.Modal(modalElement);
                
                document.getElementById('modalTitulo').textContent = button.dataset.titulo;
                document.getElementById('modalMensaje').textContent = button.dataset.mensaje;
                document.getElementById('modalFecha').textContent = button.dataset.fecha;
                
                modal.show();
            });
        });
    </script>
</body>
</html>