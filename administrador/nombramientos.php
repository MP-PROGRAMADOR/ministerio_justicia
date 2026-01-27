<?php
include_once '../includes/header.php';
include_once '../includes/silebar_admin.php';
?>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="main-content" id="mainContent">

                <div class="header-section">
                    <div class="row align-items-center">
                        <div class="col-md-12 text-md-end mt-3 mt-md-0">
                            <div
                                class="d-flex justify-content-md-end align-items-center gap-2 flex-wrap justify-content-center">
                                <button class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#addNombramientoModal">
                                    <i class="bi bi-plus-circle me-2"></i> Nuevo Nombramiento
                                </button>
                                <div class="input-group" style="width: 300px;">
                                    <input type="text" class="form-control shadow-none border-secondary-subtle"
                                        id="liveSearchInput" placeholder="Buscar en tabla...">
                                    <span class="input-group-text bg-light border-secondary-subtle"><i
                                            class="bi bi-search"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 mt-3">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div id="mensajeFlash" class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION['error']);
                            unset($_SESSION['error']); ?>

                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['exito'])): ?>
                        <div id="mensajeFlash" class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION['exito']);
                            unset($_SESSION['exito']); ?>

                        </div>
                    <?php endif; ?>
                </div>

                <div class="container-fluid px-4">



                    <?php
                    $sql = "SELECT 
                            n.*, 
                            CONCAT(f.Nombre, ' ', f.Apellidos) AS Funcionario,
                            c.Nombre AS Cargo,
                            s.nombre AS Seccion,
                            cat.nombre AS Categoria,
                            d.nombre AS Direccion
                        FROM nombramientos n
                        INNER JOIN funcionarios f ON n.Id_funcionario = f.Id_funcionario
                        INNER JOIN cargos c ON n.Id_cargo = c.Id_cargo
                        LEFT JOIN secciones s ON n.Id_seccion = s.Id_seccion
                        LEFT JOIN direcciones d ON s.Id_direccion = d.Id_direccion
                        LEFT JOIN categorias cat ON n.Id_categoria = cat.Id_categoria
                        ORDER BY 
                            (n.Fecha_finalizacion_nombramiento IS NULL) DESC, 
                            n.Fecha_nombramiento DESC";

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


                    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

                    if (count($rows) > 0):
                        foreach ($rows as $row):
                        // contenido
                        endforeach;
                    endif;

                    ?>


                    <div class="table-custom mb-4 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-semibold">Registro de Nombramientos</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="nombramientosTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Funcionario</th>
                                        <th>Cargo / Categoría</th>
                                        <th>Ubicación</th>
                                        <th>Fechas</th>
                                        <th>Documentos</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="nombramientosTableBody">
                                    <?php if ($rows):
                                        foreach ($rows as $row): ?>
                                            <tr>
                                                <td>#<?= $row['Id_nombramiento'] ?></td>

                                                <td class="fw-semibold">
                                                    <?= htmlspecialchars($row['Funcionario']) ?>
                                                </td>

                                                <td>
                                                    <small
                                                        class="d-block fw-medium"><?= htmlspecialchars($row['Cargo']) ?></small>
                                                    <small
                                                        class="text-muted"><?= htmlspecialchars($row['Categoria'] ?? '—') ?></small>
                                                </td>

                                                <td>
                                                    <small class="d-block"><strong>Dir:</strong>
                                                        <?= htmlspecialchars($row['Direccion'] ?? 'N/A') ?></small>
                                                    <small class="d-block text-muted"><strong>Sec:</strong>
                                                        <?= htmlspecialchars($row['Seccion'] ?? 'N/A') ?></small>
                                                </td>

                                                <td style="font-size: 0.85rem;">
                                                    <div><i
                                                            class="bi bi-calendar-check text-success me-1"></i><?= $row['Fecha_nombramiento'] ?>
                                                    </div>
                                                    <div><i
                                                            class="bi bi-geo-alt text-primary me-1"></i><?= $row['Fecha_toma_posesion'] ?: 'Pendiente' ?>
                                                    </div>
                                                    <div class="mt-1">
                                                        <?php if (empty($row['Fecha_finalizacion_nombramiento'])): ?>
                                                            <span class=" px-3 text-primary">
                                                                <i class="bi bi-check-circle-fill me-1"></i> Vigente
                                                            </span>
                                                        <?php else: ?>
                                                            <div class="text-muted small">
                                                                <i class="bi bi-calendar-x me-1"></i>
                                                                <strong>Finalizó:</strong> <?= date('d/m/Y', strtotime($row['Fecha_finalizacion_nombramiento'])) ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <?php if ($row['Copia_doc_nomb']): ?>
                                                            <a href="../uploads/nombramientos/<?= $row['Copia_doc_nomb'] ?>" target="_blank"
                                                                class="btn btn-sm btn-outline-danger">
                                                                <i class="bi bi-file-pdf"></i>
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if ($row['Copia_doc_tom_posesion']): ?>
                                                            <a href="../uploads/nombramientos/<?= $row['Copia_doc_tom_posesion'] ?>"
                                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-file-pdf"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>

                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-warning btn-edit"
                                                        data-id="<?= $row['Id_nombramiento'] ?>"
                                                        data-id_func="<?= $row['Id_funcionario'] ?>"
                                                        data-id_cargo="<?= $row['Id_cargo'] ?>"
                                                        data-fecha_n="<?= $row['Fecha_nombramiento'] ?>"
                                                        data-fecha_p="<?= $row['Fecha_toma_posesion'] ?>"
                                                        data-id_sec="<?= $row['Id_seccion'] ?>"
                                                        data-id_cat="<?= $row['Id_categoria'] ?>">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach;
                                    else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                No se encontraron nombramientos.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>

                            </table>
                        </div>
                    </div>
                    <nav aria-label="Navegación de tabla" class="mt-3">
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

                <footer class="footer bg-white shadow-sm py-3 mt-auto">
                    <div class="container-fluid text-center">
                        <span class="text-muted">© 2024 Themis | Ministerio de Justicia. Todos los derechos
                            reservados.</span>
                    </div>
                </footer>
            </div>
        </div>
    </div>






    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('liveSearchInput');
            const tableBody = document.getElementById('nombramientosTableBody');
            const paginationControls = document.getElementById('paginationControls');

            const rowsPerPage = 8;
            let currentPage = 1;

            function updateTable() {
                const term = input.value.toLowerCase().trim();
                const allRows = Array.from(tableBody.querySelectorAll('tr:not(.no-results-row)'));

                // 1. Filtrado
                const filteredRows = allRows.filter(row => row.textContent.toLowerCase().includes(term));

                // 2. Mensaje de "Sin coincidencias"
                const existingMsg = tableBody.querySelector('.no-results-row');
                if (filteredRows.length === 0) {
                    allRows.forEach(row => row.style.display = 'none');
                    if (!existingMsg) {
                        const tr = document.createElement('tr');
                        tr.className = 'no-results-row';
                        tr.innerHTML = `<td colspan="7" class="text-center py-5 text-muted">
                    <i class="bi bi-search d-block fs-1 mb-2"></i>
                    No se encontraron coincidencias para "<strong>${input.value}</strong>"
                </td>`;
                        tableBody.appendChild(tr);
                    }
                    paginationControls.innerHTML = '';
                    return;
                } else if (existingMsg) {
                    existingMsg.remove();
                }

                // 3. Lógica de Paginación
                const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
                if (currentPage > totalPages) currentPage = totalPages || 1;

                // Ocultar todas y mostrar solo el rango de la página actual
                allRows.forEach(row => row.style.display = 'none');
                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                filteredRows.slice(start, end).forEach(row => {
                    row.style.display = '';
                });

                // 4. GENERAR BOTONES (Aquí es donde se ve el número de página)
                renderPagination(totalPages);
            }

            function renderPagination(total) {
                paginationControls.innerHTML = '';
                if (total <= 1) return;

                const maxButtons = 5; 
                let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
                let endPage = Math.min(total, startPage + maxButtons - 1);

                
                if (endPage - startPage + 1 < maxButtons) {
                    startPage = Math.max(1, endPage - maxButtons + 1);
                }

                // --- Botón Anterior ---
                const prev = document.createElement('li');
                prev.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
                prev.innerHTML = `<a class="page-link" href="#">Anterior</a>`;
                prev.onclick = (e) => {
                    e.preventDefault();
                    if (currentPage > 1) {
                        currentPage--;
                        updateTable();
                        window.scrollTo(0, 0);
                    }
                };
                paginationControls.appendChild(prev);

                // --- Primera página y puntos suspensivos (si es necesario) ---
                if (startPage > 1) {
                    paginationControls.appendChild(createPageBtn(1));
                    if (startPage > 2) {
                        const dots = document.createElement('li');
                        dots.className = "page-item disabled";
                        dots.innerHTML = `<span class="page-link">...</span>`;
                        paginationControls.appendChild(dots);
                    }
                }

                // --- Números de Página Dinámicos ---
                for (let i = startPage; i <= endPage; i++) {
                    paginationControls.appendChild(createPageBtn(i));
                }

                // --- Última página y puntos suspensivos (si es necesario) ---
                if (endPage < total) {
                    if (endPage < total - 1) {
                        const dots = document.createElement('li');
                        dots.className = "page-item disabled";
                        dots.innerHTML = `<span class="page-link">...</span>`;
                        paginationControls.appendChild(dots);
                    }
                    paginationControls.appendChild(createPageBtn(total));
                }

                // --- Botón Siguiente ---
                const next = document.createElement('li');
                next.className = `page-item ${currentPage === total ? 'disabled' : ''}`;
                next.innerHTML = `<a class="page-link" href="#">Siguiente</a>`;
                next.onclick = (e) => {
                    e.preventDefault();
                    if (currentPage < total) {
                        currentPage++;
                        updateTable();
                        window.scrollTo(0, 0);
                    }
                };
                paginationControls.appendChild(next);
            }

            // Función auxiliar para no repetir código de creación de botones
            function createPageBtn(pageNumber) {
                const li = document.createElement('li');
                li.className = `page-item ${pageNumber === currentPage ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#">${pageNumber}</a>`;
                li.onclick = (e) => {
                    e.preventDefault();
                    currentPage = pageNumber;
                    updateTable();
                    window.scrollTo(0, 0);
                };
                return li;
            }

            // Escuchar el buscador
            input.addEventListener('input', () => {
                currentPage = 1;
                updateTable();
            });

            // Carga inicial
            updateTable();
        });
    </script>









    <!-- Modal de Registrar Nombramiento -->
    <div class="modal fade" id="addNombramientoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow">

                <!-- HEADER -->
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i> Nuevo Nombramiento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST" action="../api/guardar_nombramiento.php" enctype="multipart/form-data" id="formFuncionario">
                    <!-- BODY -->
                    <div class="modal-body">

                        <!-- BUSCADOR DE FUNCIONARIO -->
                        <div class="row mb-4">
                            <div class="col-12 position-relative">
                                <label class="form-label fw-semibold">Buscar Funcionario</label>

                                <input type="text" id="funcionario_search" class="form-control shadow-none"
                                    placeholder="Escriba nombre o apellido..." autocomplete="off">

                                <div id="search_results" class="list-group position-absolute w-100 shadow-sm d-none"
                                    style="z-index: 1055;"></div>

                                <div id="selected_funcionario" class="mt-2 d-none"></div>
                                <input type="hidden" name="id_funcionario" id="form_func" required>
                            </div>
                        </div>

                        <!-- RESTO DEL FORMULARIO -->
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Cargo</label>
                                <select name="id_cargo" id="form_cargo" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <?php
                                    // CONEXIÓN Y CONSULTA PHP para Cargos
                                    require_once "../includes/conexion.php";
                                    $stmt = $pdo->query("SELECT Id_cargo, nombre FROM cargos ORDER BY nombre ASC");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="' . $row['Id_cargo'] . '">' . htmlspecialchars($row['nombre']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fecha Nombramiento</label>
                                <input type="date" name="fecha_nombramiento" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fecha Toma Posesión</label>
                                <input type="date" name="fecha_toma_posesion" class="form-control">
                            </div>




                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Sección / Departamento</label>
                                <select name="id_seccion" class="form-select">
                                    <option value="">Seleccione una sección</option>
                                    <?php
                                    require_once "../includes/conexion.php";

                                    // Mantenemos el orden por Dirección para que el agrupamiento funcione
                                    $sql = "SELECT s.Id_seccion, s.nombre AS nombre_seccion, d.nombre AS nombre_direccion 
                                            FROM secciones s 
                                            LEFT JOIN direcciones d ON s.Id_direccion = d.Id_direccion 
                                            ORDER BY d.nombre ASC, s.nombre ASC";

                                    $stmt = $pdo->query($sql);
                                    $current_dir = null;

                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $dir = $row['nombre_direccion'] ?? 'OTRAS DEPENDENCIAS';

                                        // Lógica de agrupamiento visual
                                        if ($current_dir !== $dir) {
                                            if ($current_dir !== null) echo '</optgroup>';
                                            echo '<optgroup label="' . htmlspecialchars(strtoupper($dir)) . '">';
                                            $current_dir = $dir;
                                        }

                                        $labelCompleto = htmlspecialchars($row['nombre_seccion'] . " — " . $dir);

                                        echo '<option value="' . $row['Id_seccion'] . '">' . $labelCompleto . '</option>';
                                    }

                                    if ($current_dir !== null) echo '</optgroup>';
                                    ?>
                                </select>
                                <div class="form-text">Al seleccionar, se mostrará la sección junto a su dirección.</div>
                            </div>




                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Categoría</label>
                                <select name="id_categoria" class="form-select">
                                    <option value="">Ninguna</option>
                                    <?php
                                    // CONEXIÓN Y CONSULTA PHP para Categorías
                                    $stmt = $pdo->query("SELECT Id_categoria, nombre, descripcion FROM categorias ORDER BY nombre ASC");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="' . $row['Id_categoria'] . '">' . htmlspecialchars($row['nombre']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Doc. Nombramiento</label>
                                <input type="file" name="doc_nombramiento" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Doc. Toma Posesión</label>
                                <input type="file" name="doc_posesion" class="form-control">
                            </div>

                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer">

                        <button type="submit" class="btn btn-success">
                            Guardar Nombramiento
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>



    <script>
        const input = document.getElementById('funcionario_search');
        const results = document.getElementById('search_results');
        const selectedBox = document.getElementById('selected_funcionario');
        const hidden = document.getElementById('form_func');

        let timeout;

        input.addEventListener('input', () => {
            const q = input.value.trim();

            clearTimeout(timeout);

            if (!q) {
                results.classList.add('d-none');
                return;
            }

            // Retraso para no saturar el servidor
            timeout = setTimeout(() => {
                fetch(`../api/obtener_funcionarios.php?q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(data => {
                        results.innerHTML = '';
                        if (!data.length) {
                            results.innerHTML = `<div class="list-group-item text-muted">Sin resultados</div>`;
                            results.classList.remove('d-none');
                            return;
                        }

                        results.classList.remove('d-none');

                        data.forEach(f => {
                            const item = document.createElement('button');
                            item.type = 'button';
                            item.className = 'list-group-item list-group-item-action';
                            item.textContent = `${f.Nombre} ${f.Apellidos} (${f.CODIGO})`;
                            item.onclick = () => seleccionarFuncionario(f);
                            results.appendChild(item);
                        });
                    })
                    .catch(err => console.error(err));
            }, 300);
        });

        function seleccionarFuncionario(f) {
            hidden.value = f.Id_funcionario;
            input.value = '';
            input.disabled = true;
            results.classList.add('d-none');

            selectedBox.innerHTML = `
        <div class="alert alert-success d-flex justify-content-between align-items-center py-2">
            <span><i class="bi bi-person-check me-2"></i>${f.Nombre} ${f.Apellidos} (${f.CODIGO})</span>
            <button type="button" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    `;
            selectedBox.classList.remove('d-none');
            selectedBox.querySelector('button').onclick = quitarFuncionario;
        }

        function quitarFuncionario() {
            hidden.value = '';
            input.disabled = false;
            input.focus();
            selectedBox.classList.add('d-none');
            selectedBox.innerHTML = '';
        }

        // Cerrar resultados si clic fuera
        document.addEventListener('click', e => {
            if (!input.contains(e.target)) results.classList.add('d-none');
        });
    </script>







    <div class="modal fade" id="editNombramientoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow">

                <!-- HEADER -->
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-2"></i> Editar Nombramiento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body">
                    <form id="editNombramientoForm" method="POST" action="../api/actualizar_nombramiento.php" enctype="multipart/form-data">

                        <input type="hidden" name="id_nombramiento" id="edit_id">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Cargo</label>
                                <select name="id_cargo" id="edit_cargo" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT Id_cargo, nombre FROM cargos ORDER BY nombre ASC");
                                    while ($c = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="' . $c['Id_cargo'] . '">' . htmlspecialchars($c['nombre']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fecha Nombramiento</label>
                                <input type="date" name="fecha_nombramiento" id="edit_fecha_n" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fecha Toma Posesión</label>
                                <input type="date" name="fecha_toma_posesion" id="edit_fecha_p" class="form-control">
                            </div>

                            <!-- NUEVO CAMPO -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fecha Finalización Nombramiento</label>
                                <input type="date" name="fecha_finalizacion_nombramiento" id="edit_fecha_f"
                                    class="form-control bg-warning text-dark">
                            </div>


                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Sección</label>
                                <select name="id_seccion" id="edit_sec" class="form-select">
                                    <option value="">Ninguna</option>
                                    <?php
                                    // 1. Consulta corregida con alias claros
                                    $stmtSec = $pdo->query("SELECT s.Id_seccion, s.nombre AS nombre_seccion, d.nombre AS nombre_direccion 
                                                                    FROM secciones s LEFT JOIN direcciones d ON s.Id_direccion = d.Id_direccion 
                                                                    ORDER BY d.nombre ASC, s.nombre ASC");
                                    while ($s = $stmtSec->fetch(PDO::FETCH_ASSOC)) {
                                        // Concatenamos Dirección + Sección para que sea más fácil de identificar
                                        $label = htmlspecialchars(($s['nombre_seccion'] . '-' . $s['nombre_direccion'] ?? 'SIN DIR.'));

                                        echo '<option value="' . $s['Id_seccion'] . '">' . $label . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Categoría</label>
                                <select name="id_categoria" id="edit_cat" class="form-select">
                                    <option value="">Ninguna</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT Id_categoria, nombre FROM categorias ORDER BY nombre ASC");
                                    while ($cat = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="' . $cat['Id_categoria'] . '">' . htmlspecialchars($cat['nombre']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- NOTA IMPORTANTE -->
                            <div class="col-12">
                                <small class="text-muted">Si no se envían nuevos documentos, se mantendrán los anteriores.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Doc. Nombramiento</label>
                                <input type="file" name="doc_nombramiento" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Doc. Toma Posesión</label>
                                <input type="file" name="doc_posesion" class="form-control">
                            </div>



                        </div>

                        <div class="modal-footer mt-3">
                            <button type="submit" class="btn btn-warning">Actualizar Nombramiento</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const editModal = new bootstrap.Modal(document.getElementById('editNombramientoModal'));

            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.getAttribute('data-id');
                    const id_func = button.getAttribute('data-id_func');
                    const id_cargo = button.getAttribute('data-id_cargo');
                    const fecha_n = button.getAttribute('data-fecha_n');
                    const fecha_p = button.getAttribute('data-fecha_p');
                    const fecha_f = button.getAttribute('data-fecha_f'); 
                    const id_sec = button.getAttribute('data-id_sec');
                    const id_cat = button.getAttribute('data-id_cat');

                    document.getElementById('edit_id').value = id;
                    document.getElementById('edit_cargo').value = id_cargo;
                    document.getElementById('edit_fecha_n').value = fecha_n;
                    document.getElementById('edit_fecha_p').value = fecha_p;
                    document.getElementById('edit_fecha_f').value = fecha_f; 
                    document.getElementById('edit_sec').value = id_sec;
                    document.getElementById('edit_cat').value = id_cat;

                    editModal.show();
                });
            });

        });
    </script>






</body>

<?php include_once '../includes/footer.php'; ?>