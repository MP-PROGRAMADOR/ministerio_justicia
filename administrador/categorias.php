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
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoriaModal">
                                    <i class="bi bi-plus-circle me-2"></i> Nueva Categoria
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
                            <h5 class="mb-0 fw-semibold">Listado de Categorias</h5>
                        </div>
                        <div class="table-responsive">
                            <?php
                            try {
                                $pdo = new PDO($dsn, $user, $pass, $options);
                            } catch (PDOException $e) {
                                die("Error de conexión: " . $e->getMessage());
                            }

                            // Consulta para obtener categorías
                            $sql = "SELECT Id_categoria, nombre, descripcion 
            FROM categorias 
            ORDER BY Id_categoria DESC";

                            $stmt = $pdo->prepare($sql);
                            $stmt->execute();
                            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            ?>

                            <table class="table table-hover align-middle mb-0" id="funcionariosTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="funcionariosTableBody">
                                    <?php if ($categorias): ?>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($categoria['Id_categoria']) ?></td>
                                                <td class="fw-semibold">
                                                    <?= htmlspecialchars($categoria['nombre']) ?>
                                                </td>
                                                <td>
                                                    <?= htmlspecialchars($categoria['descripcion'] ?? '—') ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">

                                                        <!-- EDITAR -->
                                                        <button
                                                            class="btn btn-sm btn-warning btn-editar-categoria"
                                                            data-id="<?= $categoria['Id_categoria'] ?>"
                                                            data-nombre="<?= htmlspecialchars($categoria['nombre']) ?>"
                                                            data-descripcion="<?= htmlspecialchars($categoria['descripcion']) ?>"
                                                            title="Editar categoría">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>

                                                      

                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">
                                                No hay categorías registradas
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





   

    <!-- Modal para Registrar categoria -->
<div class="modal fade" id="addCategoriaModal" tabindex="-1" aria-labelledby="addCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow">

            <!-- HEADER -->
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addCategoriaModalLabel">
                    <i class="bi bi-tags-fill me-2"></i>Registrar Categoría
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Cerrar"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">
                <form method="POST" action="../api/guardar_categoria.php" id="formCategoria">

                    <div class="row g-3">
                        <!-- Nombre -->
                        <div class="col-md-12 mt-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-tag text-success me-2"></i>Nombre de la categoría
                            </label>
                            <input type="text" name="nombre" class="form-control"
                                placeholder="Ej. Material Médico" required>
                        </div>

                        <!-- Descripción -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-card-text text-success me-2"></i>Descripción
                            </label>
                            <textarea name="descripcion" class="form-control" rows="4"
                                placeholder="Descripción opcional de la categoría..."></textarea>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="mt-4 mb-2 d-flex justify-content-end gap-2">
                       
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save me-1"></i> Registrar Categoría
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

   



<div class="modal fade" id="editCategoriaModal" tabindex="-1" aria-labelledby="editCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow">

            <!-- HEADER -->
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editCategoriaModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Editar Categoría
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Cerrar"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">
                <form method="POST" action="../api/actualizar_categoria.php" id="formEditarCategoria">

                    <input type="hidden" name="Id_categoria" id="editIdCategoria">

                    <div class="mb-3 mt-4">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-tag me-2 text-warning"></i>Nombre de la categoría
                        </label>
                        <input type="text" name="nombre" id="editNombreCategoria"
                            class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-card-text me-2 text-warning"></i>Descripción
                        </label>
                        <textarea name="descripcion" id="editDescripcionCategoria"
                            class="form-control" rows="4"></textarea>
                    </div>

                    <!-- FOOTER -->
                    <div class="d-flex justify-content-end gap-2 mb-3">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save me-1"></i> Actualizar Cambios
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', () => {
    const editModal = new bootstrap.Modal(document.getElementById('editCategoriaModal'));

    document.querySelectorAll('.btn-editar-categoria').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('editIdCategoria').value = btn.dataset.id;
            document.getElementById('editNombreCategoria').value = btn.dataset.nombre;
            document.getElementById('editDescripcionCategoria').value = btn.dataset.descripcion || '';

            editModal.show();
        });
    });
});
</script>




    <?php
    include_once '../includes/footer.php';
    ?>