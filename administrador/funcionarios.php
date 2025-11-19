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

            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


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
                                    <li><a class="dropdown-item" href="./perfil_admin.php">
                                            <i class="bi bi-person me-2"></i>Mi Perfil</a>
                                    </li>
                                    <li><a class="dropdown-item" href="./configuracion.php"><i
                                                class="bi bi-gear me-2"></i>Configuración</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                            <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
                                        </button>
                                    </li>
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
                                <button class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#addFuncionarioModal">
                                    <i class="bi bi-plus-circle me-1"></i> Añadir Funcionarios
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

                            // Consulta para obtener datos de funcionarios
                            $sql = "SELECT 
                            ID_Funcionario, Codigo_Funcionario, Nombres, Apellidos, 
                            DNI_Pasaporte, Fecha_Nacimiento, Genero, Nacionalidad, 
                            Direccion_Residencia, Telefono_Contacto, Email_Oficial, 
                            Fecha_Ingreso, Estado_Laboral, Fotografia 
                            FROM tbl_funcionarios 
                            ORDER BY ID_Funcionario ASC";

                            $stmt = $pdo->query($sql);
                            $funcionarios = $stmt->fetchAll();
                            ?>

                            <table class="table table-hover align-middle mb-0" id="funcionariosTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Foto</th>
                                        <th>Código</th>
                                        <th>Nombres</th>
                                        <th>Apellidos</th>
                                        <th>DIP/Pasaporte</th>
                                        <th>Fecha Nacimiento</th>
                                        <th>Género</th>
                                        <th>Nacionalidad</th>
                                        <th>Teléfono</th>
                                        <th>Estado Laboral</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="funcionariosTableBody">
                                    <?php foreach ($funcionarios as $f): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($f['ID_Funcionario']) ?></td>
                                            <?php
                                            // Detectar el protocolo y host dinámicamente
                                            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
                                            $host = $_SERVER['HTTP_HOST'];

                                            // Ruta absoluta de la imagen
                                            $fotoURL = '';
                                            if (!empty($f['Fotografia'])) {
                                                $fotoURL = $protocol . $host . '/ministerio_justicia/api/' . ltrim($f['Fotografia'], '/');
                                            }

                                            // Debug en consola
                                            echo "<script>console.log('Ruta de imagen: " . addslashes($fotoURL) . "');</script>";
                                            ?>

                                            <td>
                                                <?php if (!empty($f['Fotografia'])): ?>
                                                    <img src="<?= htmlspecialchars($fotoURL) ?>" alt="Foto" class="rounded"
                                                        style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;">
                                                <?php else: ?>
                                                    <i class="bi bi-image-fill text-muted"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($f['Codigo_Funcionario']) ?></td>
                                            <td><?= htmlspecialchars($f['Nombres']) ?></td>
                                            <td><?= htmlspecialchars($f['Apellidos']) ?></td>
                                            <td><?= htmlspecialchars($f['DNI_Pasaporte']) ?></td>
                                            <td><?= htmlspecialchars($f['Fecha_Nacimiento']) ?></td>
                                            <td><?= htmlspecialchars($f['Genero']) ?></td>
                                            <td><?= htmlspecialchars($f['Nacionalidad']) ?></td>
                                            <td><?= htmlspecialchars($f['Telefono_Contacto']) ?></td>

                                            <td>
                                                <?php
                                                $estado_actual = $f['Estado_Laboral'];
                                                $estados_inactivos = ['Cesado', 'Baja Temporal'];
                                                $esta_inactivo = in_array($estado_actual, $estados_inactivos);
                                                $btn_class = $esta_inactivo ? 'btn-secondary' : 'btn-danger';
                                                $disabled_attr = $esta_inactivo ? 'disabled' : '';

                                                ?>


                                                <?php
                                                // 1. Mapa de colores que asigna un color de Bootstrap a cada estado laboral
                                                $color_map = [
                                                    'Activo'         => 'bg-success',
                                                    'Permiso'        => 'bg-info',
                                                    'Vacaciones'     => 'bg-primary',
                                                    'Cesado'         => 'bg-danger',
                                                    'Baja Temporal'  => 'bg-warning',
                                                    'Jubilado'       => 'bg-dark',

                                                ];


                                                $estados_activos = ['Activo', 'Permiso', 'Vacaciones'];
                                                $estados_inactivos_activables = ['Cesado', 'Baja Temporal'];
                                                $estado_actual = $f['Estado_Laboral'];

                                                $es_activo_o_temporal = in_array($estado_actual, $estados_activos);
                                                $es_reactivable = in_array($estado_actual, $estados_inactivos_activables);
                                                $es_interactivo = $es_activo_o_temporal || $es_reactivable;
                                                $badge_color = $color_map[$estado_actual] ?? 'bg-light text-dark';

                                                if ($es_interactivo) {

                                                    // Si el estado es Activo, Permiso o Vacaciones, el switch se muestra 'checked' (ON).
                                                    $is_checked = $es_activo_o_temporal;
                                                    $checked_attr = $is_checked ? 'checked' : '';
                                                ?>
                                                    <div class="form-check form-switch d-inline-block ">
                                                        <input
                                                            class="form-check-input funcionario-toggle"
                                                            title="Desactivar/Activar"
                                                            type="checkbox"
                                                            role="switch"
                                                            id="toggle-<?= $f['ID_Funcionario'] ?>"
                                                            data-funcionario-id="<?= $f['ID_Funcionario'] ?>"
                                                            data-funcionario-nombres="<?= $f['Nombres'] . ' ' . $f['Apellidos'] ?>"
                                                            <?= $checked_attr ?>>
                                                        <label class="form-check-label" for="toggle-<?= $f['ID_Funcionario'] ?>">
                                                            <span class="badge <?= $badge_color ?>">
                                                                <?= $estado_actual ?>
                                                            </span>
                                                        </label>
                                                    </div>
                                                <?php
                                                } else {
                                                    // Si el estado no es interactivo (ej. 'Jubilado'), muestra solo la etiqueta con su color específico.
                                                ?>
                                                    <div class="d-inline-block">
                                                        Estado:
                                                        <span class="badge <?= $badge_color ?>">
                                                            <?= $estado_actual ?>
                                                        </span>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            </td>




                                            <td>
                                                <div class="d-flex gap-2">

                                                    <!-- Boton de editar funcionario -->
                                                    <button class="btn btn-sm btn-warning btn-editar-funcionario"
                                                        data-id="<?= $f['ID_Funcionario'] ?>"
                                                        data-codigo="<?= htmlspecialchars($f['Codigo_Funcionario']) ?>"
                                                        data-nombres="<?= htmlspecialchars($f['Nombres']) ?>"
                                                        data-apellidos="<?= htmlspecialchars($f['Apellidos']) ?>"
                                                        data-dni="<?= htmlspecialchars($f['DNI_Pasaporte']) ?>"
                                                        data-fecha-nacimiento="<?= $f['Fecha_Nacimiento'] ?>"
                                                        data-genero="<?= $f['Genero'] ?>"
                                                        data-nacionalidad="<?= htmlspecialchars($f['Nacionalidad']) ?>"
                                                        data-direccion="<?= htmlspecialchars($f['Direccion_Residencia']) ?>"
                                                        data-telefono="<?= htmlspecialchars($f['Telefono_Contacto']) ?>"
                                                        data-email="<?= htmlspecialchars($f['Email_Oficial']) ?>"
                                                        data-fecha-ingreso="<?= $f['Fecha_Ingreso'] ?>"
                                                        data-estado="<?= $f['Estado_Laboral'] ?>"
                                                        data-foto="<?= htmlspecialchars($f['Fotografia']) ?>"
                                                        title="Editar">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>

                                                    <!-- Boton de ver detalles del funcionario -->

                                                    <button type="button" class="btn btn-sm btn-info shadow-sm" title="Ver Detalles"
                                                        data-bs-toggle="modal" data-bs-target="#employeeDetailModal"
                                                        data-funcionario-id="<?= $f['ID_Funcionario'] ?>">
                                                        <i class="bi bi-person-fill"></i>
                                                    </button>


                                                    <!-- Boton de Descargar PDF del funcionario -->
                                                    <button class="btn btn-sm btn-success" title="Descargar PDF"
                                                        onclick="downloadFile(<?= (int) $f['ID_Funcionario'] ?>)">
                                                        <i class="bi bi-filetype-pdf"></i>
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



    <!-- script para la funcion de PDF imprimible para los funcionarios -->
    <script>
        function downloadFile(id_funcionario) {
            console.log(id_funcionario)
            const url = '../fpdf/cv_funcionario.php?id_funcionario=' + id_funcionario;
            window.open(url, '_blank');
        }
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






    <!--Modal de Reigistrar funcionario  -->
    <div class="modal fade" id="addFuncionarioModal" tabindex="-1" aria-labelledby="addFuncionarioModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addFuncionarioModalLabel"><i
                            class="bi bi-person-plus-fill me-2"></i>Añadir Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="../api/guardar_funcionario.php" enctype="multipart/form-data"
                        class="py-4 px-3">
                        <div class="row g-3">

                            <!-- Nombres -->
                            <div class="col-md-6">
                                <label for="nombres" class="form-label fw-semibold">
                                    <i class="bi bi-person-vcard me-2 text-primary"></i>Nombres
                                </label>
                                <input type="text" class="form-control" id="nombres" name="Nombres" required
                                    placeholder="Ej: Ana Trini">
                            </div>

                            <!-- Apellidos -->
                            <div class="col-md-6">
                                <label for="apellidos" class="form-label fw-semibold">
                                    <i class="bi bi-person-badge me-2 text-primary"></i>Apellidos
                                </label>
                                <input type="text" class="form-control" id="apellidos" name="Apellidos" required
                                    placeholder="Ej: Gómez Pérez">
                            </div>

                            <!-- DNI / Pasaporte -->
                            <div class="col-md-6">
                                <label for="dni" class="form-label fw-semibold">
                                    <i class="bi bi-credit-card-2-front me-2 text-primary"></i>D.I.P / Pasaporte
                                </label>
                                <input type="text" class="form-control" id="dni" name="DNI_Pasaporte" required
                                    placeholder="Ej: 12345678A">
                            </div>

                            <!-- Fecha de nacimiento -->
                            <div class="col-md-6">
                                <label for="fechaNacimiento" class="form-label fw-semibold">
                                    <i class="bi bi-calendar-date me-2 text-primary"></i>Fecha de Nacimiento
                                </label>
                                <input type="date" class="form-control" id="fechaNacimiento" name="Fecha_Nacimiento">
                            </div>

                            <!-- Género -->
                            <div class="col-md-6">
                                <label for="genero" class="form-label fw-semibold">
                                    <i class="bi bi-gender-ambiguous me-2 text-primary"></i>Género
                                </label>
                                <select class="form-select" id="genero" name="Genero">
                                    <option value="" disabled selected>Selecciona género</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>

                            <!-- Nacionalidad -->
                            <div class="col-md-6">
                                <label for="nacionalidad" class="form-label fw-semibold">
                                    <i class="bi bi-flag me-2 text-primary"></i>Nacionalidad
                                </label>
                                <input type="text" class="form-control" id="nacionalidad" name="Nacionalidad"
                                    placeholder="Ej: Ecuatoguineana">
                            </div>

                            <!-- Dirección -->
                            <div class="col-md-6">
                                <label for="direccion" class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt-fill me-2 text-primary"></i>Dirección de Residencia
                                </label>
                                <input type="text" class="form-control" id="direccion" name="Direccion_Residencia"
                                    placeholder="Ej: Calle Mayor, 123">
                            </div>

                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <label for="telefono" class="form-label fw-semibold">
                                    <i class="bi bi-telephone-fill me-2 text-primary"></i>Teléfono de Contacto
                                </label>
                                <input type="text" class="form-control" id="telefono" name="Telefono_Contacto"
                                    placeholder="Ej: +240 222 111 222">
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="bi bi-envelope-fill me-2 text-primary"></i>Email Oficial
                                </label>
                                <input type="email" class="form-control" id="email" name="Email_Oficial"
                                    placeholder="Ej: ana.gomez@mj.gov.gq">
                            </div>

                            <!-- Fecha Ingreso -->
                            <div class="col-md-6">
                                <label for="fechaIngreso" class="form-label fw-semibold">
                                    <i class="bi bi-calendar-check-fill me-2 text-primary"></i>Fecha de Ingreso
                                </label>
                                <input type="date" class="form-control" id="fechaIngreso" name="Fecha_Ingreso" required>
                            </div>

                            <!-- Estado Laboral -->
                            <div class="col-md-6">
                                <label for="estadoLaboral" class="form-label fw-semibold">
                                    <i class="bi bi-person-lines-fill me-2 text-primary"></i>Estado Laboral
                                </label>
                                <select class="form-select" id="estadoLaboral" name="Estado_Laboral">
                                    <option value="Activo" selected>Activo</option>
                                    <option value="Baja Temporal">Baja Temporal</option>
                                    <option value="Jubilado">Jubilado</option>
                                    <option value="Cesado">Cesado</option>
                                    <option value="Permiso">Permiso</option>
                                    <option value="Vacaciones">Vacaciones</option>
                                </select>
                            </div>

                            <!-- Fotografía -->
                            <div class="col-md-6">
                                <label for="foto" class="form-label fw-semibold">
                                    <i class="bi bi-camera-fill me-2 text-primary"></i>Fotografía
                                </label>
                                <input type="file" class="form-control" id="foto" name="Fotografia" accept="image/*">
                                <div class="mt-2">
                                    <img id="previewFoto" src="#" alt="Vista previa"
                                        style="display: none; max-width: 150px; max-height: 120px; border-radius: 8px; border: 1px solid #ddd; padding: 3px;">
                                </div>
                            </div>
                        </div>

                        <!-- Footer con botones -->
                        <div class="mt-4 d-flex justify-content-end gap-3">
                            <!-- <button type="button" class="btn btn-outline-secondary" id="cancelBtn">
                                <i class="bi bi-x-circle me-1"></i> Cancelar
                            </button> -->
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-person-plus-fill me-1"></i> Registrar Funcionario
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>





    <!-- Modal de Detalles del Funcionario -->
    <div class="modal fade" id="employeeDetailModal" tabindex="-1" aria-labelledby="employeeDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-5 shadow-lg position-relative">
                <!-- Spinner de carga -->
                <div id="loadingSpinner" class="spinner-overlay d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>

                <!-- Cabecera del modal -->
                <div class="modal-header px-4 pt-4 pb-2 border-0">
                    <h2 class="modal-title fs-3 fw-bold text-dark d-flex align-items-center"
                        id="employeeDetailModalLabel">
                        <i class="bi bi-person-badge-fill text-primary me-3 fs-2"></i>
                        Detalles del Funcionario
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <!-- Cuerpo del modal -->
                <div class="modal-body p-4 modal-body-scrollable">
                    <div class="row g-4" id="modalContentData">
                        <!-- Aquí se cargan los datos dinámicamente vía JavaScript -->
                        <div class="col-12 text-center text-muted py-5">
                            <i class="bi bi-person-circle fs-1 text-secondary mb-3"></i>
                            <p class="fs-5">Cargando datos del funcionario...</p>
                        </div>
                    </div>
                </div>

                <!-- Footer del modal -->
                <div class="modal-footer px-4 pt-2 pb-4 border-0 justify-content-between">
                    <div class="text-muted small">
                        <i class="bi bi-info-circle-fill me-2"></i>Verifica que los datos estén actualizados.
                    </div>
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap 5.3 JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        // Función para formatear fecha
        function formatDate(dateString) {
            if (!dateString || dateString === '0000-000-00') return 'N/A'; // Added 0000-000-00 check
            const [year, month, day] = dateString.split('-');
            return `${day}/${month}/${year}`;
        }

        async function loadEmployeeData(funcionarioId) {
            const modalContentData = document.getElementById('modalContentData');
            const loadingSpinner = document.getElementById('loadingSpinner');

            loadingSpinner.classList.remove('d-none');
            modalContentData.innerHTML = `
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-person-circle fs-1 text-secondary mb-3"></i>
                    <p class="fs-5">Cargando datos del funcionario...</p>
                </div>
            `; // Placeholder while loading

            console.log(`[DEBUG] Solicitando datos para el funcionario ID: ${funcionarioId}`);

            try {
                // Ajusta esta ruta si tu archivo PHP está en un lugar diferente
                const phpScriptPath = `../api/get_funcionario_data.php?id=${funcionarioId}`;
                console.log(`[DEBUG] URL de la petición PHP: ${phpScriptPath}`);

                const response = await fetch(phpScriptPath);
                console.log(`[DEBUG] Estado de la respuesta HTTP: ${response.status} ${response.statusText}`);

                if (!response.ok) {
                    const errorText = await response.text(); // Get raw error text
                    console.error(`[DEBUG] Error HTTP en la respuesta: ${errorText}`);
                    throw new Error(`Error HTTP! Estado: ${response.status} - ${errorText}`);
                }

                const data = await response.json();
                console.log(`[DEBUG] Datos recibidos del PHP:`, data);

                if (data.error) {
                    modalContentData.innerHTML = `
                        <div class="col-12 text-center text-danger py-5">
                            <i class="bi bi-exclamation-triangle-fill fs-1 mb-3"></i>
                            <p class="fs-5">Error al cargar los datos: ${data.error}</p>
                            <p class="text-muted small">Asegúrate de que el script PHP esté configurado correctamente y la base de datos sea accesible.</p>
                        </div>
                    `;
                    console.error(`[DEBUG] Error reportado por el script PHP: ${data.error}`);
                    return;
                }

                const {
                    funcionario,
                    asignaciones,
                    formacion_academica,
                    capacitaciones,
                    permisos
                } = data;

                if (!funcionario) {
                    modalContentData.innerHTML = `
                        <div class="col-12 text-center text-muted py-5">
                            <i class="bi bi-person-x-fill fs-1 mb-3"></i>
                            <p class="fs-5">No se encontraron datos para el funcionario con ID ${funcionarioId}.</p>
                        </div>
                    `;
                    console.log(`[DEBUG] No se encontraron datos de funcionario para ID: ${funcionarioId}`);
                    return;
                }

                // Construir el contenido dinámico con el nuevo diseño de 3 columnas
                modalContentData.innerHTML = `
                    <!-- Columna de Información Personal y Contacto (col-md-4) -->
                    <div class="col-12 col-md-4">
                        <div class="info-card h-100 d-flex flex-column align-items-center text-center">
                            <img src="${funcionario.Fotografia || 'https://placehold.co/120x120/0d6efd/FFFFFF?text=Foto'}" alt="Fotografía del Funcionario" class="img-fluid rounded-circle object-fit-cover mb-4 profile-pic">
                            <h3 class="fs-4 fw-semibold text-dark mb-1">${funcionario.Nombres} ${funcionario.Apellidos}</h3>
                            <p class="text-muted mb-4">D.I.P: ${funcionario.DNI_Pasaporte}</p>

                            <div class="w-100 text-start">
                                <h4 class="section-title text-primary"><i class="bi bi-info-circle-fill me-2"></i> Información Personal</h4>
                                <p class="text-secondary d-flex align-items-center mb-2"><i class="bi bi-calendar-event me-2 text-primary"></i> <span class="fw-medium">Nacimiento:</span> <span class="ms-2">${formatDate(funcionario.Fecha_Nacimiento)}</span></p>
                                <p class="text-secondary d-flex align-items-center mb-2"><i class="bi bi-gender-ambiguous me-2 text-primary"></i> <span class="fw-medium">Género:</span> <span class="ms-2">${funcionario.Genero}</span></p>
                                <p class="text-secondary d-flex align-items-center mb-2"><i class="bi bi-flag-fill me-2 text-primary"></i> <span class="fw-medium">Nacionalidad:</span> <span class="ms-2">${funcionario.Nacionalidad}</span></p>
                                <p class="text-secondary d-flex align-items-center mb-2"><i class="bi bi-house-door-fill me-2 text-primary"></i> <span class="fw-medium">Residencia:</span> <span class="ms-2">${funcionario.Direccion_Residencia}</span></p>

                                <h4 class="section-title text-primary mt-4"><i class="bi bi-telephone-fill me-2"></i> Contacto</h4>
                                <p class="text-secondary d-flex align-items-center mb-2"><i class="bi bi-phone-fill me-2 text-primary"></i> <span class="fw-medium">Teléfono:</span> <span class="ms-2">${funcionario.Telefono_Contacto}</span></p>
                                <p class="text-secondary d-flex align-items-center mb-2"><i class="bi bi-envelope-fill me-2 text-primary"></i> <span class="fw-medium">Email:</span> <span class="ms-2">${funcionario.Email_Oficial}</span></p>
                                <p class="text-secondary d-flex align-items-center mb-2"><i class="bi bi-calendar-check-fill me-2 text-primary"></i> <span class="fw-medium">Ingreso:</span> <span class="ms-2">${formatDate(funcionario.Fecha_Ingreso)}</span></p>
                                <p class="text-secondary d-flex align-items-center mb-2"><i class="bi bi-briefcase-fill me-2 text-primary"></i> <span class="fw-medium">Estado:</span> <span class="ms-2">${funcionario.Estado_Laboral}</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Columna para Asignaciones y Formación Académica (col-md-4) -->
                    <div class="col-12 col-md-4">
                        <div class="section-card h-100">
                            <!-- Asignaciones -->
                            <div class="mb-4">
                                <h4 class="section-title text-purple"><i class="bi bi-person-workspace me-2 text-purple-600"></i> Asignaciones</h4>
                                <div id="asignacionesContainer">
                                    ${asignaciones.length > 0 ? asignaciones.map(a => `
                                        <div class="section-item bg-purple-100">
                                            <p class="text-dark fw-semibold d-flex align-items-center mb-1"><i class="bi bi-tag-fill me-2 text-purple-600"></i> ${a.Nombre_Cargo}</p>
                                            <p class="text-muted d-flex align-items-center mb-1"><i class="bi bi-building-fill me-2 text-purple-600"></i> ${a.Nombre_Departamento}</p>
                                            <p class="text-muted d-flex align-items-center mb-1"><i class="bi bi-geo-alt-fill me-2 text-purple-600"></i> ${a.Nombre_Destino}</p>
                                            <p class="text-muted small mt-2 d-flex align-items-center"><i class="bi bi-calendar-range me-2 text-purple-600"></i> ${formatDate(a.Fecha_Inicio_Asignacion)} - ${formatDate(a.Fecha_Fin_Asignacion)}</p>
                                        </div>
                                    `).join('') : '<p class="text-muted">No hay asignaciones registradas.</p>'}
                                </div>
                            </div>

                            <!-- Formación Académica -->
                            <div>
                                <h4 class="section-title text-success"><i class="bi bi-book-fill me-2 text-success-600"></i> Formación Académica</h4>
                                <div id="formacionContainer">
                                    ${formacion_academica.length > 0 ? formacion_academica.map(f => `
                                        <div class="section-item bg-success-100">
                                            <p class="text-dark fw-semibold d-flex align-items-center mb-1"><i class="bi bi-award-fill me-2 text-success-600"></i> ${f.Titulo_Obtenido} (${f.Nivel_Educativo})</p>
                                            <p class="text-muted d-flex align-items-center mb-1"><i class="bi bi-mortarboard-fill me-2 text-success-600"></i> ${f.Institucion_Educativa}</p>
                                            <p class="text-muted small mt-2 d-flex align-items-center"><i class="bi bi-calendar-check-fill me-2 text-success-600"></i> ${formatDate(f.Fecha_Graduacion)}</p>
                                        </div>
                                    `).join('') : '<p class="text-muted">No hay formación académica registrada.</p>'}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna para Capacitaciones y Permisos (col-md-4) -->
                    <div class="col-12 col-md-4">
                        <div class="section-card h-100">
                            <!-- Capacitaciones -->
                            <div class="mb-4">
                                <h4 class="section-title text-warning"><i class="bi bi-lightbulb-fill me-2 text-warning-600"></i> Capacitaciones</h4>
                                <div id="capacitacionesContainer">
                                    ${capacitaciones.length > 0 ? capacitaciones.map(c => `
                                        <div class="section-item bg-warning-100">
                                            <p class="text-dark fw-semibold d-flex align-items-center mb-1"><i class="bi bi-journal-check me-2 text-warning-600"></i> ${c.Nombre_Curso}</p>
                                            <p class="text-muted d-flex align-items-center mb-1"><i class="bi bi-building-fill-add me-2 text-warning-600"></i> ${c.Institucion_Organizadora}</p>
                                            <p class="text-muted small mt-2 d-flex align-items-center"><i class="bi bi-calendar-range me-2 text-warning-600"></i> ${formatDate(c.Fecha_Inicio_Curso)} - ${formatDate(c.Fecha_Fin_Curso)}</p>
                                           ${c.Certificado_URL ? `<a href="${c.Certificado_URL}" download class="text-primary-emphasis text-decoration-none small mt-2 d-flex align-items-center">
                                            <i class="bi bi-download me-1"></i> Ver Certificado
                                        </a>` : ''}
                                        </div>
                                    `).join('') : '<p class="text-muted">No hay capacitaciones registradas.</p>'}
                                </div>
                            </div>

                            <!-- Permisos -->
                            <div>
                                <h4 class="section-title text-danger"><i class="bi bi-briefcase-fill me-2 text-danger-600"></i> Permisos</h4>
                                <div id="permisosContainer">
                                    ${permisos.length > 0 ? permisos.map(p => `
                                        <div class="section-item bg-danger-100">
                                            <p class="text-dark fw-semibold d-flex align-items-center mb-1"><i class="bi bi-file-earmark-text-fill me-2 text-danger-600"></i> ${p.Tipo_Permiso}</p>
                                            <p class="text-muted d-flex align-items-center mb-1"><i class="bi bi-calendar-range me-2 text-danger-600"></i> ${formatDate(p.Fecha_Inicio_Permiso)} - ${formatDate(p.Fecha_Fin_Permiso)}</p>
                                            <p class="text-muted d-flex align-items-center mb-1"><i class="bi bi-clipboard-check-fill me-2 text-danger-600"></i> <span class="fw-medium">Estado:</span> ${p.Estado_Permiso}</p>
                                            <p class="text-muted small mt-2 d-flex align-items-center"><i class="bi bi-chat-dots-fill me-2 text-danger-600"></i> ${p.Motivo}</p>
                                         ${p.Documento_Soporte_URL ? `
                                            <a href="${p.Documento_Soporte_URL}" 
                                                download 
                                                class="text-primary-emphasis text-decoration-none small mt-2 d-flex align-items-center" 
                                                target="_blank" 
                                                rel="noopener noreferrer">
                                                <i class="bi bi-file-earmark-arrow-down-fill me-1"></i> Ver Documento
                                            </a>
                                            ` : ''}
                                        </div>
                                    `).join('') : '<p class="text-muted">No hay permisos registrados.</p>'}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } catch (error) {
                console.error('Error al cargar los datos del funcionario:', error);
                modalContentData.innerHTML = `
                    <div class="col-12 text-center text-danger py-5">
                        <i class="bi bi-exclamation-triangle-fill fs-1 mb-3"></i>
                        <p class="fs-5">Error al cargar los datos. Por favor, intente de nuevo más tarde.</p>
                        <p class="text-muted small">Detalles: ${error.message}</p>
                    </div>
                `;
            } finally {
                loadingSpinner.classList.add('d-none');
            }
        }

        const employeeDetailModalElement = document.getElementById('employeeDetailModal');
        const myModal = new bootstrap.Modal(employeeDetailModalElement);

        employeeDetailModalElement.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const funcionarioId = button.getAttribute('data-funcionario-id');
            if (funcionarioId) {
                loadEmployeeData(funcionarioId);
            } else {
                document.getElementById('modalContentData').innerHTML = `
                    <div class="col-12 text-center text-warning py-5">
                        <i class="bi bi-exclamation-circle-fill fs-1 mb-3"></i>
                        <p class="fs-5">No se proporcionó un ID de funcionario.</p>
                    </div>
                `;
            }
        });
    </script>





    <!-- Modal de Editar -->
    <div class="modal fade" id="editFuncionarioModal" tabindex="-1" aria-labelledby="editFuncionarioModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editFuncionarioModalLabel"><i class="bi bi-pencil-fill me-2"></i>Editar
                        Funcionario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarFuncionario" method="POST" action="../api/actualizar_funcionario.php"
                        enctype="multipart/form-data" class="py-4 px-3">

                        <!-- Campo oculto -->
                        <input type="hidden" name="ID_Funcionario" id="editIDFuncionario">

                        <div class="row g-3">
                            <!-- Código de Funcionario -->
                            <div class="col-md-6">
                                <label for="editCodigoFuncionario" class="form-label fw-semibold">
                                    <i class="bi bi-hash text-primary me-2"></i>Código Funcionario
                                </label>
                                <input type="text" class="form-control" id="editCodigoFuncionario"
                                    name="Codigo_Funcionario" required readonly>
                            </div>

                            <!-- Nombres -->
                            <div class="col-md-6">
                                <label for="editNombres" class="form-label fw-semibold">
                                    <i class="bi bi-person-vcard text-primary me-2"></i>Nombres
                                </label>
                                <input type="text" class="form-control" id="editNombres" name="Nombres" required>
                            </div>

                            <!-- Apellidos -->
                            <div class="col-md-6">
                                <label for="editApellidos" class="form-label fw-semibold">
                                    <i class="bi bi-person-badge text-primary me-2"></i>Apellidos
                                </label>
                                <input type="text" class="form-control" id="editApellidos" name="Apellidos" required>
                            </div>

                            <!-- DNI / Pasaporte -->
                            <div class="col-md-6">
                                <label for="editDNI" class="form-label fw-semibold">
                                    <i class="bi bi-credit-card-2-front text-primary me-2"></i>D.I.P / Pasaporte
                                </label>
                                <input type="text" class="form-control" id="editDNI" name="DNI_Pasaporte" required>
                            </div>

                            <!-- Fecha de Nacimiento -->
                            <div class="col-md-6">
                                <label for="editFechaNacimiento" class="form-label fw-semibold">
                                    <i class="bi bi-calendar-date text-primary me-2"></i>Fecha de Nacimiento
                                </label>
                                <input type="date" class="form-control" id="editFechaNacimiento"
                                    name="Fecha_Nacimiento">
                            </div>

                            <!-- Género -->
                            <div class="col-md-6">
                                <label for="editGenero" class="form-label fw-semibold">
                                    <i class="bi bi-gender-ambiguous text-primary me-2"></i>Género
                                </label>
                                <select class="form-select" id="editGenero" name="Genero">
                                    <option value="">Seleccionar</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>

                            <!-- Nacionalidad -->
                            <div class="col-md-6">
                                <label for="editNacionalidad" class="form-label fw-semibold">
                                    <i class="bi bi-flag text-primary me-2"></i>Nacionalidad
                                </label>
                                <input type="text" class="form-control" id="editNacionalidad" name="Nacionalidad">
                            </div>

                            <!-- Dirección -->
                            <div class="col-md-6">
                                <label for="editDireccion" class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>Dirección de Residencia
                                </label>
                                <input type="text" class="form-control" id="editDireccion" name="Direccion_Residencia">
                            </div>

                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <label for="editTelefono" class="form-label fw-semibold">
                                    <i class="bi bi-telephone text-primary me-2"></i>Teléfono de Contacto
                                </label>
                                <input type="text" class="form-control" id="editTelefono" name="Telefono_Contacto">
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="editEmail" class="form-label fw-semibold">
                                    <i class="bi bi-envelope text-primary me-2"></i>Email Oficial
                                </label>
                                <input type="email" class="form-control" id="editEmail" name="Email_Oficial">
                            </div>

                            <!-- Fecha de Ingreso -->
                            <div class="col-md-6">
                                <label for="editFechaIngreso" class="form-label fw-semibold">
                                    <i class="bi bi-calendar-check text-primary me-2"></i>Fecha de Ingreso
                                </label>
                                <input type="date" class="form-control" id="editFechaIngreso" name="Fecha_Ingreso"
                                    required>
                            </div>

                            <!-- Estado Laboral -->
                            <?php

                            $estado_actual = $f['Estado_Laboral'];

                            // 2. Definir los estados que SÍ se pueden seleccionar en este modal
                            $estados_editables = ['Activo', 'Permiso', 'Vacaciones'];


                            $opciones_finales = $estados_editables;

                            if (!in_array($estado_actual, $estados_editables)) {
                                // Si el estado actual es 'Cesado', 'Baja Temporal', etc., lo añadimos al inicio de la lista.
                                $opciones_finales = [$estado_actual] + $estados_editables;
                            }
                            ?>

                            <div class="col-md-6">
                                <label for="editEstadoLaboral" class="form-label fw-semibold">
                                    <i class="bi bi-briefcase text-primary me-2"></i>Estado Laboral
                                </label>
                                <select class="form-select" id="editEstadoLaboral" name="Estado_Laboral">
                                    <?php foreach ($opciones_finales as $estado) : ?>
                                        <?php
                                        // Determina si esta es la opción actual
                                        $is_selected = ($estado === $estado_actual) ? 'selected' : '';

                                        // Determina si la opción debe estar deshabilitada (si no está en los editables, se deshabilita)
                                        $is_disabled = in_array($estado, $estados_editables) ? '' : 'disabled';
                                        ?>
                                        <option value="<?= $estado ?>" <?= $is_selected ?> <?= $is_disabled ?>>
                                            <?= $estado ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Fotografía -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-camera-fill text-primary me-2"></i>Fotografía
                                </label>
                                <div class="mb-2">
                                    <img id="previewEditFoto" src="" alt="Foto actual"
                                        style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;">
                                </div>
                                <input type="file" class="form-control" id="editFoto" name="Fotografia"
                                    accept="image/*">
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="mt-4 d-flex justify-content-end gap-3">
                            <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i> Cancelar
                            </button> -->
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save me-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>






    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ... (Tu código JavaScript existente aquí) ...

            // Previsualización de la imagen
            const fotoInput = document.getElementById('foto');
            const previewFoto = document.getElementById('previewFoto');

            fotoInput.addEventListener('change', function(event) {
                if (event.target.files && event.target.files[0]) {
                    const reader = new FileReader(); // Crea un nuevo objeto FileReader

                    reader.onload = function(e) {
                        // Cuando el archivo se ha leído, actualiza el src de la imagen y la muestra
                        previewFoto.src = e.target.result;
                        previewFoto.style.display = 'block'; // Muestra la imagen
                    };

                    // Lee el contenido del archivo como una URL de datos (Data URL)
                    reader.readAsDataURL(event.target.files[0]);
                } else {
                    // Si no hay archivo seleccionado, oculta la imagen de previsualización
                    previewFoto.src = '#';
                    previewFoto.style.display = 'none';
                }
            });

            // Añadir funcionalidad al botón Cancelar para limpiar la previsualización
            const cancelBtn = document.getElementById('cancelBtn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    // Limpia el input de archivo
                    fotoInput.value = '';
                    // Oculta la previsualización
                    previewFoto.src = '#';
                    previewFoto.style.display = 'none';
                    // Cierra el modal (Bootstrap 5)
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addFuncionarioModal'));
                    if (modal) {
                        modal.hide();
                    }
                });
            }
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


    <!-- Modal de activar y desactivar -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggles = document.querySelectorAll('.funcionario-toggle');

            toggles.forEach(toggle => {
                toggle.addEventListener('change', function(e) {
                    // Detiene el cambio visual hasta que haya confirmación del servidor
                    e.preventDefault();

                    const toggleElement = this;
                    const funcionarioId = toggleElement.getAttribute('data-funcionario-id');
                    const funcionarioNombre = toggleElement.getAttribute('data-funcionario-nombres');

                    // Determina la acción deseada (true = Activar, false = Desactivar)
                    const activate = toggleElement.checked;

                    // Función que ejecuta la llamada Fetch (AJAX)
                    const executeFetch = (apiPath, bodyData, successMessage, action) => {
                        fetch(apiPath, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: bodyData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.close();
                                    // Se asume que el backend ya establece el mensaje de éxito en $_SESSION
                                    window.location.reload();
                                } else {
                                    Swal.fire('Error', data.message || `No se pudo ${action.toLowerCase()}.`, 'error');
                                    // Si falla, revierte el switch
                                    toggleElement.checked = !activate;
                                }
                            })
                            .catch(error => {
                                Swal.fire('Error de Conexión', `Hubo un problema: ${error.message}`, 'error');
                                // Si falla, revierte el switch
                                toggleElement.checked = !activate;
                            });
                    };


                    if (activate) {
                        // --- FLUJO DE ACTIVACIÓN ---
                        const apiPath = '../api/activar_funcionario.php';
                        const action = 'ACTIVAR';

                        Swal.fire({
                            title: `¿Confirmas ${action}?`,
                            html: `Estás a punto de ${action} al funcionario: <br> 
                            <span style="color: #198754; font-weight: bold;">${funcionarioNombre}</span>`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#198754',
                            cancelButtonColor: '#5a5d5fff',
                            confirmButtonText: `<i class="bi bi-check-circle"></i> Sí, ${action}`,
                            cancelButtonText: '<i class="bi bi-x-circle"></i> Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                executeFetch(apiPath, `id=${funcionarioId}`, 'activado', action);
                            } else {
                                toggleElement.checked = !activate;
                            }
                        });

                    } else {
                        // --- FLUJO DE DESACTIVACIÓN (CON OPCIONES) ---
                        const apiPathWithState = '../api/desactivar_funcionario.php'; // Usaremos esta API para manejar ambos estados de desactivación

                        Swal.fire({
                            title: 'Selecciona la acción a realizar',
                            html: `¿Cómo deseas desactivar a <span style="color: #dc3545; font-weight: bold;">${funcionarioNombre}</span>?`,
                            icon: 'warning',
                            showCancelButton: true,
                            showConfirmButton: true,
                            showDenyButton: true,

                            // Configuración de los tres botones
                            confirmButtonText: '<i class="bi bi-check-circle"></i> Baja Temporal',
                            confirmButtonColor: '#ebbb2cff',
                            denyButtonText: 'Cesar (Definitivo)',
                            denyButtonColor: '#970110ff',
                            cancelButtonText: '<i class="bi bi-x-circle"></i> Cancelar',
                            cancelButtonColor: '#5a5d5fff'

                        }).then((result) => {
                            let newState = null;
                            let action = '';

                            if (result.isConfirmed) {
                                // Opción 1: Baja Temporal
                                newState = 'Baja Temporal';
                                action = 'dar de Baja Temporal';
                            } else if (result.isDenied) {
                                // Opción 2: Cesado
                                newState = 'Cesado';
                                action = 'Cesar';
                            }

                            if (newState) {
                                // Si se eligió una opción, ejecuta la llamada AJAX
                                const bodyData = `id=${funcionarioId}&new_state=${newState}`;
                                executeFetch(apiPathWithState, bodyData, action, action);
                            } else {
                                // El usuario canceló o cerró el modal
                                toggleElement.checked = !activate;
                            }
                        });
                    }
                });
            });
        });
    </script>





    <!-- Script para que se abra el modal de funcionario desde el dashboard -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Función para obtener parámetros de la URL
            function getQueryParameter(name) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(name);
            }

            // Comprobamos el parámetro 'action=register'
            const action = getQueryParameter('action');
            const modalId = 'addFuncionarioModal'; // El ID de tu modal

            if (action === 'register') {
                const modalElement = document.getElementById(modalId);

                // Verificamos que el modal exista y que la clase 'bootstrap.Modal' esté disponible
                if (modalElement && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    // 1. Crear la instancia del modal
                    const funcionarioModal = new bootstrap.Modal(modalElement);

                    // 2. Mostrar el modal automáticamente
                    funcionarioModal.show();

                    // 3. Opcional: Limpiar el parámetro de la URL (para que al recargar la página, el modal no vuelva a aparecer)
                    if (history.replaceState) {
                        const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                        window.history.replaceState({
                            path: cleanUrl
                        }, '', cleanUrl);
                    }
                } else {
                    console.error("El modal con ID #" + modalId + " no se encontró o Bootstrap JS no se cargó correctamente.");
                }
            }
        });
    </script>




    <?php
    include_once '../includes/footer.php';
    ?>