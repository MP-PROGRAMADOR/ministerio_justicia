<?php
include_once '../includes/header.php';
include_once '../includes/silebar_admin.php';

// Consultas para llenar los selectores del Modal
try {
    $funcionarios_list = $pdo->query("SELECT Id_funcionario, Nombre, Apellidos FROM funcionarios ORDER BY Nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
    $cargos_list       = $pdo->query("SELECT Id_cargo, Nombre FROM cargos ORDER BY Nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
    $direcciones_list  = $pdo->query("SELECT Id_direccion, nombre FROM direcciones ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
    $secciones_list    = $pdo->query("SELECT Id_seccion, nombre FROM secciones ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
    $categorias_list   = $pdo->query("SELECT Id_categoria, nombre FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="main-content" id="mainContent">

                <div class="header-section">
                    <div class="row align-items-center">
                        <div class="col-md-12 text-md-end mt-3 mt-md-0">
                            <div class="d-flex justify-content-md-end align-items-center gap-2 flex-wrap justify-content-center">
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addNombramientoModal">
                                    <i class="bi bi-plus-circle me-2"></i> Nuevo Nombramiento
                                </button>
                                <div class="input-group" style="width: auto;">
                                    <input type="text" class="form-control" id="liveSearchInput" placeholder="Buscar en tabla...">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
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
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['exito'])): ?>
                        <div id="mensajeFlash" class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION['exito']);
                            unset($_SESSION['exito']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="container-fluid px-4">
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
                                    <?php
                                    $sql = "SELECT n.*, CONCAT(f.Nombre, ' ', f.Apellidos) as Funcionario, 
                                                   c.Nombre as Cargo, d.nombre as Direccion, s.nombre as Seccion, cat.nombre as Categoria
                                            FROM nombramientos n
                                            JOIN funcionarios f ON n.Id_funcionario = f.Id_funcionario
                                            JOIN cargos c ON n.Id_cargo = c.Id_cargo
                                            LEFT JOIN direcciones d ON n.Id_direccion = d.Id_direccion
                                            LEFT JOIN secciones s ON n.Id_seccion = s.Id_seccion
                                            LEFT JOIN categorias cat ON n.Id_categoria = cat.Id_categoria
                                            ORDER BY n.Id_nombramiento DESC";
                                    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

                                    if (count($rows) > 0):
                                        foreach ($rows as $row): ?>
                                            <tr>
                                                <td>#<?= $row['Id_nombramiento'] ?></td>
                                                <td class="fw-semibold"><?= htmlspecialchars($row['Funcionario']) ?></td>
                                                <td>
                                                    <small class="d-block text-dark fw-medium"><?= htmlspecialchars($row['Cargo']) ?></small>
                                                    <small class="text-muted"><?= htmlspecialchars($row['Categoria'] ?? '—') ?></small>
                                                </td>
                                                <td>
                                                    <small class="d-block"><strong>Dir:</strong> <?= htmlspecialchars($row['Direccion'] ?? 'N/A') ?></small>
                                                    <small class="d-block text-muted"><strong>Sec:</strong> <?= htmlspecialchars($row['Seccion'] ?? 'N/A') ?></small>
                                                </td>
                                                <td style="font-size: 0.85rem;">
                                                    <div class="mb-1"><i class="bi bi-calendar-check text-success me-1"></i><?= $row['Fecha_nombramiento'] ?></div>
                                                    <div><i class="bi bi-geo-alt text-primary me-1"></i><?= $row['Fecha_toma_posesion'] ?: 'Pendiente' ?></div>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <?php if ($row['Copia_doc_nomb']): ?>
                                                            <a href="../uploads/<?= $row['Copia_doc_nomb'] ?>" target="_blank" class="btn btn-sm btn-outline-success" title="Nombramiento"><i class="bi bi-file-pdf"></i></a>
                                                        <?php endif; ?>
                                                        <?php if ($row['Copia_doc_tom_posesion']): ?>
                                                            <a href="../uploads/<?= $row['Copia_doc_tom_posesion'] ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Posesión"><i class="bi bi-file-pdf"></i></a>
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
                                                        data-id_dir="<?= $row['Id_direccion'] ?>"
                                                        data-id_sec="<?= $row['Id_seccion'] ?>"
                                                        data-id_cat="<?= $row['Id_categoria'] ?>">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach;
                                    else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">No se encontraron registros.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <nav aria-label="Page navigation example" class="mt-3">
                        <ul class="pagination justify-content-center" id="paginationControls">
                            <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1"
                                    aria-disabled="true">Anterior</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">Siguiente</a></li>
                        </ul>
                    </nav>
                </div>

                <footer class="footer bg-white shadow-sm py-3 mt-auto">
                    <div class="container-fluid text-center">
                        <span class="text-muted">© 2024 Themis | Ministerio de Justicia. Todos los derechos reservados.</span>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addNombramientoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-success text-white" id="modalHeader">
                    <h5 class="modal-title" id="modalTitle"><i class="bi bi-file-earmark-plus me-2"></i>Registrar Nombramiento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="nombramientoForm" method="POST" action="../api/guardar_nombramiento.php?accion=crear" enctype="multipart/form-data">
                    <input type="hidden" name="id_nombramiento" id="form_id">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Funcionario</label>
                                <select name="id_funcionario" id="form_func" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($funcionarios_list as $f): ?>
                                        <option value="<?= $f['Id_funcionario'] ?>"><?= htmlspecialchars($f['Nombre'] . ' ' . $f['Apellidos']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Cargo</label>
                                <select name="id_cargo" id="form_cargo" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($cargos_list as $c): ?>
                                        <option value="<?= $c['Id_cargo'] ?>"><?= htmlspecialchars($c['Nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fecha Nombramiento</label>
                                <input type="date" name="fecha_nombramiento" id="form_fecha_n" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fecha Toma Posesión</label>
                                <input type="date" name="fecha_toma_posesion" id="form_fecha_p" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Dirección</label>
                                <select name="id_direccion" id="form_dir" class="form-select">
                                    <option value="">Ninguna</option>
                                    <?php foreach ($direcciones_list as $d): ?>
                                        <option value="<?= $d['Id_direccion'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Sección</label>
                                <select name="id_seccion" id="form_sec" class="form-select">
                                    <option value="">Ninguna</option>
                                    <?php foreach ($secciones_list as $s): ?>
                                        <option value="<?= $s['Id_seccion'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Categoría</label>
                                <select name="id_categoria" id="form_cat" class="form-select">
                                    <option value="">Ninguna</option>
                                    <?php foreach ($categorias_list as $cat): ?>
                                        <option value="<?= $cat['Id_categoria'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Doc. Nombramiento</label>
                                <input type="file" name="doc_nombramiento" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Doc. Toma Posesión</label>
                                <input type="file" name="doc_posesion" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnSubmit" class="btn btn-success px-4">Guardar Registro</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Lógica para modo edición
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('addNombramientoModal'));
                document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Editar Nombramiento';
                document.getElementById('modalHeader').classList.replace('bg-success', 'bg-warning');
                document.getElementById('modalHeader').classList.add('text-dark');
                document.getElementById('btnSubmit').classList.replace('btn-success', 'btn-warning');
                document.getElementById('nombramientoForm').action = "../api/guardar_nombramiento.php?accion=actualizar";

                document.getElementById('form_id').value = this.dataset.id;
                document.getElementById('form_func').value = this.dataset.id_func;
                document.getElementById('form_cargo').value = this.dataset.id_cargo;
                document.getElementById('form_fecha_n').value = this.dataset.fecha_n;
                document.getElementById('form_fecha_p').value = this.dataset.fecha_p;
                document.getElementById('form_dir').value = this.dataset.id_dir;
                document.getElementById('form_sec').value = this.dataset.id_sec;
                document.getElementById('form_cat').value = this.dataset.id_cat;
                modal.show();
            });
        });

        // Limpiar modal al cerrar
        document.getElementById('addNombramientoModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('modalTitle').innerHTML = '<i class="bi bi-file-earmark-plus me-2"></i>Registrar Nombramiento';
            document.getElementById('modalHeader').className = 'modal-header bg-success text-white';
            document.getElementById('btnSubmit').className = 'btn btn-success px-4';
            document.getElementById('nombramientoForm').action = "../api/guardar_nombramiento.php?accion=crear";
            document.getElementById('nombramientoForm').reset();
            document.getElementById('form_id').value = "";
        });

        // Buscador Live
        document.getElementById('liveSearchInput').addEventListener('keyup', function() {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#nombramientosTableBody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
            });
        });

        // Auto-cierre de alertas
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alerta => {
                const bsAlert = new bootstrap.Alert(alerta);
                bsAlert.close();
            });
        }, 3000);
    </script>
</body>

<?php include_once '../includes/footer.php'; ?>