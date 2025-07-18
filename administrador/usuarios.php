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
                                <input type="text" class="form-control border-start-0" placeholder="Buscar funcionario...">
                            </div>
                            <button class="btn btn-outline-primary btn-refresh" onclick="refreshData()">
                                <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle me-1"></i> Juan Doe
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Mi Perfil</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Configuración</a></li>
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
                            <div class="d-flex justify-content-md-end align-items-center gap-2 flex-wrap justify-content-center">
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addFuncionarioModal">
                                    <i class="bi bi-plus-circle me-1"></i> Añadir Usuario
                                </button>
                                <div class="input-group" style="width: auto;">
                                    <input type="text" class="form-control" id="liveSearchInput" placeholder="Buscar en tabla...">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <?php




                if (isset($_SESSION['mensaje_exito'])) {
                    echo '<div id="mensajeExito" class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 1050;">'
                        . htmlspecialchars($_SESSION['mensaje_exito']) .
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                    unset($_SESSION['mensaje_exito']);
                }
                ?>






                <div class="container-fluid px-4">
                    <div class="table-custom mb-4 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-semibold">Listado de Funcionarios</h5>
                        </div>
                        <div class="table-responsive">

                        <?php
                            try {
                            
                            $pdo = new PDO($dsn, $user, $pass, $options);
                            } catch (PDOException $e) {
                            die("Error de conexión: " . $e->getMessage());
                            }

                            // Consulta para obtener usuarios
                            $sql = "SELECT ID_Usuario, Nombre_Usuario, Rol_Usuario, Email_Contacto, Fecha_Creacion, Ultimo_Acceso, Activo FROM tbl_Usuarios ORDER BY ID_Usuario ASC";
                            $stmt = $pdo->query($sql);
                            $usuarios = $stmt->fetchAll();
                            ?>

                            <table class="table table-hover align-middle mb-0" id="usuariosTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre Usuario</th>
                                        <th>Rol</th>
                                        <th>Email</th>
                                        <th>Fecha Creación</th>
                                        <th>Último Acceso</th>
                                        <th>Activo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuarios as $u): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($u['ID_Usuario']) ?></td>
                                            <td><?= htmlspecialchars($u['Nombre_Usuario']) ?></td>
                                            <td><?= htmlspecialchars($u['Rol_Usuario']) ?></td>
                                            <td><?= htmlspecialchars($u['Email_Contacto']) ?></td>
                                            <td><?= htmlspecialchars($u['Fecha_Creacion']) ?></td>
                                            <td><?= $u['Ultimo_Acceso'] ? htmlspecialchars($u['Ultimo_Acceso']) : '<em>No registrado</em>' ?></td>
                                            <td>
                                                <?php if ($u['Activo']): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm btn-primary" title="Editar"><i class="bi bi-pencil"></i></button>
                                                    <button class="btn btn-sm btn-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                                                    <button class="btn btn-sm btn-info" title="Detalles"><i class="bi bi-info-circle"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>
                        <nav aria-label="Page navigation example" class="mt-3">
                            <ul class="pagination justify-content-center" id="paginationControls">
                                <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a></li>
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
    <div class="modal fade" id="addFuncionarioModal" tabindex="-1" aria-labelledby="addFuncionarioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addFuncionarioModalLabel"><i class="bi bi-person-plus-fill me-2"></i>Añadir Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="../api/guardar_usuario.php" class="py-4 px-3">
                        <div class="row g-3">
                            <!-- Nombre de Usuario -->
                            <div class="col-md-6">
                                <label for="nombreUsuario" class="form-label fw-semibold">
                                    <i class="bi bi-person-fill me-2 text-primary"></i>Nombre de Usuario
                                </label>
                                <input type="text" class="form-control" id="nombreUsuario" name="Nombre_Usuario" placeholder="Ej: jdoe" required>
                            </div>

                            <!-- Contraseña -->
                            <div class="col-md-6">
                                <label for="contrasena" class="form-label fw-semibold">
                                    <i class="bi bi-lock-fill me-2 text-primary"></i>Contraseña
                                </label>
                                <input type="password" class="form-control" id="contrasena" name="Contrasena_Hash" placeholder="Ingresa una contraseña" required>
                            </div>

                            <!-- Email de Contacto -->
                            <div class="col-md-6">
                                <label for="emailContacto" class="form-label fw-semibold">
                                    <i class="bi bi-envelope-fill me-2 text-primary"></i>Email de Contacto
                                </label>
                                <input type="email" class="form-control" id="emailContacto" name="Email_Contacto" placeholder="Ej: correo@ejemplo.com" required>
                            </div>

                            <!-- Rol de Usuario -->
                            <div class="col-md-6">
                                <label for="rolUsuario" class="form-label fw-semibold">
                                    <i class="bi bi-person-badge-fill me-2 text-primary"></i>Rol de Usuario
                                </label>
                                <select class="form-select" id="rolUsuario" name="Rol_Usuario" required>
                                    <option value="" disabled selected>Selecciona un rol</option>
                                    <option value="Administrador">Administrador</option>
                                     <option value="Secretaria">Secretaria</option>
                                    <option value="Recursos Humanos">Recursos Humanos</option>
                                    <option value="Consulta">Consulta</option>
                                    <option value="Auditor">Auditor</option>
                                </select>
                            </div>

                            <!-- Usuario Activo -->
                            <div class="col-12 d-flex align-items-center">
                                <input class="form-check-input me-2" type="checkbox" id="activo" name="Activo" checked>
                                <label class="form-check-label fw-semibold" for="activo">
                                    <i class="bi bi-check-circle-fill text-success me-1"></i> Usuario Activo
                                </label>
                            </div>
                        </div>

                        <!-- Footer con botones -->
                        <div class="mt-4 d-flex justify-content-end gap-3">
                            <button type="button" id="cancelBtn" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cerrar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Guardar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <?php
 include_once '../includes/footer.php';
?>