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

// Consulta para obtener secciones
$sql = "SELECT Id_seccion, Id_direccion, nombre
        FROM secciones
        ORDER BY Id_seccion DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$secciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<table class="table table-hover align-middle mb-0" id="seccionesTable">
    <thead class="table-light">
        <tr>
            <th>ID Sección</th>
            <th>ID Dirección</th>
            <th>Nombre Sección</th>
            <th class="text-center">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($secciones): ?>
            <?php foreach ($secciones as $seccion): ?>
                <tr>
                    <td><?= htmlspecialchars($seccion['Id_seccion']) ?></td>
                    <td><?= htmlspecialchars($seccion['Id_direccion']) ?></td>
                    <td><?= htmlspecialchars($seccion['nombre']) ?></td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <!-- EDITAR SECCIÓN -->
                            <button
                                class="btn btn-sm btn-warning btn-editar-seccion"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditarSeccion"
                                data-id="<?= $seccion['Id_seccion'] ?>"
                                data-direccion-id="<?= $seccion['Id_direccion'] ?>"
                                data-nombre="<?= htmlspecialchars($seccion['nombre']) ?>"
                                title="Editar sección">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <!-- ELIMINAR SECCIÓN -->
                            <button
                                class="btn btn-sm btn-danger btn-eliminar-seccion"
                                data-id="<?= $seccion['Id_seccion'] ?>"
                                title="Eliminar sección">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center text-muted">
                    No hay secciones registradas
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





    <!-- Modal para Registrar / Editar Dirección -->
    <div class="modal fade" id="modalEditarDireccion" tabindex="-1" aria-labelledby="modalEditarDireccionLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow">

                <!-- HEADER -->
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalEditarDireccionLabel">
                        <i class="bi bi-geo-alt-fill me-2"></i><span id="tituloModal">Registrar Dirección</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body">
                    <form method="POST" action="../api/guardar_direccion.php" id="formDireccion">

                        <!-- Campo oculto para ID -->
                        <input type="hidden" name="id_direccion" id="id_direccion">

                        <div class="row g-3">
                            <!-- Nombre -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt text-success me-2"></i>Nombre
                                </label>
                                <input type="text" name="nombre" id="nombre" class="form-control"
                                    placeholder="Ej. Sede Central" required>
                            </div>

                            <!-- Ubicación -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-map text-success me-2"></i>Ubicación
                                </label>
                                <input type="text" name="ubicacion" id="ubicacion" class="form-control"
                                    placeholder="Ej. Avenida Principal, Nº 123">
                            </div>

                            <!-- Distrito -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-building text-success me-2"></i>Distrito
                                </label>
                                <select name="distrito" id="distrito" class="form-select" required>
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

                            <!-- Provincia -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-flag text-success me-2"></i>Provincia
                                </label>
                                <select name="provincia" id="provincia" class="form-select" required>
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

                            <!-- Región -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-flag text-success me-2"></i>Región
                                </label>
                                <select name="region" id="region" class="form-select" required>
                                    <option value="" selected disabled>Seleccione una Región</option>
                                    <option value="Region Continental">Region Continental</option>
                                    <option value="Region Insular">Region Insular</option>
                                </select>
                            </div>

                            <!-- FOOTER -->
                            <div class="mt-4 d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle me-1"></i>Cancelar
                                </button>
                                <button type="submit" class="btn btn-success" id="btnGuardar">
                                    <i class="bi bi-save me-1"></i>Guardar Dirección
                                </button>
                            </div>

                    </form>
                </div>

            </div>
        </div>
    </div>




    <!-- SCRIPT PARA RELLENAR MODAL -->
    <script>
        // Detectar clic en botón de editar
        document.querySelectorAll('.btn-editar-direccion').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const nombre = this.dataset.nombre;
                const ubicacion = this.dataset.ubicacion;
                const distrito = this.dataset.distrito;
                const provincia = this.dataset.provincia;
                const region = this.dataset.region;

                // Cambiar título y botón
                document.getElementById('tituloModal').textContent = 'Editar Dirección';
                document.getElementById('btnGuardar').innerHTML = '<i class="bi bi-save me-1"></i>Actualizar Dirección';

                // Rellenar formulario
                document.getElementById('id_direccion').value = id;
                document.getElementById('nombre').value = nombre;
                document.getElementById('ubicacion').value = ubicacion;
                document.getElementById('distrito').value = distrito;
                document.getElementById('provincia').value = provincia;
                document.getElementById('region').value = region;

                // Cambiar action si deseas diferenciar entre guardar y actualizar
                document.getElementById('formDireccion').action = '../api/actualizar_direccion.php';
            });
        });

        // Reset modal al cerrar
        var modalEditar = document.getElementById('modalEditarDireccion');
        modalEditar.addEventListener('hidden.bs.modal', function() {
            document.getElementById('tituloModal').textContent = 'Registrar Dirección';
            document.getElementById('btnGuardar').innerHTML = '<i class="bi bi-save me-1"></i>Guardar Dirección';
            document.getElementById('formDireccion').reset();
            document.getElementById('formDireccion').action = '../api/guardar_direccion.php';
        });
    </script>





    <?php
    include_once '../includes/footer.php';
    ?>