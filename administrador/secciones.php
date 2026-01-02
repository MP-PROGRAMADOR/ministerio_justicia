<?php
include_once '../includes/header.php';
?>
<?php
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
                                <!-- Botón para abrir modal de registrar asignación -->
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSeccionModal">
                                    <i class="bi bi-plus-circle me-2"></i> Nueva Seccion
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



                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>




                <div class="container-fluid px-4">
                    <div class="table-custom mb-4 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-semibold">Listado de Secciones</h5>
                        </div>

                        <div class="table-responsive">
                            <?php
                            try {
                                $pdo = new PDO($dsn, $user, $pass, $options);
                            } catch (PDOException $e) {
                                die("Error de conexión: " . $e->getMessage());
                            }

                            // Consulta para obtener secciones junto con el nombre de la dirección
                            $sql = "SELECT s.Id_seccion, s.Id_direccion, s.nombre AS nombre_seccion, d.nombre AS nombre_direccion
            FROM secciones s
            JOIN direcciones d ON s.Id_direccion = d.Id_direccion
            ORDER BY s.Id_seccion DESC";

                            $stmt = $pdo->prepare($sql);
                            $stmt->execute();
                            $secciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            ?>

                            <table class="table table-hover align-middle mb-0" id="funcionariosTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID Sección</th>
                                        <th>Sección</th>
                                        <th>Dirección</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                              <tbody id="funcionariosTableBody">
    <?php if ($secciones): ?>
        <?php foreach ($secciones as $seccion): ?>
            <tr>
                <td><?= htmlspecialchars($seccion['Id_seccion']) ?></td>
                <td><?= htmlspecialchars($seccion['nombre_seccion']) ?></td>
                <td><?= htmlspecialchars($seccion['nombre_direccion']) ?></td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-2">
                        <!-- BOTÓN DE EDITAR SECCIÓN -->
                        <button
                            class="btn btn-sm btn-warning btn-editar-seccion"
                            data-bs-toggle="modal"
                            data-bs-target="#editSeccionModal"
                            data-id="<?= $seccion['Id_seccion'] ?>"
                            data-id_direccion="<?= $seccion['Id_direccion'] ?>"
                            data-nombre="<?= htmlspecialchars($seccion['nombre_seccion']) ?>"
                            title="Editar sección"
                        >
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <!-- AQUÍ PUEDES AGREGAR EL BOTÓN ELIMINAR SI QUIERES -->
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4" class="text-center text-muted">No hay secciones registradas</td>
        </tr>
    <?php endif; ?>
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






    <!-- Modal para Registrar Sección -->
    <div class="modal fade" id="addSeccionModal" tabindex="-1" aria-labelledby="addSeccionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow">

                <!-- HEADER -->
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addSeccionModalLabel">
                        <i class="bi bi-diagram-3-fill me-2"></i>Registrar Sección
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body">
                    <form method="POST" action="../api/guardar_seccion.php" id="formSeccion">

                        <div class="row g-3">
                            <!-- Dirección -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt text-success me-2"></i>Dirección
                                </label>
                                <select name="Id_direccion" class="form-select" required>
                                    <option value="" selected disabled>Seleccione una dirección</option>
                                    <?php
                                    // Traer direcciones para llenar el select
                                    $sqlDirecciones = "SELECT Id_direccion, nombre FROM direcciones ORDER BY nombre ASC";
                                    $stmtDirecciones = $pdo->prepare($sqlDirecciones);
                                    $stmtDirecciones->execute();
                                    $direcciones = $stmtDirecciones->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($direcciones as $direccion) {
                                        echo '<option value="' . htmlspecialchars($direccion['Id_direccion']) . '">' . htmlspecialchars($direccion['nombre']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Nombre de la Sección -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-card-text text-success me-2"></i>Nombre de la Sección
                                </label>
                                <input type="text" name="nombre" class="form-control" placeholder="Ej. Sección A" required>
                            </div>
                        </div>

                        <!-- FOOTER -->
                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-1"></i>Guardar Sección
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>




  <!-- Modal para Registrar/Editar Sección -->
<div class="modal fade" id="editSeccionModal" tabindex="-1" aria-labelledby="editSeccionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow">

            <!-- HEADER -->
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="editSeccionModalLabel">
                    <i class="bi bi-diagram-3-fill me-2"></i><span id="modalSeccionTitle">Registrar Sección</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">
                <form method="POST" action="../api/actualizar_seccion.php" id="formSeccion">
                    <!-- Campo oculto para edición -->
                    <input type="hidden" name="Id_seccion" id="Id_seccion">

                    <div class="row g-3">
                        <!-- Dirección -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-geo-alt text-success me-2"></i>Dirección
                            </label>
                            <select name="Id_direccion" id="Id_direccion" class="form-select" required>
                                <option value="" selected disabled>Seleccione una dirección</option>
                                <?php
                                // Traer direcciones para llenar el select
                                $sqlDirecciones = "SELECT Id_direccion, nombre FROM direcciones ORDER BY nombre ASC";
                                $stmtDirecciones = $pdo->prepare($sqlDirecciones);
                                $stmtDirecciones->execute();
                                $direcciones = $stmtDirecciones->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($direcciones as $direccion) {
                                    echo '<option value="' . htmlspecialchars($direccion['Id_direccion']) . '">' . htmlspecialchars($direccion['nombre']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Nombre de la Sección -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-card-text text-success me-2"></i>Nombre de la Sección
                            </label>
                            <input type="text" name="nombre" id="nombreSeccion" class="form-control" placeholder="Ej. Sección A" required>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save me-1"></i><span id="btnSeccionText">Guardar Sección</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    var seccionModal = document.getElementById('editSeccionModal');

    // Función para abrir modal y rellenar datos si es edición
    seccionModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;

        var id = button.getAttribute('data-id') || '';
        var idDireccion = button.getAttribute('data-id_direccion') || '';
        var nombre = button.getAttribute('data-nombre') || '';

        // Rellenar campos
        document.getElementById('Id_seccion').value = id;
        document.getElementById('Id_direccion').value = idDireccion;
        document.getElementById('nombreSeccion').value = nombre;

        // Cambiar títulos y botón según si es edición o registro
        if (id) {
            document.getElementById('modalSeccionTitle').textContent = 'Editar Sección';
            document.getElementById('btnSeccionText').textContent = 'Actualizar Sección';
            document.getElementById('formSeccion').action = '../api/actualizar_seccion.php';
        } else {
            document.getElementById('modalSeccionTitle').textContent = 'Registrar Sección';
            document.getElementById('btnSeccionText').textContent = 'Guardar Sección';
            document.getElementById('formSeccion').action = '../api/guardar_seccion.php';
        }
    });

    // Resetear modal al cerrarlo
    seccionModal.addEventListener('hidden.bs.modal', function () {
        document.getElementById('formSeccion').reset();
        document.getElementById('Id_seccion').value = '';
    });
</script>









    <?php
    include_once '../includes/footer.php';
    ?>