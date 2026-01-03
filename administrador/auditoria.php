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
                            <div class="d-flex justify-content-md-end align-items-center gap-2 flex-wrap justify-content-center">
                                
                                <div class="input-group" style="width: auto;">
                                    <input type="text" class="form-control" id="liveSearchInput" placeholder="Buscar en tabla...">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>








                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>




                <div class="container-fluid px-4">
                    <div class="table-custom mb-4 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-semibold">Listado de logs</h5>
                        </div>
                        <div class="table-responsive">
                            <?php
                            try {
                                $pdo = new PDO($dsn, $user, $pass, $options);
                            } catch (PDOException $e) {
                                die("Error de conexión: " . $e->getMessage());
                            }

                            // Consulta para traer todos los logs con información del usuario
                            $sql = "SELECT l.Id_log, u.Nombre_Usuario, l.Tabla_afectada, l.Accion, l.Registro_id, 
                   l.Fecha, l.IP, l.Dispositivo
            FROM logs l
            LEFT JOIN tbl_usuarios u ON l.Usuario_id = u.ID_Usuario
            ORDER BY l.Fecha DESC";

                            $stmt = $pdo->prepare($sql);
                            $stmt->execute();
                            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            ?>

                            <table class="table table-hover align-middle mb-0" id="funcionariosTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID Log</th>
                                        <th>Usuario</th>
                                        <th>Tabla Afectada</th>
                                        <th>Acción</th>
                                        <th>ID Registro</th>
                                        <th>Fecha / Hora</th>
                                        <th>IP</th>
                                        <th>Dispositivo</th>
                                    </tr>
                                </thead>
                                <tbody id="funcionariosTableBody">
                                    <?php if ($logs): ?>
                                        <?php foreach ($logs as $log): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($log['Id_log']) ?></td>
                                                <td><?= htmlspecialchars($log['Nombre_Usuario'] ?? 'Desconocido') ?></td>
                                                <td><?= htmlspecialchars($log['Tabla_afectada']) ?></td>
                                                <td>
                                                    <?php
                                                    $accionClass = match ($log['Accion']) {
                                                        'INSERT' => 'badge bg-success',
                                                        'UPDATE' => 'badge bg-warning text-dark',
                                                        'DELETE' => 'badge bg-danger',
                                                        'LOGIN'  => 'badge bg-primary',
                                                        'LOGOUT' => 'badge bg-secondary',
                                                        default  => 'badge bg-light text-dark'
                                                    };
                                                    ?>
                                                    <span class="<?= $accionClass ?>"><?= $log['Accion'] ?></span>
                                                </td>
                                                <td><?= htmlspecialchars($log['Registro_id']) ?></td>
                                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($log['Fecha']))) ?></td>
                                                <td><?= htmlspecialchars($log['IP'] ?? '-') ?></td>
                                                <td>
                                                    <small class="text-muted"><?= htmlspecialchars($log['Dispositivo'] ?? '-') ?></small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                No hay logs registrados
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









    <?php
    include_once '../includes/footer.php';
    ?>