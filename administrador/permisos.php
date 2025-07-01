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
                <div class="top-navbar">
                    <div class="d-flex justify-content-between align-items-center">
                        <button class="btn btn-outline-secondary d-md-none me-2 menu-toggle" id="sidebarToggle">
                            <i class="bi bi-list"></i>
                        </button>
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-custom mb-0">
                                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Inicio</a></li>
                                    <li class="breadcrumb-item active">Dashboard</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="input-group" style="width: 300px;">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0"
                                    placeholder="Buscar funcionario...">
                            </div>
                            <button class="btn btn-outline-primary btn-refresh" onclick="refreshData()">
                                <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle me-1"></i> <?= $nombre_usuario; ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Mi Perfil</a>
                                    </li>
                                    <li><a class="dropdown-item" href="#"><i
                                                class="bi bi-gear me-2"></i>Configuración</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item text-danger" href="../api/logout.php"><i
                                                class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

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
                            <h5 class="mb-0 fw-semibold">Listado de Funcionarios</h5>
                        </div>
                        <div class="table-responsive">
                            <?php
                            try {
                                $pdo = new PDO($dsn, $user, $pass, $options);
                            } catch (PDOException $e) {
                                die("Error de conexión: " . $e->getMessage());
                            }

                            // Consulta para obtener permisos con datos del funcionario
                            $sql = "SELECT p.*, f.Nombres, f.Apellidos, f.DNI_Pasaporte 
        FROM tbl_permisos p
        JOIN tbl_funcionarios f ON p.ID_Funcionario = f.ID_Funcionario
        ORDER BY p.ID_Permiso DESC";
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
                                    <th>Documento</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody id="funcionariosTableBody">
                                    <?php foreach ($permisos as $permiso): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($permiso['ID_Permiso']) ?></td>
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
                                                    default => 'bg-warning' // Pendiente
                                                };
                                                ?>
                                                <span class="badge <?= $clase ?>"><?= $estado ?></span>
                                            </td>
                                            <td><?= nl2br(htmlspecialchars($permiso['Motivo'])) ?></td>
                                            <td>
                                                <?php if (!empty($permiso['Documento_Soporte_URL'])): ?>
                                                    <a href="../<?= htmlspecialchars($permiso['Documento_Soporte_URL']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-file-earmark-text"></i> Ver
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Ninguno</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">



                                                    <button class="btn btn-sm btn-warning btn-editar-permiso"
                                                        data-id="<?= $permiso['ID_Permiso'] ?>"
                                                        data-funcionario="<?= htmlspecialchars($permiso['Nombres'] . ' ' . $permiso['Apellidos']) ?>"
                                                        data-tipo="<?= $permiso['Tipo_Permiso'] ?>"
                                                        data-estado="<?= $permiso['Estado_Permiso'] ?>"
                                                        data-inicio="<?= $permiso['Fecha_Inicio_Permiso'] ?>"
                                                        data-fin="<?= $permiso['Fecha_Fin_Permiso'] ?>"
                                                        data-motivo="<?= htmlspecialchars($permiso['Motivo']) ?>"
                                                        data-observaciones="<?= htmlspecialchars($permiso['Observaciones']) ?>"
                                                        title="Editar Permiso">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>



                                                    <button class="btn btn-sm btn-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
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
                                    <i class="bi bi-upload text-primary me-2"></i>Documento Soporte (opcional)
                                </label>
                                <input type="file" name="Documento_Soporte_URL" class="form-control" id="documento" accept=".pdf,.jpg,.png,.doc,.docx">
                            </div>
                        </div>

                        <!-- Botón enviar -->
                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-2"></i>Registrar Permiso
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>






    <!-- Modal Editar Permiso -->
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
                                <select class="form-select" name="Estado_Permiso" id="edit_Estado_Permiso" required>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Aprobado">Aprobado</option>
                                    <option value="Denegado">Denegado</option>
                                    <option value="Cancelado">Cancelado</option>
                                    <option value="Disfrutado">Disfrutado</option>
                                </select>
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

                            <!-- Documento -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-upload me-2 text-primary"></i>Documento Soporte
                                </label>
                                <input type="file" name="Documento_Soporte_URL" class="form-control" accept=".pdf,.jpg,.png">
                            </div>
                        </div>

                        <!-- Botón -->
                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save2 me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>







    <!-- Script para buscar y seleccionar funcionario -->
    <!-- Agrega este script justo antes de </body> -->
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
                            item.textContent = `${f.Nombres} ${f.Apellidos} - ${f.DNI_Pasaporte}`;
                            item.addEventListener('click', () => {
                                idFuncionarioInput.value = f.ID_Funcionario;
                                nombreFuncionarioSpan.textContent = `${f.Nombres} ${f.Apellidos} - DOCUMENTO: ${f.DNI_Pasaporte}`;
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

            // Function for refreshing data (example)
            window.refreshData = function() {
                const refreshBtn = document.querySelector('.btn-refresh');
                refreshBtn.classList.add('refreshing');
                // Simulate data fetching
                setTimeout(() => {
                    alert('Datos actualizados!');
                    refreshBtn.classList.remove('refreshing');
                }, 1000);
            };




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
                    document.getElementById('editNombres').value = datos.nombres;
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


    <?php
    include_once '../includes/footer.php';
    ?>