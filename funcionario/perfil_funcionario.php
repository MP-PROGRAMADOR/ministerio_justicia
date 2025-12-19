<?php
// Asegúrate de que estos includes existan y contengan la lógica de conexión ($dsn, $user, $pass, $options)
include_once '../includes/header.php'; 
?>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>




<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="container-fluid p-0">
        <div class="row g-0">

           
            // Se asume que este archivo maneja la lógica de la sesión y el menú lateral.
                <?php require('header_funcionario.php') ?>

        


            <?php

            // 1. **VALIDACIÓN DE ACCESO Y OBTENCIÓN DE ID DEL FUNCIONARIO**
            
            // Si el usuario no ha iniciado sesión, redirigir
            if (!isset($_SESSION['ID_Usuario'])) {
                header('Location: login.php');
                exit();
            }

            // OBTENER EL ID DEL FUNCIONARIO A VISUALIZAR DESDE LA URL (ej: perfil_funcionario.php?id=5)
            $funcionario_id_a_mostrar = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

            if (!$funcionario_id_a_mostrar) {
                // Redirigir o mostrar un error si no hay ID válido en la URL
                $_SESSION['alerta_mensaje'] = "ID de Funcionario no proporcionado o inválido.";
                $_SESSION['alerta_tipo'] = "danger";
                // En un entorno real, puedes redirigir a una página de error o a la lista de funcionarios.
                // header('Location: lista_funcionarios.php'); 
                // exit();
                $funcionario_id_a_mostrar = 0; // Para forzar que no se encuentre en la BDD y mostrar un error interno
            }


            $pdo = null; // Usaremos $pdo para la conexión

            try {
                // Reemplaza esto con tu configuración de conexión real si no viene del header.php
                $pdo = new PDO($dsn, $user, $pass, $options); 
            } catch (\PDOException $e) {
                // Error fatal si la conexión falla
                die("Error Fatal de Conexión: " . $e->getMessage());
            }

            $datos_funcionario = null;
            $nombre_a_mostrar = 'Funcionario Desconocido';
            $correo_a_mostrar = 'N/A';
            $user_initials = 'FN'; // Iniciales por defecto
            $cargo_usuario = 'No Asignado';
            $rol_a_mostrar = 'Sin Cuenta'; // Rol asociado a la cuenta de usuario
            $fecha_creacion = null;
            $ultimo_acceso = null;
            $fotografia_url = '../assets/img/default-profile.png'; // Foto por defecto
            $dni_a_mostrar = 'N/A';


            try {
                // 2. **CONSULTAR DATOS DEL FUNCIONARIO Y SU CUENTA DE USUARIO ASOCIADA**
                $sql_data = "
                    SELECT 
                        f.ID_Funcionario,
                        f.Nombres,
                        f.Apellidos,
                        f.DNI_Pasaporte,
                        f.Email_Oficial,
                        f.Fotografia,
                        c.Nombre_Cargo,
                        u.Rol_Usuario, 
                        u.Fecha_Creacion, 
                        u.Ultimo_Acceso
                    FROM tbl_funcionarios f
                    LEFT JOIN tbl_asignaciones a ON f.ID_Funcionario = a.ID_Funcionario AND a.Fecha_Fin_Asignacion IS NULL
                    LEFT JOIN tbl_cargos c ON a.ID_Cargo = c.ID_Cargo
                    LEFT JOIN tbl_usuarios u ON f.Email_Oficial = u.Email_Contacto
                    WHERE f.ID_Funcionario = :funcionario_id
                ";

                $stmt_data = $pdo->prepare($sql_data);
                $stmt_data->bindParam(':funcionario_id', $funcionario_id_a_mostrar, PDO::PARAM_INT);
                $stmt_data->execute();
                $datos_funcionario = $stmt_data->fetch();

                if ($datos_funcionario) {
                    // Asignación de variables de visualización
                    $nombre_completo = $datos_funcionario['Nombres'] . ' ' . $datos_funcionario['Apellidos'];

                    $nombre_a_mostrar = $nombre_completo;
                    $dni_a_mostrar = $datos_funcionario['DNI_Pasaporte'] ?? 'N/A';
                    $correo_a_mostrar = $datos_funcionario['Email_Oficial'] ?? 'N/A';
                    $cargo_usuario = $datos_funcionario['Nombre_Cargo'] ?? 'Sin Asignación';
                    
                    // Datos de la cuenta de usuario (pueden ser NULL si no tiene cuenta asociada)
                    $rol_a_mostrar    = $datos_funcionario['Rol_Usuario'] ?? 'Sin Cuenta';
                    $fecha_creacion   = $datos_funcionario['Fecha_Creacion'];
                    $ultimo_acceso    = $datos_funcionario['Ultimo_Acceso'];

                    // Obtener URL de la fotografía
                    if (!empty($datos_funcionario['Fotografia'])) {
                         // Asume que las fotos se encuentran en la ruta configurada en la BDD
                        $fotografia_url = '../api/' . $datos_funcionario['Fotografia']; 
                    }


                    // 3. **Obtener iniciales**
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

                } else {
                    die("Error: Funcionario con ID " . htmlspecialchars($funcionario_id_a_mostrar) . " no encontrado.");
                }
            } catch (PDOException $e) {
                die("Error de base de datos en consulta: " . $e->getMessage());
            }


            ?>




            <div class="container-fluid py-5">

                <?php
                // Manejo de la alerta (mantiene la lógica de la alerta de sesión)
                if (isset($_SESSION['alerta_mensaje']) && isset($_SESSION['alerta_tipo'])) {
                    $mensaje = $_SESSION['alerta_mensaje'];
                    $tipo = $_SESSION['alerta_tipo'];
                    unset($_SESSION['alerta_mensaje']);
                    unset($_SESSION['alerta_tipo']);
                ?>
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
                    <i class="bi bi-person-badge me-2 text-primary"></i> Perfil del Funcionario
                </h3>

                <div class="row justify-content-center ">
                    <div class="col-lg-8 col-xl-6 ">
                        <div class="card shadow-2xl border-0 rounded-4 overflow-hidden profile-card pt-4">

                            <div class="card-header text-white text-center py-5 rounded-top-4 max-auto"
                                style="background: #095fa5ff; position: relative; overflow: visible;">
                                <div class="shadow-lg my-5"
                                    style="width: 120px; height: 120px; border: 4px solid white; border-radius: 50%; overflow: hidden; 
                                        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10;">

                                    <?php if ($fotografia_url !== '../assets/img/default-profile.png'): ?>
                                        <img src="<?= htmlspecialchars($fotografia_url) ?>" alt="Foto de Perfil" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <span class="initials"
                                            style="display: block; width: 100%; height: 100%; border-radius: 50%; 
                                            background-color: #0875ceff; color: white; 
                                            font-size: 3.5rem; font-weight: 800; line-height: 118px; text-align: center;">
                                            <?php echo htmlspecialchars($user_initials ?? 'FN'); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                            </div>


                            <div class="card-body p-4 pt-5 bg-light-subtle">

                                <h4 class="text-center fw-bold mt-4 mb-4 text-primary">
                                    <?php echo htmlspecialchars($nombre_a_mostrar); ?>
                                </h4>

                                <h5 class="mb-3 text-dark fw-bold border-bottom pb-2 text-primary">
                                    <i class="bi bi-info-circle-fill me-2 fw-"></i> Información Personal y Laboral
                                </h5>
                                <div class="row g-3 small">
                                    <div class="col-md-6 border-bottom pb-2">
                                        <i class="bi bi-fingerprint me-2 fw-"></i>ID Funcionario: </br>
                                        <span class="text-secondary fw-bold"><?php echo htmlspecialchars($datos_funcionario['ID_Funcionario'] ?? 'N/A'); ?></span>

                                    </div>
                                    <div class="col-md-6 border-bottom pb-2">
                                        <i class="bi bi-card-heading me-2 fw-"></i>DNI/Pasaporte: </br>
                                        <span class="text-primary fw-bold"> <?php echo htmlspecialchars($dni_a_mostrar); ?></span>

                                    </div>
                                    <div class="col-md-6 border-bottom pb-2 fw-">
                                        <i class="bi bi-briefcase-fill me-2 fw-"></i>Cargo Actual: </br>
                                        <span class="text-secondary fw-bold"><?php echo htmlspecialchars($cargo_usuario); ?></span>
                                    </div>
                                    <div class="col-md-6 border-bottom pb-2 fw-">
                                        <i class="bi bi-envelope-fill me-2 fw-"></i>Email Oficial: </br>
                                        <span class="text-secondary"><?php echo htmlspecialchars($correo_a_mostrar); ?></span>
                                    </div>
                                    
                                </div>
                                
                                <h5 class="mt-5 mb-3 text-dark fw-bold border-bottom pb-2 text-primary">
                                    <i class="bi bi-person-circle me-2"></i> Detalles de la Cuenta (Si Existe)
                                </h5>

                                <div class="row g-3 small">
                                    <div class="col-md-6 border-bottom pb-2 fw-">
                                        <i class="bi bi-person-badge-fill me-2 fw-"></i>Rol en el Sistema: </br>
                                        <span class="badge <?php echo ($rol_a_mostrar !== 'Sin Cuenta' ? 'bg-primary' : 'bg-secondary'); ?> fs-6 p-2">
                                            <?php echo htmlspecialchars($rol_a_mostrar); ?>
                                        </span>
                                    </div>
                                    <div class="col-md-6 border-bottom pb-2 fw-">
                                        <i class="bi bi-activity me-2 fw-"></i>Estado de Cuenta: </br>
                                        <span class="badge <?php echo ($rol_a_mostrar !== 'Sin Cuenta' ? 'bg-success' : 'bg-warning text-dark'); ?> fs-6 p-2">
                                            <?php echo ($rol_a_mostrar !== 'Sin Cuenta' ? 'Activa' : 'Sin Cuenta Asignada'); ?>
                                        </span>
                                    </div>

                                    <div class="col-md-6">
                                        <i class="bi bi-calendar-check-fill me-2 fw-"></i>Fecha de Creación: </br>
                                        <span class="text-secondary"><?php echo ($fecha_creacion ? date('d/m/Y', strtotime($fecha_creacion)) : 'N/A'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <i class="bi bi bi-clock-fill me-2"></i>Último Acceso: </br>
                                        <span class="fw-semibold text-muted d-block"></span>
                                        <span class="text-secondary"><?php echo ($ultimo_acceso ? date('d/m/Y H:i A', strtotime($ultimo_acceso)) : 'Nunca'); ?></span>
                                    </div>
                                </div>

                                
                                <?php if ($_SESSION['Rol_Usuario'] === 'Administrador'): ?>
                                    <h5 class="mt-5 mb-3 text-dark fw-bold border-bottom pb-2 text-danger">
                                        <i class="bi bi-gear-fill me-2"></i> Acciones Administrativas
                                    </h5>

                                    <div class="d-flex flex-column gap-3">
                                        <button class="btn btn-outline-danger rounded-pill action-button" 
                                            data-bs-toggle="modal" data-bs-target="#resetPasswordModal"
                                            title="Restablecer contraseña de la cuenta asociada">
                                            <i class="bi bi-arrow-clockwise me-2"></i> Restablecer Contraseña (Admin)
                                        </button>
                                        <button class="btn btn-outline-warning rounded-pill action-button" 
                                            title="Editar información del funcionario">
                                            <i class="bi bi-pencil-square me-2"></i> Editar Datos del Funcionario
                                        </button>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4">
                        <div class="modal-header bg-danger text-white rounded-top-4">
                            <h5 class="modal-title" id="resetPasswordModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i> Restablecer Contraseña</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <form action="../api/reset_password_admin.php" method="POST">
                            <div class="modal-body p-4">
                                <p>Estás a punto de restablecer la contraseña para **<?php echo htmlspecialchars($nombre_a_mostrar); ?>**.</p>
                                <p class="fw-bold text-danger">Esta acción no se puede deshacer.</p>
                                
                                <input type="hidden" name="funcionario_id" value="<?= htmlspecialchars($datos_funcionario['ID_Funcionario']) ?>">
                                <input type="hidden" name="user_email" value="<?= htmlspecialchars($correo_a_mostrar) ?>">

                                <div class="mb-3">
                                    <label for="newPasswordAdmin" class="form-label fw-bold">Nueva Contraseña Temporal</label>
                                    <input type="text" class="form-control" id="newPasswordAdmin" name="new_password" required>
                                </div>
                                <div class="form-text">
                                    Asegúrate de comunicar la nueva contraseña temporal al funcionario.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger"> <i class="bi bi-arrow-clockwise me-1"></i> Confirmar Restablecimiento</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-2"></i>Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            
            </div>
    </div>
</body>
</html>