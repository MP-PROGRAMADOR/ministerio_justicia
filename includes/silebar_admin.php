<?php

// Verifica si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION['ID_Usuario']) || $_SESSION['Rol_Usuario'] !== 'Administrador') {
    $_SESSION['error_login'] = "No tienes permiso para acceder a esta sección.";
    header("Location: ../index.php");
    exit;
}
?>

<div class="sidebar" id="sidebar">
    <div class="logo-section">
        <div class="d-flex align-items-center justify-content-center mb-3">
            <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                <i class="fas fa-balance-scale fa-2x text-primary"></i>
            </div>
            <div class="text-start">
                <h5 class="mb-0 text-white">THEMIS</h5>
                <small class="text-light opacity-75">Ministerio de Justicia</small>
            </div>
        </div>
        <div class="user-profile">
            <div class="user-avatar">JD</div>
            <div class="flex-grow-1 text-start">
                <div class="text-white fw-semibold"><?= $nombre_usuario; ?></div>
                <small class="text-light opacity-75">Administrador</small>
            </div>
            <i class="bi bi-chevron-down text-light"></i>
        </div>
    </div>

    <nav class="nav flex-column px-2">
        <a class="nav-link active" href="index.php">
            <i class="bi bi-speedometer2 me-3"></i> Dashboard
        </a>
        <a class="nav-link" href="funcionarios.php">
            <i class="bi bi-people me-3"></i> Funcionarios
            <span class="badge bg-primary ms-auto" id="totalFuncionariosSidebar">
                <?php echo $dashboardData['totalFuncionarios'] ?? 'N/A'; ?>
            </span>
        </a>
        <a class="nav-link position-relative" href="permisos.php">
            <i class="bi bi-calendar-check me-3"></i> Permisos
            <span class="notification-dot" id="permisosNotifDot"
                style="display: <?php echo ($dashboardData['permisosPendientes'] ?? 0) > 0 ? 'block' : 'none'; ?>;"></span>
            <span class="badge bg-danger ms-auto" id="permisosPendientesSidebar">
                <?php echo $dashboardData['permisosPendientes'] ?? 'N/A'; ?>
            </span>
        </a>
        <a class="nav-link" href="asignaciones.php">
            <i class="bi bi-diagram-3 me-3"></i> Asignaciones
        </a>
        <a class="nav-link" href="destinos.php">
            <i class="bi bi-geo-alt me-3"></i> Destinos
            <span class="badge bg-secondary ms-auto" id="totalDestinosSidebar">
                <?php echo $dashboardData['destinosActivos'] ?? 'N/A'; ?>
            </span>
        </a>
        <a class="nav-link" href="departamentos.php">
            <i class="bi bi-building me-3"></i> Departamentos
        </a>
        <a class="nav-link" href="cargo.php">
            <i class="bi bi-briefcase me-3"></i> Cargos
        </a>
        <a class="nav-link" href="formacion_academica.php">
            <i class="bi bi-mortarboard me-3"></i> Formación
        </a>
        <a class="nav-link" href="capacitaciones.php">
            <i class="bi bi-award me-3"></i> Capacitaciones
        </a>
        <a class="nav-link d-flex align-items-center" href="capacitaciones.php">
            <i class="bi bi-award fs-5 text-primary me-2"></i>
            <span class="fw-semibold">Cursos del Ministerio</span>
        </a>
       

        <a class="nav-link" href="usuarios.php">
            <i class="bi bi-award me-3"></i> Usuarios
        </a>
        <a class="nav-link" href="reportes.php">
            <i class="bi bi-file-earmark-text me-3"></i> Reportes
        </a>
        <a class="nav-link" href="#auditoria">
            <i class="bi bi-shield-check me-3"></i> Auditoría
        </a>
       
    </nav>
</div>






<!-- Modal de Cierre de Sesión -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="logoutModalLabel">
                    <i class="bi bi-box-arrow-right me-2"></i> Confirmar Cierre de Sesión
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-person-circle display-4 text-primary mb-3"></i>
                <p class="fs-5">¿Estás seguro de que deseas cerrar sesión?</p>
                <p class="text-muted">Tu sesión actual se cerrará y volverás a la pantalla de inicio.</p>
            </div>
            <div class="modal-footer justify-content-between px-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </button>
                <a href="../api/logout.php" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </div>
</div>