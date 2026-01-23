<?php
include_once '../includes/header.php';
?>





<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="container-fluid p-0">
        <div class="row g-0">


            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <?php
            include_once '../includes/silebar_admin.php';
            ?>


            <div class="main-content" id="mainContent">


                <div class="header-section">
                    <div class="row align-items-center">

                        <div class="col-md-12 text-md-end mt-3 mt-md-0">
                            <div
                                class="d-flex justify-content-md-end align-items-center gap-2 flex-wrap justify-content-center">
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addInstruccionModal">
                                    <i class="bi bi-plus-circle me-1"></i> Añadir Instruccion
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


                <div class="container-fluid px-4">
                    <div>
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
                    </div>

                    <div class="table-custom mb-4 p-4">
                        <!-- Filtros para imprimir Instrucción -->
                        <div class="card border-0 shadow-sm mb-4 bg-light">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold"><i class="bi bi-person me-1"></i>Funcionario:</label>
                                        <input type="text" class="form-control" id="filterFuncionario" placeholder="Nombre del funcionario...">
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold"><i class="bi bi-flag me-1"></i>Estado:</label>
                                        <select class="form-select" id="filterEstado">
                                            <option value="">Todos</option>
                                            <option value="PENDIENTE">Pendiente</option>
                                            <option value="EN-PROCESO">En Proceso</option>
                                            <option value="FINALIZADO">Finalizado</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold"><i class="bi bi-calendar3 me-1"></i>Rango de Fechas:</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="filterFechaInicio">
                                            <span class="input-group-text">a</span>
                                            <input type="date" class="form-control" id="filterFechaFin">
                                        </div>
                                    </div>

                                    <div class="col-md-3 d-flex gap-2">
                                        <button class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                        <button class="btn btn-dark w-100" onclick="imprimirReporte()">
                                            <i class="bi bi-printer me-1"></i> Imprimir
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-semibold">Listado de Funcionarios con Instrucciones</h5>
                        </div>
                        <div class="table-responsive">

                            <?php
                            try {
                                $pdo = new PDO($dsn, $user, $pass, $options);
                            } catch (PDOException $e) {
                                die("Error de conexión: " . $e->getMessage());
                            }
                            $sql = "SELECT i.*, f.Nombre, f.Apellidos, f.Dip_Pasaporte, f.Foto, u.Nombre_Usuario AS CreadorNombre 
                                    FROM tbl_instrucciones i
                                    JOIN funcionarios f ON i.ID_Funcionario = f.Id_funcionario 
                                    JOIN tbl_usuarios u ON i.Usuario_creador = u.ID_Usuario
                                    ORDER BY i.ID_Instruccion DESC";

                            $stmt = $pdo->query($sql);
                            $instrucciones = $stmt->fetchAll();
                            ?>

                            <table class="table table-hover align-middle mb-0" id="asignacionesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Funcionario</th>
                                        <th>Título</th>
                                        <th>Mensaje</th>
                                        
                                        <th>Estado</th>
                                        <th>Fecha Envío</th>
                                        <th>Leído</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="funcionariosTableBody">
                                    <?php foreach ($instrucciones as $instr): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($instr['ID_Instruccion']) ?></td>
                                            <td><?= htmlspecialchars($instr['Nombre'] . ' ' . $instr['Apellidos']) ?></td>
                                            <td><?= htmlspecialchars($instr['Titulo']) ?></td>

                                            <td>
                                                <?= nl2br(htmlspecialchars(mb_strimwidth($instr['Mensaje'], 0, 30, '...'))) ?>
                                            </td>
                                            <td>
                                                <?php if ($instr['Estado'] == 'PENDIENTE'): ?>
                                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                                <?php elseif ($instr['Estado'] == 'EN-PROCESO'): ?>
                                                    <span class="badge bg-info text-dark">En Proceso</span>
                                                <?php elseif ($instr['Estado'] == 'FINALIZADO'): ?>
                                                    <span class="badge bg-success">Finalizado</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Sin Estado</span>
                                                <?php endif; ?>
                                            </td>

                                            <td><?= htmlspecialchars($instr['Fecha_Envio']) ?></td>
                                            <td>
                                                <?php if ($instr['Leido'] == 1): ?>
                                                    <span class="badge bg-success">Sí</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger ">No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <!-- Boton de detalle de Instrucción -->

                                                    <button class="btn btn-sm btn-info btn-detalles-instruccion"
                                                        data-id="<?= $instr['ID_Instruccion'] ?>"
                                                        data-funcionario="<?= htmlspecialchars($instr['Nombre'] . ' ' . $instr['Apellidos']) ?>"
                                                        data-dni="<?= htmlspecialchars($instr['Dip_Pasaporte']) ?>"
                                                        data-titulo="<?= htmlspecialchars($instr['Titulo']) ?>"
                                                        data-mensaje="<?= htmlspecialchars($instr['Mensaje']) ?>"
                                                        data-fecha="<?= htmlspecialchars($instr['Fecha_Envio']) ?>"
                                                        data-leido="<?= $instr['Leido'] ?>"
                                                        data-usuario-registro="<?= htmlspecialchars($instr['CreadorNombre']) ?>"
                                                        data-estado="<?= $instr['Estado'] ?>"
                                                        data-foto="<?= htmlspecialchars($instr['Foto']) ?>"
                                                        data-fecha-lectura="<?= htmlspecialchars($instr['Fecha_Lectura']) ?>"
                                                        title="Ver Detalles">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <!-- Boton de Leido -->
                                                    <?php if ($instr['Leido'] == 0): ?>
                                                        <button class="btn btn-sm btn-warning btn-editar-instruccion"
                                                            data-id="<?= $instr['ID_Instruccion'] ?>"
                                                            data-funcionario="<?= htmlspecialchars($instr['Nombre'] . ' ' . $instr['Apellidos']) ?>"
                                                            data-titulo="<?= htmlspecialchars($instr['Titulo']) ?>"
                                                            data-mensaje="<?= htmlspecialchars($instr['Mensaje']) ?>"
                                                            data-leido="<?= $instr['Leido'] ?>"
                                                            title="Editar Instrucción">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <!-- Boton de finalizar instrucción -->
                                                    <?php if ($instr['Estado'] === 'EN-PROCESO'): ?>
                                                        <button class="btn btn-sm btn-success btn-finalizar-instruccion"
                                                            data-id="<?= $instr['ID_Instruccion'] ?>"
                                                            title="Finalizar Instrucción">
                                                            <i class="bi bi-check-circle"></i>
                                                        </button>
                                                    <?php endif; ?>
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




    <!-- Modal para Registrar Instrucciones diarias -->
    <div class="modal fade" id="addInstruccionModal" tabindex="-1" aria-labelledby="addInstruccionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addInstruccionModalLabel">
                        <i class="bi bi-clipboard-check me-2"></i>Registrar Instrucción
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">

                    <!-- Buscador -->
                    <div class="mb-4 mt-2">
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

                    <!-- Formulario de instrucción -->
                    <form method="POST" action="../api/guardar_instruccion.php">
                        <input type="hidden" name="ID_Funcionario" id="ID_Funcionario">

                        <div class="row g-3">
                            <!-- Título -->
                            <div class="col-md-12">
                                <label for="titulo" class="form-label fw-semibold">
                                    <i class="bi bi-card-text text-primary me-2"></i>Título
                                </label>
                                <input type="text" name="Titulo" class="form-control" id="titulo" required>
                            </div>

                            <!-- Mensaje -->
                            <div class="col-md-12">
                                <label for="mensaje" class="form-label fw-semibold">
                                    <i class="bi bi-chat-square-text text-primary me-2"></i>Mensaje
                                </label>
                                <textarea name="Mensaje" class="form-control" id="mensaje" rows="5" required></textarea>
                            </div>
                        </div>

                        <!-- Botón enviar -->
                        <div class="mt-4 d-flex justify-content-end mb-3">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-2"></i>Guardar Instrucción
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>









    <!-- Modal Editar Instrucción -->
    <div class="modal fade" id="editInstruccionModal" tabindex="-1" aria-labelledby="editInstruccionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editInstruccionModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Editar Instrucción
                    </h5>
                    <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">

                    <form method="POST" action="../api/actualizar_instruccion.php" id="formEditarInstruccion">
                        <input type="hidden" name="ID_Instruccion" id="editID_Instruccion">
                        <input type="hidden" name="ID_Funcionario" id="editID_Funcionario">

                        <!-- Funcionario -->
                        <div class="mb-3 mt-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-person-badge me-2"></i>Funcionario
                            </label>
                            <input type="text" id="editFuncionarioNombre" class="form-control bg-light"
                                readonly tabindex="-1" style="pointer-events: none; cursor: default;">
                        </div>

                        <!-- Título -->
                        <div class="mb-3">
                            <label for="editTitulo" class="form-label fw-semibold"><i class="bi bi-card-text me-2"></i>Título</label>
                            <input type="text" name="Titulo" id="editTitulo" class="form-control" required>
                        </div>

                        <!-- Mensaje -->
                        <div class="mb-3">
                            <label for="editMensaje" class="form-label fw-semibold"><i class="bi bi-chat-square-text me-2"></i>Mensaje</label>
                            <textarea name="Mensaje" id="editMensaje" class="form-control" rows="4" required></textarea>
                        </div>

                        <!-- Leído -->
                        <div class="mb-3">
                            <label for="displayLeido" class="form-label fw-semibold">
                                <i class="bi bi-check2-square me-2"></i>Estado de Lectura
                            </label>

                            <?php

                            $valor_leido = 0;

                            // Simplificación de la lógica:
                            $texto_leido = ($valor_leido == 1) ? 'Sí Leído' : 'No Leído';
                            $clase_color = ($valor_leido == 0) ? 'text-danger' : 'text-success'; // text-success es opcional para el Sí
                            ?>

                            <input type="text"
                                id="displayLeido"
                                class="form-control <?php echo $clase_color; ?> fw-bold"
                                value="<?php echo $texto_leido; ?>"
                                readonly>

                            <input type="hidden"
                                name="Leido"
                                value="<?php echo $valor_leido; ?>">
                        </div>


                        <!-- Botones -->
                        <div class="d-flex justify-content-end mb-3">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save me-1"></i>Actualizar Instruccion
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>




    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const listaFuncionarios = document.getElementById('listaFuncionarios');
            const searchFuncionario = document.getElementById('searchFuncionario');
            const funcionarioSeleccionado = document.getElementById('funcionarioSeleccionado');
            const nombreFuncionario = document.getElementById('nombreFuncionario');
            const ID_Funcionario = document.getElementById('ID_Funcionario');
            const quitarSeleccion = document.getElementById('quitarSeleccion');
            const formInstruccion = document.querySelector('#addInstruccionModal form');

            let todosFuncionarios = [];

            // Cargar lista de funcionarios al iniciar
            fetch('../api/obtener_funcionarios.php')
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        console.error("Error desde PHP:", data.error);
                    } else {
                        todosFuncionarios = data;
                        console.log("Funcionarios cargados:", todosFuncionarios.length);
                    }
                })
                .catch(err => console.error('Error de conexión:', err));

            // Buscador dinámico
            searchFuncionario.addEventListener('input', () => {
                const filtro = searchFuncionario.value.trim().toLowerCase();

                // Si el campo está vacío, limpiamos la lista y salimos
                if (filtro === '') {
                    listaFuncionarios.innerHTML = '';
                    return;
                }

                // Filtrar
                const coincidencias = todosFuncionarios.filter(func => {
                    const nom = func.Nombre ? func.Nombre.toLowerCase() : '';
                    const ape = func.Apellidos ? func.Apellidos.toLowerCase() : '';
                    const cod = func.CODIGO ? func.CODIGO.toLowerCase() : '';
                    return nom.includes(filtro) || ape.includes(filtro) || cod.includes(filtro);
                });

                // Dibujar resultados
                listaFuncionarios.innerHTML = '';
                coincidencias.forEach(func => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    // Usamos clases de Bootstrap para que sea visible
                    item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                    item.style.zIndex = "1050"; // Asegurar que se vea sobre el modal

                    item.innerHTML = `
                    <div>
                        <strong>${func.Nombre} ${func.Apellidos}</strong><br>
                        <small class="text-muted">${func.CODIGO}</small>
                    </div>
                `;

                    item.addEventListener('click', () => {
                        ID_Funcionario.value = func.Id_funcionario;
                        nombreFuncionario.textContent = `${func.Nombre} ${func.Apellidos}`;
                        funcionarioSeleccionado.classList.remove('d-none');
                        listaFuncionarios.innerHTML = '';
                        searchFuncionario.value = '';
                    });

                    listaFuncionarios.appendChild(item);
                });
            });

            // Quitar selección
            quitarSeleccion.addEventListener('click', () => {
                ID_Funcionario.value = '';
                funcionarioSeleccionado.classList.add('d-none');
            });
        });
    </script>






    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Definimos una variable para el modal
            let miModalElement = document.getElementById('editInstruccionModal');
            let bsModal = new bootstrap.Modal(miModalElement);

            document.querySelectorAll('.btn-editar-instruccion').forEach(boton => {
                boton.addEventListener('click', function() {
                    const datos = this.dataset;

                    // Llenar campos...
                    document.getElementById('editID_Instruccion').value = datos.id;
                    document.getElementById('editFuncionarioNombre').value = datos.funcionario;
                    document.getElementById('editTitulo').value = datos.titulo;
                    document.getElementById('editMensaje').value = datos.mensaje;

                    // Mostrar el modal usando la instancia ya creada
                    bsModal.show();
                });
            });

            // ESCUCHAR CUANDO SE CIERRA: Para limpiar cualquier sombra rebelde
            miModalElement.addEventListener('hidden.bs.modal', function() {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(b => b.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
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
            // 1. Detectar clic en los botones de editar instrucción
            const botonesEditar = document.querySelectorAll('.btn-editar-instruccion');

            botonesEditar.forEach(boton => {
                boton.addEventListener('click', function() {
                    // 2. Extraer datos del dataset del botón
                    const datos = this.dataset;

                    // 3. Mapear datos a los IDs de tu Modal
                    document.getElementById('editID_Instruccion').value = datos.id;
                    document.getElementById('editFuncionarioNombre').value = datos.funcionario;
                    document.getElementById('editTitulo').value = datos.titulo;
                    document.getElementById('editMensaje').value = datos.mensaje;

                    // 4. Lógica para el estado de lectura (el campo readonly y el hidden)
                    const inputDisplayLeido = document.getElementById('displayLeido');
                    const inputHiddenLeido = document.querySelector('input[name="Leido"]');

                    if (datos.leido == "1") {
                        inputDisplayLeido.value = "Sí Leído";
                        inputDisplayLeido.classList.remove('text-danger');
                        inputDisplayLeido.classList.add('text-success');
                        inputHiddenLeido.value = "1";
                    } else {
                        inputDisplayLeido.value = "No Leído";
                        inputDisplayLeido.classList.remove('text-success');
                        inputDisplayLeido.classList.add('text-danger');
                        inputHiddenLeido.value = "0";
                    }

                    // 5. Abrir el modal manualmente
                    const modal = new bootstrap.Modal(document.getElementById('editInstruccionModal'));
                    modal.show();
                });
            });
        });
    </script>







    <!-- Modal de ver instrucciones -->
    <script>
        function mostrarDetallesInstruccion(id, funcionario, dni, titulo, mensaje, fecha, leido, creador, fotoUrl, estado, fechaLectura) {
            const rutaBaseFotos = '../api/';
            const urlFoto = (fotoUrl && fotoUrl !== '') ? (rutaBaseFotos + fotoUrl) : (rutaBaseFotos + 'default.jpg');

            // Lógica para el badge
            let badgeHtml = '';
            if (leido == 1) {
                badgeHtml = `<span class="badge rounded-pill bg-success-subtle text-success border border-success px-3 py-2">
                        <i class="bi bi-check-all me-1"></i> Instrucción Abierta
                     </span>`;
            } else {
                badgeHtml = `<span class="badge rounded-pill bg-warning-subtle text-warning border border-warning px-3 py-2">
                        <i class="bi bi-clock me-1"></i> ${estado}
                     </span>`;
            }

            // Lógica para mostrar la hora de lectura
            const infoLectura = (leido == 1 && fechaLectura) ?
                `<div class="text-success fw-bold">${fechaLectura}</div>` :
                `<div class="text-muted small italic">Aún no visualizado</div>`;

            // Llamada al API para marcar como leída
            if (leido == 0) {
                fetch('../api/marcar_leida.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `id=${id}`
                }).catch(err => console.error("Error:", err));
            }

            const htmlContent = `
    <div class="text-start">
        <div class="d-flex align-items-center p-4 mt-4 mb-4 rounded-3 shadow-sm text-white" 
             style="background-color: #2c3e50; margin-right: 10px; margin-left: 10px;">
            <div class="position-relative">
                <img src="${urlFoto}" class="rounded-circle border border-4 border-white-50 shadow"
                     style="width: 100px; height: 100px; object-fit: cover;">
            </div>
            <div class="ms-4 me-5">
                <h4 class="mb-1 fw-bold">${funcionario}</h4>
                <p class="mb-2 small opacity-75"><i class="bi bi-person-vcard me-2"></i>DIP: ${dni}</p>
                ${badgeHtml}
            </div>
        </div>

        <div class="px-4">
            <div class="row g-3 mb-4">
                <div class="col-md-7">
                    <div class="card border-0 bg-light h-100 p-3 border-start border-4 border-primary shadow-sm">
                        <small class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.7rem;">Título</small>
                        <div class="text-dark fw-bold">${titulo}</div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card border-0 bg-light h-100 p-3 border-start border-4 border-dark shadow-sm">
                        <small class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.7rem;">Creado por:</small>
                        <div class="text-dark fw-bold">${creador}</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card border-0 bg-light p-3 shadow-sm">
                        <small class="text-muted fw-bold" style="font-size: 0.7rem;">FECHA DE REGISTRO</small>
                        <div class="text-secondary small fw-semibold">${fecha}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 bg-light p-3 shadow-sm border-start border-3 border-success">
                        <small class="text-muted fw-bold" style="font-size: 0.7rem;">HORA DE APERTURA</small>
                        <div class="small">${infoLectura}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-dark text-white border-0 py-2">
                    <span class="fw-semibold small text-uppercase">Contenido del Mensaje</span>
                </div>
                <div class="card-body bg-white border border-top-0 rounded-bottom" style="max-height: 200px; overflow-y: auto;">
                    <p class="card-text text-dark" style="white-space: pre-wrap;">${mensaje}</p>
                </div>
            </div>
        </div>
    </div>`;

            Swal.fire({
                html: htmlContent,
                showCloseButton: true,
                showConfirmButton: false,
                width: '800px',
                customClass: {
                    popup: 'rounded-4 border-0'
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-detalles-instruccion');
                if (btn) {
                    e.preventDefault();
                    mostrarDetallesInstruccion(
                        btn.getAttribute('data-id'),
                        btn.getAttribute('data-funcionario'),
                        btn.getAttribute('data-dni'),
                        btn.getAttribute('data-titulo'),
                        btn.getAttribute('data-mensaje'),
                        btn.getAttribute('data-fecha'),
                        btn.getAttribute('data-leido'),
                        btn.getAttribute('data-usuario-registro'),
                        btn.getAttribute('data-foto'),
                        btn.getAttribute('data-estado'),
                        btn.getAttribute('data-fecha-lectura') // <-- Nuevo atributo capturado
                    );
                }
            });
        });
    </script>





    <!-- Script para los filtros de la Instruccion -->
    <script>
        const inputsFiltro = ['filterFuncionario', 'filterEstado', 'filterFechaInicio', 'filterFechaFin'];

        inputsFiltro.forEach(id => {
            document.getElementById(id).addEventListener('input', aplicarFiltros);
        });

        function aplicarFiltros() {
            const valFunc = document.getElementById('filterFuncionario').value.toLowerCase();
            const valEst = document.getElementById('filterEstado').value.toUpperCase();
            const valFInicio = document.getElementById('filterFechaInicio').value;
            const valFFin = document.getElementById('filterFechaFin').value;

            const filas = document.querySelectorAll('#funcionariosTableBody tr:not(.no-results)');
            let contadorVisibles = 0;

            filas.forEach(fila => {
                const txtNombre = fila.cells[1].innerText.toLowerCase();
                // Limpiamos el texto del estado para asegurar que coincida (quitando espacios/saltos)
                const txtEstado = fila.cells[4].innerText.toUpperCase().trim().replace(/\s+/g, ' ');
                const txtFecha = fila.cells[5].innerText.split(' ')[0];

                let visible = true;

                if (valFunc && !txtNombre.includes(valFunc)) visible = false;
                // Cambiamos includes por una lógica que soporte el guion de EN-PROCESO
                if (valEst && !txtEstado.includes(valEst.replace('-', ' '))) {
                    if (!txtEstado.includes(valEst)) visible = false;
                }
                if (valFInicio && txtFecha < valFInicio) visible = false;
                if (valFFin && txtFecha > valFFin) visible = false;

                fila.style.display = visible ? '' : 'none';
                if (visible) contadorVisibles++;
            });

            // Gestionar mensaje de "No se encontraron coincidencias"
            let filaNoResultados = document.getElementById('filaNoResultados');
            if (contadorVisibles === 0) {
                if (!filaNoResultados) {
                    filaNoResultados = document.createElement('tr');
                    filaNoResultados.id = 'filaNoResultados';
                    filaNoResultados.className = 'no-results text-center';
                    filaNoResultados.innerHTML = `<td colspan="8" class="py-5 text-muted">
                <i class="bi bi-search mb-2 d-block fs-2"></i>
                No se encontraron coincidencias con los filtros aplicados.
            </td>`;
                    document.getElementById('funcionariosTableBody').appendChild(filaNoResultados);
                }
            } else if (filaNoResultados) {
                filaNoResultados.remove();
            }
        }

        function limpiarFiltros() {
            // 1. Resetear todos los inputs a su valor inicial
            inputsFiltro.forEach(id => {
                const input = document.getElementById(id);
                if (input) input.value = '';
            });

            // 2. Eliminar físicamente el mensaje de "No se encontraron coincidencias" si existe
            const filaNoResultados = document.getElementById('filaNoResultados');
            if (filaNoResultados) {
                filaNoResultados.remove();
            }

            // 3. Volver a mostrar todas las filas de la tabla
            const filas = document.querySelectorAll('#funcionariosTableBody tr');
            filas.forEach(fila => {
                fila.style.display = '';
            });

            console.log("Filtros limpiados y tabla restablecida");
        }

        function imprimirReporte() {
            const tablaOriginal = document.getElementById("asignacionesTable");
            const tablaClonada = tablaOriginal.cloneNode(true);

            // Quitar columna de acciones
            const ths = tablaClonada.querySelectorAll('thead th');
            ths[ths.length - 1].remove();

            const filasBody = tablaClonada.querySelectorAll('tbody tr:not(.no-results)');
            let hayDatos = false;

            filasBody.forEach(fila => {
                const idOriginal = fila.cells[0].innerText;
                const filaOriginal = Array.from(tablaOriginal.querySelectorAll('tbody tr'))
                    .find(f => f.cells[0].innerText === idOriginal);

                if (filaOriginal && filaOriginal.style.display === 'none') {
                    fila.remove();
                } else {
                    fila.lastElementChild.remove();
                    hayDatos = true;
                }
            });

            if (!hayDatos) {
                Swal.fire('Atención', 'No hay datos para mostrar en el reporte.', 'warning');
                return;
            }

            const ventanaPrint = window.open('', '', 'height=900,width=1100');

            // Diseño mejorado del Reporte
            ventanaPrint.document.write(`
        <html>
        <head>
            <title>Vista Previa - Reporte de Instrucciones</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { padding: 50px; background-color: #f4f7f6; color: #333; }
                .sheet { 
                    background: white; 
                    padding: 40px; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.1); 
                    border-radius: 8px;
                    min-height: 297mm;
                }
                .report-header { 
                    border-bottom: 3px solid #0d6efd; 
                    margin-bottom: 30px; 
                    padding-bottom: 20px; 
                }
                .logo-placeholder {
                    font-weight: 800;
                    letter-spacing: -1px;
                    color: #0d6efd;
                    font-size: 2rem;
                }
                table { font-size: 0.9rem; }
                thead { background-color: #f8f9fa; }
                .no-print-btn {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    z-index: 9999;
                }
                @media print {
                    body { background: white; padding: 0; }
                    .sheet { box-shadow: none; border: none; padding: 0; }
                    .no-print-btn { display: none; }
                }
            </style>
        </head>
        <body>
            <button class="btn btn-primary btn-lg rounded-pill no-print-btn shadow" onclick="window.print()">
                <i class="bi bi-printer"></i> Confirmar e Imprimir
            </button>

            <div class="sheet mx-auto">
                <div class="report-header d-flex justify-content-between align-items-center">
                    <div>
                        <div class="logo-placeholder">THEMIS</div>
                        <h5 class="text-uppercase fw-bold mb-0">Ministerio de Justicia</h5>
                        <p class="text-muted mb-0">Sistema de Control de Instrucciones</p>
                    </div>
                    <div class="text-end">
                        <h4 class="text-primary">REPORTE ADMINISTRATIVO</h4>
                        <p class="small mb-0"><strong>Generado:</strong> ${new Date().toLocaleString()}</p>
                        <p class="small mb-0"><strong>Funcionario:</strong> ${document.getElementById('filterFuncionario').value || 'Todos'}</p>
                    </div>
                </div>

                <div class="table-responsive">
                    ${tablaClonada.outerHTML}
                </div>

                <div class="mt-5 row">
                    <div class="col-4 text-center">
                        <div style="border-top: 1px solid #ccc; margin-top: 50px;" class="pt-2 small text-muted">Firma Responsable</div>
                    </div>
                    <div class="col-4 offset-4 text-center">
                        <div style="border-top: 1px solid #ccc; margin-top: 50px;" class="pt-2 small text-muted">Sello de Recibido</div>
                    </div>
                </div>
            </div>
        </body>
        </html>
    `);

            ventanaPrint.document.close();
        }
    </script>









    <!-- Modal de confirmacion de finalizar Instruccion -->
    <div class="modal fade" id="modalConfirmarFinalizado" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-success" id="modalLabel">
                        <i class="bi bi-check2-all me-2"></i>Finalizar Tarea
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST" action="../api/finalizar_instruccion.php">
                    <div class="modal-body py-4 text-center">
                        <div class="mb-3">
                            <i class="bi bi-question-circle text-success opacity-50" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">¿Confirmar acción?</h5>
                        <p class="text-muted px-4 mb-0">
                            Está a punto de marcar que esta instrucción se ha <span class="badge bg-success-subtle text-success border border-success-subtle">FINALIZADO</span>
                        </p>

                        <input type="hidden" name="id" id="input_finalizar_id">
                        <input type="hidden" name="action" value="finalizar_tarea">
                    </div>

                    <div class="modal-footer border-top-0 d-flex justify-content-center pb-4">
                        <button type="button" class="btn btn-light px-4 me-2" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success px-4 fw-semibold">
                            <i class="bi bi-check-lg me-1"></i> Sí, finalizar ahora
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const botonesFinalizar = document.querySelectorAll('.btn-finalizar-instruccion');
            const inputId = document.getElementById('input_finalizar_id');
            const modalConfirm = new bootstrap.Modal(document.getElementById('modalConfirmarFinalizado'));

            botonesFinalizar.forEach(boton => {
                boton.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    inputId.value = id;
                    modalConfirm.show();
                });
            });
        });
    </script>





    <?php
    include_once '../includes/footer.php';
    ?>