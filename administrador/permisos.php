<?php
include_once '../includes/header.php';
?>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="container-fluid p-0">
        <div class="row g-0">

            <?php
            include_once '../includes/silebar_admin.php';
            ?>


            <div class="main-content" id="mainContent">


                <div class="header-section">
                    <div class="row align-items-center">

                        <div class="col-md-12 text-md-end mt-3 mt-md-0">
                            <div
                                class="d-flex justify-content-md-end align-items-center gap-2 flex-wrap justify-content-center">
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPermisoModal">
                                    <i class="bi bi-plus-circle me-1"></i> Añadir Permiso
                                </button>
                                <div class="input-group" style="width: auto;">
                                    <input type="text" class="form-control" id="liveSearchInput"
                                        placeholder="Buscar en tabla...">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>





                <?php

                if (isset($_SESSION['error'])) {
                    echo "<div id='mensajeFlash' class='alert alert-danger'>" . htmlspecialchars($_SESSION['error']) . "</div>";
                    unset($_SESSION['error']);
                }
                if (isset($_SESSION['exito'])) {
                    echo "<div id='mensajeFlash' class='alert alert-success'>" . htmlspecialchars($_SESSION['exito']) . "</div>";
                    unset($_SESSION['exito']);
                }
                ?>








                <div class="container-fluid px-4">
                    <div class="table-custom mb-4 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-semibold">Listado de Permisos Aprobados</h5>
                        </div>
                        <div class="table-responsive">

                            <?php
                            try {
                                $pdo = new PDO($dsn, $user, $pass, $options);
                            } catch (PDOException $e) {
                                die("Error de conexión: " . $e->getMessage());
                            }

                            // Asumiendo que tienes el rol en una variable de sesión
                            $rol = $_SESSION['Rol_Usuario'] ?? ''; // Por ejemplo: "Administrador"

                            // Consulta base
                            $sql = "SELECT p.*, f.Nombre, f.Apellidos, f.Dip_Pasaporte, f.Foto
                            FROM tbl_permisos p
                            JOIN funcionarios f ON p.ID_Funcionario = f.ID_Funcionario";

                         

                            // Orden final
                            $sql .= " ORDER BY p.ID_Permiso DESC";

                            $stmt = $pdo->query($sql);
                            $permisos = $stmt->fetchAll();
                            ?>



                            <table class="table table-hover align-middle mb-0" id="funcionariosTable">
                                <thead class=" table-light">
                                    <tr>
                                        <th>ID</th>
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
                                    <?php foreach ($permisos as $permiso): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($permiso['ID_Permiso']) ?></td>
                                            <td><?= htmlspecialchars($permiso['Nombre'] . ' ' . $permiso['Apellidos']) ?></td>
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
                                                    default => 'bg-warning' // Pendiente
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


                                                    <?php if ($_SESSION['Rol_Usuario'] !== 'Usuario'): ?>

                                                        <?php if (
                                                            $_SESSION['Rol_Usuario'] === 'Administrador' ||
                                                            ($_SESSION['Rol_Usuario'] === 'Jefe Personal' && $permiso['Estado_Permiso'] === 'Pendiente')
                                                        ): ?>
                                                            <button class="btn btn-sm btn-warning btn-editar-permiso"
                                                                data-id="<?= $permiso['ID_Permiso'] ?>"
                                                                data-funcionario="<?= htmlspecialchars($permiso['Nombre'] . ' ' . $permiso['Apellidos']) ?>"
                                                                data-tipo="<?= $permiso['Tipo_Permiso'] ?>"
                                                                data-estado="<?= $permiso['Estado_Permiso'] ?>"
                                                                data-inicio="<?= $permiso['Fecha_Inicio_Permiso'] ?>"
                                                                data-fin="<?= $permiso['Fecha_Fin_Permiso'] ?>"
                                                                data-motivo="<?= htmlspecialchars($permiso['Motivo']) ?>"
                                                                data-observaciones="<?= htmlspecialchars($permiso['Observaciones']) ?>"
                                                                title="Editar Permiso">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                        <?php endif; ?>



                                                        <?php if ($_SESSION['Rol_Usuario'] === 'Jefe Personal' && $permiso['token'] == 0): ?>
                                                            <button class="btn btn-sm btn-success btn-token"
                                                                data-id="<?= $permiso['ID_Permiso'] ?>"
                                                                data-funcionario="<?= htmlspecialchars($permiso['Nombre'] . ' ' . $permiso['Apellidos']) ?>"
                                                                title="Enviar Documentos">
                                                                <i class="bi bi-send"></i>
                                                            </button>
                                                        <?php endif; ?>





                                                        <?php if ($_SESSION['Rol_Usuario'] !== 'Jefe Personal'): ?>

                                                            <!-- <button class="btn btn-sm btn-danger" title="Eliminar"><i class="bi bi-trash"></i></button> -->

                                                        <?php endif; ?>

                                                    <?php endif; ?>


                                                    <button class="btn btn-sm btn-info btn-detalles-permiso"
                                                        data-id="<?= $permiso['ID_Permiso'] ?>"
                                                        data-funcionario="<?= htmlspecialchars($permiso['Nombre'] . ' ' . $permiso['Apellidos']) ?>"
                                                        data-dni="<?= htmlspecialchars($permiso['Dip_Pasaporte']) ?>"
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


                                                    <button class="btn btn-sm btn-success btn-aprobar-permiso"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalAceptarPermiso"
                                                        data-id="<?= $permiso['ID_Permiso'] ?>"
                                                        data-funcionario="<?= htmlspecialchars($permiso['Nombre'] . ' ' . $permiso['Apellidos']) ?>"
                                                        title="Aprobar Permiso">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-denegar-permiso"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalDenegarPermiso"
                                                        data-id="<?= $permiso['ID_Permiso'] ?>"
                                                        data-funcionario="<?= htmlspecialchars($permiso['Nombre'] . ' ' . $permiso['Apellidos']) ?>"
                                                        title="Denegar Permiso">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>




                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>


                        </div>
                        <nav aria-label="Page navigation example" class="mt-3">
                            <ul class="pagination justify-content-center" id="paginationControls">
                                <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1"
                                        aria-disabled="true">Anterior</a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#">Siguiente</a></li>
                            </ul>
                        </nav>
                    </div>




                </div>



                <footer class="footer bg-white shadow-sm py-3 mt-auto">
                    <div class="container-fluid text-center">
                        <span class="text-muted">© 2024 Themis | Ministerio de Justicia. Todos los derechos
                            reservados.</span>
                    </div>
                </footer>
            </div>
        </div>
    </div>




    <!-- Modal para Registrar Permiso -->
    <div class="modal fade" id="addPermisoModal" tabindex="-1" aria-labelledby="addPermisoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addPermisoModalLabel">
                        <i class="bi bi-clipboard-check me-2"></i>Solicitud de Permiso
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">

                    <!-- Buscador -->
                    <div class="mb-4">
                        <label for="searchFuncionario" class="form-label fw-semibold">
                            <i class="bi bi-search me-2 text-primary"></i>Buscar Funcionario
                        </label>
                        <input type="text" id="searchFuncionario" class="form-control" placeholder="Escriba un nombre...">
                    </div>

                    <!-- Lista de funcionarios -->
                    <div class="list-group mb-3" id="listaFuncionarios"></div>

                    <!-- Funcionario seleccionado -->
                    <div id="funcionarioSeleccionado" class="mb-4 d-none">
                        <div class="alert alert-info d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-person-check-fill me-2"></i>
                                <span id="nombreFuncionario"></span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="quitarSeleccion">
                                <i class="bi bi-x-circle"></i> Quitar
                            </button>
                        </div>
                    </div>

                    <!-- Formulario de permiso -->
                    <form method="POST" action="../api/guardar_permiso.php" enctype="multipart/form-data">
                        <input type="hidden" name="ID_Funcionario" id="ID_Funcionario">

                        <div class="row g-3">
                            <!-- Tipo de Permiso -->
                            <div class="col-md-6">
                                <label for="tipoPermiso" class="form-label fw-semibold">
                                    <i class="bi bi-ui-checks-grid text-primary me-2"></i>Tipo de Permiso
                                </label>
                                <select class="form-select" name="Tipo_Permiso" id="tipoPermiso" required>
                                    <option selected disabled>Selecciona tipo</option>
                                    <option value="Vacaciones">Vacaciones</option>
                                    <option value="Enfermedad">Enfermedad</option>
                                    <option value="Maternidad">Maternidad</option>
                                    <option value="Paternidad">Paternidad</option>
                                    <option value="Asuntos Propios">Asuntos Propios</option>
                                    <option value="Estudios">Estudios</option>
                                    <option value="Comisión Servicio">Comisión Servicio</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>

                            <!-- Fechas -->
                            <div class="col-md-3">
                                <label for="fechaInicio" class="form-label fw-semibold">
                                    <i class="bi bi-calendar-event text-primary me-2"></i>Inicio
                                </label>
                                <input type="date" name="Fecha_Inicio_Permiso" class="form-control" id="fechaInicio" required>
                            </div>
                            <div class="col-md-3">
                                <label for="fechaFin" class="form-label fw-semibold">
                                    <i class="bi bi-calendar-check text-primary me-2"></i>Fin
                                </label>
                                <input type="date" name="Fecha_Fin_Permiso" class="form-control" id="fechaFin" required>
                            </div>

                            <!-- Motivo -->
                            <div class="col-md-12">
                                <label for="motivo" class="form-label fw-semibold">
                                    <i class="bi bi-chat-square-text text-primary me-2"></i>Motivo
                                </label>
                                <textarea name="Motivo" class="form-control" id="motivo" rows="3"></textarea>
                            </div>

                            <!-- Observaciones -->
                            <div class="col-md-12">
                                <label for="observaciones" class="form-label fw-semibold">
                                    <i class="bi bi-info-circle text-primary me-2"></i>Observaciones
                                </label>
                                <textarea name="Observaciones" class="form-control" id="observaciones" rows="2"></textarea>
                            </div>

                            <!-- Documento Soporte -->
                            <div class="col-md-6">
                                <label for="documento" class="form-label fw-semibold">
                                    <i class="bi bi-upload text-primary me-2"></i>Documento Soporte (Obligatorio)
                                </label>
                                <input type="file" name="Documento_Soporte_URL" class="form-control" id="documento" accept=".pdf,.jpg,.png,.doc,.docx" required>
                            </div>
                        </div>

                        <!-- Botón enviar -->
                        <div class="mt-4 d-flex justify-content-end mb-3">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-2"></i>Registrar Permiso
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>



    <!-- Modal para Aceptar Permiso -->
    <div class="modal fade" id="modalAceptarPermiso" tabindex="-1" aria-labelledby="modalAceptarPermisoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="tu_script_de_procesamiento.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="modalAceptarPermisoLabel">Aprobar Permiso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body mt-2">
                        <p>¿Estás seguro de que deseas APROBAR este permiso? <strong id="funcionarioAprobar"></strong>?</p>
                        <div class="mb-3">
                            <label for="observacionesAceptar" class="form-label">Observaciones (Opcional):</label>
                            <textarea class="form-control" id="observacionesAceptar" name="observaciones" rows="3"></textarea>
                        </div>
                        <input type="hidden" name="id_permiso" id="idPermisoAprobar">
                        <input type="hidden" name="accion" value="aprobar">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success"> <i class="bi bi-check-circle me-1"></i>Confirmar Aprobacion</button>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <!-- Modal para Denegar Permiso -->
    <div class="modal fade" id="modalDenegarPermiso" tabindex="-1" aria-labelledby="modalDenegarPermisoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="tu_script_de_procesamiento.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modalDenegarPermisoLabel">Denegar Permiso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body mt-2">
                        <p>¿Estás seguro de que deseas DENEGAR este permiso <strong id="funcionarioDenegar"></strong>?</p>
                        <div class="mb-3">
                            <label for="observacionesDenegar" class="form-label">Motivo de Denegación (Requerido):</label>
                            <textarea class="form-control" id="observacionesDenegar" name="observaciones" rows="3" required></textarea>
                        </div>
                        <input type="hidden" name="id_permiso" id="idPermisoDenegar">
                        <input type="hidden" name="accion" value="denegar">
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button> -->
                        <button type="submit" class="btn btn-danger"> <i class="bi bi-check-circle me-1"></i> Confirmar Denegación</button>
                    </div>
                </div>
            </form>
        </div>
    </div>





    <!-- Modal Editar Permiso -->
    <div class="modal fade" id="editPermisoModal" tabindex="-1" aria-labelledby="editPermisoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editPermisoModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Editar Permiso
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="../api/actualizar_permiso.php" enctype="multipart/form-data">
                        <input type="hidden" name="ID_Permiso" id="edit_ID_Permiso">

                        <div class="row g-3">
                            <!-- Nombre Funcionario -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person-circle me-2 text-primary"></i>Funcionario
                                </label>
                                <input type="text" class="form-control" id="edit_nombreFuncionario" disabled>
                            </div>

                            <!-- Tipo de Permiso -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-ui-checks-grid me-2 text-primary"></i>Tipo de Permiso
                                </label>
                                <select class="form-select" name="Tipo_Permiso" id="edit_Tipo_Permiso" required>
                                    <option value="Vacaciones">Vacaciones</option>
                                    <option value="Enfermedad">Enfermedad</option>
                                    <option value="Maternidad">Maternidad</option>
                                    <option value="Paternidad">Paternidad</option>
                                    <option value="Asuntos Propios">Asuntos Propios</option>
                                    <option value="Estudios">Estudios</option>
                                    <option value="Comisión Servicio">Comisión Servicio</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>

                            <!-- Estado -->



                            <div class="col-md-6">




                                <label class="form-label fw-semibold">
                                    <i class="bi bi-toggle-on me-2 text-primary"></i>Estado del Permiso
                                </label>
                                <select class="form-select" name="Estado_Permiso" id="edit_Estado_Permiso"
                                    <?php if ($_SESSION['Rol_Usuario'] !== 'Administrador') echo 'disabled'; ?>>
                                    <option value="Pendiente" <?= ($permiso['Estado_Permiso'] == 'Pendiente') ? 'selected' : '' ?>>Pendiente</option>
                                    <option value="Aprobado" <?= ($permiso['Estado_Permiso'] == 'Aprobado') ? 'selected' : '' ?>>Aprobado</option>
                                    <option value="Denegado" <?= ($permiso['Estado_Permiso'] == 'Denegado') ? 'selected' : '' ?>>Denegado</option>
                                    <option value="Cancelado" <?= ($permiso['Estado_Permiso'] == 'Cancelado') ? 'selected' : '' ?>>Cancelado</option>
                                    <option value="Disfrutado" <?= ($permiso['Estado_Permiso'] == 'Disfrutado') ? 'selected' : '' ?>>Disfrutado</option>
                                </select>

                                <?php if ($_SESSION['Rol_Usuario'] !== 'Administrador'): ?>
                                    <input type="hidden" name="Estado_Permiso" value="<?= htmlspecialchars($permiso['Estado_Permiso']) ?>">
                                <?php endif; ?>

                            </div>




                            <!-- Fechas -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-event me-2 text-primary"></i>Fecha Inicio
                                </label>
                                <input type="date" name="Fecha_Inicio_Permiso" id="edit_Fecha_Inicio" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-check me-2 text-primary"></i>Fecha Fin
                                </label>
                                <input type="date" name="Fecha_Fin_Permiso" id="edit_Fecha_Fin" class="form-control" required>
                            </div>

                            <!-- Motivo -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-chat-left-dots me-2 text-primary"></i>Motivo
                                </label>
                                <textarea name="Motivo" id="edit_Motivo" rows="3" class="form-control"></textarea>
                            </div>

                            <!-- Observaciones -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-info-circle me-2 text-primary"></i>Observaciones
                                </label>
                                <textarea name="Observaciones" id="edit_Observaciones" rows="2" class="form-control"></textarea>
                            </div>


                            <?php if ($_SESSION['Rol_Usuario'] === 'Jefe Personal'): ?>
                                <!-- Documento Soporte -->
                                <div class="col-md-12">
                                    <label for="documento" class="form-label fw-semibold">
                                        <i class="bi bi-upload text-primary me-2"></i>Puedes subir un documento para Sustituir al Anterior (Opcional)
                                    </label>
                                    <input type="file" name="Documento_Soporte_URL" class="form-control" id="documento" accept=".pdf,.jpg,.png,.doc,.docx">
                                </div>
                            <?php endif; ?>

                            <!-- Documento -->
                            <div class="col-md-6">


                                <?php if ($_SESSION['Rol_Usuario'] === 'Administrador'): ?>
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-upload me-2 text-primary"></i>Documento De Respuesta del Permiso
                                    </label>
                                    <input type="file" name="documento_permiso" class="form-control" accept=".pdf,.jpg,.png" required>
                                <?php endif; ?>


                            </div>
                        </div>

                        <!-- Botón -->
                        <div class="mt-4 d-flex justify-content-end mb-3">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save2 me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>





    <!-- Modal Confirmar Envío de Documentos -->
    <div class="modal fade" id="confirmTokenModal" tabindex="-1" aria-labelledby="confirmTokenModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <form id="formConfirmToken" method="POST" action="../api/ruta_actualizar_token.php" class="modal-content border-primary shadow-sm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="confirmTokenModalLabel">
                        <i class="bi bi-send-check-fill me-2"></i>Confirmar envío
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body fs-6">
                    ¿Seguro que quieres enviar los documentos de <strong id="modalNombreFuncionario"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1 "></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-check-circle me-1 "></i> Aceptar
                    </button>
                </div>
                <input type="hidden" name="ID_Permiso" id="modalIDPermiso" />
            </form>
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







    <script>
        // cogiendo los datos del modal para lo del token
        document.querySelectorAll('.btn-token').forEach(button => {
            button.addEventListener('click', () => {
                const idPermiso = button.getAttribute('data-id');
                const nombreFuncionario = button.getAttribute('data-funcionario');

                document.getElementById('modalIDPermiso').value = idPermiso;
                document.getElementById('modalNombreFuncionario').textContent = nombreFuncionario;

                // Abrir modal con Bootstrap 5
                const modal = new bootstrap.Modal(document.getElementById('confirmTokenModal'));
                modal.show();
            });
        });
    </script>



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


    <!-- Script para buscar y seleccionar funcionario -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchFuncionario');
            const listaFuncionarios = document.getElementById('listaFuncionarios');
            const seleccionadoDiv = document.getElementById('funcionarioSeleccionado');
            const nombreFuncionarioSpan = document.getElementById('nombreFuncionario');
            const quitarBtn = document.getElementById('quitarSeleccion');
            const idFuncionarioInput = document.getElementById('ID_Funcionario');

            searchInput.addEventListener('input', () => {
                const query = searchInput.value.trim();
                if (query.length < 2) {
                    listaFuncionarios.innerHTML = '';
                    return;
                }
                fetch(`../api/buscar_funcionarios.php?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            listaFuncionarios.innerHTML = `<div class="text-danger">Error: ${data.error}</div>`;
                            return;
                        }
                        if (!Array.isArray(data) || data.length === 0) {
                            listaFuncionarios.innerHTML = `<div class="text-muted">No se encontraron funcionarios</div>`;
                            return;
                        }
                        listaFuncionarios.innerHTML = '';
                        data.forEach(f => {
                            const item = document.createElement('button');
                            item.type = 'button';
                            item.className = 'list-group-item list-group-item-action';
                            item.textContent = `${f.Nombre} ${f.Apellidos} - ${f.Dip_Pasaporte}`;
                            item.addEventListener('click', () => {
                                idFuncionarioInput.value = f.ID_Funcionario;
                                nombreFuncionarioSpan.textContent = `${f.Nombre} ${f.Apellidos} - DOCUMENTO: ${f.Dip_Pasaporte}`;
                                seleccionadoDiv.classList.remove('d-none');
                                listaFuncionarios.innerHTML = '';
                                searchInput.value = '';
                            });
                            listaFuncionarios.appendChild(item);
                        });
                    })
                    .catch(err => {
                        listaFuncionarios.innerHTML = `<div class="text-danger">Error al buscar funcionarios</div>`;
                        console.error(err);
                    });
            });

            quitarBtn.addEventListener('click', () => {
                idFuncionarioInput.value = '';
                nombreFuncionarioSpan.textContent = '';
                seleccionadoDiv.classList.add('d-none');
            });
        });
    </script>







    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const botonesEditar = document.querySelectorAll('.btn-editar-permiso');

            botonesEditar.forEach(boton => {
                boton.addEventListener('click', () => {
                    const modal = new bootstrap.Modal(document.getElementById('editPermisoModal'));

                    // Obtener los datos del botón
                    document.getElementById('edit_ID_Permiso').value = boton.dataset.id;
                    document.getElementById('edit_nombreFuncionario').value = boton.dataset.funcionario;
                    document.getElementById('edit_Tipo_Permiso').value = boton.dataset.tipo;
                    document.getElementById('edit_Estado_Permiso').value = boton.dataset.estado;
                    document.getElementById('edit_Fecha_Inicio').value = boton.dataset.inicio;
                    document.getElementById('edit_Fecha_Fin').value = boton.dataset.fin;
                    document.getElementById('edit_Motivo').value = boton.dataset.motivo;
                    document.getElementById('edit_Observaciones').value = boton.dataset.observaciones;

                    modal.show();
                });
            });
        });
    </script>









    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle for mobile
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            });

            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });


            // Live Search for Table
            const liveSearchInput = document.getElementById('liveSearchInput');
            const funcionariosTableBody = document.getElementById('funcionariosTableBody');

            liveSearchInput.addEventListener('keyup', function() {
                const searchTerm = liveSearchInput.value.toLowerCase();
                const rows = funcionariosTableBody.getElementsByTagName('tr');

                for (let i = 0; i < rows.length; i++) {
                    let rowText = rows[i].textContent.toLowerCase();
                    if (rowText.includes(searchTerm)) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            });




            // Pagination logic (Client-side example)
            const rowsPerPage = 8; // Number of rows per page
            const tableRows = funcionariosTableBody.getElementsByTagName('tr');
            const totalPages = Math.ceil(tableRows.length / rowsPerPage);
            const paginationControls = document.getElementById('paginationControls');

            function displayPage(page) {
                const startIndex = (page - 1) * rowsPerPage;
                const endIndex = startIndex + rowsPerPage;

                for (let i = 0; i < tableRows.length; i++) {
                    if (i >= startIndex && i < endIndex) {
                        tableRows[i].style.display = '';
                    } else {
                        tableRows[i].style.display = 'none';
                    }
                }

                // Update pagination controls
                updatePaginationButtons(page);
            }

            function setupPagination() {
                paginationControls.innerHTML = ''; // Clear existing buttons

                const prevButton = document.createElement('li');
                prevButton.classList.add('page-item');
                prevButton.innerHTML = '<a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a>';
                paginationControls.appendChild(prevButton);

                for (let i = 1; i <= totalPages; i++) {
                    const pageItem = document.createElement('li');
                    pageItem.classList.add('page-item');
                    pageItem.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    pageItem.addEventListener('click', function(e) {
                        e.preventDefault();
                        displayPage(i);
                    });
                    paginationControls.appendChild(pageItem);
                }

                const nextButton = document.createElement('li');
                nextButton.classList.add('page-item');
                nextButton.innerHTML = '<a class="page-link" href="#">Siguiente</a>';
                paginationControls.appendChild(nextButton);

                displayPage(1); // Show first page initially
            }

            function updatePaginationButtons(currentPage) {
                const pageItems = paginationControls.children;
                for (let i = 0; i < pageItems.length; i++) {
                    pageItems[i].classList.remove('active');
                }

                if (currentPage === 1) {
                    pageItems[0].classList.add('disabled'); // Previous button
                } else {
                    pageItems[0].classList.remove('disabled');
                }

                if (currentPage === totalPages) {
                    pageItems[pageItems.length - 1].classList.add('disabled'); // Next button
                } else {
                    pageItems[pageItems.length - 1].classList.remove('disabled');
                }

                // Mark current page as active
                pageItems[currentPage].classList.add('active'); // Adjust index because of prev button
            }

            setupPagination();

            // Event listeners for Previous and Next buttons
            paginationControls.children[0].addEventListener('click', function(e) {
                e.preventDefault();
                const currentPage = parseInt(paginationControls.querySelector('.page-item.active .page-link').textContent);
                if (currentPage > 1) {
                    displayPage(currentPage - 1);
                }
            });

            paginationControls.children[paginationControls.children.length - 1].addEventListener('click', function(e) {
                e.preventDefault();
                const currentPage = parseInt(paginationControls.querySelector('.page-item.active .page-link').textContent);
                if (currentPage < totalPages) {
                    displayPage(currentPage + 1);
                }
            });
        });
    </script>







    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-editar-funcionario').forEach(button => {
                button.addEventListener('click', function() {
                    const datos = this.dataset;

                    // Llenar el formulario con los valores
                    document.getElementById('editIDFuncionario').value = datos.id;
                    document.getElementById('editCodigoFuncionario').value = datos.codigo;
                    document.getElementById('editNombres').value = datos.nombre;
                    document.getElementById('editApellidos').value = datos.apellidos;
                    document.getElementById('editDNI').value = datos.dni;
                    document.getElementById('editFechaNacimiento').value = datos.fechaNacimiento;
                    document.getElementById('editGenero').value = datos.genero;
                    document.getElementById('editNacionalidad').value = datos.nacionalidad;
                    document.getElementById('editDireccion').value = datos.direccion;
                    document.getElementById('editTelefono').value = datos.telefono;
                    document.getElementById('editEmail').value = datos.email;
                    document.getElementById('editFechaIngreso').value = datos.fechaIngreso;
                    document.getElementById('editEstadoLaboral').value = datos.estado;

                    // Imagen
                    const rutaFoto = datos.foto && datos.foto !== '' ? `../api/${datos.foto}` : '';
                    document.getElementById('previewEditFoto').src = rutaFoto;

                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById('editFuncionarioModal'));
                    modal.show();
                });
            });
        });
    </script>


    <!-- Script para que se abra el modal de permisos desde el dashboard -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'register') {
                const modalElement = document.getElementById('addPermisoModal');
                if (modalElement && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    new bootstrap.Modal(modalElement).show();
                    // Limpiar URL
                    if (history.replaceState) {
                        const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                        window.history.replaceState({
                            path: cleanUrl
                        }, '', cleanUrl);
                    }
                }
            }
        });
    </script>


    <?php
    include_once '../includes/footer.php';
    ?>