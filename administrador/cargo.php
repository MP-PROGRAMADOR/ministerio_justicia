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
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCargoModal">
                                    <i class="bi bi-plus-circle me-1"></i> Añadir Cargo
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

                            $sql = "SELECT * FROM tbl_cargos ORDER BY ID_Cargo DESC";
                            $stmt = $pdo->query($sql);
                            $cargos = $stmt->fetchAll();
                            ?>

                            <table class="table table-bordered table-hover" id="funcionariosTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre del Cargo</th>
                                        <th>Descripción</th>
                                        <th>Nivel Jerárquico</th>
                                        <th>Creado Por</th>
                                        <th>Fecha Creación</th>
                                        <th>Modificado Por</th>
                                        <th>Última Modificación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="funcionariosTableBody">
                                    <?php foreach ($cargos as $cargo): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($cargo['ID_Cargo']) ?></td>
                                            <td><?= htmlspecialchars($cargo['Nombre_Cargo']) ?></td>
                                            <td>
                                                <?php
                                                $desc = $cargo['Descripcion_Cargo'] ?? '';
                                                echo htmlspecialchars(mb_strimwidth($desc, 0, 50, '...'));
                                                ?>
                                            </td>
                                            <td><?= htmlspecialchars($cargo['Nivel_Jerarquico']) ?></td>
                                            <td><?= htmlspecialchars($cargo['ID_Usuario_Creador']) ?></td>
                                            <td><?= htmlspecialchars($cargo['Fecha_Creacion_Registro']) ?></td>
                                            <td><?= htmlspecialchars($cargo['ID_Usuario_Ultima_Modificacion'] ?? '---') ?></td>
                                            <td><?= htmlspecialchars($cargo['Fecha_Ultima_Modificacion'] ?? '---') ?></td>
                                            <td>
                                                <button class="btn btn-warning btn-sm btn-editar-cargo"
                                                    data-id="<?= $cargo['ID_Cargo'] ?>"
                                                    data-nombre="<?= $cargo['Nombre_Cargo'] ?>"
                                                    data-descripcion="<?= $cargo['Descripcion_Cargo'] ?>"
                                                    data-nivel="<?= $cargo['Nivel_Jerarquico'] ?>">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <button class="btn btn-danger btn-sm" title="Eliminar"><i class="bi bi-trash"></i></button>
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





    <!-- Modal para Registrar Cargo -->
    <div class="modal fade" id="addCargoModal" tabindex="-1" aria-labelledby="addCargoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addCargoModalLabel">
                        <i class="bi bi-briefcase me-2"></i>Registrar Cargo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">

                    <form method="POST" action="../api/guardar_cargo.php" enctype="multipart/form-data" novalidate>
                        <!-- No ID_Cargo porque es auto increment o manejado en backend -->

                        <div class="mb-3">
                            <label for="Nombre_Cargo" class="form-label fw-semibold">
                                <i class="bi bi-card-text me-2 text-primary"></i>Nombre del Cargo *
                            </label>
                            <input type="text" class="form-control" name="Nombre_Cargo" id="Nombre_Cargo" required>
                        </div>

                        <div class="mb-3">
                            <label for="Descripcion_Cargo" class="form-label fw-semibold">
                                <i class="bi bi-journal-text me-2 text-primary"></i>Descripción del Cargo
                            </label>
                            <textarea name="Descripcion_Cargo" id="Descripcion_Cargo" rows="4" class="form-control" placeholder="Descripción detallada del cargo"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="Nivel_Jerarquico" class="form-label fw-semibold">
                                <i class="bi bi-bar-chart-line me-2 text-primary"></i>Nivel Jerárquico *
                            </label>
                            <input type="number" class="form-control" name="Nivel_Jerarquico" id="Nivel_Jerarquico" min="1" required>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-2"></i>Guardar Cargo
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>








    <!-- Modal para Editar Cargo -->
    <div class="modal fade" id="editCargoModal" tabindex="-1" aria-labelledby="editCargoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editCargoModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Editar Cargo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="../api/actualizar_cargo.php">
                        <input type="hidden" name="ID_Cargo" id="edit_ID_Cargo">

                        <div class="mb-3">
                            <label for="edit_Nombre_Cargo" class="form-label fw-semibold">
                                <i class="bi bi-briefcase text-primary me-2"></i>Nombre del Cargo <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="Nombre_Cargo" id="edit_Nombre_Cargo" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_Descripcion_Cargo" class="form-label fw-semibold">
                                <i class="bi bi-card-text text-primary me-2"></i>Descripción del Cargo
                            </label>
                            <textarea class="form-control" name="Descripcion_Cargo" id="edit_Descripcion_Cargo" rows="3" maxlength="500"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="edit_Nivel_Jerarquico" class="form-label fw-semibold">
                                <i class="bi bi-sort-numeric-up text-primary me-2"></i>Nivel Jerárquico <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" name="Nivel_Jerarquico" id="edit_Nivel_Jerarquico" required min="1">
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".btn-editar-cargo").forEach(btn => {
                btn.addEventListener("click", function() {
                    document.getElementById("edit_ID_Cargo").value = this.dataset.id;
                    document.getElementById("edit_Nombre_Cargo").value = this.dataset.nombre;
                    document.getElementById("edit_Descripcion_Cargo").value = this.dataset.descripcion;
                    document.getElementById("edit_Nivel_Jerarquico").value = this.dataset.nivel;

                    const modal = new bootstrap.Modal(document.getElementById("editCargoModal"));
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