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
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDireccionModal">
                                    <i class="bi bi-plus-circle me-2"></i> Nueva Direccion
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
                            <h5 class="mb-0 fw-semibold">Listado de Direcciones</h5>
                        </div>
                        <div class="table-responsive">
                            <?php
                            try {
                                $pdo = new PDO($dsn, $user, $pass, $options);
                            } catch (PDOException $e) {
                                die("Error de conexión: " . $e->getMessage());
                            }

                            // Consulta para obtener direcciones
                            $sql = "SELECT Id_direccion, nombre, ubicacion, distrito, provincia, region
            FROM direcciones
            ORDER BY Id_direccion DESC";

                            $stmt = $pdo->prepare($sql);
                            $stmt->execute();
                            $direcciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            ?>

                            <table class="table table-hover align-middle mb-0" id="funcionariosTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Ubicación</th>
                                        <th>Distrito</th>
                                        <th>Provincia</th>
                                        <th>Región</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="funcionariosTableBody">
                                    <?php if ($direcciones): ?>
                                        <?php foreach ($direcciones as $direccion): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($direccion['Id_direccion']) ?></td>
                                                <td class="fw-semibold"><?= htmlspecialchars($direccion['nombre']) ?></td>
                                                <td><?= htmlspecialchars($direccion['ubicacion'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($direccion['distrito'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($direccion['provincia'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($direccion['region'] ?? '—') ?></td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">

                                                        <!-- EDITAR -->
                                                        <!-- EDITAR -->
                                                        <!-- EDITAR DIRECCIÓN -->
                                                        <button
                                                            class="btn btn-sm btn-warning btn-editar-direccion"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#addDireccionModal2"
                                                            data-id="<?= $direccion['Id_direccion'] ?>"
                                                            data-nombre="<?= htmlspecialchars($direccion['nombre']) ?>"
                                                            data-ubicacion="<?= htmlspecialchars($direccion['ubicacion']) ?>"
                                                            data-distrito="<?= htmlspecialchars($direccion['distrito']) ?>"
                                                            data-provincia="<?= htmlspecialchars($direccion['provincia']) ?>"
                                                            data-region="<?= htmlspecialchars($direccion['region']) ?>"
                                                            title="Editar dirección">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>





                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                No hay direcciones registradas
                                            </td>
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







    <!-- Modal para Registrar Dirección -->
    <div class="modal fade" id="addDireccionModal" tabindex="-1" aria-labelledby="addDireccionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow">

                <!-- HEADER -->
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addDireccionModalLabel">
                        <i class="bi bi-geo-alt-fill me-2"></i>Registrar Dirección
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body">
                    <form method="POST" action="../api/guardar_direccion.php" id="formDireccion">

                        <div class="row g-3">
                            <!-- Nombre -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt text-success me-2"></i>Nombre
                                </label>
                                <input type="text" name="nombre" class="form-control"
                                    placeholder="Ej. Sede Central" required>
                            </div>

                            <!-- Ubicación -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-map text-success me-2"></i>Ubicación
                                </label>
                                <input type="text" name="ubicacion" class="form-control"
                                    placeholder="Ej. Avenida Principal, Nº 123">
                            </div>

                            <!-- Distrito -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-building text-success me-2"></i>Distrito
                                </label>
                                <select name="distrito" class="form-select" required>
                                    <option value="" selected disabled>Seleccione un distrito</option>
                                    <!-- Distritos de Guinea Ecuatorial -->
                                    <option value="Bata">Bata</option>
                                    <option value="Mbini">Mbini</option>
                                    <option value="Evinayong">Evinayong</option>
                                    <option value="Micomeseng">Micomeseng</option>
                                    <option value="Mongomo">Mongomo</option>
                                    <option value="Mongomo">Mengomeyén</option>
                                    <option value="Nsok">Nsok</option>
                                    <option value="Aconibe">Aconibe</option>
                                    <option value="Riaba">Riaba</option>
                                    <option value="Luba">Luba</option>
                                    <option value="Malabo">Malabo</option>
                                    <option value="Baney">Baney</option>
                                    <option value="Rebola">Rebola</option>
                                    <option value="Bikok">Bikok</option>
                                    <!-- Agrega el resto según sea necesario -->
                                </select>
                            </div>

                            <!-- Provincia -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-flag text-success me-2"></i>Provincia
                                </label>
                                <select name="provincia" class="form-select" required>
                                    <option value="" selected disabled>Seleccione una provincia</option>
                                    <!-- Provincias de Guinea Ecuatorial -->
                                    <option value="Litoral">Litoral</option>
                                    <option value="Centro Sur">Centro Sur</option>
                                    <option value="Kié-Ntem">Kié-Ntem</option>
                                    <option value="Wele-Nzas">Wele-Nzas</option>
                                    <option value="Annobón">Annobón</option>
                                    <option value="Bioko Norte">Bioko Norte</option>
                                    <option value="Bioko Sur">Bioko Sur</option>
                                </select>
                            </div>




                            <!-- Provincia -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-flag text-success me-2"></i>Región
                                </label>
                                <select name="region" class="form-select" required>
                                    <option value="" selected disabled>Seleccione una Región</option>
                                    <!-- Provincias de Guinea Ecuatorial -->
                                    <option value="Region Continental">Region Continental</option>
                                    <option value="Region Insular">Region Insular</option>
                                </select>
                            </div>

                            <!-- FOOTER -->
                            <div class="mt-4 d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle me-1"></i>Cancelar
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-save me-1"></i>Guardar Dirección
                                </button>
                            </div>

                    </form>
                </div>

            </div>
        </div>
    </div>





    <!-- Modal para Editar Dirección -->
    <div class="modal fade" id="addDireccionModal2" tabindex="-1" aria-labelledby="addDireccionModal2Label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow">

                <!-- HEADER -->
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="addDireccionModal2Label">
                        <i class="bi bi-geo-alt-fill me-2"></i><span id="modalTitle2">Editar Dirección</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body">
                    <form method="POST" action="../api/actualizar_direccion.php" id="formDireccion2">
                        <input type="hidden" name="Id_direccion" id="Id_direccion2">

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt text-success me-2"></i>Nombre
                                </label>
                                <input type="text" name="nombre" id="nombre2" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-map text-success me-2"></i>Ubicación
                                </label>
                                <input type="text" name="ubicacion" id="ubicacion2" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-building text-success me-2"></i>Distrito
                                </label>
                                <select name="distrito" id="distrito2" class="form-select" required>
                                    <option value="" selected disabled>Seleccione un distrito</option>
                                    <option value="Bata">Bata</option>
                                    <option value="Mbini">Mbini</option>
                                    <option value="Evinayong">Evinayong</option>
                                    <option value="Micomeseng">Micomeseng</option>
                                    <option value="Mongomo">Mongomo</option>
                                    <option value="Mengomeyén">Mengomeyén</option>
                                    <option value="Nsok">Nsok</option>
                                    <option value="Aconibe">Aconibe</option>
                                    <option value="Riaba">Riaba</option>
                                    <option value="Luba">Luba</option>
                                    <option value="Malabo">Malabo</option>
                                    <option value="Baney">Baney</option>
                                    <option value="Rebola">Rebola</option>
                                    <option value="Bikok">Bikok</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-flag text-success me-2"></i>Provincia
                                </label>
                                <select name="provincia" id="provincia2" class="form-select" required>
                                    <option value="" selected disabled>Seleccione una provincia</option>
                                    <option value="Litoral">Litoral</option>
                                    <option value="Centro Sur">Centro Sur</option>
                                    <option value="Kié-Ntem">Kié-Ntem</option>
                                    <option value="Wele-Nzas">Wele-Nzas</option>
                                    <option value="Annobón">Annobón</option>
                                    <option value="Bioko Norte">Bioko Norte</option>
                                    <option value="Bioko Sur">Bioko Sur</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-globe text-success me-2"></i>Región
                                </label>
                                <select name="region" id="region2" class="form-select" required>
                                    <option value="" selected disabled>Seleccione una Región</option>
                                    <option value="Region Continental">Región Continental</option>
                                    <option value="Region Insular">Región Insular</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save me-1"></i><span id="btnText2">Actualizar Dirección</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        var direccionModal2 = document.getElementById('addDireccionModal2');

        function setSelectByText(selectId, text) {
            var select = document.getElementById(selectId);
            if (!text) return;
            for (var i = 0; i < select.options.length; i++) {
                if (select.options[i].text.trim() === text.trim()) {
                    select.selectedIndex = i;
                    break;
                }
            }
        }

        direccionModal2.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;

            document.getElementById('Id_direccion2').value = button.getAttribute('data-id') || '';
            document.getElementById('nombre2').value = button.getAttribute('data-nombre') || '';
            document.getElementById('ubicacion2').value = button.getAttribute('data-ubicacion') || '';
            setSelectByText('distrito2', button.getAttribute('data-distrito'));
            setSelectByText('provincia2', button.getAttribute('data-provincia'));
            setSelectByText('region2', button.getAttribute('data-region'));
        });

        direccionModal2.addEventListener('hidden.bs.modal', function() {
            document.getElementById('formDireccion2').reset();
            document.getElementById('Id_direccion2').value = '';
        });
    </script>








    <?php
    include_once '../includes/footer.php';
    ?>