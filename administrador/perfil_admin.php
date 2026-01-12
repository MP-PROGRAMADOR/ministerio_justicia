<style>
    .form-floating {
        position: relative;
    }

    .input-group-icon-profile {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #adb5bd;
        z-index: 5;
        pointer-events: none;
    }

    .form-control:focus~.input-group-icon-profile {
        color: #3b82f6;
    }

    .avatar-circle {
        width: 100px;
        height: 100px;
        background-color: white;
        color: #1e3a8a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 800;
        border: 4px solid rgba(255, 255, 255, 0.3);
    }

    .icon-box {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    /* Mejora responsiva para la línea divisoria */
    @media (min-width: 768px) {
        .col-md-7 {
            border-right: 1px solid #f0f0f0;
        }
    }
</style>



<?php

include_once '../includes/header.php';

if (!isset($_SESSION['ID_Usuario'])) {
    header('Location: login.php');
    exit();
}

include_once '../includes/silebar_admin.php';

// Configuración de visualización inicial
$usuario_id = $_SESSION['ID_Usuario'];
$nombre_a_mostrar = 'Usuario Desconocido';
$correo_a_mostrar = 'N/A';
$rol_a_mostrar = 'Invitado';
$fecha_creacion = null;
$ultimo_acceso = null;
$cargo_usuario = 'Sin Cargo Asignado';
$user_initials = 'NA';

try {
    // 1. Consultar datos del usuario y unir con la tabla 'funcionarios' por correo
    $sql_user = "
        SELECT 
            u.Nombre_Usuario, 
            u.Rol_Usuario, 
            u.Email_Contacto, 
            u.Fecha_Creacion, 
            u.Ultimo_Acceso,
            f.Id_funcionario,
            f.Nombre AS F_Nombre,
            f.Apellidos AS F_Apellidos
        FROM tbl_usuarios u
        LEFT JOIN funcionarios f ON u.Email_Contacto = f.Correo
        WHERE u.ID_Usuario = :usuario_id
    ";

    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute([':usuario_id' => $usuario_id]);
    $datos_usuario = $stmt_user->fetch(PDO::FETCH_ASSOC);

    if ($datos_usuario) {
        // Lógica de nombre: Si existe en la tabla funcionarios, usar ese, si no, el Nombre_Usuario
        $nombre_completo = (!empty($datos_usuario['F_Nombre']))
            ? $datos_usuario['F_Nombre'] . ' ' . $datos_usuario['F_Apellidos']
            : $datos_usuario['Nombre_Usuario'];

        $nombre_a_mostrar = $nombre_completo;
        $correo_a_mostrar = $datos_usuario['Email_Contacto'];
        $rol_a_mostrar    = $datos_usuario['Rol_Usuario'];
        $fecha_creacion   = $datos_usuario['Fecha_Creacion'];
        $ultimo_acceso    = $datos_usuario['Ultimo_Acceso'];

        // Generar Iniciales
        $parts = explode(" ", $nombre_a_mostrar);
        $user_initials = (count($parts) >= 2)
            ? mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1)
            : mb_substr($parts[0], 0, 2);
        $user_initials = strtoupper($user_initials);

        // 2. Consultar Cargo Actual mediante la tabla 'nombramientos'
        if (!empty($datos_usuario['Id_funcionario'])) {
            $sql_cargo = "
                SELECT c.Nombre 
                FROM nombramientos n
                JOIN cargos c ON n.Id_cargo = c.Id_cargo
                WHERE n.Id_funcionario = :id_func
                ORDER BY n.Fecha_nombramiento DESC
                LIMIT 1
            ";
            $stmt_cargo = $pdo->prepare($sql_cargo);
            $stmt_cargo->execute([':id_func' => $datos_usuario['Id_funcionario']]);
            $cargo_data = $stmt_cargo->fetch(PDO::FETCH_ASSOC);

            if ($cargo_data) {
                $cargo_usuario = $cargo_data['Nombre'];
            }
        }
    }
} catch (PDOException $e) {
    error_log("Error en Perfil: " . $e->getMessage());
}
?>


<div class="container py-5">
    <?php if (isset($_SESSION['alerta_mensaje'])): ?>
        <div id="contenedor-alerta" class="alert alert-<?= $_SESSION['alerta_tipo']; ?> alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i id="alerta-icono" class="bi bi-info-circle me-2"></i>
            <span id="alerta-texto"><?= htmlspecialchars($_SESSION['alerta_mensaje']); ?></span>
        </div>
        <?php unset($_SESSION['alerta_mensaje'], $_SESSION['alerta_tipo']); ?>
    <?php else: ?>
        <div id="contenedor-alerta" class="alert alert-danger alert-dismissible fade hide shadow-sm mb-4 d-none" role="alert">
            <i id="alerta-icono" class="bi bi-exclamation-triangle me-2"></i>
            <span id="alerta-texto"></span>
        </div>
    <?php endif; ?>



    <!-- Modal para ver mi perfil -->
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
                <div class="profile-header text-center text-white py-5" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); position: relative;">
                    <div class="avatar-circle shadow-lg mx-auto">
                        <?= htmlspecialchars($user_initials); ?>
                    </div>
                    <h2 class="mt-4 fw-bold mb-1"><?= htmlspecialchars($nombre_a_mostrar); ?></h2>
                    <span class="badge rounded-pill bg-white text-primary px-3 py-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">
                        <?= htmlspecialchars($rol_a_mostrar); ?>
                    </span>
                </div>

                <div class="card-body p-4 p-md-5 bg-white">
                    <div class="row g-4">
                        <div class="col-md-7">
                            <h5 class="text-dark fw-bold mb-4 border-bottom pb-2">
                                <i class="bi bi-person-lines-fill me-2 text-primary"></i>Datos de Identidad
                            </h5>
                            <div class="mb-3">
                                <label class="text-muted small d-block">Usuario del Sistema</label>
                                <p class="fw-semibold text-dark mb-0"><?= htmlspecialchars($datos_usuario['Nombre_Usuario']); ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small d-block">Correo Electrónico</label>
                                <p class="fw-semibold text-dark mb-0"><?= htmlspecialchars($correo_a_mostrar); ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small d-block">Cargo / Función</label>
                                <span class="badge bg-light text-primary border border-primary-subtle px-3 py-2">
                                    <?= htmlspecialchars($cargo_usuario); ?>
                                </span>
                            </div>
                        </div>

                        <div class="col-md-5 bg-light rounded-4 p-4">
                            <h5 class="text-dark fw-bold mb-4">Actividad</h5>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-box bg-primary-subtle text-primary me-3">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Registro</small>
                                        <span class="fw-bold small"><?= $fecha_creacion ? date('d/m/Y', strtotime($fecha_creacion)) : 'N/A'; ?></span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-success-subtle text-success me-3">
                                        <i class="bi bi-shield-check"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Último Acceso</small>
                                        <span class="fw-bold small"><?= $ultimo_acceso ? date('d/m/Y H:i', strtotime($ultimo_acceso)) : 'Primera vez'; ?></span>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <button class="btn btn-primary w-100 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="bi bi-key-fill me-2"></i> Cambiar Contraseña
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Modal para cambiar de contraseña -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="changePasswordModalLabel">
                    <i class="bi bi-shield-lock text-primary me-2"></i>Seguridad de la Cuenta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-4">Para proteger tu cuenta, asegúrate de que tu nueva contraseña sea robusta (mínimo 8 caracteres).</p>

                <form id="formCambiarPass" action="../api/cambiar_password.php" method="POST">

                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="currentPassword" name="currentPassword" placeholder="Contraseña Actual" required>
                        <label for="currentPassword">Contraseña Actual</label>
                        <i class="bi bi-lock input-group-icon-profile"></i>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="Nueva Contraseña" required minlength="8">
                        <label for="newPassword">Nueva Contraseña</label>
                        <i class="bi bi-key input-group-icon-profile"></i>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirmar Contraseña" required>
                        <label for="confirmPassword">Confirmar Nueva Contraseña</label>
                        <i class="bi bi-check2-all input-group-icon-profile"></i>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill shadow-sm fw-bold">
                        <span class="spinner-border spinner-border-sm d-none" id="pass-spinner" role="status"></span>
                        <span id="btn-text">Actualizar Contraseña</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>



<!--Script para cambiar de contraseña y autocierre del modal de alerta-->
<script>
    function configurarAutoCierre() {
        const alerta = document.getElementById('contenedor-alerta');
        if (alerta && !alerta.classList.contains('d-none')) {
            setTimeout(() => {
                if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                    const bsAlert = new bootstrap.Alert(alerta);
                    bsAlert.close();
                }
            }, 4000);
        }
    }

    document.addEventListener("DOMContentLoaded", configurarAutoCierre);

    document.getElementById('formCambiarPass').addEventListener('submit', function(e) {
        const newPass = document.getElementById('newPassword').value;
        const confirmPass = document.getElementById('confirmPassword').value;

        if (newPass !== confirmPass) {
            e.preventDefault();

            // 1. Localizamos el div de error que ya existe
            const contenedor = document.getElementById('contenedor-alerta');
            const texto = document.getElementById('alerta-texto');
            const icono = document.getElementById('alerta-icono');

            // 2. Cambiamos su contenido y estilo sin crear nuevos elementos
            texto.textContent = "La nueva contraseña y la confirmación no coinciden.";
            contenedor.className = "alert alert-danger alert-dismissible fade show shadow-sm mb-4"; // Quitamos d-none y hide
            contenedor.classList.remove('d-none');
            icono.className = "bi bi-exclamation-triangle me-2";

            // 3. Cerramos el modal
            const modalInstance = bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
            if (modalInstance) modalInstance.hide();

            // 4. Activamos el autocierre para este error
            configurarAutoCierre();
            return;
        }

        // Si todo está bien, mostramos el spinner
        this.querySelector('button[type="submit"]').disabled = true;
        document.getElementById('pass-spinner').classList.remove('d-none');
        document.getElementById('btn-text').textContent = ' Procesando...';
    });
</script>



<?php

include_once '../includes/footer.php';
?>