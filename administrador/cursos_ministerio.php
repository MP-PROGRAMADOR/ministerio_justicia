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
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCapacitacionModal">
                                    <i class="bi bi-journal-plus me-1"></i> Añadir Curso
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
                            <h5 class="mb-0 fw-semibold">Listado de Cursos del Ministerio</h5>
                        </div>
                        <div class="table-responsive">
                            <?php
                            try {
                                $pdo = new PDO($dsn, $user, $pass, $options);
                            } catch (PDOException $e) {
                                die("Error de conexión: " . $e->getMessage());
                            }

                            // Consulta para obtener cursos
                            $sql = "SELECT ID_Curso, Nombre_Curso, Descripcion, Fecha_Inicio, Fecha_Fin, Cupo
                            FROM tbl_cursos
                            ORDER BY ID_Curso DESC";
                            $stmt = $pdo->query($sql);
                            $cursos = $stmt->fetchAll();
                            ?>

                            <table class="table table-hover align-middle mb-0" id="funcionariosTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre del Curso</th>
                                        <th>Descripción</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Cupo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="funcionariosTableBody">
                                    <?php foreach ($cursos as $curso): ?>
                                        <?php
                                        $hoy = date("Y-m-d");

                                        // Determinar estado
                                        if ($curso['Fecha_Fin'] < $hoy) {
                                            $estado = '<span class="badge bg-danger">Finalizado</span>';
                                            $finalizado = true;
                                        } elseif ($curso['Fecha_Inicio'] > $hoy) {
                                            $estado = '<span class="badge bg-warning text-dark">Próximo</span>';
                                            $finalizado = false;
                                        } else {
                                            $estado = '<span class="badge bg-success">En curso</span>';
                                            $finalizado = false;
                                        }

                                        // Determinar cupo
                                        $cupo = ($curso['Cupo'] > 0)
                                            ? "<span class='badge bg-primary'>{$curso['Cupo']} plazas</span>"
                                            : "<span class='badge bg-secondary'>Cupo completo</span>";
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($curso['ID_Curso']) ?></td>
                                            <td><?= htmlspecialchars($curso['Nombre_Curso']) ?></td>
                                            <td><?= htmlspecialchars(mb_strimwidth($curso['Descripcion'], 0, 20, '...')) ?></td>
                                            <td><?= htmlspecialchars($curso['Fecha_Inicio']) ?></td>
                                            <td><?= htmlspecialchars($curso['Fecha_Fin']) ?></td>
                                            <td><?= $cupo ?></td>
                                            <td><?= $estado ?></td>
                                            <td>
                                                <?php if (!$finalizado): ?>
                                                    <!-- Botón Inscribir -->
                                                    <button class="btn btn-sm btn-success btn-inscribir"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#inscribirModal"
                                                        onclick="abrirModalInscripcion(<?= $curso['ID_Curso'] ?>)">
                                                        <i class="bi bi-person-plus me-1"></i> Inscribir Funcionarios
                                                    </button>

                                                    <!-- Botón Editar -->
                                                    <button class="btn btn-sm btn-warning btn-editar-curso"
                                                        data-id="<?= $curso['ID_Curso'] ?>"
                                                        data-nombre="<?= htmlspecialchars($curso['Nombre_Curso']) ?>"
                                                        data-descripcion="<?= htmlspecialchars($curso['Descripcion']) ?>"
                                                        data-inicio="<?= $curso['Fecha_Inicio'] ?>"
                                                        data-fin="<?= $curso['Fecha_Fin'] ?>"
                                                        data-cupo="<?= $curso['Cupo'] ?>"
                                                        title="Editar Curso">
                                                        <i class="bi bi-pencil-square me-1"></i>
                                                    </button>

                                                    <button class="btn btn-sm btn-danger btn-eliminar-curso"
                                                        onclick="confirmarEliminacionCurso(<?= $curso['ID_Curso'] ?>, '<?= htmlspecialchars($curso['Nombre_Curso']) ?>')"
                                                        title="Eliminar Curso">
                                                        <i class="bi bi-trash"></i>
                                                    </button>

                                                <?php else: ?>
                                                    <!-- Botón Inscribir desactivado -->
                                                    <button class="btn btn-sm btn-secondary" disabled
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Curso finalizado — no se puede inscribir">
                                                        <i class="bi bi-person-plus me-1"></i> Inscribir Funcionarios
                                                    </button>

                                                    <!-- Botón Editar desactivado -->
                                                    <button class="btn btn-sm btn-secondary" disabled
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Curso finalizado — no se puede editar">
                                                        <i class="bi bi-pencil-square me-1"></i>
                                                    </button>

                                                    <!-- Botón Eliminar desactivado -->
                                                    <button class="btn btn-sm btn-secondary" disabled
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Curso finalizado — no se puede eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>


                                                <?php endif; ?>


                                                <button class="btn btn-sm btn-info btn-ver-inscritos"
                                                    data-id="<?= $curso['ID_Curso'] ?>"
                                                    data-nombre="<?= htmlspecialchars($curso['Nombre_Curso']) ?>"
                                                    data-inicio="<?= $curso['Fecha_Inicio'] ?>"
                                                    data-fin="<?= $curso['Fecha_Fin'] ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalInscritos">
                                                    <i class="bi bi-people-fill me-1"></i> Ver Inscritos
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




    <!-- Modal para Registrar Curso del minsterio -->
    <div class="modal fade" id="addCapacitacionModal" tabindex="-1" aria-labelledby="addCapacitacionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addCursoModalLabel">
                        <i class="bi bi-journal-text me-2"></i>Registrar Curso
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">

                    <!-- Formulario de curso -->
                    <form method="POST" action="../api/guardar_curso.php" enctype="multipart/form-data">
                        <div class="row g-3">

                            <!-- Nombre del Curso -->
                            <div class="col-md-6 mt-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-book text-primary me-2"></i>Nombre del Curso
                                </label>
                                <input type="text" name="Nombre_Curso" class="form-control" required>
                            </div>

                            <!-- Descripción -->
                            <div class="col-md-6 mt-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-card-text text-primary me-2"></i>Descripción
                                </label>
                                <input type="text" name="Descripcion" class="form-control" required>
                            </div>

                            <!-- Fecha Inicio -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-event text-primary me-2"></i>Fecha Inicio
                                </label>
                                <input type="date" name="Fecha_Inicio" class="form-control" required>
                            </div>

                            <!-- Fecha Fin -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-check text-primary me-2"></i>Fecha Fin
                                </label>
                                <input type="date" name="Fecha_Fin" class="form-control" required>
                            </div>

                            <!-- Cupo -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-people text-primary me-2"></i>Cupo
                                </label>
                                <input type="number" name="Cupo" class="form-control" min="1" required>
                            </div>

                        </div>

                        <!-- Botón enviar -->
                        <div class="mt-4 d-flex justify-content-end mb-3">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-2"></i>Registrar Curso
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>







    <!-- Modal Editar Curso -->
    <div class="modal fade" id="modalEditarCurso" tabindex="-1" aria-labelledby="modalEditarCursoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">

                <!-- Encabezado -->
                <div class="modal-header bg-warning text-dark rounded-top-4">
                    <h5 class="modal-title fw-bold" id="modalEditarCursoLabel">
                        <i class="bi bi-pencil-square me-2"></i>Editar Curso
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Formulario -->
                <form method="POST" action="../api/editar_curso.php">
                    <div class="modal-body">
                        <input type="hidden" name="ID_Curso" id="edit_id_curso">

                        <div class="row g-3">
                            <!-- Nombre del Curso -->
                            <div class="col-md-6 mt-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-book text-primary me-2"></i>Nombre del Curso
                                </label>
                                <input type="text" name="Nombre_Curso" id="edit_nombre_curso" class="form-control" required>
                            </div>

                            <!-- Descripción -->
                            <div class="col-md-6 mt-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-card-text text-primary me-2"></i>Descripción
                                </label>
                                <input type="text" name="Descripcion" id="edit_descripcion" class="form-control" required maxlength="200">
                            </div>

                            <!-- Fecha Inicio -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-event text-primary me-2"></i>Fecha Inicio
                                </label>
                                <input type="date" name="Fecha_Inicio" id="edit_fecha_inicio" class="form-control" required>
                            </div>

                            <!-- Fecha Fin -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-check text-primary me-2"></i>Fecha Fin
                                </label>
                                <input type="date" name="Fecha_Fin" id="edit_fecha_fin" class="form-control" required>
                            </div>

                            <!-- Cupo -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-people text-primary me-2"></i>Cupo
                                </label>
                                <input type="number" name="Cupo" id="edit_cupo" class="form-control" min="1" required>
                            </div>
                        </div>
                    </div>

                    <!-- Botón Guardar -->
                    <div class="modal-footer mb-3">
                        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
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








    <!-- modal para matricula -->


    <!-- Modal de Inscribir Funcionarios a un curso -->
    <div class="modal fade" id="inscribirModal" tabindex="-1" aria-labelledby="inscribirModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="inscribirModalLabel">Inscribir Funcionarios</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">

                    <!-- Campo oculto para el ID del curso -->
                    <input type="hidden" id="idCursoActual" name="ID_Curso">

                    <!-- Buscador -->
                    <input type="text" id="buscarFuncionario" class="form-control mb-3" placeholder="Buscar funcionario...">

                    <!-- Resultados -->
                    <div id="listaFuncionarios" style="max-height:200px; overflow-y:auto;"></div>

                    <hr>

                    <!-- Lista seleccionada -->
                    <h6>Funcionarios seleccionados:</h6>
                    <ul id="listaSeleccionados" class="list-group"></ul>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" id="btnGuardarInscripcion" class="btn btn-success">Guardar</button>
                </div>
            </div>
        </div>
    </div>





    <!-- Modal de Funcionarios Inscritos -->
    <div class="modal fade" id="modalInscritos" tabindex="-1" aria-labelledby="modalInscritosLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInscritosLabel">Funcionarios Inscritos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <input type="text" id="buscadorInscritos" class="form-control" placeholder="Buscar funcionario...">
                    </div>
                    <table class="table table-striped" id="tablaInscritos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellidos</th>
                                <th>Codigo del Funcionario</th>
                                <th>D.I.P/Pasaporte</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se llenará dinámicamente -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button id="btnImprimirInscritos" class="btn btn-primary"><i class="bi bi-printer"></i> Imprimir</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>





    <!-- Incluye SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Para que los tooltips funcionen debes inicializarlos en tu JavaScript principal:

        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>





    <script>
        //  JavaScript para cargar los funcionarios y buscador
        document.querySelectorAll('.btn-ver-inscritos').forEach(button => {
            button.addEventListener('click', function() {
                const idCurso = this.dataset.id;
                const nombreCurso = this.dataset.nombre;
                const fechaInicio = this.dataset.inicio; // Asegúrate de pasarlo como data-inicio
                const fechaFin = this.dataset.fin; // Asegúrate de pasarlo como data-fin

                // Cambiar título del modal
                document.getElementById('modalInscritosLabel').textContent = `Funcionarios inscritos en: ${nombreCurso}`;

                // Limpiar tabla
                const tbody = document.querySelector('#tablaInscritos tbody');
                tbody.innerHTML = '';

                // Guardar info para imprimir
                document.getElementById('btnImprimirInscritos').dataset.nombreCurso = nombreCurso;
                document.getElementById('btnImprimirInscritos').dataset.fechaInicio = fechaInicio;
                document.getElementById('btnImprimirInscritos').dataset.fechaFin = fechaFin;

                // Fetch a tu API para obtener los funcionarios inscritos
                fetch(`../api/funcionarios_inscritos.php?ID_Curso=${idCurso}`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(func => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `<td>${func.ID_Funcionario}</td>
                                    <td>${func.Nombres}</td>
                                    <td>${func.Apellidos}</td>
                                    <td>${func.Codigo_Funcionario}</td>
                                    <td>${func.DNI_Pasaporte}</td>`;
                            tbody.appendChild(tr);
                        });
                    });
            });
        });





        // Buscador dinámico
        document.getElementById('buscadorInscritos').addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('#tablaInscritos tbody tr').forEach(row => {
                const nombre = row.cells[1].textContent.toLowerCase();
                const apellidos = row.cells[2].textContent.toLowerCase();
                row.style.display = (nombre.includes(filter) || apellidos.includes(filter)) ? '' : 'none';
            });
        });



        // document.getElementById('btnImprimirInscritos').addEventListener('click', function() {
        //     const nombreCurso = this.dataset.nombreCurso;
        //     const fechaInicio = this.dataset.fechaInicio;
        //     const fechaFin = this.dataset.fechaFin;

        //     const tabla = document.getElementById('tablaInscritos').cloneNode(true); // clonamos para no alterar el DOM
        //     // Quitamos el buscador de la impresión
        //     tabla.removeAttribute('id');

        //     const ventana = window.open('', '', 'height=700,width=900');
        //     ventana.document.write('<html><head><title>Funcionarios Inscritos</title>');
        //     ventana.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">');
        //     ventana.document.write('<style>table{border-collapse: collapse;} th, td{border:1px solid #000 !important;}</style>');
        //     ventana.document.write('</head><body>');
        //     ventana.document.write(`<div class="mb-3"><h3>Curso: ${nombreCurso}</h3>`);
        //     ventana.document.write(`<p>Fecha de inicio: ${fechaInicio} | Fecha de fin: ${fechaFin}</p></div>`);
        //     ventana.document.write(tabla.outerHTML);
        //     ventana.document.write('</body></html>');
        //     ventana.document.close();
        //     ventana.print();
        // });


        // Botón imprimir con información del curso
        document.getElementById('btnImprimirInscritos').addEventListener('click', function() {
            const tabla = document.getElementById('tablaInscritos').outerHTML;
            const nombreCurso = this.dataset.nombreCurso;
            const fechaInicio = this.dataset.fechaInicio;
            const fechaFin = this.dataset.fechaFin;

            const ventana = window.open('', '', 'height=600,width=800');
            ventana.document.write('<html><head><title>Funcionarios Inscritos</title>');
            ventana.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">');
            ventana.document.write('</head><body>');
            ventana.document.write(`<h3>Curso: ${nombreCurso}</h3>`);
            ventana.document.write(`<p>Fecha de inicio: ${fechaInicio} | Fecha de fin: ${fechaFin}</p>`);
            ventana.document.write(tabla);
            ventana.document.write('</body></html>');
            ventana.document.close();
            ventana.print();
        });
    </script>

    <script>
        let seleccionados = [];

        // Abrir modal y asignar ID del curso
        function abrirModalInscripcion(idCurso) {
            document.getElementById('idCursoActual').value = idCurso;
            seleccionados = []; // Limpiar lista
            renderListaSeleccionados();
            document.getElementById('buscarFuncionario').value = '';
            document.getElementById('listaFuncionarios').innerHTML = '';
        }

        // Buscar funcionario en tiempo real
        document.getElementById('buscarFuncionario').addEventListener('input', function() {
            const query = this.value.trim();
            const lista = document.getElementById('listaFuncionarios');

            // Si no hay texto, limpiar y salir
            if (!query) {
                lista.innerHTML = '';
                return;
            }

            fetch(`../api/buscar_funcionarios.php?q=${encodeURIComponent(query)}`)
                .then(res => {
                    if (!res.ok) throw new Error("Error en la solicitud");
                    return res.json();
                })
                .then(data => {
                    lista.innerHTML = '';

                    if (data.length > 0) {
                        data.forEach(func => {
                            const div = document.createElement('div');
                            div.className = 'list-group-item list-group-item-action';
                            div.textContent = `${func.Nombres} ${func.Apellidos}`;
                            div.style.cursor = 'pointer';
                            div.addEventListener('click', () => agregarASeleccionados(func));
                            lista.appendChild(div);
                        });
                    } else {
                        lista.innerHTML = '<div class="text-muted p-2">No se encontraron resultados</div>';
                    }
                })
                .catch(err => {
                    console.error("Error al buscar funcionarios:", err);
                    lista.innerHTML = '<div class="text-danger p-2">Error al buscar</div>';
                });
        });


        // Agregar funcionario a la lista seleccionada
        function agregarASeleccionados(func) {
            if (!seleccionados.some(f => f.ID_Funcionario === func.ID_Funcionario)) {
                seleccionados.push(func);
                renderListaSeleccionados();
            }
        }

        // Renderizar lista seleccionada
        function renderListaSeleccionados() {
            const listaSel = document.getElementById('listaSeleccionados');
            listaSel.innerHTML = '';
            seleccionados.forEach(func => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.textContent = `${func.Nombres} ${func.Apellidos}`;

                const btn = document.createElement('button');
                btn.className = 'btn btn-sm btn-danger';
                btn.textContent = 'Eliminar';
                btn.addEventListener('click', () => {
                    seleccionados = seleccionados.filter(f => f.ID_Funcionario !== func.ID_Funcionario);
                    renderListaSeleccionados();
                });

                li.appendChild(btn);
                listaSel.appendChild(li);
            });
        }

        // Guardar inscripción
        document.getElementById('btnGuardarInscripcion').addEventListener('click', function() {
            if (seleccionados.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: 'Debes seleccionar al menos un funcionario.'
                });
                return;
            }

            const idCurso = document.getElementById('idCursoActual').value;
            const formData = new FormData();
            formData.append('ID_Curso', idCurso);
            formData.append('funcionarios', JSON.stringify(seleccionados.map(f => f.ID_Funcionario)));

            fetch('../api/guardar_matricula.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        let htmlMsg = '';

                        if (response.inscritos.length > 0) {
                            htmlMsg += `<p><b>✅ Inscritos correctamente:</b><br>${response.inscritos.join('<br>')}</p>`;
                        }
                        if (response.noInscritos.length > 0) {
                            htmlMsg += `<p><b>⚠ No se inscribieron:</b><br>`;
                            response.noInscritos.forEach(f => {
                                htmlMsg += `- ${f.nombre}: ${f.motivo}<br>`;
                            });
                            htmlMsg += `</p>`;
                        }

                        Swal.fire({
                            icon: 'info',
                            title: 'Resultado de la inscripción',
                            html: htmlMsg
                        }).then(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('inscribirModal'));
                            modal.hide();
                        });

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error en la petición.'
                    });
                });
        });
    </script>





    <!-- Script para llenar datos -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".btn-editar-curso").forEach(function(boton) {
                boton.addEventListener("click", function() {
                    document.getElementById("edit_id_curso").value = this.dataset.id;
                    document.getElementById("edit_nombre_curso").value = this.dataset.nombre;
                    document.getElementById("edit_descripcion").value = this.dataset.descripcion;
                    document.getElementById("edit_fecha_inicio").value = this.dataset.inicio;
                    document.getElementById("edit_fecha_fin").value = this.dataset.fin;
                    document.getElementById("edit_cupo").value = this.dataset.cupo;
                    new bootstrap.Modal(document.getElementById("modalEditarCurso")).show();
                });
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






    <!-- Modal de confirmacion para eliminar cursos del ministerio -->
    <script>
        function confirmarEliminacionCurso(idCurso, nombreCurso) {
            Swal.fire({
                title: '¿Estás seguro?',
                html: `
        ¡Vas a eliminar el curso:
        <br>
        <strong style="color: #007bff; font-size: 1.2em;">${nombreCurso}</strong>
        <br><br>
        <span style="color: red;">
            Esta acción es irreversible.
        </span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#5a5d5fff',
                confirmButtonText: '<i class="bi bi-trash"></i> Sí, Eliminar',
                cancelButtonText: '<i class="bi bi-x-circle"></i> Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Crear y enviar el formulario dinámico al script de eliminación
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '../api/eliminar_curso.php'; // Asegúrate de que esta ruta sea correcta

                    const idField = document.createElement('input');
                    idField.type = 'hidden';
                    idField.name = 'id_curso';
                    idField.value = idCurso;

                    form.appendChild(idField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

    <?php
    include_once '../includes/footer.php';
    ?>