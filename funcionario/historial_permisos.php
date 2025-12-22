<?php
session_start();

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</head>

<body>



    <?php require('header_funcionario.php') ?>






    <div class="container mt-4">
        <?php
        // --- Alerta de Éxito ---
        if (isset($_SESSION['exito'])) {
        ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>¡Éxito!</strong> <?= htmlspecialchars($_SESSION['exito']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        <?php
            // Importante: Eliminar la variable de sesión para que no se muestre de nuevo
            unset($_SESSION['exito']);
        }

        // --- Alerta de Error ---
        if (isset($_SESSION['error'])) {
        ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle me-2"></i>
                <strong>¡Error!</strong> <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        <?php
            // Importante: Eliminar la variable de sesión para que no se muestre de nuevo
            unset($_SESSION['error']);
        }
        ?>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <div class=" my-5">
        <?php

        // 1. OBTENER ID DEL FUNCIONARIO DE LA SESIÓN
        $ID_Funcionario_Session = $_SESSION['ID_Funcionario'] ?? null;

        // Si el ID del funcionario no está en la sesión, forzamos un error o redirigimos.
        if (!$ID_Funcionario_Session) {
            echo '<div class="alert alert-danger">Error: ID de Funcionario no encontrado en la sesión.</div>';
            $permisos = []; // Evita intentar la consulta SQL sin ID
        } else {
            try {
                $pdo = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                die("Error de conexión: " . $e->getMessage());
            }

            // Asumiendo que tienes el rol en una variable de sesión
            $rol = $_SESSION['Rol_Usuario'] ?? '';

            // Consulta base
            $sql = "SELECT p.*, f.Nombres, f.Apellidos, f.DNI_Pasaporte, f.Fotografia 
            FROM tbl_permisos p
            JOIN tbl_funcionarios f ON p.ID_Funcionario = f.ID_Funcionario
            WHERE p.ID_Funcionario = :ID_Funcionario_Session";

            $sql .= " ORDER BY p.ID_Permiso DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':ID_Funcionario_Session' => $ID_Funcionario_Session]);
            $permisos = $stmt->fetchAll();
        }
        ?>



        <!-- Tablas de permisos -->
        <div class="container-fluid px-4">
            <div class="table-custom mb-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-light">
                    <div class="d-flex align-items-center">
                        <div class="icon-badge bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3">
                            <i class="bi bi-file-earmark-person fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Mis Permisos Solicitados</h5>
                            <p class="text-muted small mb-0">Gestione y visualice el estado de sus trámites</p>
                        </div>
                    </div>

                    <a href="panel_funcionario.php" class="btn btn-outline-primary btn-sm fw-bold px-3 shadow-0">
                        <i class="bi bi-house-door-fill me-1"></i> Volver al Inicio
                    </a>
                </div>

                <style>
                    /* Estilo adicional para el efecto del ícono si no usas MDB completo */
                    .icon-badge {
                        width: 45px;
                        height: 45px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }


                    /* Opcional: un ligero efecto hover en el encabezado */
                    .text-dark {
                        letter-spacing: -0.5px;
                    }
                </style>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="funcionariosTable">
                        <thead class=" table-light">
                            <tr>
                                <th>Funcionario</th>
                                <th>DNI</th>
                                <th>Tipo</th>
                                <th>Solicitud</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Estado</th>
                                <th>Motivo</th>
                                <th>Procesado</th>
                                <th>Documento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="funcionariosTableBody">
                            <?php if (!empty($permisos)): ?>
                                <?php foreach ($permisos as $permiso): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($permiso['Nombres'] . ' ' . $permiso['Apellidos']) ?></td>
                                        <td><?= htmlspecialchars($permiso['DNI_Pasaporte']) ?></td>
                                        <td><?= htmlspecialchars($permiso['Tipo_Permiso']) ?></td>
                                        <td><?= htmlspecialchars($permiso['Fecha_Solicitud']) ?></td>
                                        <td><?= htmlspecialchars($permiso['Fecha_Inicio_Permiso']) ?></td>
                                        <td><?= htmlspecialchars($permiso['Fecha_Fin_Permiso']) ?></td>
                                        <td>
                                            <?php
                                            $estado = $permiso['Estado_Permiso'];
                                            $clase = match ($estado) {
                                                'Aprobado' => 'bg-success',
                                                'Denegado' => 'bg-danger',
                                                'Cancelado' => 'bg-secondary',
                                                'Disfrutado' => 'bg-info',
                                                default => 'bg-warning'
                                            };
                                            ?>
                                            <span class="badge <?= $clase ?>"><?= $estado ?></span>
                                        </td>
                                        <td><?= nl2br(htmlspecialchars($permiso['Motivo'])) ?></td>

                                        <td>
                                            <?php if ($permiso['token'] == 0): ?>
                                                <span class="badge bg-warning text-dark">No procesado</span>
                                            <?php elseif ($permiso['token'] == 1): ?>
                                                <span class="badge bg-success">Procesado</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Desconocido</span>
                                            <?php endif; ?>
                                        </td>


                                        <td>
                                            <?php if (!empty($permiso['Documento_Soporte_URL'])): ?>
                                                <a href="../uploads/<?= htmlspecialchars($permiso['Documento_Soporte_URL']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-file-earmark-text"></i> Ver
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Ninguno</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-sm btn-info btn-detalles-permiso"
                                                    data-id="<?= $permiso['ID_Permiso'] ?>"
                                                    data-funcionario="<?= htmlspecialchars($permiso['Nombres'] . ' ' . $permiso['Apellidos']) ?>"
                                                    data-dni="<?= htmlspecialchars($permiso['DNI_Pasaporte']) ?>"
                                                    data-tipo="<?= htmlspecialchars($permiso['Tipo_Permiso']) ?>"
                                                    data-fechasolicitud="<?= htmlspecialchars($permiso['Fecha_Solicitud']) ?>"
                                                    data-fechainicio="<?= htmlspecialchars($permiso['Fecha_Inicio_Permiso']) ?>"
                                                    data-fechafin="<?= htmlspecialchars($permiso['Fecha_Fin_Permiso']) ?>"
                                                    data-estado="<?= htmlspecialchars($permiso['Estado_Permiso']) ?>"
                                                    data-token="<?= htmlspecialchars($permiso['token']) ?>"
                                                    data-motivo="<?= htmlspecialchars($permiso['Motivo']) ?>"
                                                    data-observaciones="<?= htmlspecialchars($permiso['Observaciones']) ?>"
                                                    data-docsoporte="<?= htmlspecialchars($permiso['Documento_Soporte_URL']) ?>"
                                                    data-docrespuesta="<?= htmlspecialchars($permiso['documento_permiso']) ?>"
                                                    data-fotografia="<?= htmlspecialchars('../api/' . $permiso['Fotografia']) ?>"
                                                    title="Ver Detalles">
                                                    <i class="bi bi-eye"></i>
                                                </button>


                                                <!-- <button class="btn btn-sm btn-danger" title="Eliminar"><i class="bi bi-trash"></i></button> -->
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="12" class="text-center text-muted">No has solicitado ningún permiso aún.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>




    <!-- Modal Detalles del permiso -->
    <div class="modal fade" id="detallesPermisoModal" tabindex="-1" aria-labelledby="detallesPermisoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content border-primary shadow">
                <div class="modal-header bg-primary text-white d-flex align-items-center">
                    <img src="" alt="Foto Funcionario" id="modalFotoPerfil" class="rounded-circle me-3" style="width:50px; height:50px; object-fit:cover; border: 2px solid white;">
                    <h5 class="modal-title flex-grow-1" id="detallesPermisoModalLabel">
                        <i class="bi bi-person-badge me-2"></i> Detalles del Permiso
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <dl class="row gy-3">
                        <dt class="col-sm-4"><i class="bi bi-person me-1 text-primary"></i> Funcionario</dt>
                        <dd class="col-sm-8 fw-semibold" id="modalFuncionario"></dd>

                        <dt class="col-sm-4"><i class="bi bi-credit-card me-1 text-primary"></i> DNI / Pasaporte</dt>
                        <dd class="col-sm-8" id="modalDNI"></dd>

                        <dt class="col-sm-4"><i class="bi bi-file-earmark-text me-1 text-primary"></i> Tipo de Permiso</dt>
                        <dd class="col-sm-8" id="modalTipo"></dd>

                        <dt class="col-sm-4"><i class="bi bi-calendar-event me-1 text-primary"></i> Fecha de Solicitud</dt>
                        <dd class="col-sm-8" id="modalFechaSolicitud"></dd>

                        <dt class="col-sm-4"><i class="bi bi-calendar-check me-1 text-primary"></i> Fecha Inicio</dt>
                        <dd class="col-sm-8" id="modalFechaInicio"></dd>

                        <dt class="col-sm-4"><i class="bi bi-calendar-x me-1 text-primary"></i> Fecha Fin</dt>
                        <dd class="col-sm-8" id="modalFechaFin"></dd>

                        <dt class="col-sm-4"><i class="bi bi-info-circle me-1 text-primary"></i> Estado</dt>
                        <dd class="col-sm-8">
                            <span id="modalEstado" class="badge fs-6"></span>
                        </dd>

                        <dt class="col-sm-4"><i class="bi bi-check2-circle me-1 text-primary"></i> Estancia actual</dt>
                        <dd class="col-sm-8">
                            <span id="modalToken" class="badge fs-6"></span>
                        </dd>

                        <dt class="col-sm-4"><i class="bi bi-chat-text me-1 text-primary"></i> Motivo de solicitud</dt>
                        <dd class="col-sm-8" id="modalMotivo"></dd>

                        <dt class="col-sm-4"><i class="bi bi-pencil-square me-1 text-primary"></i> Observaciones</dt>
                        <dd class="col-sm-8" id="modalObservaciones"></dd>

                        <dt class="col-sm-4"><i class="bi bi-file-earmark-arrow-down me-1 text-primary"></i>Antecedentes</dt>
                        <dd class="col-sm-8" id="modalDocSoporteContainer"></dd>

                        <dt class="col-sm-4"><i class="bi bi-file-earmark-arrow-down me-1 text-primary"></i> Decreto-Secretario</dt>
                        <dd class="col-sm-8" id="modalDocRespuestaContainer"></dd>
                    </dl>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary fw-semibold" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cerrar
                    </button>
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
                    $apellidos = $_SESSION['Apellidos'];
                    ?>
                    <!-- Nombre del usuario -->
                    <p class="fw-bold mb-2">¿Deseas cerrar sesión, <span class="text-primary"><?= htmlspecialchars($nombre . " " . $apellidos) ?></span>?</p>
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




    <script>
        // datos del modal de seguimiento
        document.querySelectorAll('.btn-detalles-permiso').forEach(button => {
            button.addEventListener('click', () => {
                const modal = new bootstrap.Modal(document.getElementById('detallesPermisoModal'));

                document.getElementById('modalFuncionario').textContent = button.dataset.funcionario;
                document.getElementById('modalDNI').textContent = button.dataset.dni;
                document.getElementById('modalTipo').textContent = button.dataset.tipo;
                document.getElementById('modalFechaSolicitud').textContent = button.dataset.fechasolicitud;
                document.getElementById('modalFechaInicio').textContent = button.dataset.fechainicio;
                document.getElementById('modalFechaFin').textContent = button.dataset.fechafin;

                // Estado con color
                const estado = button.dataset.estado;
                const estadoSpan = document.getElementById('modalEstado');
                estadoSpan.textContent = estado;
                estadoSpan.className = 'badge fs-6 ' + ({
                    'Aprobado': 'bg-success',
                    'Denegado': 'bg-danger',
                    'Cancelado': 'bg-secondary',
                    'Disfrutado': 'bg-info',
                    'Pendiente': 'bg-warning'
                } [estado] || 'bg-secondary');

                // Token con color
                const token = button.dataset.token;
                const tokenSpan = document.getElementById('modalToken');
                if (token === '0') {
                    tokenSpan.textContent = 'No procesado';
                    tokenSpan.className = 'badge bg-warning text-dark fs-6';
                } else if (token === '1') {
                    tokenSpan.textContent = 'Procesado';
                    tokenSpan.className = 'badge bg-success fs-6';
                } else {
                    tokenSpan.textContent = 'Desconocido';
                    tokenSpan.className = 'badge bg-secondary fs-6';
                }

                document.getElementById('modalMotivo').textContent = button.dataset.motivo || '-';
                document.getElementById('modalObservaciones').textContent = button.dataset.observaciones || '-';

                // Foto perfil
                const fotoPerfil = button.dataset.fotografia || 'https://via.placeholder.com/50?text=No+Foto';
                document.getElementById('modalFotoPerfil').src = fotoPerfil;

                // Documento Soporte
                const docSoporteUrl = button.dataset.docsoporte;
                const docSoporteContainer = document.getElementById('modalDocSoporteContainer');
                if (docSoporteUrl) {
                    docSoporteContainer.innerHTML = `<a href="../${docSoporteUrl}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark-text me-1"></i> Descargar Documento Soporte</a>`;
                } else {
                    docSoporteContainer.textContent = 'Ninguno';
                }

                // Documento Respuesta
                const docRespuestaUrl = button.dataset.docrespuesta;
                const docRespuestaContainer = document.getElementById('modalDocRespuestaContainer');
                if (docRespuestaUrl) {
                    docRespuestaContainer.innerHTML = `<a href="../${docRespuestaUrl}" target="_blank" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-arrow-down me-1"></i> Descargar Documento Respuesta</a>`;
                } else {
                    docRespuestaContainer.textContent = 'Ninguno';
                }

                modal.show();
            });
        });
    </script>




</body>

</html>