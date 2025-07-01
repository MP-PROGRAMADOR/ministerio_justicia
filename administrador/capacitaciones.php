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
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCapacitacionModal">
                                    <i class="bi bi-journal-plus me-1"></i> Añadir Capacitación
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

                            // Consulta para obtener capacitaciones con datos del funcionario
                            $sql = "SELECT c.ID_Capacitacion, c.Nombre_Curso, c.Institucion_Organizadora, c.Fecha_Inicio_Curso, c.Fecha_Fin_Curso, c.Certificado_URL,
               f.Nombres, f.Apellidos, f.DNI_Pasaporte
        FROM tbl_capacitaciones c
        JOIN tbl_funcionarios f ON c.ID_Funcionario = f.ID_Funcionario
        ORDER BY c.ID_Capacitacion DESC";
                            $stmt = $pdo->query($sql);
                            $capacitaciones = $stmt->fetchAll();
                            ?>

                            <table class="table table-hover align-middle mb-0" id="funcionariosTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Funcionario</th>
                                        <th>DNI</th>
                                        <th>Curso</th>
                                        <th>Institución</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Certificado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="funcionariosTableBody">
                                    <?php foreach ($capacitaciones as $cap): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($cap['ID_Capacitacion']) ?></td>
                                            <td><?= htmlspecialchars($cap['Nombres'] . ' ' . $cap['Apellidos']) ?></td>
                                            <td><?= htmlspecialchars($cap['DNI_Pasaporte']) ?></td>
                                            <td><?= htmlspecialchars($cap['Nombre_Curso']) ?></td>
                                            <td><?= htmlspecialchars($cap['Institucion_Organizadora']) ?></td>
                                            <td><?= htmlspecialchars($cap['Fecha_Inicio_Curso']) ?></td>
                                            <td><?= htmlspecialchars($cap['Fecha_Fin_Curso']) ?></td>
                                            <td>
                                                <?php if (!empty($cap['Certificado_URL'])): ?>
                                                    <a href="../<?= htmlspecialchars($cap['Certificado_URL']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-file-earmark-text"></i> Ver
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Ninguno</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <!-- Aquí podrías poner botones para editar o eliminar -->
                                                <button class="btn btn-sm btn-warning btn-editar-capacitacion"
                                                    data-id="<?= $cap['ID_Capacitacion'] ?>"
                                                    data-funcionario="<?= htmlspecialchars($cap['Nombres'] . ' ' . $cap['Apellidos']) ?>"
                                                    data-curso="<?= htmlspecialchars($cap['Nombre_Curso']) ?>"
                                                    data-institucion="<?= htmlspecialchars($cap['Institucion_Organizadora']) ?>"
                                                    data-inicio="<?= $cap['Fecha_Inicio_Curso'] ?>"
                                                    data-fin="<?= $cap['Fecha_Fin_Curso'] ?>"
                                                    data-certificado="<?= htmlspecialchars($cap['Certificado_URL']) ?>"
                                                    title="Editar Capacitacion">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <button class="btn btn-sm btn-danger" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
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
    <div class="modal fade" id="addCapacitacionModal" tabindex="-1" aria-labelledby="addCapacitacionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addCapacitacionModalLabel">
                        <i class="bi bi-journal-text me-2"></i>Registrar Capacitación
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

                    <!-- Formulario de capacitación -->
                    <form method="POST" action="../api/guardar_capacitacion.php" enctype="multipart/form-data">
                        <input type="hidden" name="ID_Funcionario" id="ID_Funcionario">

                        <div class="row g-3">

                            <!-- Nombre del Curso -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-book text-primary me-2"></i>Nombre del Curso
                                </label>
                                <input type="text" name="Nombre_Curso" class="form-control" required>
                            </div>

                            <!-- Institución Organizadora -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-building text-primary me-2"></i>Institución Organizadora
                                </label>
                                <input type="text" name="Institucion_Organizadora" class="form-control" required>
                            </div>

                            <!-- Fechas -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-event text-primary me-2"></i>Fecha Inicio
                                </label>
                                <input type="date" name="Fecha_Inicio_Curso" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-check text-primary me-2"></i>Fecha Fin
                                </label>
                                <input type="date" name="Fecha_Fin_Curso" class="form-control">
                            </div>

                            <!-- Certificado -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-upload text-primary me-2"></i>Certificado (opcional)
                                </label>
                                <input type="file" name="Certificado_URL" class="form-control" accept=".pdf,.jpg,.png,.doc,.docx">
                            </div>

                        </div>

                        <!-- Botón enviar -->
                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-2"></i>Registrar Capacitación
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>









    <!-- Modal para Editar Capacitación -->
<div class="modal fade" id="editCapacitacionModal" tabindex="-1" aria-labelledby="editCapacitacionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="editCapacitacionModalLabel">
          <i class="bi bi-pencil-square me-2"></i>Editar Capacitación
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../api/actualizar_capacitacion.php" enctype="multipart/form-data">
          <input type="hidden" name="ID_Capacitacion" id="editID_Capacitacion">
          <input type="hidden" name="ID_Funcionario" id="editID_Funcionario">

          <div class="mb-3">
            <label for="editNombreCurso" class="form-label fw-semibold">Nombre del Curso</label>
            <input type="text" name="Nombre_Curso" id="editNombreCurso" class="form-control" required maxlength="200">
          </div>

          <div class="mb-3">
            <label for="editInstitucion" class="form-label fw-semibold">Institución Organizadora</label>
            <input type="text" name="Institucion_Organizadora" id="editInstitucion" class="form-control" required maxlength="200">
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label for="editFechaInicio" class="form-label fw-semibold">Fecha Inicio</label>
              <input type="date" name="Fecha_Inicio_Curso" id="editFechaInicio" class="form-control">
            </div>
            <div class="col-md-6">
              <label for="editFechaFin" class="form-label fw-semibold">Fecha Fin</label>
              <input type="date" name="Fecha_Fin_Curso" id="editFechaFin" class="form-control">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Certificado Actual:</label>
            <div id="certificadoActualContainer" class="mb-2">
              <!-- Aquí se puede mostrar enlace o texto con el certificado actual -->
            </div>
            <label for="editCertificado" class="form-label fw-semibold">Subir nuevo certificado (opcional)</label>
            <input type="file" name="Certificado_URL" id="editCertificado" class="form-control" accept=".pdf,.jpg,.png,.doc,.docx">
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-2"></i>Actualizar Capacitación
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  // Función para abrir el modal y rellenar los datos (ejemplo con jQuery)
document.querySelectorAll('.btn-editar-capacitacion').forEach(button => {
  button.addEventListener('click', () => {
    const id = button.dataset.id;
    const funcionario = button.dataset.funcionario;
    const curso = button.dataset.curso;
    const institucion = button.dataset.institucion;
    const inicio = button.dataset.inicio;
    const fin = button.dataset.fin;
    const certificado = button.dataset.certificado;

    document.getElementById('editID_Capacitacion').value = id;
    document.getElementById('editNombreCurso').value = curso;
    document.getElementById('editInstitucion').value = institucion;
    document.getElementById('editFechaInicio').value = inicio;
    document.getElementById('editFechaFin').value = fin;

    const certificadoContainer = document.getElementById('certificadoActualContainer');
    if(certificado) {
      certificadoContainer.innerHTML = `
        <a href="../${certificado}" target="_blank" class="btn btn-sm btn-outline-primary">
          <i class="bi bi-file-earmark-text"></i> Ver certificado actual
        </a>`;
    } else {
      certificadoContainer.innerHTML = '<span class="text-muted">Ninguno</span>';
    }

    // Mostrar el modal con Bootstrap 5
    const modal = new bootstrap.Modal(document.getElementById('editCapacitacionModal'));
    modal.show();
  });
});


</script>







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