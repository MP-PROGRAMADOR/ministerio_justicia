<?php
include_once '../includes/header.php';
include_once '../includes/silebar_admin.php';

// Conexión a la base de datos
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Consulta para obtener direcciones
$sql = "SELECT Id_direccion, nombre, ubicacion, distrito, provincia, region FROM direcciones ORDER BY Id_direccion DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$direcciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="main-content" id="mainContent">

                <div class="header-section">
                    <div class="row align-items-center">
                        <div class="col-md-12 text-md-end mt-3 mt-md-0">
                            <div class="d-flex justify-content-md-end align-items-center gap-2 flex-wrap justify-content-center">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalDireccion" onclick="prepararModal('registrar')">
                                    <i class="bi bi-plus-circle me-2"></i> Nueva Direccion
                                </button>

                                <div class="input-group" style="width: auto;">
                                    <input type="text" class="form-control" id="liveSearchInput" placeholder="Buscar en tabla...">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show m-3 alert-auto-dismiss" role="alert">
                        <?= htmlspecialchars($_SESSION['error']);
                        unset($_SESSION['error']); ?>
                        
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['exito'])): ?>
                    <div class="alert alert-success alert-dismissible fade show m-3 alert-auto-dismiss" role="alert">
                        <?= htmlspecialchars($_SESSION['exito']);
                        unset($_SESSION['exito']); ?>
                        
                    </div>
                <?php endif; ?>

                <div class="container-fluid px-4">
                    <div class="table-custom mb-4 p-4 shadow-sm bg-white rounded">
                        <h5 class="mb-3 fw-semibold">Listado de Direcciones</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="funcionariosTable">
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
                                <tbody>
                                    <?php if ($direcciones): ?>
                                        <?php foreach ($direcciones as $dir): ?>
                                            <tr>
                                                <td><?= $dir['Id_direccion'] ?></td>
                                                <td class="fw-semibold"><?= htmlspecialchars($dir['nombre']) ?></td>
                                                <td><?= htmlspecialchars($dir['ubicacion'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($dir['distrito'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($dir['provincia'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($dir['region'] ?? '—') ?></td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-warning"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalDireccion"
                                                        onclick="prepararModal('editar', <?= htmlspecialchars(json_encode($dir)) ?>)">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No hay registros</td>
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
                        <span class="text-muted">© 2024 Themis | Ministerio de Justicia. Todos los derechos reservados.</span>
                        
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDireccion" tabindex="-1" aria-labelledby="modalDireccionLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-success text-white mb-2">
                    <h5 class="modal-title" id="modalDireccionLabel">
                        <i class="bi bi-geo-alt-fill me-2"></i><span id="tituloModal">Registrar Dirección</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="../api/guardar_direccion.php" id="formDireccion">
                        <input type="hidden" name="id_direccion" id="field_id">

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Nombre</label>
                                <input type="text" name="nombre" id="field_nombre" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ubicación</label>
                                <input type="text" name="ubicacion" id="field_ubicacion" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Distrito</label>
                                <select name="distrito" id="field_distrito" class="form-select" required>
                                    <option value="" disabled selected>Seleccione...</option>
                                    <option value="Malabo">Malabo</option>
                                    <option value="Bata">Bata</option>
                                    <option value="Luba">Luba</option>
                                    <option value="Evinayong">Evinayong</option>
                                    <option value="Mongomo">Mongomo</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Provincia</label>
                                <select name="provincia" id="field_provincia" class="form-select" required>
                                    <option value="" disabled selected>Seleccione...</option>
                                    <option value="Bioko Norte">Bioko Norte</option>
                                    <option value="Litoral">Litoral</option>
                                    <option value="Centro Sur">Centro Sur</option>
                                    <option value="Wele-Nzas">Wele-Nzas</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Región</label>
                                <select name="region" id="field_region" class="form-select" required>
                                    <option value="" disabled selected>Seleccione...</option>
                                    <option value="Region Continental">Region Continental</option>
                                    <option value="Region Insular">Region Insular</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2 mb-2">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success" id="btnSubmit">Guardar Dirección</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function prepararModal(accion, data = null) {
            const form = document.getElementById('formDireccion');
            const titulo = document.getElementById('tituloModal');
            const btn = document.getElementById('btnSubmit');

            if (accion === 'editar') {
                titulo.innerText = 'Editar Dirección';
                btn.innerText = 'Actualizar Dirección';
                form.action = '../api/actualizar_direccion.php';

                // Rellenar campos
                document.getElementById('field_id').value = data.Id_direccion;
                document.getElementById('field_nombre').value = data.nombre;
                document.getElementById('field_ubicacion').value = data.ubicacion;
                document.getElementById('field_distrito').value = data.distrito;
                document.getElementById('field_provincia').value = data.provincia;
                document.getElementById('field_region').value = data.region;
            } else {
                titulo.innerText = 'Registrar Dirección';
                btn.innerText = 'Guardar Dirección';
                form.action = '../api/guardar_direccion.php';
                form.reset();
                document.getElementById('field_id').value = '';
            }
        }
    </script>

    <!-- Script para que la alerta desaparezca -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const alertas = document.querySelectorAll('.alert-auto-dismiss');
            alertas.forEach(function(alerta) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alerta);
                    bsAlert.close();
                }, 3000);
            });
        });
    </script>

    <?php include_once '../includes/footer.php'; ?>