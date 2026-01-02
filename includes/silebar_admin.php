<?php

// Verifica si el usuario ha iniciado sesión y es administrador
if (
    !isset($_SESSION['ID_Usuario']) ||
    ($_SESSION['Rol_Usuario'] !== 'Administrador' && $_SESSION['Rol_Usuario'] !== 'Usuario' && $_SESSION['Rol_Usuario'] !== 'Jefe Personal')
) {
    $_SESSION['error_login'] = "No tienes permiso para acceder a esta sección.";
    header("Location: ../index.php");
    exit;
}

?>

<?php
// 1. Obtener el nombre del archivo de la página actual (ej: 'index.php')
$current_page = basename($_SERVER['PHP_SELF']);
// Nota: $nombre_usuario; y $dashboardData[] deben estar definidos antes de este bloque.
?>


<div class="sidebar-overlay" id="sidebarOverlay"></div>


<!-- Acciones de arriba: buscar funcionario, actualizar, usuario conectado -->
<div class="top-navbar">
    <div class="d-flex justify-content-between align-items-center">
        <button class="btn btn-outline-secondary d-md-none me-2 menu-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom mb-0">
                    <li class="breadcrumb-item"><a href="../administrador/index.php" class="text-decoration-none">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-3">
            <!-- <div class="input-group" style="width: 300px;">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0"
                    placeholder="Buscar funcionario...">
            </div> -->


            <button class="btn btn-outline-primary btn-refresh" onclick="refreshData()">
                <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                    data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i> <?= $nombre_usuario; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="./perfil_admin.php">
                            <i class="bi bi-person me-2"></i>Mi Perfil</a>
                    </li>
                    <li><a class="dropdown-item" href="./configuracion.php"><i
                                class="bi bi-gear me-2"></i>Configuración</a></li>
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



<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="successModalLabel">✅ Actualización Exitosa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-check-circle-fill text-success fs-3 mb-2"></i>
                <p>Los datos han sido actualizados correctamente.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>








<!-- ASIDE -->
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
                <small class="text-light opacity-75">La Justicia</small>
            </div>
        </div>
    </div>

    <nav class="nav flex-column px-2">
        <a class="nav-link <?php echo ($current_page === 'index.php') ? 'active' : ''; ?>" href="index.php">
            <i class="bi bi-speedometer2 me-3"></i> Dashboard
        </a>

        <?php if ($_SESSION['Rol_Usuario'] === 'Administrador' || $_SESSION['Rol_Usuario'] === 'Usuario' || $_SESSION['Rol_Usuario'] === 'Jefe Personal'): ?>
            <a class="nav-link <?php echo ($current_page === 'funcionarios.php') ? 'active' : ''; ?>" href="funcionarios.php">
                <i class="bi bi-people me-3"></i> Funcionarios
                <span class="badge bg-primary ms-auto" id="totalFuncionariosSidebar">
                    <?php echo $dashboardData['totalFuncionarios'] ?? 'N/A'; ?>
                </span>
            </a>

            <a class="nav-link position-relative <?php echo ($current_page === 'permisos.php') ? 'active' : ''; ?>" href="permisos.php">
                <i class="bi bi-calendar-check me-3"></i> Permisos
                <span class="notification-dot" id="permisosNotifDot"
                    style="display: <?php echo ($dashboardData['permisosPendientes'] ?? 0) > 0 ? 'block' : 'none'; ?>;"></span>
                <span class="badge bg-danger ms-auto" id="permisosPendientesSidebar">
                    <?php echo $dashboardData['permisosPendientes'] ?? 'N/A'; ?>
                </span>
            </a>
        <?php endif; ?>

        <?php if ($_SESSION['Rol_Usuario'] === 'Administrador' || $_SESSION['Rol_Usuario'] === 'Jefe Personal'): ?>
            <a class="nav-link <?php echo ($current_page === 'categorias.php') ? 'active' : ''; ?>" href="categorias.php">
                <i class="bi bi-diagram-3 me-3"></i> Categorias
            </a>

             <a class="nav-link <?php echo ($current_page === 'direcciones.php') ? 'active' : ''; ?>" href="direcciones.php">
                <i class="bi bi-diagram-3 me-3"></i> Dirrecciones
            </a>

            <a class="nav-link <?php echo ($current_page === 'secciones.php') ? 'active' : ''; ?>" href="secciones.php">
                <i class="bi bi-diagram-3 me-3"></i> Secciones
            </a>
            

             <a class="nav-link <?php echo ($current_page === 'nombramientos.php') ? 'active' : ''; ?>" href="nombramientos.php">
               <i class="bi bi-briefcase"></i> Nombramientos
            </a>

            <a class="nav-link <?php echo ($current_page === 'destinos.php') ? 'active' : ''; ?>" href="destinos.php">
                <i class="bi bi-geo-alt me-3"></i> Destinos
                <span class="badge bg-secondary ms-auto" id="totalDestinosSidebar">
                    <?php echo $dashboardData['destinosActivos'] ?? 'N/A'; ?>
                </span>
            </a>
            <a class="nav-link <?php echo ($current_page === 'departamentos.php') ? 'active' : ''; ?>" href="departamentos.php">
                <i class="bi bi-building-fill me-3"></i> Departamentos
            </a>
            <a class="nav-link <?php echo ($current_page === 'cargo.php') ? 'active' : ''; ?>" href="cargo.php">
                <i class="bi bi-briefcase me-3"></i> Cargos
            </a>
            <a class="nav-link <?php echo ($current_page === 'formacion_academica.php') ? 'active' : ''; ?>" href="formacion_academica.php">
                <i class="bi bi-mortarboard me-3"></i> Formación
            </a>
            <a class="nav-link <?php echo ($current_page === 'capacitaciones.php') ? 'active' : ''; ?>" href="capacitaciones.php">
                <i class="bi bi-award me-3"></i> Capacitaciones Externas
            </a>
            <a class="nav-link d-flex align-items-center <?php echo ($current_page === 'cursos_ministerio.php') ? 'active' : ''; ?>" href="cursos_ministerio.php">
                <i class="bi bi-award fs-5 me-3"></i> Cursos del Ministerio
            </a>

            <a class="nav-link <?php echo ($current_page === 'instrucciones_diarias.php') ? 'active' : ''; ?>" href="instrucciones_diarias.php">
                <i class="bi bi-file-earmark-text me-3"></i> Instrucciones Diarias
            </a>

            <a class="nav-link <?php echo ($current_page === 'reportes.php') ? 'active' : ''; ?>" href="reportes.php">
                <i class="bi bi-file-earmark-text me-3"></i> Reportes
            </a>
        <?php endif; ?>


        <?php if ($_SESSION['Rol_Usuario'] === 'Administrador'): ?>
            <a class="nav-link <?php echo ($current_page === 'usuarios.php') ? 'active' : ''; ?>" href="usuarios.php">
                <i class="bi bi-person me-3"></i> Usuarios
            </a>

            <a class="nav-link" href="#auditoria">
                <i class="bi bi-shield-check me-3"></i> Auditoría
            </a>
        <?php endif; ?>
    </nav>
</div>



<style>
    .sidebar {
        background-color: #212529;
        color: #f8f9fa;
        width: 280px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1030;
        transition: all 0.3s ease;
        padding: 0;
        box-shadow: 4px 0 10px rgba(0, 0, 0, 0.2);
    }


    .logo-section {
        padding: 1.5rem 1rem 0.75rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .logo-section h5,
    .logo-section small {
        color: #f8f9fa !important;
    }


    .logo-section .fa-balance-scale {
        color: #007bff !important;
    }



    .user-profile {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        margin-bottom: 1.5rem;
        cursor: pointer;
        transition: background-color 0.2s;
        border-radius: 0.5rem;
    }

    .user-profile:hover {
        background-color: rgba(255, 255, 255, 0.08);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 50%;
        background-color: #007bff;
        color: white;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        border: 2px solid rgba(255, 255, 255, 0.5);
    }


    .sidebar .nav {
        padding-top: 0.5rem;
    }

    .sidebar .nav-link {
        color: #adb5bd;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 0.25rem;
        transition: background-color 0.2s, color 0.2s;
        font-size: 0.95rem;
    }


    .sidebar .nav-link.active {
        background-color: #007bff;
        color: white;
        font-weight: 600;
        box-shadow: 0 2px 5px rgba(0, 123, 255, 0.3);

    }

    .sidebar .nav-link:not(.active):hover {
        color: #f8f9fa;
        background-color: rgba(255, 255, 255, 0.15);

    }

    /* Estilo para los iconos dentro de los enlaces */
    .sidebar .nav-link i {
        font-size: 1.2rem;
        width: 20px;

    }

    /* Enlaces con formato especial (Cursos del Ministerio) */
    .sidebar .nav-link .fw-semibold {
        color: #f8f9fa;

    }


    .sidebar .nav-link .badge {
        padding: 0.4em 0.6em;
        font-size: 0.75em;
        line-height: 1;
        min-width: 25px;
    }

    /* Punto de Notificación (el círculo rojo para Permisos) */
    .notification-dot {
        position: absolute;
        top: 10px;
        right: 25px;
        height: 10px;
        width: 10px;
        background-color: #dc3545;
        border-radius: 50%;
        border: 2px solid #212529;
        z-index: 10;
    }
</style>






<!-- Modal de Cierre de Sesión -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-3 border-top border-primary rounded-3">

            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bolder text-dark" id="logoutModalLabel">
                    <i class="bi bi-box-arrow-right me-2 text-primary"></i> Cierre de Sesión
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body text-center pt-2 pb-4 px-5">

                <i class="bi bi-person-check-fill display-5 text-primary mb-3"></i>

                <h6 class="fw-semibold mb-2">¿Confirmas el cierre de tu sesión?</h6>
                <p class="text-secondary small mb-3">
                    Tu conexión actual será desconectada. Esta acción es irreversible.
                </p>

                <div class="bg-light p-2 rounded text-start session-details">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="bi bi-clock me-2 text-muted"></i>
                        <small class="text-muted">Último Acceso: <span id="lastAccessTime">Cargando...</span></small>
                    </div>
                </div>
            </div>

            <div class="modal-footer justify-content-center border-top pt-3 pb-3">
                <button type="button" class="btn btn-sm btn-outline-secondary px-4 me-2" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <a href="../api/logout.php" class="btn btn-sm btn-danger px-4">
                    <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </div>
</div>










<!-- Para recoger la ultima hora de acceso -->
<script>
    function formatCurrentTime() {
        const now = new Date();

        // Opciones de formato (ejemplo: "jueves, 13 de noviembre a las 11:28 AM")
        const options = {
            weekday: 'long', // Muestra el día de la semana
            day: 'numeric',
            month: 'long',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true // Para AM/PM
        };

        // Usa 'es-ES' para español
        const formattedTime = now.toLocaleDateString('es-ES', options);
        return formattedTime.charAt(0).toUpperCase() + formattedTime.slice(1); // Capitaliza el primer carácter
    }

    // Esperar a que el modal se muestre para actualizar la hora
    document.addEventListener('DOMContentLoaded', function() {
        const logoutModal = document.getElementById('logoutModal');

        // Evento de Bootstrap que se dispara cuando el modal es visible
        logoutModal.addEventListener('shown.bs.modal', function() {
            const timeSpan = document.getElementById('lastAccessTime');
            if (timeSpan) {
                // Actualiza el texto con la hora formateada
                timeSpan.textContent = formatCurrentTime();
            }
        });
    });
</script>








<!-- Script para Actualizar la pagina -->
<script>
    window.refreshData = function() {
        const refreshBtn = document.querySelector('.btn-refresh');
        // 1. Añadir el estilo de "cargando"
        refreshBtn.classList.add('refreshing');

        // Deshabilitar el botón durante la carga
        refreshBtn.setAttribute('disabled', 'true');

        // Simulate data fetching (reemplaza esto con tu llamada real a la API/BD)
        setTimeout(() => {

            // 2. Quitar el estilo de "cargando"
            refreshBtn.classList.remove('refreshing');

            // Habilitar el botón
            refreshBtn.removeAttribute('disabled');

            // 3. Mostrar el Modal (Reemplaza alert())
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();

        }, 1000);
    };
</script>








<!-- Script para saber donde estamos -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pageMap = {
            'dashboard': 'Dashboard',
            'funcionarios': 'Funcionarios',
            'formacion': 'Formación',
            'departamentos': 'Departamentos',
            'permisos': 'Permisos',
            'reportes': 'Reportes',
            'usuarios': 'Usuarios',
            'destinos': 'Destinos',
            'cargos': 'Cargos',
            'nombramientos': 'Nombramientos'
        };

        let path = window.location.pathname.toLowerCase();

        let pathSegments = path.split('/').filter(segment => segment.length > 0);
        let currentPageSegment = pathSegments[pathSegments.length - 1];
        if (currentPageSegment === '' || currentPageSegment === 'index.php' || currentPageSegment === 'ministerio_justicia') {
            currentPageSegment = 'dashboard';
        }


        if (currentPageSegment.endsWith('.php')) {
            currentPageSegment = currentPageSegment.replace('.php', '');
        }

        const activePageName = pageMap[currentPageSegment] || currentPageSegment;

        // 6. Actualizar el elemento activo del breadcrumb.
        const activeBreadcrumbItem = document.querySelector('.breadcrumb-custom .breadcrumb-item.active');

        if (activeBreadcrumbItem) {
            activeBreadcrumbItem.textContent = activePageName;
            activeBreadcrumbItem.setAttribute('aria-current', 'page');
        }
    });
</script>