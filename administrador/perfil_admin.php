<?php
include_once '../includes/header.php';
?>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>




<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="container-fluid p-0">
        <div class="row g-0">

            <?php
            include_once '../includes/silebar_admin.php';
            ?>


            <?php

            // session_start();


            if (!isset($_SESSION['ID_Usuario'])) {
                header('Location: login.php');
                exit();
            }

            $usuario_id = $_SESSION['ID_Usuario'];
            $datos_usuario = null;
            $user_initials = 'NA';
            $cargo_usuario = 'No Asignado';

            // Variables para el HTML (inicializadas para evitar errores)
            $nombre_a_mostrar = 'Usuario Desconocido';
            $correo_a_mostrar = 'N/A';
            $rol_a_mostrar = 'Invitado';
            $fecha_creacion = null;
            $ultimo_acceso = null;



            $pdo = null; // Usaremos $pdo para la conexión

            try {
                $pdo = new PDO($dsn, $user, $pass, $options);
            } catch (\PDOException $e) {
                // Error fatal si la conexión falla
                die("Error Fatal de Conexión: " . $e->getMessage());
            }


            try {
                // 3.1. Consultar datos principales del usuario
                $sql_user = "
                    SELECT 
                        u.Nombre_Usuario, 
                        u.Rol_Usuario, 
                        u.Email_Contacto, 
                        u.Fecha_Creacion, 
                        u.Ultimo_Acceso,
                        f.Nombres,
                        f.Apellidos
                    FROM tbl_usuarios u
                    LEFT JOIN tbl_funcionarios f ON u.Email_Contacto = f.Email_Oficial
                    WHERE u.ID_Usuario = :usuario_id
                ";

                $stmt_user = $pdo->prepare($sql_user);
                $stmt_user->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmt_user->execute();
                $datos_usuario = $stmt_user->fetch();

                if ($datos_usuario) {
                    // Asignación de variables de visualización
                    $nombre_completo = $datos_usuario['Nombres'] && $datos_usuario['Apellidos']
                        ? $datos_usuario['Nombres'] . ' ' . $datos_usuario['Apellidos']
                        : $datos_usuario['Nombre_Usuario'];

                    $nombre_a_mostrar = $nombre_completo;
                    $correo_a_mostrar = $datos_usuario['Email_Contacto'];
                    $rol_a_mostrar    = $datos_usuario['Rol_Usuario'];
                    $fecha_creacion   = $datos_usuario['Fecha_Creacion'];
                    $ultimo_acceso    = $datos_usuario['Ultimo_Acceso'];

                    // 3.2. Obtener iniciales
                    function get_initials($name)
                    {
                        $parts = explode(' ', trim($name));
                        $initials = '';
                        foreach ($parts as $part) {
                            if (!empty($part)) {
                                $initials .= strtoupper(substr($part, 0, 1));
                            }
                        }
                        return substr($initials, 0, 2);
                    }
                    $user_initials = get_initials($nombre_completo);

                    // 3.3. Consultar Cargo (si se encontraron nombres/apellidos)
                    if ($datos_usuario['Nombres'] && $datos_usuario['Email_Contacto']) {
                        $sql_cargo = "
                            SELECT c.Nombre_Cargo
                            FROM tbl_asignaciones a
                            JOIN tbl_funcionarios f ON a.ID_Funcionario = f.ID_Funcionario
                            JOIN tbl_cargos c ON a.ID_Cargo = c.ID_Cargo
                            WHERE f.Email_Oficial = :email AND a.Fecha_Fin_Asignacion IS NULL
                            ORDER BY a.Fecha_Inicio_Asignacion DESC
                            LIMIT 1
                        ";
                        $stmt_cargo = $pdo->prepare($sql_cargo);
                        $stmt_cargo->bindParam(':email', $datos_usuario['Email_Contacto']);
                        $stmt_cargo->execute();
                        $cargo_data = $stmt_cargo->fetch();

                        if ($cargo_data) {
                            $cargo_usuario = $cargo_data['Nombre_Cargo'];
                        }
                    }
                } else {
                    die("Error: Usuario con ID " . htmlspecialchars($usuario_id) . " no encontrado.");
                }
            } catch (PDOException $e) {
                die("Error de base de datos en consulta: " . $e->getMessage());
            }



            ?>




            <div class="container-fluid py-5">

                <?php

                // session_start();

                if (isset($_SESSION['alerta_mensaje']) && isset($_SESSION['alerta_tipo'])) {
                    $mensaje = $_SESSION['alerta_mensaje'];
                    $tipo = $_SESSION['alerta_tipo'];

                    // Limpiar las variables de sesión para que la alerta no se muestre de nuevo al recargar
                    unset($_SESSION['alerta_mensaje']);
                    unset($_SESSION['alerta_tipo']);
                ?>
                    <!-- Contenedor de la alerta de Bootstrap 5 -->
                    <div class="container my-4">
                        <div class="alert alert-<?php echo htmlspecialchars($tipo); ?> alert-dismissible fade show" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <?php echo htmlspecialchars($mensaje); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                <?php
                }
                ?>
                <h3 class="fw-bold mb-4 text-center text-dark">
                    <i class="bi bi-person-badge me-2 text-primary"></i> Mi Perfil de Usuario
                </h3>

                <div class="row justify-content-center ">
                    <div class="col-lg-8 col-xl-6 ">
                        <!-- Tarjeta Principal del Perfil -->
                        <div class="card shadow-2xl border-0 rounded-4 overflow-hidden profile-card pt-4">

                            <!-- Encabezado con Gradiente -->
                            <div class="card-header text-white text-center py-5 rounded-top-4 max-auto"
                                style="background: #095fa5ff; position: relative; overflow: visible;">
                                <div class="shadow-lg my-5"
                                    style="width: 120px; height: 120px; border: 4px solid white; border-radius: 50%; overflow: hidden; 
                                        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10;">

                                    <span class="initials"
                                        style="display: block; width: 100%; height: 100%; border-radius: 50%; 
                                        background-color: #0875ceff; color: white; 
                                        font-size: 3.5rem; font-weight: 800; line-height: 118px; text-align: center;">
                                        <?php echo htmlspecialchars($user_initials ?? 'NA'); ?>
                                    </span>
                                </div>

                            </div>


                            <!-- Cuerpo de la Tarjeta -->
                            <div class="card-body p-4 pt-5 bg-light-subtle">

                                <h5 class="mb-3 text-dark fw-bold border-bottom pb-2 text-primary">
                                    <i class="bi bi-info-circle-fill me-2 fw-"></i> Detalles de la Cuenta
                                </h5>
                                <div class="row g-3 small">
                                    <!-- ID del Sistema-->
                                    <div class="col-md-6 border-bottom pb-2">
                                        <i class="bi bi-fingerprint me-2 fw-"></i>ID del Sistema: </br>
                                        <span class="text-secondary fw-bold"><?php echo htmlspecialchars($usuario_id ?? 'N/A'); ?></span>

                                    </div>
                                    <!-- Nombre de Usuario-->
                                    <div class="col-md-6 border-bottom pb-2">
                                        <i class="bi bi-person-circle me-2 fw-"></i>Nombre de Usuario: </br>
                                        <span class="text-primary fw-bold"> <?php echo htmlspecialchars($nombre_a_mostrar ?? 'Nombre de Usuario'); ?></span>


                                    </div>
                                    <!-- Correo Electrónico -->
                                    <div class="col-md-6 border-bottom pb-2 fw-">
                                        <i class="bi bi-envelope-fill me-2 fw-"></i>Correo Electrónico: </br>

                                        <span class="text-secondary"><?php echo htmlspecialchars($correo_a_mostrar ?? 'correo@ejemplo.com'); ?></span>
                                    </div>
                                    <!-- Rol en el Sistema -->
                                    <div class="col-md-6 border-bottom pb-2 fw-">
                                        <i class="bi bi bi-person-badge-fill me-2 fw-"></i>Rol en el Sistema: </br>
                                        <span class="badge  text-primary fs-6 p-2"><?php echo htmlspecialchars($rol_a_mostrar ?? 'Usuario'); ?></span>
                                    </div>
                                    <!-- Fecha de Creación -->
                                    <div class="col-md-6">
                                        <i class="bi bi-calendar-check-fill me-2 fw-"></i>Fecha de Creación: </br>

                                        <span class="text-secondary"><?php echo ($fecha_creacion ? date('d/m/Y', strtotime($fecha_creacion)) : 'N/A'); ?></span>
                                    </div>
                                    <!-- Último Acceso -->
                                    <div class="col-md-6">
                                        <i class="bi bi bi-clock-fill me-2"></i>Último Acceso: </br>
                                        <span class="fw-semibold text-muted d-block"></span>
                                        <span class="text-secondary"><?php echo ($ultimo_acceso ? date('d/m/Y H:i A', strtotime($ultimo_acceso)) : 'Nunca'); ?></span>
                                    </div>
                                </div>

                                <h5 class="mt-5 mb-3 text-dark fw-bold border-bottom pb-2 text-primary">
                                    <i class="bi bi-shield-lock-fill me-2"></i> Acciones de Seguridad
                                </h5>

                                <div class="d-flex flex-column gap-3">
                                    <button class="btn btn-primary rounded-pill  action-button" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                        <i class="bi bi-key me-2"></i> Cambiar Contraseña
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>




            <!-- Modal de Cambio de Contraseña -->
            <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4">
                        <div class="modal-header bg-primary text-white rounded-top-4">
                            <h5 class="modal-title" id="changePasswordModalLabel"><i class="bi bi-shield-lock me-2"></i> Cambiar Contraseña</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <form action="../api/cambiar_password.php" method="POST" id="changePasswordForm">
                            <div class="modal-body p-4">
                                <form id="changePasswordForm" action="api/update_password.php" method="POST">

                                    <div class="mb-3">
                                        <label for="currentPassword" class="form-label fw-bold">Contraseña Actual</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="currentPassword" name="currentPassword" required autocomplete="off">
                                            <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword" title="Mostrar/Ocultar Contraseña">
                                                <i class="bi bi-eye-slash" id="eyeIconCurrent"></i>
                                            </button>
                                        </div>
                                        <div class="invalid-feedback">
                                            La contraseña actual es incorrecta.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="newPassword" class="form-label fw-bold">Nueva Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="newPassword" name="newPassword" required autocomplete="off">
                                            <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword" title="Mostrar/Ocultar Contraseña">
                                                <i class="bi bi-eye-slash" id="eyeIconNew"></i>
                                            </button>
                                        </div>

                                    </div>

                                    <div class="mb-3">
                                        <label for="confirmPassword" class="form-label fw-bold">Confirmar Nueva Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required autocomplete="off">
                                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword" title="Mostrar/Ocultar Contraseña">
                                                <i class="bi bi-eye-slash" id="eyeIconConfirm"></i>
                                            </button>
                                        </div>
                                        <div class="invalid-feedback">
                                            Las contraseñas no coinciden.
                                        </div>
                                    </div>


                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary"> <i class="bi bi-check-circle me-1"></i> Guardar Cambios</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-2"></i>Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Scripts necesarios (Bootstrap JS y Popper) -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


            <script>
                function togglePasswordVisibility(inputId, iconId) {
                    const passwordInput = document.getElementById(inputId);
                    const eyeIcon = document.getElementById(iconId);

                    // Verifica el tipo actual y lo cambia
                    if (passwordInput.getAttribute('type') === 'password') {
                        passwordInput.setAttribute('type', 'text');
                        // Cambia el icono de ojo cerrado (slash) a ojo abierto
                        eyeIcon.classList.remove('bi-eye-slash');
                        eyeIcon.classList.add('bi-eye');
                    } else {
                        passwordInput.setAttribute('type', 'password');
                        // Cambia el icono de ojo abierto a ojo cerrado (slash)
                        eyeIcon.classList.remove('bi-eye');
                        eyeIcon.classList.add('bi-eye-slash');
                    }
                }


                // 1. Contraseña Actual
                document.getElementById('toggleCurrentPassword').addEventListener('click', function() {
                    togglePasswordVisibility('currentPassword', 'eyeIconCurrent');
                });

                // 2. Nueva Contraseña
                document.getElementById('toggleNewPassword').addEventListener('click', function() {
                    togglePasswordVisibility('newPassword', 'eyeIconNew');
                });

                // 3. Confirmar Nueva Contraseña
                document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
                    togglePasswordVisibility('confirmPassword', 'eyeIconConfirm');
                });
            </script>

        <?php
