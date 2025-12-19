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
                            <div class="d-flex justify-content-md-end align-items-center gap-2 flex-wrap justify-content-center">
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addFuncionarioModal">
                                    <i class="bi bi-plus-circle me-1"></i> Añadir Usuario
                                </button>
                                <div class="input-group" style="width: auto;">
                                    <input type="text" class="form-control" id="liveSearchInput" placeholder="Buscar en tabla...">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <?php




                if (isset($_SESSION['mensaje_exito'])) {
                    echo '<div id="mensajeExito" class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 1050;">'
                        . htmlspecialchars($_SESSION['mensaje_exito']) .
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                    unset($_SESSION['mensaje_exito']);
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

                            // Consulta para obtener usuarios
                            $sql = "SELECT ID_Usuario, Nombre_Usuario, Rol_Usuario, Email_Contacto, Fecha_Creacion, Ultimo_Acceso, Activo FROM tbl_usuarios ORDER BY ID_Usuario ASC";
                            $stmt = $pdo->query($sql);
                            $usuarios = $stmt->fetchAll();
                            ?>

                            <table class="table table-hover align-middle mb-0" id="funcionariosTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre Usuario</th>
                                        <th>Rol</th>
                                        <th>Email</th>
                                        <th>Fecha Creación</th>
                                        <th>Último Acceso</th>
                                        <th>Activo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="funcionariosTableBody">
                                    <?php foreach ($usuarios as $u): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($u['ID_Usuario']) ?></td>
                                            <td><?= htmlspecialchars($u['Nombre_Usuario']) ?></td>
                                            <td><?= htmlspecialchars($u['Rol_Usuario']) ?></td>
                                            <td><?= htmlspecialchars($u['Email_Contacto']) ?></td>
                                            <td><?= htmlspecialchars($u['Fecha_Creacion']) ?></td>
                                            <td><?= $u['Ultimo_Acceso'] ? htmlspecialchars($u['Ultimo_Acceso']) : '<em>No registrado</em>' ?></td>
                                            <td>
                                                <?php if ($u['Activo']): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm btn-secondary disabled" title="Editar"><i class="bi bi-pencil-square"></i></i></button>

                                                    <?php if ($_SESSION['Rol_Usuario'] !== 'Usuario'): ?>
                                                        <button class="btn btn-sm btn-secondary disabled" title="Eliminar"><i class="bi bi-trash"></i></button>
                                                    <?php endif; ?>

                                                    <button class="btn btn-sm btn-secondary disabled" title="Detalles"><i class="bi bi-eye"></i></button>
                                                </div>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>
                        <nav aria-label="Page navigation example" class="mt-3">
                            <ul class="pagination justify-content-center" id="paginationControls">
                                <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a></li>
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
                        <span class="text-muted">© 2024 Themis | Ministerio de Justicia. Todos los derechos reservados.</span>
                    </div>
                </footer>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addFuncionarioModal" tabindex="-1" aria-labelledby="addFuncionarioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addFuncionarioModalLabel"><i class="bi bi-person-plus-fill me-2"></i>Añadir Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="../api/guardar_usuario.php" class="py-4 px-3">
                        <div class="row g-3">
                            <!-- Nombre de Usuario -->
                            <div class="col-md-6">
                                <label for="nombreUsuario" class="form-label fw-semibold">
                                    <i class="bi bi-person-fill me-2 text-primary"></i>Nombre de Usuario
                                </label>
                                <input type="text" class="form-control" id="nombreUsuario" name="Nombre_Usuario" placeholder="Ej: jdoe" required>
                            </div>

                            <!-- Contraseña -->
                            <div class="col-md-6">
                                <label for="contrasena" class="form-label fw-semibold">
                                    <i class="bi bi-lock-fill me-2 text-primary"></i>Contraseña
                                </label>
                                <input type="password" class="form-control" id="contrasena" name="Contrasena_Hash" placeholder="Ingresa una contraseña" required>
                            </div>

                            <!-- Email de Contacto -->
                            <div class="col-md-6">
                                <label for="emailContacto" class="form-label fw-semibold">
                                    <i class="bi bi-envelope-fill me-2 text-primary"></i>Email de Contacto
                                </label>
                                <input type="email" class="form-control" id="emailContacto" name="Email_Contacto" placeholder="Ej: correo@ejemplo.com" required>
                            </div>

                            <!-- Rol de Usuario -->
                            <div class="col-md-6">
                                <label for="rolUsuario" class="form-label fw-semibold">
                                    <i class="bi bi-person-badge-fill me-2 text-primary"></i>Rol de Usuario
                                </label>
                                <select class="form-select" id="rolUsuario" name="Rol_Usuario" required>
                                    <option value="" disabled selected>Selecciona un rol</option>
                                    <option value="Administrador">Administrador</option>
                                    <option value="Secretaria">Secretaria</option>
                                    <option value="Jefe Personal">Jefe Personal</option>
                                    <option value="Usuario">Usuario</option>
                                    <!-- <option value="Auditor">Auditor</option> -->
                            </div>

                            <!-- Usuario Activo -->
                            <div class="col-12 d-flex align-items-center">
                                <input class="form-check-input me-2" type="checkbox" id="activo" name="Activo" checked>
                                <label class="form-check-label fw-semibold" for="activo">
                                    <i class="bi bi-check-circle-fill text-success me-1"></i> Usuario Activo
                                </label>
                            </div>
                        </div>

                        <!-- Footer con botones -->
                        <div class="mt-4 d-flex justify-content-end gap-3">
                            <button type="button" id="cancelBtn" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cerrar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Guardar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


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


    <?php
    include_once '../includes/footer.php';
    ?>