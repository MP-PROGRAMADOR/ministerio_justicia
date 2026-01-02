<?php
include_once '../includes/header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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
                            <div
                                class="d-flex justify-content-md-end align-items-center gap-2 flex-wrap justify-content-center">
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCargoModal">
                                    <i class="bi bi-plus-circle me-1"></i> Añadir Cargo
                                </button>
                                <div class="input-group" style="width: auto;">
                                    <input type="text" class="form-control" id="liveSearchInput"
                                        placeholder="Buscar cargo...">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="container-fluid px-4">

                    <?php
                    if (isset($_SESSION['error'])) {
                        echo "<div id='mensajeFlash' class='alert alert-danger alert-dismissible fade show' role='alert'>"
                                . htmlspecialchars($_SESSION['error']) .
                                "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                              </div>"; unset($_SESSION['error']);
                    }

                    if (isset($_SESSION['exito'])) {
                        echo "<div id='mensajeFlash' class='alert alert-success alert-dismissible fade show' role='alert'>"
                                . htmlspecialchars($_SESSION['exito']) .
                                "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>"; unset($_SESSION['exito']);
                    }
                    ?>
                    <div class="table-custom mb-4 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-semibold">Listado de Cargos Registrados</h5>
                        </div>
                        <div class="table-responsive">
                            <?php if (isset($_SESSION['exito'])): ?>
                                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                    <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['exito']);
                                                                                unset($_SESSION['exito']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            <table class="table table-hover align-middle" id="cargosTable">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">ID</th>
                                        <th>Nombre del Cargo</th>
                                        <th>Nivel</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="cargosTableBody">
                                    <?php
                                    // Se usa tbl_cargos (nombre real en tu SQL)
                                    $sql = "SELECT * FROM cargos ORDER BY Id_cargo DESC";
                                    $stmt = $pdo->query($sql);
                                    $cargos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    if (count($cargos) > 0):
                                        foreach ($cargos as $cargo):
                                    ?>
                                            <tr>
                                                <td class="ps-3 text-muted">#<?= $cargo['Id_cargo'] ?></td>
                                                <td class=" text-dark"><?= htmlspecialchars($cargo['Nombre']) ?></td>

                                                <td><span class="badge bg-info text-dark px-3">Nivel <?= $cargo['Nivel_jerarquico'] ?></span></td>
                                                <td class="text-center">

                                                    <button class="btn btn-warning btn-sm btn-editar-cargo"
                                                        data-id="<?= $cargo['Id_cargo'] ?>"
                                                        data-nombre="<?= htmlspecialchars($cargo['Nombre']) ?>"
                                                        data-nivel="<?= $cargo['Nivel_jerarquico'] ?>">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="confirmarEliminacion(<?= $cargo['Id_cargo'] ?>, '<?= htmlspecialchars($cargo['Nombre']) ?>')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>

                                                </td>
                                            </tr>
                                        <?php
                                        endforeach;
                                    else:
                                        ?>
                                        <tr>
                                            <td colspan="5" class="text-center no-data-msg">
                                                <div class="py-5">
                                                    <i class="bi bi-briefcase text-secondary opacity-25 display-1"></i>
                                                    <p class="mt-3 fs-5 fw-bold">No hay cargos registrados actualmente.</p>
                                                    <p class="text-muted">Parece que la base de datos está vacía. ¡Añade tu primer cargo!</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

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
    </div>






    <!-- Modal de Añadir Cargo-->
    <div class="modal fade" id="addCargoModal" tabindex="-1" aria-labelledby="addCargoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addCargoLabel"><i class="bi bi-plus-circle me-2"></i>Nuevo Cargo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="../api/guardar_cargo.php?accion=crear">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre del Cargo </label>
                            <input type="text" class="form-control" name="nombre" placeholder="Ej. Jefe de Sección" required>
                        </div>
                        <!-- <div class="mb-3">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea name="descripcion" rows="3" class="form-control" placeholder="Detalles del cargo..."></textarea>
                        </div> -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nivel Jerárquico</label>
                            <input type="number" class="form-control" name="nivel" min="1" value="1" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="submit" class="btn btn-success px-4">Guardar Cargo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- Modal de Editar Cargo-->
    <div class="modal fade" id="editCargoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Editar Cargo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="../api/guardar_cargo.php?accion=actualizar">
                    <div class="modal-body p-4">
                        <input type="hidden" name="id" id="edit_ID_Cargo">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre del Cargo</label>
                            <input type="text" class="form-control" name="nombre" id="edit_Nombre_Cargo" required>
                        </div>
                        <!-- <div class="mb-3">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea class="form-control" name="descripcion" id="edit_Descripcion_Cargo" rows="3"></textarea>
                        </div> -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nivel Jerárquico</label>
                            <input type="number" class="form-control" name="nivel" id="edit_Nivel_Jerarquico" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="submit" class="btn btn-warning px-4 fw-bold">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




    <script>
        //Script Editar cargo
        document.addEventListener("DOMContentLoaded", function() {
            const editModalEl = document.getElementById('editCargoModal');
            // Solo inicializamos si el elemento existe
            const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;

            document.querySelectorAll(".btn-editar-cargo").forEach(btn => {
                btn.addEventListener("click", function() {
                    document.getElementById("edit_ID_Cargo").value = this.dataset.id;
                    document.getElementById("edit_Nombre_Cargo").value = this.dataset.nombre;
                    // document.getElementById("edit_Descripcion_Cargo").value = this.dataset.descripcion;
                    document.getElementById("edit_Nivel_Jerarquico").value = this.dataset.nivel;
                    if (editModal) editModal.show();
                });
            });

            // Buscador en vivo
            const searchInput = document.getElementById('liveSearchInput');
            searchInput.addEventListener('keyup', function() {
                const term = this.value.toLowerCase();
                const rows = document.querySelectorAll('#cargosTableBody tr');
                rows.forEach(row => {
                    if (!row.querySelector('.no-data-msg')) {
                        row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
                    }
                });
            });
        });

        // SweetAlert para eliminación
        function confirmarEliminacion(id, nombre) {
            Swal.fire({
                title: '¿Estas Seguro?',
                text: `Estás a punto de eliminar "${nombre}".`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: ' <i class="bi bi-trash"></i> Sí, eliminar',
                cancelButtonText: ' <i class="bi bi-x-circle"></i> Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `../api/eliminar_cargo.php?accion=eliminar&id=${id}`;
                }
            });
        }
    </script>
</body>

</html>