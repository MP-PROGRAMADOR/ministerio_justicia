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

            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <!-- Bootstrap 5.3 JavaScript Bundle with Popper -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
                xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


            <div class="main-content" id="mainContent">


                <div class="header-section">
                    <div class="row align-items-center">

                        <div class="col-md-12 text-md-end mt-3 mt-md-0">
                            <div
                                class="d-flex justify-content-md-end align-items-center gap-2 flex-wrap justify-content-center">
                                <button class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#addFuncionarioModal">
                                    <i class="bi bi-plus-circle me-1"></i> Añadir Funcionarios
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









                <div class="container-fluid px-4">
                    <div id="mensajeFlashContenedor">
                        <?php
                        if (isset($_SESSION['error'])) {
                            echo "<div id='mensajeFlash' class='alert alert-danger alert-dismissible fade show'>" . htmlspecialchars($_SESSION['error']);
                            unset($_SESSION['error']);
                        }
                        if (isset($_SESSION['exito'])) {
                            echo "<div id='mensajeFlash' class='alert alert-success alert-dismissible fade show'>" . htmlspecialchars($_SESSION['exito']);
                            unset($_SESSION['exito']);
                        }
                        ?>
                    </div>

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

                            // Consulta para obtener datos de funcionarios con sección y categoría
                            $sql = "SELECT 
                                f.Id_funcionario,
                                f.CODIGO, f.Nombre, f.Apellidos, f.Estado_Laboral, f.Dip_Pasaporte, f.Sexo, f.Fecha_nacimiento,
                                f.Lugar_nacimiento, f.Nacionalidad, f.Telefono, f.Correo, f.Domicilio, f.Num_carnet_fun,
                                f.Fecha_nombramiento, f.Fecha_posesion, f.Id_seccion, s.nombre AS nombre_seccion,
                                f.Funcion, f.Id_categoria, c.nombre AS nombre_categoria,
                                f.Profesion,f.Maximo_nivel_estudios,f.Titulacion_academica,f.Universidad_centro_formacion,f.Fecha_graduacion,
                                f.Foto,f.Dip_pass_copia,f.Copia_doc_nomb,f.Copia_carnet_func,f.Copia_doc_tom_posesion,f.Copia_doc_academicos,
                                f.Usuario_creador, f.Fecha_registro FROM funcionarios f
                                LEFT JOIN secciones s ON f.Id_seccion = s.Id_seccion
                                LEFT JOIN categorias c ON f.Id_categoria = c.Id_categoria
                                ORDER BY f.Id_funcionario ASC
                            ";

                            $stmt = $pdo->query($sql);
                            $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            ?>

                            <table class="table table-hover align-middle mb-0" id="funcionariosTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Foto</th>
                                        <th>Código</th>
                                        <th>Nombres</th>
                                        <th>Apellidos</th>
                                        <th>DIP/Pasaporte</th>
                                        <th>Fecha Nacimiento</th>
                                        <th>Género</th>
                                        <th>Sección</th>
                                        <th>Categoría</th>
                                        <th>Estado Laboral</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="funcionariosTableBody">
                                    <?php foreach ($funcionarios as $f): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($f['Id_funcionario']) ?></td>

                                            <?php
                                            // Detectar protocolo y host
                                            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
                                            $host = $_SERVER['HTTP_HOST'];
                                            $fotoURL = !empty($f['Foto']) ? $protocol . $host . '/ministerio_justicia/api/' . ltrim($f['Foto'], '/') : '';
                                            ?>

                                            <td>
                                                <?php if ($fotoURL): ?>
                                                    <img src="<?= htmlspecialchars($fotoURL) ?>" alt="Foto" class="rounded"
                                                        style="width:40px; height:40px; object-fit:cover; border-radius:8px;">
                                                <?php else: ?>
                                                    <i class="bi bi-image-fill text-muted"></i>
                                                <?php endif; ?>
                                            </td>

                                            <td><?= htmlspecialchars($f['CODIGO']) ?></td>
                                            <td><?= htmlspecialchars($f['Nombre']) ?></td>
                                            <td><?= htmlspecialchars($f['Apellidos']) ?></td>
                                            <td><?= htmlspecialchars($f['Dip_Pasaporte']) ?></td>
                                            <td><?= htmlspecialchars($f['Fecha_nacimiento']) ?></td>
                                            <td><?= htmlspecialchars($f['Sexo']) ?></td>
                                            <td><?= htmlspecialchars($f['nombre_seccion']) ?></td>
                                            <td><?= htmlspecialchars($f['nombre_categoria']) ?></td>

                                            <td>
                                                <?php
                                                $estado_actual = $f['Estado_Laboral'] ?? 'Activo';
                                                $color_map = [
                                                    'Activo'        => 'bg-success',
                                                    'Permiso'       => 'bg-info',
                                                    'Vacaciones'    => 'bg-primary',
                                                    'Cesado'        => 'bg-danger',
                                                    'Baja Temporal' => 'bg-warning',
                                                    'Jubilado'      => 'bg-dark',
                                                ];

                                                $estados_activos = ['Activo', 'Permiso', 'Vacaciones'];
                                                $estados_inactivos = ['Cesado', 'Baja Temporal'];
                                                $es_interactivo = in_array($estado_actual, $estados_activos) || in_array($estado_actual, $estados_inactivos);
                                                $badge_color = $color_map[$estado_actual] ?? 'bg-light text-dark';

                                                if ($es_interactivo):
                                                    $is_checked = in_array($estado_actual, $estados_activos);
                                                ?>
                                                    <div class="form-check form-switch d-inline-block">
                                                        <input class="form-check-input funcionario-toggle"
                                                            type="checkbox"
                                                            role="switch"
                                                            id="toggle-<?= $f['Id_funcionario'] ?>"
                                                            data-funcionario-id="<?= $f['Id_funcionario'] ?>"
                                                            data-funcionario-nombres="<?= htmlspecialchars($f['Nombre'] . ' ' . $f['Apellidos']) ?>"
                                                            <?= $is_checked ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="toggle-<?= $f['Id_funcionario'] ?>">
                                                            <span class="badge <?= $badge_color ?>"><?= $estado_actual ?></span>
                                                        </label>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="badge <?= $badge_color ?>"><?= $estado_actual ?></span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <div class="d-flex gap-2">
                                                    <!-- Botón editar -->
                                                    <button
                                                        class="btn btn-sm btn-warning btn-editar-funcionario"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editarFuncionarioModal"

                                                        data-id="<?= $f['Id_funcionario'] ?>"
                                                        data-codigo="<?= htmlspecialchars($f['CODIGO']) ?>"

                                                        data-nombre="<?= htmlspecialchars($f['Nombre']) ?>"
                                                        data-apellidos="<?= htmlspecialchars($f['Apellidos']) ?>"
                                                        data-estado="<?= $f['Estado_Laboral'] ?>"
                                                        data-dip="<?= htmlspecialchars($f['Dip_Pasaporte']) ?>"
                                                        data-sexo="<?= $f['Sexo'] ?>"
                                                        data-fecha_nacimiento="<?= $f['Fecha_nacimiento'] ?>"
                                                        data-lugar_nacimiento="<?= htmlspecialchars($f['Lugar_nacimiento']) ?>"
                                                        data-nacionalidad="<?= htmlspecialchars($f['Nacionalidad']) ?>"
                                                        data-telefono="<?= htmlspecialchars($f['Telefono']) ?>"
                                                        data-correo="<?= htmlspecialchars($f['Correo']) ?>"
                                                        data-domicilio="<?= htmlspecialchars($f['Domicilio']) ?>"
                                                        data-carnet="<?= htmlspecialchars($f['Num_carnet_fun']) ?>"

                                                        data-fecha_nombramiento="<?= $f['Fecha_nombramiento'] ?>"
                                                        data-fecha_posesion="<?= $f['Fecha_posesion'] ?>"

                                                        data-id_seccion="<?= $f['Id_seccion'] ?>"
                                                        data-funcion="<?= htmlspecialchars($f['Funcion']) ?>"
                                                        data-id_categoria="<?= $f['Id_categoria'] ?>"

                                                        data-profesion="<?= htmlspecialchars($f['Profesion']) ?>"
                                                        data-nivel="<?= htmlspecialchars($f['Maximo_nivel_estudios']) ?>"
                                                        data-titulacion="<?= htmlspecialchars($f['Titulacion_academica']) ?>"
                                                        data-universidad="<?= htmlspecialchars($f['Universidad_centro_formacion']) ?>"
                                                        data-fecha_graduacion="<?= $f['Fecha_graduacion'] ?>"

                                                        data-foto="<?= htmlspecialchars($f['Foto']) ?>"
                                                        data-dip_copia="<?= htmlspecialchars($f['Dip_pass_copia']) ?>"
                                                        data-doc_nomb="<?= htmlspecialchars($f['Copia_doc_nomb']) ?>"
                                                        data-carnet_copia="<?= htmlspecialchars($f['Copia_carnet_func']) ?>"
                                                        data-doc_posesion="<?= htmlspecialchars($f['Copia_doc_tom_posesion']) ?>"
                                                        data-doc_academicos="<?= htmlspecialchars($f['Copia_doc_academicos']) ?>"

                                                        title="Editar funcionario">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>


                                                    <!-- Botón ver detalles -->
                                                    <button type="button"
                                                        class="btn btn-sm btn-info shadow-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#employeeDetailModal"
                                                        data-funcionario-id="<?= $f['Id_funcionario'] ?>"
                                                        title="Ver detalles">

                                                        <i class="bi bi-person-fill"></i>
                                                    </button>


                                                    <!-- Botón descargar PDF -->
                                                    <button class="btn btn-sm btn-success"
                                                        onclick="downloadFile(<?= (int)$f['Id_funcionario'] ?>)" title="Descargar PDF">
                                                        <i class="bi bi-filetype-pdf"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
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











    <!-- Modal de Registar Funcionario -->
    <div class="modal fade" id="addFuncionarioModal" tabindex="-1" aria-labelledby="addFuncionarioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addFuncionarioModalLabel">
                        <i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Funcionario
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <form method="POST" action="../api/guardar_funcionario.php" enctype="multipart/form-data" id="formFuncionario">

                        <ul class="nav nav-pills nav-fill mb-4 mt-3" id="funcionarioTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="personales-tab" data-bs-toggle="pill" data-bs-target="#personales" type="button" role="tab" aria-controls="personales" aria-selected="true">
                                    <i class="bi bi-info-circle me-1"></i> Datos Personales
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="laboral-contacto-tab" data-bs-toggle="pill" data-bs-target="#laboral-contacto" type="button" role="tab" aria-controls="laboral-contacto" aria-selected="false">
                                    <i class="bi bi-briefcase me-1"></i> Contacto y Laboral
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="academicos-docs-tab" data-bs-toggle="pill" data-bs-target="#academicos-docs" type="button" role="tab" aria-controls="academicos-docs" aria-selected="false">
                                    <i class="bi bi-mortarboard me-1"></i> Académicos y Documentos
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="funcionarioTabContent">

                            <div class="tab-pane fade show active" id="personales" role="tabpanel" aria-labelledby="personales-tab">
                                <h6 class="fw-semibold text-primary border-bottom pb-2 mb-3">Datos Personales</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-9">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Nombres</label>
                                                <input type="text" class="form-control" name="Nombre" placeholder="Ej: Ana Trini" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Apellidos</label>
                                                <input type="text" class="form-control" name="Apellidos" placeholder="Ej: Gómez Pérez" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">D.I.P / Pasaporte</label>
                                                <input type="text" class="form-control" name="Dip_Pasaporte" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Número de Carnet de Funcionario</label>
                                                <input type="text" class="form-control" name="Num_carnet_fun" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Sexo</label>
                                                <select class="form-select" name="Sexo" required>
                                                    <option value="" disabled selected>Selecciona género</option>
                                                    <option value="Masculino">Masculino</option>
                                                    <option value="Femenino">Femenino</option>
                                                    <option value="Otro">Otro</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Fecha de Nacimiento</label>
                                                <input type="date" class="form-control" name="Fecha_nacimiento" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Nacionalidad</label>
                                                <input type="text" class="form-control" name="Nacionalidad" placeholder="Ej: Ecuatoguineana" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Lugar de Nacimiento</label>
                                                <input type="text" class="form-control" name="Lugar_nacimiento" placeholder="Ej: Malabo" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="card p-3 text-center h-100">
                                            <label class="form-label d-block text-muted">Fotografía (Requerida)</label>
                                            <div class="mb-3 mx-auto" style="width: 150px; height: 150px; border: 1px dashed #ccc; border-radius: 8px;">
                                                <img id="previewFoto" src="#" alt="Vista previa de foto" style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                                                <div id="noFoto" class="d-flex align-items-center justify-content-center h-100 text-muted">
                                                    <i class="bi bi-person-circle" style="font-size: 3rem;"></i>
                                                </div>
                                            </div>
                                            <input type="file" class="form-control form-control-sm" id="Foto" name="Foto" accept="image/*" required>
                                            <small class="text-muted mt-2">Max. 2MB</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="laboral-contacto" role="tabpanel" aria-labelledby="laboral-contacto-tab">
                                <div class="row">
                                    <div class="col-md-6 border-end">
                                        <h6 class="fw-semibold text-primary border-bottom pb-2 mb-3"><i class="bi bi-phone me-1"></i> Contacto</h6>
                                        <div class="row g-3 mb-3">
                                            <div class="col-12">
                                                <label class="form-label">Teléfono</label>
                                                <input type="text" class="form-control" name="Telefono" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Correo Electrónico</label>
                                                <input type="email" class="form-control" name="Correo" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Domicilio</label>
                                                <textarea class="form-control" name="Domicilio" rows="2" required></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="fw-semibold text-primary border-bottom pb-2 mb-3"><i class="bi bi-person-workspace me-1"></i> Datos Laborales</h6>
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Fecha de Nombramiento</label>
                                                <input type="date" class="form-control" name="Fecha_nombramiento" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Fecha de Posesión</label>
                                                <input type="date" class="form-control" name="Fecha_posesion" required>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label">Función / Cargo</label>
                                                <select class="form-select" name="Funcion" required>
                                                    <option value="" disabled selected>Selecciona El Cargo</option>
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
                                            <div class="col-md-12">
                                                <label class="form-label">Sección</label>
                                                <select class="form-select" name="Id_seccion" required>
                                                    <option value="" disabled selected>Selecciona sección</option>
                                                    <?php
                                                    // CONEXIÓN Y CONSULTA PHP para Secciones
                                                    require_once "../includes/conexion.php";
                                                    $stmt = $pdo->query("SELECT s.Id_seccion,s.nombre AS nombre_seccion,s.Id_direccion,d.nombre AS nombre_direccion FROM secciones s LEFT JOIN direcciones d ON s.Id_direccion = d.Id_direccion ORDER BY s.nombre ASC");
                                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        echo '<option value="' . $row['Id_seccion'] . '">' . htmlspecialchars($row['nombre_seccion']) . ' ' . htmlspecialchars($row['nombre_direccion']) . '</option>';
                                                    }
                                                    ?>

                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Categoría</label>
                                                <select class="form-select" name="Id_categoria" required>
                                                    <option value="" disabled selected>Selecciona categoría</option>
                                                    <?php
                                                    // CONEXIÓN Y CONSULTA PHP para Categorías
                                                    $stmt = $pdo->query("SELECT Id_categoria, nombre, descripcion FROM categorias ORDER BY nombre ASC");
                                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        echo '<option value="' . $row['Id_categoria'] . '">' . htmlspecialchars($row['nombre']) . ' ' . htmlspecialchars($row['descripcion']) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Estado Laboral</label>
                                                <select class="form-select" name="Estado_Laboral" required>
                                                    <option value="Activo" selected>Activo</option>
                                                    <option value="Baja Temporal">Baja Temporal</option>
                                                    <option value="Jubilado">Jubilado</option>
                                                    <option value="Cesado">Cesado</option>
                                                    <option value="Permiso">Permiso</option>
                                                    <option value="Vacaciones">Vacaciones</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="academicos-docs" role="tabpanel" aria-labelledby="academicos-docs-tab">
                                <div class="row">
                                    <div class="col-md-6 border-end">
                                        <h6 class="fw-semibold text-primary border-bottom pb-2 mb-3"><i class="bi bi-book me-1"></i> Datos Académicos</h6>
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Profesión</label>
                                                <input type="text" class="form-control" name="Profesion" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Máximo Nivel de Estudios</label>
                                                <input type="text" class="form-control" name="Maximo_nivel_estudios" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Titulación Académica</label>
                                                <input type="text" class="form-control" name="Titulacion_academica" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Universidad / Centro de Formación</label>
                                                <input type="text" class="form-control" name="Universidad_centro_formacion" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Fecha de Graduación</label>
                                                <input type="date" class="form-control" name="Fecha_graduacion" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="fw-semibold text-primary border-bottom pb-2 mb-3"><i class="bi bi-file-earmark-arrow-up me-1"></i> Documentos (PDF/JPG/PNG)</h6>
                                        <div class="row g-3 mb-3">
                                            <div class="col-12">
                                                <label class="form-label">Copia DIP/Pasaporte</label>
                                                <input type="file" class="form-control" name="Dip_pass_copia" accept=".pdf,.jpg,.png" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Copia Documento Nombramiento</label>
                                                <input type="file" class="form-control" name="Copia_doc_nomb" accept=".pdf,.jpg,.png" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Copia Carnet Funcionario</label>
                                                <input type="file" class="form-control" name="Copia_carnet_func" accept=".pdf,.jpg,.png" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Copia Documento Toma Posesión</label>
                                                <input type="file" class="form-control" name="Copia_doc_tom_posesion" accept=".pdf,.jpg,.png" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Copia Documentos Académicos</label>
                                                <input type="file" class="form-control" name="Copia_doc_academicos" accept=".pdf,.jpg,.png" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4 mb-3">

                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-2"></i> Registrar Funcionario
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('Foto').addEventListener('change', function(e) {
            const preview = document.getElementById('previewFoto');
            const noFoto = document.getElementById('noFoto');
            const file = e.target.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
                noFoto.style.display = 'none';
            } else {
                preview.style.display = 'none';
                noFoto.style.display = 'flex';
            }
        });
    </script>



    <!-- Modal de Detalles del Funcionario -->
    <div class="modal fade" id="employeeDetailModal" tabindex="-1" aria-labelledby="employeeDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow-lg position-relative border-start border-primary border-5">

                <!-- Spinner de carga -->
                <div id="loadingSpinner" class="spinner-overlay d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>

                <div class="spinner-overlay-content">

                    <!-- Header -->
                    <div class="modal-header px-5 pt-4 pb-3 border-bottom">
                        <h3 class="modal-title fs-4 fw-bold text-dark d-flex align-items-center"
                            id="employeeDetailModalLabel">
                            <i class="bi bi-person-badge-fill text-primary me-3 fs-3"></i>
                            Perfil Detallado del Funcionario
                        </h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body p-5 modal-body-scrollable">
                        <div class="row g-4 modal-body-content" id="modalContentData">
                            <div class="col-12 text-center text-muted py-5">
                                <i class="bi bi-person-circle fs-1 text-secondary mb-3"></i>
                                <p class="fs-5">Esperando selección de funcionario...</p>
                            </div>
                        </div>
                    </div>



                    <!-- Footer con botones de descarga -->
                    <div class="modal-footer border-top justify-content-between flex-column flex-md-row">
                        <div class="text-muted small mb-4 mb-md-0">
                            <i class="bi bi-info-circle-fill me-2 text-primary"></i> Datos sensibles. Última verificación:
                            <span id="lastVerificationDate">--</span>
                        </div>

                        <!-- Contenedor de botones de documentos -->
                        <div class="d-flex flex-wrap gap-2" id="documentButtonsContainer">
                            <!-- Botones se generan dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>








    <!-- Script para cargar datos del funcionario para mostrarlos en el modal de detalles del funcionario -->
    <script>
        // Función para formatear fechas de forma segura
        function formatDate(dateString) {
            if (!dateString || dateString === '0000-00-00') return 'N/A';
            try {
                const [y, m, d] = dateString.split('-');
                return new Date(y, m - 1, d).toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            } catch (e) {
                return 'Fecha inválida';
            }
        }

        // Función principal de carga
        async function loadEmployeeData(funcionarioId) {
            const modalContentData = document.getElementById('modalContentData');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const lastVerificationDate = document.getElementById('lastVerificationDate');

            loadingSpinner.classList.replace('d-none', 'd-flex');
            modalContentData.innerHTML = '';

            try {
                const response = await fetch(`../api/get_funcionario_data.php?id=${funcionarioId}`);
                if (!response.ok) throw new Error('Error en el servidor');
                const data = await response.json();

                if (data.error) {
                    modalContentData.innerHTML = `<div class="col-12 text-center alert alert-danger">${data.error}</div>`;
                    return;
                }

                const {
                    funcionario,
                    formacion_academica,
                    capacitaciones,
                    permisos,
                    asignaciones,
                    historial_cargos
                } = data;

                // Calcular edad
                const calcularEdad = (fecha) => {
                    if (!fecha) return 'N/A';
                    const hoy = new Date();
                    const cumple = new Date(fecha);
                    let edad = hoy.getFullYear() - cumple.getFullYear();
                    if (hoy.getMonth() < cumple.getMonth() || (hoy.getMonth() === cumple.getMonth() && hoy.getDate() < cumple.getDate())) edad--;
                    return edad;
                };

                const coloresEstado = {
                    'Activo': 'bg-success',
                    'Permiso': 'bg-info',
                    'Vacaciones': 'bg-primary',
                    'Cesado': 'bg-danger',
                    'Baja Temporal': 'bg-warning text-dark', // Añadimos text-dark para mejor legibilidad en amarillo
                    'Jubilado': 'bg-dark'
                };

                // 2. Obtener el color (usamos 'bg-secondary' como color por defecto si no coincide ninguno)
                const colorBadge = coloresEstado[funcionario.Estado_Laboral] || 'bg-secondary';

                modalContentData.innerHTML = `
                <div class="col-12 mb-4">
                    <div class="d-flex align-items-center bg-light p-3 rounded-4 shadow-sm">
                        <img src="${funcionario.Foto || 'https://placehold.co/150x150?text=Perfil'}" 
                            class="rounded-circle border border-3 border-primary me-4" style="width:100px; height:100px; object-fit:cover;">
                        <div>
                            <h2 class="fw-bold mb-0">${funcionario.Nombre} ${funcionario.Apellidos}</h2>
                            
                          <span class="badge ${colorBadge} text-uppercase">${funcionario.Estado_Laboral}</span>
                            <p class="text-muted mb-0"><i class="bi bi-tag-fill me-2"></i>Código: ${funcionario.CODIGO}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm p-4">
                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3"><i class="bi bi-person-fill me-2"></i>1. Información Personal</h6>
                        <div class="row small g-2">
                            <div class="col-6 text-muted">Nombre Completo:</div><div class="col-6 fw-bold">${funcionario.Nombre} ${funcionario.Apellidos}</div>
                            <div class="col-6 text-muted">DIP/Pasaporte:</div><div class="col-6 fw-bold">${funcionario.Dip_Pasaporte}</div>
                            <div class="col-6 text-muted">Edad:</div><div class="col-6 fw-bold">${calcularEdad(funcionario.Fecha_nacimiento)} años</div>
                            <div class="col-6 text-muted">Fecha de Nacimiento:</div><div class="col-6 fw-bold">${formatDate(funcionario.Fecha_nacimiento)}</div>
                            <div class="col-6 text-muted">Lugar de Nacimiento:</div><div class="col-6 fw-bold">${funcionario.Lugar_nacimiento || 'N/A'}</div>
                            <div class="col-6 text-muted">Natural de:</div><div class="col-6 fw-bold">${funcionario.Nacionalidad} (Prov: ${funcionario.Nacionalidad})</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm p-4">
                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3"><i class="bi bi-telephone-fill me-2"></i>2. Información de Contacto</h6>
                        <div class="row small g-2">
                            <div class="col-6 text-muted">Teléfono:</div><div class="col-6 fw-bold">${funcionario.Telefono || 'N/A'}</div>
                            <div class="col-6 text-muted">Correo:</div><div class="col-6 fw-bold">${funcionario.Correo || 'N/A'}</div>
                            <div class="col-6 text-muted">Ubicación / Barrio:</div><div class="col-6 fw-bold">${funcionario.Domicilio || 'N/A'}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card border-0 shadow-sm p-4 mt-2">
                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3"><i class="bi bi-briefcase-fill me-2"></i>3. Información de Funcionario</h6>
                        <div class="row small">
                            <div class="col-md-6 border-end">
                                <p class="mb-1"><strong>Función (Cargo):</strong> ${funcionario.Funcion || 'N/A'}</p>
                                <p class="mb-1"><strong>Sección:</strong> ${funcionario.Nombre_Seccion || 'N/A'}</p>
                                <p class="mb-1"><strong>Dirección:</strong> ${funcionario.Nombre_Direccion || 'N/A'}</p>
                            </div>
                            <div class="col-md-6 ps-md-4">
                                <p class="mb-1"><strong>Ubicación Actual:</strong> ${funcionario.Provincia_Direccion || 'N/A'}, ${funcionario.Distrito_Direccion || 'N/A'}</p>
                                <p class="mb-1"><strong>Fecha Nombramiento:</strong> ${formatDate(funcionario.Fecha_nombramiento)}</p>
                                <p class="mb-1"><strong>Fecha Toma Posesión:</strong> ${formatDate(funcionario.Fecha_posesion)}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm p-4 mt-2">
                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3"><i class="bi bi-mortarboard-fill me-2"></i>4. Información de Estudio</h6>
                        <p class="small mb-1"><strong>Profesión:</strong> ${funcionario.Profesion}</p>
                        <p class="small mb-1"><strong>Nivel Máximo:</strong> ${funcionario.Maximo_nivel_estudios}</p>
                        <hr class="my-2">
                        <label class="small fw-bold text-muted">Títulos Académicos:</label>
                        <ul class="list-unstyled small">
                            ${formacion_academica.map(f => `<li><i class="bi bi-check-circle text-success me-2"></i>${f.Titulo_Obtenido} (${f.Nivel_Educativo})</li>`).join('') || '<li>No registrado</li>'}
                        </ul>
                        <label class="small fw-bold text-muted">Otras Formaciones:</label>
                        <ul class="list-unstyled small">
                            ${capacitaciones.map(c => `<li><i class="bi bi-patch-check text-info me-2"></i>${c.Nombre_Curso}</li>`).join('') || '<li>No registrado</li>'}
                        </ul>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm p-4 mt-2 bg-light">
                        <h6 class="text-danger fw-bold border-bottom pb-2 mb-3">
                            <i class="bi bi-clock-history me-2"></i>5. Historial y Acciones
                        </h6>
                        
                        ${(funcionario.Copia_doc_nomb && funcionario.Estado_Laboral !== 'Activo') ? `
                            <div class="mb-3">
                                <label class="small fw-bold text-muted d-block mb-1">Documento de Estado Actual:</label>
                                <a href="${funcionario.Copia_doc_nomb}" target="_blank" class="btn btn-sm btn-outline-danger w-100 shadow-sm">
                                    <i class="bi bi-file-earmark-pdf-fill me-2"></i>Ver Justificante de ${funcionario.Estado_Laboral}
                                </a>
                            </div>
                            <hr>` : '' 
                        }

                        <div class="timeline-small">
                            <p class="small fw-bold mb-1 text-muted">Cargos Anteriores:</p>
                            ${historial_cargos && historial_cargos.length > 0 ? historial_cargos.map(h => `
                                <div class="small mb-1 border-start border-danger ps-2">
                                    ${h.Nombre_Cargo} (${formatDate(h.Fecha_nombramiento)})
                                </div>`).join('') : '<p class="small text-muted">Sin historial de cargos</p>'}
                            
                            <p class="small fw-bold mb-1 mt-3 text-muted">Permisos y Eventos:</p>
                            ${permisos && permisos.length > 0 ? permisos.map(p => `
                                <div class="small mb-1 border-start border-warning ps-2 text-truncate">
                                    ${p.Tipo_Permiso}: ${p.Estado_Permiso} (${formatDate(p.Fecha_Inicio_Permiso)})
                                </div>`).join('') : '<p class="small text-muted">Sin eventos registrados</p>'}
                        </div>
                    </div>
                </div>`;

                lastVerificationDate.textContent = "Actualizado: " + new Date().toLocaleString();

            } catch (error) {
                modalContentData.innerHTML = `<div class="alert alert-danger">Error al cargar datos: ${error.message}</div>`;
            } finally {
                loadingSpinner.classList.replace('d-flex', 'd-none');
            }
        }

        /* --------------------------
         Evento para abrir el modal
        --------------------------- */
        document.getElementById('employeeDetailModal')
            .addEventListener('show.bs.modal', e => {
                // Recuperar el ID del botón que activó el modal
                const id = e.relatedTarget.getAttribute('data-funcionario-id');
                if (id) {
                    loadEmployeeData(id);
                } else {
                    // Manejar caso sin ID
                    document.getElementById('modalContentData').innerHTML = `
                <div class="col-12 text-center alert alert-warning border-0">
                    No se proporcionó un ID de funcionario.
                </div>`;
                }
            });
    </script>







    <!-- Modal de Editar -->
    <div class="modal fade" id="editarFuncionarioModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow">

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-2"></i> Editar Funcionario: <span id="funcionario-nombre-display"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form method="POST" action="../api/actualizar_funcionario.php"
                        id="formEditarFuncionario" enctype="multipart/form-data">

                        <input type="hidden" name="Id_funcionario" id="edit_Id_funcionario">

                        <ul class="nav nav-pills nav-fill mb-4 mt-3" id="editFuncionarioTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="edit-personales-tab" data-bs-toggle="pill" data-bs-target="#edit-personales" type="button" role="tab" aria-controls="edit-personales" aria-selected="true">
                                    <i class="bi bi-person-fill me-1"></i> Personales y Contacto
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="edit-laboral-tab" data-bs-toggle="pill" data-bs-target="#edit-laboral" type="button" role="tab" aria-controls="edit-laboral" aria-selected="false">
                                    <i class="bi bi-briefcase-fill me-1"></i> Laboral
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="edit-academico-tab" data-bs-toggle="pill" data-bs-target="#edit-academico" type="button" role="tab" aria-controls="edit-academico" aria-selected="false">
                                    <i class="bi bi-mortarboard-fill me-1"></i> Académico
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link text-danger" id="edit-documentos-tab" data-bs-toggle="pill" data-bs-target="#edit-documentos" type="button" role="tab" aria-controls="edit-documentos" aria-selected="false">
                                    <i class="bi bi-upload me-1"></i> Documentos
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="editFuncionarioTabContent">

                            <div class="tab-pane fade show active" id="edit-personales" role="tabpanel" aria-labelledby="edit-personales-tab">
                                <div class="row g-3">
                                    <div class="col-md-6 border-end">
                                        <h6 class="fw-bold text-warning border-bottom pb-2 mb-3"><i class="bi bi-info-circle-fill me-1"></i> Datos Básicos</h6>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Código</label>
                                                <input type="text" id="edit_codigo" class="form-control" disabled>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label fw-semibold">DIP / Pasaporte</label>
                                                <input type="text" name="Dip_Pasaporte" id="edit_dip" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Nombre</label>
                                                <input type="text" name="Nombre" id="edit_nombre" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Apellidos</label>
                                                <input type="text" name="Apellidos" id="edit_apellidos" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Fecha Nacimiento</label>
                                                <input type="date" name="Fecha_nacimiento" id="edit_fecha_nacimiento" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Sexo</label>
                                                <select name="Sexo" id="edit_sexo" class="form-select">
                                                    <option value="Masculino">Masculino</option>
                                                    <option value="Femenino">Femenino</option>
                                                    <option value="Otro">Otro</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Lugar de Nacimiento</label>
                                                <input type="text" name="Lugar_nacimiento" id="edit_lugar_nacimiento" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Nacionalidad</label>
                                                <input type="text" name="Nacionalidad" id="edit_nacionalidad" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-warning border-bottom pb-2 mb-3"><i class="bi bi-phone-fill me-1"></i> Contacto y Domicilio</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Teléfono</label>
                                                <input type="text" name="Telefono" id="edit_telefono" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Correo</label>
                                                <input type="email" name="Correo" id="edit_correo" class="form-control" required>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-semibold">Domicilio</label>
                                                <textarea name="Domicilio" id="edit_domicilio" class="form-control" rows="4"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="edit-laboral" role="tabpanel" aria-labelledby="edit-laboral-tab">
                                <h6 class="fw-bold text-warning border-bottom pb-2 mb-3"><i class="bi bi-briefcase-fill me-1"></i> Datos Laborales y Administrativos</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Estado Laboral</label>
                                        <select name="Estado_Laboral" id="edit_estado" class="form-select">
                                            <option value="Activo">Activo</option>
                                            <option value="Baja Temporal">Baja Temporal</option>
                                            <option value="Permiso">Permiso</option>
                                            <option value="Vacaciones">Vacaciones</option>
                                            <option value="Jubilado">Jubilado</option>
                                            <option value="Cesado">Cesado</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nº Carnet Funcionario</label>
                                        <input type="text" name="Num_carnet_fun" id="edit_carnet" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Función / Cargo</label>
                                        <input type="text" name="Funcion" id="edit_funcion" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Categoría</label>
                                        <select name="Id_categoria" id="edit_categoria" class="form-select" required>
                                            <?php
                                            // $stmt = $pdo->query("SELECT Id_categoria, nombre FROM categorias ORDER BY nombre");
                                            // while ($c = $stmt->fetch()) {
                                            //     echo "<option value='{$c['Id_categoria']}'>{$c['nombre']}</option>";
                                            // }
                                            echo "<option value='1'>Ej. A1</option>";
                                            echo "<option value='2'>Ej. B2</option>";
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Sección</label>
                                        <select name="Id_seccion" id="edit_seccion" class="form-select" required>
                                            <?php
                                            // $stmt = $pdo->query("SELECT Id_seccion, nombre FROM secciones ORDER BY nombre");
                                            // while ($s = $stmt->fetch()) {
                                            //     echo "<option value='{$s['Id_seccion']}'>{$s['nombre']}</option>";
                                            // }
                                            echo "<option value='1'>Ej. RRHH</option>";
                                            echo "<option value='2'>Ej. Contabilidad</option>";
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Fecha Nombramiento</label>
                                        <input type="date" name="Fecha_nombramiento" id="edit_fecha_nombramiento" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Fecha Toma de Posesión</label>
                                        <input type="date" name="Fecha_posesion" id="edit_fecha_posesion" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="edit-academico" role="tabpanel" aria-labelledby="edit-academico-tab">
                                <h6 class="fw-bold text-warning border-bottom pb-2 mb-3"><i class="bi bi-book-fill me-1"></i> Formación Académica</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Profesión</label>
                                        <input type="text" name="Profesion" id="edit_profesion" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Máximo Nivel de Estudios</label>
                                        <input type="text" name="Maximo_nivel_estudios" id="edit_nivel" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Titulación Académica</label>
                                        <input type="text" name="Titulacion_academica" id="edit_titulacion" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Universidad / Centro</label>
                                        <input type="text" name="Universidad_centro_formacion" id="edit_universidad" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Fecha Graduación</label>
                                        <input type="date" name="Fecha_graduacion" id="edit_fecha_graduacion" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="edit-documentos" role="tabpanel" aria-labelledby="edit-documentos-tab">
                                <h6 class="fw-bold text-danger border-bottom pb-2 mb-3"><i class="bi bi-file-earmark-check-fill me-1"></i> Actualización de Documentos (Opcional)</h6>

                                <div class="alert alert-warning py-2 mb-4">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <strong>Advertencia:</strong> Solo adjunte un archivo si desea *reemplazar* el documento actual. Si se deja vacío, el archivo existente se mantendrá.
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">Actualizar Foto (Opcional)</label>
                                        <input type="file" name="Foto" class="form-control" accept="image/*">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Copia DIP / Pasaporte</label>
                                        <input type="file" name="Dip_pass_copia" class="form-control" accept=".pdf,.jpg,.png">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Documento de Nombramiento</label>
                                        <input type="file" name="Copia_doc_nomb" class="form-control" accept=".pdf,.jpg,.png">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Copia Carnet Funcionario</label>
                                        <input type="file" name="Copia_carnet_func" class="form-control" accept=".pdf,.jpg,.png">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Documento de Toma de Posesión</label>
                                        <input type="file" name="Copia_doc_tom_posesion" class="form-control" accept=".pdf,.jpg,.png">
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">Documentos Académicos</label>
                                        <input type="file" name="Copia_doc_academicos" class="form-control" accept=".pdf,.jpg,.png">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="mt-4 d-flex justify-content-end gap-2 border-top pt-3 mb-3">
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
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-editar-funcionario');
            if (!btn) return;

            // 1. Población de campos
            document.getElementById('edit_Id_funcionario').value = btn.dataset.id;
            document.getElementById('edit_codigo').value = btn.dataset.codigo;
            document.getElementById('edit_nombre').value = btn.dataset.nombre;
            document.getElementById('edit_apellidos').value = btn.dataset.apellidos;
            document.getElementById('edit_estado').value = btn.dataset.estado;
            document.getElementById('edit_dip').value = btn.dataset.dip;
            document.getElementById('edit_sexo').value = btn.dataset.sexo;
            document.getElementById('edit_fecha_nacimiento').value = btn.dataset.fecha_nacimiento;
            document.getElementById('edit_lugar_nacimiento').value = btn.dataset.lugar_nacimiento;
            document.getElementById('edit_nacionalidad').value = btn.dataset.nacionalidad;
            document.getElementById('edit_telefono').value = btn.dataset.telefono;
            document.getElementById('edit_correo').value = btn.dataset.correo;
            document.getElementById('edit_domicilio').value = btn.dataset.domicilio;
            document.getElementById('edit_carnet').value = btn.dataset.carnet;

            document.getElementById('edit_fecha_nombramiento').value = btn.dataset.fecha_nombramiento;
            document.getElementById('edit_fecha_posesion').value = btn.dataset.fecha_posesion;

            // Asegurarse de que los select se poblen si tienen valores dinámicos
            document.getElementById('edit_seccion').value = btn.dataset.id_seccion;
            document.getElementById('edit_funcion').value = btn.dataset.funcion; // Si Funcion es un campo de texto en la base de datos
            document.getElementById('edit_categoria').value = btn.dataset.id_categoria;

            document.getElementById('edit_profesion').value = btn.dataset.profesion;
            document.getElementById('edit_nivel').value = btn.dataset.nivel;
            document.getElementById('edit_titulacion').value = btn.dataset.titulacion;
            document.getElementById('edit_universidad').value = btn.dataset.universidad;
            document.getElementById('edit_fecha_graduacion').value = btn.dataset.fecha_graduacion;

            // 2. Actualizar el título del modal con el nombre del funcionario
            const fullName = `${btn.dataset.nombre} ${btn.dataset.apellidos}`;
            document.getElementById('funcionario-nombre-display').textContent = fullName;
        });
    </script>






    <!-- Modal de Cambiar el estado del funcionario sea una baja temporal o un cese definitivo -->
    <div class="modal fade" id="modalEstadoFuncionario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div id="modalHeaderEstado" class="modal-header text-white">
                    <h5 class="modal-title" id="modalTitleEstado"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEstadoFuncionario" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <p class="text-muted">Funcionario: <br><strong id="nombreFuncionarioEstado" class="text-dark"></strong></p>
                        <input type="hidden" name="funcionario_id" id="inputEstadoId">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo de Acción</label>
                            <select class="form-select" name="tipo_baja" id="tipoAccionSelect" required>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Documento Justificante</label>
                            <input type="file" class="form-control" name="documento_baja" id="documentoEstado" accept=".pdf,.jpg,.png" required>
                            <div class="form-text">Suba el acta, resolución o justificante de este cambio de estado.</div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="submit" id="btnSubmitEstado" class="btn px-4"><i class="bi bi-patch-check text-info me-2"> </i>Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- Modal de Cambiar el estado del funcionario sea una baja temporal o un cese definitivo -->
    <script>
        // Función para inyectar mensajes en el div y cerrarlos automáticamente
        function mostrarMensajeFlash(mensaje, tipo = 'danger') {
            const contenedor = document.getElementById('mensajeFlashContenedor');
            if (!contenedor) return;

            const idUnico = 'alert-' + Date.now();
            contenedor.innerHTML = `
            <div id="${idUnico}" class="alert alert-${tipo} alert-dismissible fade show shadow-sm" role="alert">
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;

            // Scroll al mensaje
            contenedor.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

            // Autocierre después de 5 segundos
            setTimeout(() => {
                const alertElement = document.getElementById(idUnico);
                if (alertElement) {
                    const bsAlert = new bootstrap.Alert(alertElement);
                    bsAlert.close();
                }
            }, 5000);
        }

        // Al cargar la página, si hay un mensaje (puesto por PHP), también ponerle temporizador
        document.addEventListener('DOMContentLoaded', function() {
            const alertaExistente = document.querySelector('#mensajeFlashContenedor .alert');
            if (alertaExistente) {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alertaExistente);
                    bsAlert.close();
                }, 3000);
            }
        });

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('funcionario-toggle')) {
                const checkbox = e.target;
                const id = checkbox.getAttribute('data-funcionario-id');
                const nombres = checkbox.getAttribute('data-funcionario-nombres');
                const badge = checkbox.closest('tr').querySelector('.badge');
                const estadoActual = badge ? badge.textContent.trim() : "";

                const modalEl = document.getElementById('modalEstadoFuncionario');
                const title = document.getElementById('modalTitleEstado');
                const header = document.getElementById('modalHeaderEstado');
                const btnSubmit = document.getElementById('btnSubmitEstado');
                const select = document.getElementById('tipoAccionSelect');

                // Mantener el switch en su estado original hasta que el modal confirme
                const estadoDeseado = checkbox.checked;
                checkbox.checked = !estadoDeseado;

                // VALIDACIÓN: ALTA
                if (estadoDeseado) {
                    if (estadoActual === 'Cesado' || estadoActual === 'Jubilado') {
                        mostrarMensajeFlash(`Acción denegada: No se puede reactivar a <b>${nombres}</b> porque su estado es definitivo (${estadoActual}).`, 'danger');
                        return;
                    }

                    title.innerHTML = '<i class="bi bi-person-check me-2"></i>Reactivar Funcionario';
                    header.className = 'modal-header bg-success text-white';
                    btnSubmit.className = 'btn btn-success px-4';
                    select.innerHTML = '<option value="activo" selected>Alta / Reincorporación</option>';
                }
                // VALIDACIÓN: BAJA
                else {
                    title.innerHTML = '<i class="bi bi-person-dash me-2"></i>Procesar Baja';
                    header.className = 'modal-header bg-danger text-white';
                    btnSubmit.className = 'btn btn-danger px-4';
                    select.innerHTML = `
                    <option value="" selected disabled>Seleccione motivo...</option>
                    <option value="temporal">Baja Temporal (Permiso/Enfermedad)</option>
                    <option value="definitivo">Cese Definitivo (Jubilación/Renuncia)</option>`;
                }

                document.getElementById('inputEstadoId').value = id;
                document.getElementById('nombreFuncionarioEstado').textContent = nombres;
                new bootstrap.Modal(modalEl).show();
            }
        });

        document.getElementById('formEstadoFuncionario').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = document.getElementById('btnSubmitEstado');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const response = await fetch('../api/procesar_baja.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // PHP ya guardó el mensaje en $_SESSION['exito']
                    location.reload();
                } else {
                    mostrarMensajeFlash(result.message, 'danger');
                    bootstrap.Modal.getInstance(document.getElementById('modalEstadoFuncionario')).hide();
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Confirmar';
                }
            } catch (error) {
                mostrarMensajeFlash('Error crítico en la red.', 'danger');
                submitBtn.disabled = false;
            }
        });
    </script>








    <?php
    include_once '../includes/footer.php';
    ?>