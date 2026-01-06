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
                            <h5 class="mb-0 fw-semibold">Listado de Funcionarios con Instrucciones</h5>
                        </div>
                        <div class="table-responsive">

                            <?php
                            try {
                                $pdo = new PDO($dsn, $user, $pass, $options);
                            } catch (PDOException $e) {
                                die("Error de conexión: " . $e->getMessage());
                            }

                            // Consulta de instrucciones con datos del funcionario
                            $sql = "SELECT i.*, f.Nombre, f.Apellidos, f.Dip_Pasaporte, f.Foto
                            FROM tbl_instrucciones i
                            JOIN funcionarios f ON i.ID_Funcionario = f.ID_Funcionario
                            ORDER BY i.ID_Instruccion DESC";

                            $stmt = $pdo->query($sql);
                            $instrucciones = $stmt->fetchAll();
                            ?>

                            <table class="table table-hover align-middle mb-0" id="asignacionesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Funcionario</th>
                                        <th>DNI</th>
                                        <th>Título</th>
                                        <th>Mensaje</th>
                                        <th>Fecha Envío</th>
                                        <th>Leído</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="funcionariosTableBody">
                                    <?php foreach ($instrucciones as $instr): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($instr['ID_Instruccion']) ?></td>
                                            <td><?= htmlspecialchars($instr['Nombres'] . ' ' . $instr['Apellidos']) ?></td>
                                            <td><?= htmlspecialchars($instr['DNI_Pasaporte']) ?></td>
                                            <td><?= htmlspecialchars($instr['Titulo']) ?></td>

                                            <td>
                                                <?= nl2br(htmlspecialchars(mb_strimwidth($instr['Mensaje'], 0, 30, '...'))) ?>
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
                                                    <?php if ($instr['Leido'] == 0): ?>
                                                        <!-- Botón de editar -->
                                                        <button class="btn btn-sm btn-warning btn-editar-instruccion"
                                                            data-id="<?= $instr['ID_Instruccion'] ?>"
                                                            data-funcionario="<?= htmlspecialchars($instr['Nombres'] . ' ' . $instr['Apellidos']) ?>"
                                                            data-titulo="<?= htmlspecialchars($instr['Titulo']) ?>"
                                                            data-mensaje="<?= htmlspecialchars($instr['Mensaje']) ?>"
                                                            data-leido="<?= $instr['Leido'] ?>"
                                                            title="Editar Instrucción">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>

                                                        <!-- Botón eliminar -->
                                                        <button class="btn btn-sm btn-danger btn-eliminar-instruccion"
                                                            data-id="<?= $instr['ID_Instruccion'] ?>"
                                                            data-titulo="<?= htmlspecialchars($instr['Titulo']) ?>"
                                                            data-funcionario="<?= htmlspecialchars($instr['Nombres'] . ' ' . $instr['Apellidos']) ?>"
                                                            title="Eliminar Instrucción">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    <?php endif; ?>


                                                    <!-- Botón detalles  -->
                                                    <button class="btn btn-sm btn-info btn-detalles-instruccion"
                                                        data-id="<?= $instr['ID_Instruccion'] ?>"
                                                        data-funcionario="<?= htmlspecialchars($instr['Nombres'] . ' ' . $instr['Apellidos']) ?>"
                                                        data-dni="<?= htmlspecialchars($instr['DNI_Pasaporte']) ?>"
                                                        data-titulo="<?= htmlspecialchars($instr['Titulo']) ?>"
                                                        data-mensaje="<?= htmlspecialchars($instr['Mensaje']) ?>"
                                                        data-fecha="<?= htmlspecialchars($instr['Fecha_Envio']) ?>"
                                                        data-leido="<?= $instr['Leido'] ?>"
                                                        data-foto="<?= htmlspecialchars($instr['Fotografia']) ?>" title="Ver Detalles">
                                                        <i class="bi bi-eye"></i>
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




    <!-- Modal para Registrar Instrucciones diarias -->
    <div class="modal fade" id="addInstruccionModal" tabindex="-1" aria-labelledby="addInstruccionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
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
                                <i class="bi bi-save me-2"></i>Registrar Instrucción
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
                            <label class="form-label fw-semibold"><i class="bi bi-person-badge me-2"></i>Funcionario</label>
                            <input type="text" id="editFuncionarioNombre" class="form-control" readonly>
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
                            <!-- <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i>Cancelar
                            </button> -->
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save me-1"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>





    <script>
        // aqui para editar
        document.querySelectorAll('.btn-editar-instruccion').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.dataset.id;
                const idFuncionario = button.dataset.funcionario; // Podrías pasar el ID real si lo tienes
                const nombre = button.dataset.funcionario;
                const titulo = button.dataset.titulo;
                const mensaje = button.dataset.mensaje;
                const leido = button.dataset.leido;

                document.getElementById('editID_Instruccion').value = id;
                document.getElementById('editID_Funcionario').value = idFuncionario;
                document.getElementById('editFuncionarioNombre').value = nombre;
                document.getElementById('editTitulo').value = titulo;
                document.getElementById('editMensaje').value = mensaje;
                // document.getElementById('editLeido').value = leido;

                const modal = new bootstrap.Modal(document.getElementById('editInstruccionModal'));
                modal.show();
            });
        });
    </script>


    <!-- Script para buscar y seleccionar funcionario -->
    <script>
        const listaFuncionarios = document.getElementById('listaFuncionarios');
        const searchFuncionario = document.getElementById('searchFuncionario');
        const funcionarioSeleccionado = document.getElementById('funcionarioSeleccionado');
        const nombreFuncionario = document.getElementById('nombreFuncionario');
        const ID_Funcionario = document.getElementById('ID_Funcionario');
        const quitarSeleccion = document.getElementById('quitarSeleccion');
        const formInstruccion = document.querySelector('#addInstruccionModal form');

        // 1️⃣ Cargar lista de funcionarios desde API
        let todosFuncionarios = [];
        fetch('../api/obtener_funcionarios.php')
            .then(res => res.json())
            .then(data => {
                todosFuncionarios = data; // Guardamos todos los funcionarios
            })
            .catch(err => console.error('Error al cargar funcionarios:', err));

        // 2️⃣ Buscador dinámico
        searchFuncionario.addEventListener('input', () => {
            const filtro = searchFuncionario.value.trim().toLowerCase();
            listaFuncionarios.innerHTML = ''; // Limpiar lista antes de filtrar

            if (filtro === '') {
                // Si no hay texto, no mostrar nada
                return;
            }

            // Filtrar funcionarios por coincidencia en nombre o apellido
            const coincidencias = todosFuncionarios.filter(func =>
                func.Nombres.toLowerCase().includes(filtro) ||
                func.Apellidos.toLowerCase().includes(filtro)
            );

            coincidencias.forEach(func => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'list-group-item list-group-item-action';
                item.textContent = `${func.Nombres} ${func.Apellidos}`;
                item.dataset.id = func.ID_Funcionario;

                item.addEventListener('click', () => {
                    ID_Funcionario.value = func.ID_Funcionario;
                    nombreFuncionario.textContent = `${func.Nombres} ${func.Apellidos}`;
                    funcionarioSeleccionado.classList.remove('d-none');

                    // Quitar alerta si existía
                    const alerta = document.getElementById('alertaInstruccion');
                    if (alerta) alerta.remove();
                });

                listaFuncionarios.appendChild(item);
            });
        });

        // 3️⃣ Quitar selección
        quitarSeleccion.addEventListener('click', () => {
            ID_Funcionario.value = '';
            funcionarioSeleccionado.classList.add('d-none');
        });

        // 4️⃣ Validación antes de enviar el formulario
        formInstruccion.addEventListener('submit', function(e) {
            const titulo = document.getElementById('titulo').value.trim();
            const mensaje = document.getElementById('mensaje').value.trim();

            if (ID_Funcionario.value === '' || titulo === '' || mensaje === '') {
                e.preventDefault();

                let alerta = document.getElementById('alertaInstruccion');
                if (!alerta) {
                    alerta = document.createElement('div');
                    alerta.id = 'alertaInstruccion';
                    alerta.className = 'alert alert-danger mb-3';
                    alerta.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>Debe seleccionar un funcionario y completar todos los campos antes de enviar.';
                    formInstruccion.prepend(alerta);
                }
            }
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


    <!-- Modal de ver instrucciones -->

    <script>
        function mostrarDetallesInstruccion(funcionario, dni, titulo, mensaje, fecha, leido, fotoUrl) {

            const rutaBaseFotos = './api/funcionarios/';

            // Verifica si hay nombre de archivo de foto. Si no, usa una imagen de placeholder.
            const urlFoto = fotoUrl && fotoUrl !== '' ?
                rutaBaseFotos :
                'default.jpg';


            const estadoLeido = leido == 1 ?
                '<span class="badge bg-success py-2 px-3 fs-6"><i class="bi bi-check-circle-fill me-1"></i> Sí</span>' :
                '<span class="badge bg-warning text-dark py-2 px-3 fs-6"><i class="bi bi-x-circle-fill me-1"></i> No</span>';

            // Estructura el contenido HTML
            const htmlContent = `
            <div class="container-fluid text-start p-0">
              <h3 class="mt-2 mb-3 text-center"><i class="bi bi-file-text me-1"></i> Instruccion Diaria </h3>
                <div class="d-flex align-items-center mb-4 p-3 bg-light border-bottom rounded-top shadow-sm">
                    <img src="${urlFoto}" 
                         alt="${funcionario}" 
                         class="img-fluid rounded-circle border border-3 border-secondary me-3"
                         style="width: 80px; height: 80px; object-fit: cover;">
                    <div class="text-start">
                        <h6 class="mb-0 text-dark">Instrucción para:</h6>
                        <h5 class="mb-0 text-primary fw-bold">${funcionario}</h5>
                        <small class="text-muted">DNI: ${dni}</small>
                    </div>
                </div>

                <h5 class="text-start text-primary mb-3 px-3"><i class="bi bi-file-text me-2"></i>Información de la Instrucción</h5>
                
                <div class="row g-3 px-3">
                    
                    <div class="col-md-6">
                        <div class="bg-light p-2 rounded border">
                            <small class="text-muted d-block">Título</small>
                            <strong class="text-dark">${titulo}</strong>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-light p-2 rounded border">
                            <small class="text-muted d-block">Fecha de Envío</small>
                            <strong class="text-dark">${fecha}</strong>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4 mt-3 text-center">
                    <small class="text-muted d-block mb-1">Estado (Leído):</small>
                    ${estadoLeido}
                </div>

                <div class="card border-primary shadow-sm mx-3 mb-3">
                    <div class="card-header bg-primary text-white p-2">
                        <h6 class="mb-0"><i class="bi bi-chat-left-text me-1"></i> Mensaje Completo</h6>
                    </div>
                    <div class="card-body p-3" style="max-height: 200px; overflow-y: auto;">
                        <p class="card-text text-wrap mb-0" style="white-space: pre-wrap;">
                            ${mensaje}
                        </p>
                    </div>
                </div>
            </div>
        `;

            Swal.fire({
                title: '',
                html: htmlContent,
                showCloseButton: true,
                showCancelButton: false,
                confirmButtonText: '<i class="bi bi-x-circle"></i> Cerrar',
                confirmButtonColor: '#56585cd5',
                width: '750px',
                customClass: {
                    container: 'swal2-wide',
                    // Añadimos padding para que el contenido no pegue con los bordes del modal
                    popup: 'p-0'
                }
            });
        }

        // El Event Listener de DOMContentLoaded se mantiene igual:
        document.addEventListener('DOMContentLoaded', function() {
            const detailsButtons = document.querySelectorAll('.btn-detalles-instruccion');

            detailsButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    const funcionario = this.getAttribute('data-funcionario');
                    const dni = this.getAttribute('data-dni');
                    const titulo = this.getAttribute('data-titulo');
                    const mensaje = this.getAttribute('data-mensaje');
                    const fecha = this.getAttribute('data-fecha');
                    const leido = this.getAttribute('data-leido');
                    const fotoUrl = this.getAttribute('data-foto');

                    mostrarDetallesInstruccion(funcionario, dni, titulo, mensaje, fecha, leido, fotoUrl);
                });
            });
        });
    </script>



    <!-- Modal de eliminar instrucciones diarias-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {


            const deleteButtons = document.querySelectorAll('.btn-eliminar-instruccion');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    const id = this.getAttribute('data-id');
                    const titulo = this.getAttribute('data-titulo');
                    const funcionario = this.getAttribute('data-funcionario');

                    Swal.fire({
                        title: '¿Estás seguro?',

                        html: `Se eliminará la instrucción: <strong style="color: #070808ff; font-size: 1.1em; ">${titulo}</strong> <br>
                    enviada a  <strong style="color: #007bff; font-size: 1.1em;">${funcionario}</strong> . <br><br>
                    
                    <br><br>
                    <span style="color: red; font-weight: bold;">
                        Esta acción no se puede deshacer
                    </span>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        cancelButtonText: '<i class="bi bi-x-circle"></i> Cancelar',
                        confirmButtonText: '<i class="bi bi-trash"></i> Sí, Eliminar'

                    }).then((result) => {
                        if (result.isConfirmed) {


                            const form = document.createElement('form');
                            form.method = 'POST';

                            form.action = '../api/eliminar_instruccion.php';

                            const idField = document.createElement('input');
                            idField.type = 'hidden';

                            idField.name = 'id_instruccion';
                            idField.value = id;

                            form.appendChild(idField);
                            document.body.appendChild(form);
                            form.submit();

                        }
                    });
                });
            });
        });
    </script>




    <?php
    include_once '../includes/footer.php';
    ?>