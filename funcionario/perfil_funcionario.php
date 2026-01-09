<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="container-fluid p-0">
        <div class="row g-0">

            <?php require('header_funcionario.php') ?>


            <?php


            /* ===============================
   FUNCIÓN PARA OBTENER INICIALES
================================= */
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

            /* ===============================
   OBTENER ID DEL FUNCIONARIO
================================= */
            $funcionario_id_a_mostrar = $_SESSION['ID_Funcionario'];

      


            if (!$funcionario_id_a_mostrar) {
                $_SESSION['alerta_mensaje'] = "ID de Funcionario no proporcionado o inválido.";
                $_SESSION['alerta_tipo'] = "danger";
                $funcionario_id_a_mostrar = 0;
            }

      
            /* ===============================
   VARIABLES POR DEFECTO
================================= */
            $nombre_a_mostrar   = 'Funcionario Desconocido';
            $correo_a_mostrar   = 'N/A';
            $cargo_usuario      = 'Sin Asignación';
            $dni_a_mostrar      = 'N/A';
            $user_initials      = 'FN';
            $fotografia_url     = '../assets/img/default-profile.png';

            /* ===============================
   CONSULTAR DATOS DEL FUNCIONARIO
================================= */
            try {

                $sql_data = "
        SELECT 
            f.Id_funcionario,
            f.Nombre,
            f.Apellidos,
            f.Dip_Pasaporte,
            f.Correo,
            f.Foto,
            f.Fecha_posesion,
            c.Nombre AS Nombre_Cargo,
            n.Fecha_nombramiento,
            n.Fecha_toma_posesion
        FROM funcionarios f
        LEFT JOIN nombramientos n 
            ON n.Id_funcionario = f.Id_funcionario
        LEFT JOIN cargos c 
            ON c.Id_cargo = n.Id_cargo
        WHERE f.Id_funcionario = :funcionario_id
        ORDER BY n.Fecha_nombramiento DESC
        LIMIT 1
    ";

                $stmt_data = $pdo->prepare($sql_data);
                $stmt_data->bindParam(':funcionario_id', $funcionario_id_a_mostrar, PDO::PARAM_INT);
                $stmt_data->execute();

                $datos_funcionario = $stmt_data->fetch(PDO::FETCH_ASSOC);

                if (!$datos_funcionario) {
                    die("Funcionario no encontrado.");
                }

                /* ===============================
       ASIGNAR DATOS
    ================================= */
                $nombre_completo = trim($datos_funcionario['Nombre'] . ' ' . $datos_funcionario['Apellidos']);

                $nombre_a_mostrar = $nombre_completo;
                $dni_a_mostrar    = $datos_funcionario['Dip_Pasaporte'] ?? 'N/A';
                $correo_a_mostrar = $datos_funcionario['Correo'] ?? 'N/A';
                $cargo_usuario    = $datos_funcionario['Nombre_Cargo'] ?? 'Sin Asignación';

                if (!empty($datos_funcionario['Foto'])) {
                    $fotografia_url = '../api/' . $datos_funcionario['Foto'];
                }

                $user_initials = get_initials($nombre_completo);
            } catch (PDOException $e) {
                die("Error de base de datos: " . $e->getMessage());
            }

            ?>




           <div class="container-fluid py-5">

    <?php
    // Mostrar alerta si existe
    if (isset($_SESSION['alerta_mensaje'], $_SESSION['alerta_tipo'])) {
        $mensaje = $_SESSION['alerta_mensaje'];
        $tipo = $_SESSION['alerta_tipo'];
        unset($_SESSION['alerta_mensaje'], $_SESSION['alerta_tipo']);
    ?>
        <div class="container my-4">
            <div class="alert alert-<?php echo htmlspecialchars($tipo); ?> alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i>
                <?php echo htmlspecialchars($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php } ?>

    <h3 class="fw-bold mb-4 text-center text-dark">
        <i class="bi bi-person-badge me-2 text-primary"></i> Perfil del Funcionario
    </h3>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="card shadow-2xl border-0 rounded-4 overflow-hidden profile-card pt-4">

                <!-- Header con foto -->
                <div class="card-header text-white text-center py-5 rounded-top-4 position-relative"
                    style="background: #095fa5ff; overflow: visible;">
                    <div class="shadow-lg my-5"
                        style="width: 120px; height: 120px; border: 4px solid white; border-radius: 50%; overflow: hidden; 
                            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10;">
                        <?php if (!empty($fotografia_url) && $fotografia_url !== '../assets/img/default-profile.png'): ?>
                            <img src="<?= htmlspecialchars($fotografia_url) ?>" alt="Foto de Perfil" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <span class="initials"
                                style="display: block; width: 100%; height: 100%; border-radius: 50%; 
                                background-color: #0875ceff; color: white; 
                                font-size: 3.5rem; font-weight: 800; line-height: 118px; text-align: center;">
                                <?= htmlspecialchars($user_initials ?? 'FN'); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Cuerpo de la tarjeta -->
                <div class="card-body p-4 pt-5 bg-light-subtle">

                    <!-- Nombre -->
                    <h4 class="text-center fw-bold mt-4 mb-4 text-primary">
                        <?= htmlspecialchars($nombre_a_mostrar); ?>
                    </h4>

                    <!-- Información personal y laboral -->
                    <h5 class="mb-3 text-dark fw-bold border-bottom pb-2 text-primary">
                        <i class="bi bi-info-circle-fill me-2"></i> Información Personal y Laboral
                    </h5>

                    <div class="row g-3 small">
                        <div class="col-md-6 border-bottom pb-2">
                            <i class="bi bi-hash me-2"></i>Código Funcionario: <br>
                            <span class="text-secondary fw-bold"><?= htmlspecialchars($datos_funcionario['Id_funcionario'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="col-md-6 border-bottom pb-2">
                            <i class="bi bi-hashtag me-2"></i>Número de Funcionario: <br>
                            <span class="text-secondary fw-bold"><?= htmlspecialchars($datos_funcionario['Id_funcionario'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="col-md-6 border-bottom pb-2">
                            <i class="bi bi-card-heading me-2"></i>DNI/Pasaporte: <br>
                            <span class="text-primary fw-bold"><?= htmlspecialchars($dni_a_mostrar); ?></span>
                        </div>
                        <div class="col-md-6 border-bottom pb-2">
                            <i class="bi bi-briefcase-fill me-2"></i>Cargo Actual: <br>
                            <span class="text-secondary fw-bold"><?= htmlspecialchars($cargo_usuario); ?></span>
                        </div>
                        <div class="col-md-6 border-bottom pb-2">
                            <i class="bi bi-envelope-fill me-2"></i>Email Oficial: <br>
                            <span class="text-secondary"><?= htmlspecialchars($correo_a_mostrar); ?></span>
                        </div>
                        <div class="col-md-6 border-bottom pb-2">
                            <i class="bi bi-calendar-event me-2"></i>Fecha Nombramiento: <br>
                            <span class="text-secondary fw-bold"><?= !empty($datos_funcionario['Fecha_posesion']) ? date('d/m/Y', strtotime($datos_funcionario['Fecha_posesion'])) : 'N/A'; ?></span>
                        </div>
                        <div class="col-md-6 border-bottom pb-2">
                            <i class="bi bi-calendar-check me-2"></i>Fecha Toma de Posesión: <br>
                            <span class="text-secondary fw-bold"><?= !empty($datos_funcionario['Fecha_posesion']) ? date('d/m/Y', strtotime($datos_funcionario['Fecha_posesion'])) : 'N/A'; ?></span>
                        </div>
                    </div>

                    <!-- Detalles de cuenta si existe -->
                    <h5 class="mt-5 mb-3 text-dark fw-bold border-bottom pb-2 text-primary">
                        <i class="bi bi-person-circle me-2"></i> Detalles de la Cuenta (Si Existe)
                    </h5>

                    <div class="row g-3 small">
                        <!-- Puedes agregar detalles de cuenta aquí si lo necesitas -->
                        <div class="col-12 text-muted">No hay información adicional de cuenta disponible.</div>
                    </div>

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