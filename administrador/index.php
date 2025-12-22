<?php
include_once '../includes/header.php';
?>

<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Overlay para cerrar sidebar en móvil -->

    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->


            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

              <?php
                include_once '../includes/silebar_admin.php';

                ?>
            <!-- Main Content -->
            <div class="main-content" id="mainContent">


              


                <!-- Header Section -->
                <div class="header-section">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2 fw-bold">Panel de Administración</h2>
                            <p class="mb-0 text-muted">Sistema de Gestión de Recursos Humanos - Ministerio de Justicia de Guinea Ecuatorial</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="d-flex justify-content-md-end align-items-center gap-2 flex-wrap justify-content-center">
                                <select class="form-select" style="width: auto;">
                                    <option value="mes">Este mes</option>
                                    <option value="trimestre">Trimestre</option>
                                    <option value="año">Este año</option>
                                </select>
                                <button class="btn btn-primary">
                                    <i class="bi bi-download me-1"></i> Exportar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container-fluid px-4">
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="stat-number text-primary" id="statTotalFuncionarios">
                                    <?php echo $dashboardData['totalFuncionarios'] ?? 'N/A'; ?>
                                </div>
                                <div class="stat-label">Total Funcionarios</div>
                                <div class="stat-change text-success">
                                    <i class="bi bi-arrow-up"></i> <span id="statNewFuncionarios">
                                        <?php echo $dashboardData['newFuncionariosThisMonth'] ?? 'N/A'; ?>
                                    </span> este mes
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-person-check"></i>
                                </div>
                                <div class="stat-number text-success" id="statFuncionariosActivos">
                                    <?php echo $dashboardData['funcionariosActivos'] ?? 'N/A'; ?>
                                </div>
                                <div class="stat-label">Funcionarios Activos</div>
                                <div class="stat-change text-success">
                                    <i class="bi bi-arrow-up"></i> <span id="statActivosPercent">
                                        <?php
                                        $total = $dashboardData['totalFuncionarios'] ?? 0;
                                        $activos = $dashboardData['funcionariosActivos'] ?? 0;
                                        echo $total > 0 ? number_format(($activos / $total) * 100, 1) : '0';
                                        ?>%
                                    </span> del total
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                                <div class="stat-number text-warning" id="statPermisosEsteMes">
                                    <?php echo $dashboardData['permisosEsteMes'] ?? 'N/A'; ?>
                                </div>
                                <div class="stat-label">Permisos Este Mes</div>
                                <div class="stat-change text-danger">
                                    <i class="bi bi-exclamation-circle"></i> <span id="statPermisosPendientes">
                                        <?php echo $dashboardData['permisosPendientes'] ?? 'N/A'; ?>
                                    </span> pendientes
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-info bg-opacity-10 text-info">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div class="stat-number text-info" id="statDestinosActivos">
                                    <?php echo $dashboardData['destinosActivos'] ?? 'N/A'; ?>
                                </div>
                                <div class="stat-label">Destinos Activos</div>
                                <div class="stat-change text-info">
                                    <i class="bi bi-building"></i> <span id="statJuzgados">
                                        <?php
                                        $juzgadosCount = 0;
                                        if (isset($dashboardData['destinationTypes'])) {
                                            foreach ($dashboardData['destinationTypes'] as $dest) {
                                                if ($dest['Tipo_Destino'] === 'Juzgado') {
                                                    $juzgadosCount = $dest['count'];
                                                    break;
                                                }
                                            }
                                        }
                                        echo $juzgadosCount;
                                        ?>
                                    </span> Juzgados
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="row mb-4">
                        <!-- Grafica -->
                        <div class="col-lg-8">
                            <div class="chart-container">
                                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                    <h5 class="mb-0 fw-semibold" id="chartTitle">Distribución de Funcionarios por Estado</h5>
                                    <div class="btn-group btn-group-sm mt-2 mt-md-0" role="group">
                                        <input type="radio" class="btn-check" name="chartOptions" id="option1" value="estado" checked>
                                        <label class="btn btn-outline-primary" for="option1">Estado</label>
                                        <input type="radio" class="btn-check" name="chartOptions" id="option2" value="genero">
                                        <label class="btn btn-outline-primary" for="option2">Género</label>
                                        <input type="radio" class="btn-check" name="chartOptions" id="option3" value="destino">
                                        <label class="btn btn-outline-primary" for="option3">Destino</label>
                                    </div>
                                </div>
                                <div class="chart-wrapper">
                                    <canvas id="funcionariosChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones rapidas -->
                        <div class="col-lg-4">
                            <div class="chart-container">
                                <h5 class="mb-3 fw-semibold">Acciones Rápidas</h5>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="funcionarios.php?action=register" class="quick-action-btn">
                                            <i class="bi bi-person-plus fa-2x mb-2 d-block"></i>
                                            <small>Nuevo Funcionario</small>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="permisos.php?action=register" class="quick-action-btn">
                                            <i class="bi bi-calendar-plus fa-2x mb-2 d-block"></i>
                                            <small>Nuevo Permiso</small>
                                        </a>
                                    </div>

                                    <div class="col-6">
                                        <a href="asignaciones.php?action=register" class="quick-action-btn">
                                            <i class="bi bi-diagram-3-fill fa-2x mb-2 d-block"></i>
                                            <small>Nueva Asignación</small>
                                        </a>
                                    </div>

                                    <div class="col-6">
                                        <a href="destinos.php?action=register" class="quick-action-btn">
                                            <i class="bi bi-building-add fa-2x mb-2 d-block"></i>
                                            <small>Nuevo Destino</small>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Department Statistics -->
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <div class="chart-container">
                                <h5 class="mb-3 fw-semibold">Departamentos con Mayor Personal</h5>
                                <div id="departmentProgressBars">
                                    <?php if (!empty($dashboardData['departmentStaff'])): ?>
                                        <?php
                                        $totalStaffDepartments = array_reduce($dashboardData['departmentStaff'], function ($sum, $dept) {
                                            return $sum + ($dept['num_funcionarios'] ?? 0);
                                        }, 0);
                                        $progressColors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary'];
                                        foreach ($dashboardData['departmentStaff'] as $index => $dept):
                                            $percentage = $totalStaffDepartments > 0 ? number_format((($dept['num_funcionarios'] ?? 0) / $totalStaffDepartments) * 100, 1) : 0;
                                            $colorClass = $progressColors[$index % count($progressColors)];
                                        ?>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="fw-medium">
                                                        <?php echo htmlspecialchars($dept['Nombre_Departamento'] ?? 'N/A'); ?>
                                                    </span>
                                                    <span class="text-<?php echo str_replace('bg-', '', $colorClass); ?> fw-bold">
                                                        <?php echo htmlspecialchars($dept['num_funcionarios'] ?? 0); ?> funcionarios
                                                    </span>
                                                </div>
                                                <div class="progress progress-custom">
                                                    <div class="progress-bar <?php echo $colorClass; ?> progress-bar-custom"
                                                        style="width: <?php echo $percentage; ?>%"></div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center text-muted py-4">No hay datos de departamentos disponibles.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="chart-container">
                                <h5 class="mb-3 fw-semibold">Tipos de Destinos</h5>
                                <div class="row text-center" id="destinationTypeCards">
                                    <?php if (!empty($dashboardData['destinationTypes'])): ?>
                                        <?php
                                        $iconMap = [
                                            'Juzgado' => 'bi-bank',
                                            'Tribunal' => 'bi-building',
                                            'Fiscalia' => 'bi-shield-check',
                                            'Sede Central' => 'bi-house-door',
                                            'Oficina Regional' => 'bi-diagram-3',
                                            'Otro' => 'bi-geo-alt'
                                        ];
                                        $colorMap = [
                                            'Juzgado' => 'primary',
                                            'Tribunal' => 'success',
                                            'Fiscalia' => 'warning',
                                            'Sede Central' => 'info',
                                            'Oficina Regional' => 'secondary',
                                            'Otro' => 'dark'
                                        ];
                                        foreach ($dashboardData['destinationTypes'] as $dest):
                                            $iconClass = $iconMap[$dest['Tipo_Destino']] ?? 'bi-question-circle';
                                            $colorClass = $colorMap[$dest['Tipo_Destino']] ?? 'secondary';
                                        ?>
                                            <div class="col-4 mb-3">
                                                <div class="stat-icon bg-<?php echo $colorClass; ?> bg-opacity-10 text-<?php echo $colorClass; ?> mx-auto mb-2"
                                                    style="width: 48px; height: 48px; font-size: 1.1rem;">
                                                    <i class="bi <?php echo $iconClass; ?>"></i>
                                                </div>
                                                <div class="fw-bold text-<?php echo $colorClass; ?> fs-5">
                                                    <?php echo htmlspecialchars($dest['count'] ?? 0); ?>
                                                </div>
                                                <small class="text-muted fw-medium">
                                                    <?php echo htmlspecialchars($dest['Tipo_Destino'] ?? 'N/A'); ?>
                                                </small>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center text-muted py-4">No hay datos de tipos de destino disponibles.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reports Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="chart-container">
                                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                    <h5 class="mb-0 fw-semibold">Reportes Disponibles</h5>
                                    <button class="btn btn-outline-primary btn-sm mt-2 mt-md-0">
                                        <i class="bi bi-plus-lg me-1"></i> Crear Reporte
                                    </button>
                                </div>
                                <div class="reports-grid">
                                    <div class="report-card">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3"
                                                style="width: 40px; height: 40px; font-size: 1rem; border-radius: 8px;">
                                                <i class="bi bi-person-lines-fill"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold">Reporte de Personal Activo</h6>
                                                <small class="text-muted">Lista detallada de funcionarios en servicio.</small>
                                            </div>
                                        </div>
                                        <a href="#" class="btn btn-sm btn-outline-primary w-100 mt-2">Ver Reporte</a>
                                    </div>
                                    <div class="report-card">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="stat-icon bg-success bg-opacity-10 text-success me-3"
                                                style="width: 40px; height: 40px; font-size: 1rem; border-radius: 8px;">
                                                <i class="bi bi-graph-up"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold">Estadísticas de Asistencia</h6>
                                                <small class="text-muted">Análisis de registros de asistencia y puntualidad.</small>
                                            </div>
                                        </div>
                                        <a href="#" class="btn btn-sm btn-outline-success w-100 mt-2">Ver Reporte</a>
                                    </div>
                                    <div class="report-card">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3"
                                                style="width: 40px; height: 40px; font-size: 1rem; border-radius: 8px;">
                                                <i class="bi bi-file-earmark-bar-graph"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold">Historial de Permisos</h6>
                                                <small class="text-muted">Registro completo de todos los permisos solicitados.</small>
                                            </div>
                                        </div>
                                        <a href="#" class="btn btn-sm btn-outline-warning w-100 mt-2">Ver Reporte</a>
                                    </div>
                                    <div class="report-card">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="stat-icon bg-info bg-opacity-10 text-info me-3"
                                                style="width: 40px; height: 40px; font-size: 1rem; border-radius: 8px;">
                                                <i class="bi bi-briefcase"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold">Reporte de Cargos y Departamentos</h6>
                                                <small class="text-muted">Visión general de la estructura organizativa.</small>
                                            </div>
                                        </div>
                                        <a href="#" class="btn btn-sm btn-outline-info w-100 mt-2">Ver Reporte</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity and Notifications -->
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <div class="chart-container">
                                <h5 class="mb-3 fw-semibold">Actividad Reciente</h5>
                                <div id="recentActivityList">
                                    <?php if (!empty($dashboardData['recentActivity'])): ?>
                                        <?php foreach ($dashboardData['recentActivity'] as $activity):
                                            $activityClass = '';
                                            $description = '';
                                            $timeAgo = new DateTime($activity['timestamp']);
                                            $now = new DateTime();
                                            $interval = $now->diff($timeAgo);
                                            $formattedTime = '';

                                            if ($interval->y > 0) $formattedTime = $interval->y . ' año(s) atrás';
                                            else if ($interval->m > 0) $formattedTime = $interval->m . ' mes(es) atrás';
                                            else if ($interval->d > 0) $formattedTime = $interval->d . ' día(s) atrás';
                                            else if ($interval->h > 0) $formattedTime = $interval->h . ' hora(s) atrás';
                                            else if ($interval->i > 0) $formattedTime = $interval->i . ' minuto(s) atrás';
                                            else $formattedTime = 'Hace un momento';

                                            switch ($activity['type']) {
                                                case 'Nuevo Funcionario':
                                                    $activityClass = 'recent';
                                                    $description = 'Departamento de Recursos Humanos';
                                                    $title = 'Nuevo funcionario añadido: ' . htmlspecialchars($activity['Nombres'] . ' ' . $activity['Apellidos']);
                                                    break;
                                                case 'Permiso':
                                                    $activityClass = ($activity['Estado_Permiso'] === 'Pendiente') ? 'warning' : '';
                                                    $description = 'Permiso por ' . htmlspecialchars($activity['Tipo_Permiso']);
                                                    $title = 'Permiso ' . htmlspecialchars($activity['Estado_Permiso']) . ' para ' . htmlspecialchars($activity['Nombres'] . ' ' . $activity['Apellidos']);
                                                    break;
                                                default:
                                                    $activityClass = '';
                                                    $description = htmlspecialchars($activity['description'] ?? '');
                                                    $title = htmlspecialchars($activity['type'] ?? 'Actividad');
                                            }
                                        ?>
                                            <div class="activity-item <?php echo $activityClass; ?>">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <p class="mb-0 fw-medium">
                                                        <?php echo $title; ?>
                                                    </p>
                                                    <small class="text-muted">
                                                        <?php echo $formattedTime; ?>
                                                    </small>
                                                </div>
                                                <small class="text-muted">
                                                    <?php echo $description; ?>
                                                </small>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center text-muted py-4">No hay actividad reciente disponible.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="chart-container">
                                <h5 class="mb-3 fw-semibold">Notificaciones Importantes</h5>
                                <div class="list-group" id="importantNotificationsList">
                                    <?php if (!empty($dashboardData['notifications'])): ?>
                                        <?php foreach ($dashboardData['notifications'] as $notification):
                                            $badgeHtml = '';
                                            if (isset($notification['count']) && $notification['count'] !== null) {
                                                $badgeClass = ($notification['type'] === 'warning') ? 'warning text-dark' : $notification['type'];
                                                $badgeHtml = '<span class="badge bg-' . $badgeClass . ' rounded-pill">' . htmlspecialchars($notification['count']) . '</span>';
                                            } else {
                                                $badgeHtml = '<i class="bi bi-chevron-right text-muted"></i>';
                                            }
                                        ?>
                                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="fw-bold text-<?php echo htmlspecialchars($notification['type']); ?>">
                                                        <?php echo htmlspecialchars($notification['title']); ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($notification['description']); ?>
                                                    </small>
                                                </div>
                                                <?php echo $badgeHtml; ?>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center text-muted py-4">No hay notificaciones importantes.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="bg-white text-center text-muted py-4 mt-4 border-top">
                    <p class="mb-0">&copy; 2025 Themis | Ministerio de Justicia de Guinea Ecuatorial. Todos los derechos reservados.</p>
                </footer>
            </div>
        </div>
    </div>







    <?php

    // Asegúrese de que 'conexion.php' contiene la configuración correcta de $dsn, $user, $pass, $options.
    require_once('../includes/conexion.php');

    // Si la conexión no se maneja en 'conexion.php' o si necesita redefinir $pdo:
    // $pdo = null;
    $dashboardData = [];
    $asideData = []; // Para datos del aside
    $error_message = '';

    try {

        $pdo = new PDO($dsn, $user, $pass, $options);



        // Función auxiliar para procesar resultados
        $process_chart_data = function ($stmt, $title) {
            $results = $stmt->fetchAll();
            $labels = array_column($results, 'label');
            $data = array_map('intval', array_column($results, 'data_count'));
            return ['labels' => $labels, 'data' => $data, 'title' => $title];
        };

        // --- Consultas para Gráficos ---

        // 1. Distribución por Estado Laboral (Usando todos los estados de tbl_funcionarios)
        $stmt_estado = $pdo->query("
            SELECT 
                Estado_Laboral AS label, 
                COUNT(*) AS data_count 
            FROM tbl_funcionarios 
            GROUP BY Estado_Laboral
        ");
        $dashboardData['funcionarioDistribution'] = $process_chart_data($stmt_estado, 'Distribución de Funcionarios por Estado Laboral');

        // 2. Distribución por Género (Usando todos los géneros de tbl_funcionarios)
        $stmt_genero = $pdo->query("
            SELECT 
                Genero AS label, 
                COUNT(*) AS data_count 
            FROM tbl_funcionarios 
            GROUP BY Genero
        ");
        $dashboardData['generoDistribution'] = $process_chart_data($stmt_genero, 'Distribución de Funcionarios por Género');

        // 3. Distribución por Destino (Histórico de Asignaciones)
        // ESTA CONSULTA INCLUYE TODOS LOS DESTINOS QUE ALGUNA VEZ TUVIERON ASIGNACIONES.
        $stmt_destino = $pdo->query("
            SELECT 
                t2.Nombre_Destino AS label, 
                COUNT(t1.ID_Funcionario) AS data_count
            FROM tbl_asignaciones t1
            JOIN tbl_destinos t2 ON t1.ID_Destino = t2.ID_Destino
            GROUP BY t2.Nombre_Destino
        ");
        $dashboardData['destinoDistribution'] = $process_chart_data($stmt_destino, 'Distribución de Destinos (Por Asignaciones)');
        // ------------------------------

        // --- Consulta para Aside (Ejemplo: Total Activos) ---

        // Total de funcionarios con estado 'Activo'
        $stmt_total_activos = $pdo->query("SELECT COUNT(*) AS total FROM tbl_funcionarios WHERE Estado_Laboral = 'Activo'");
        $asideData['totalActivos'] = $stmt_total_activos->fetchColumn();

        // Total de Quejas/Sugerencias Pendientes
        $stmt_total_qs_pendientes = $pdo->query("SELECT COUNT(*) AS total FROM tbl_quejas_sugerencias WHERE Estado = 'pendiente'");
        $asideData['totalQSPendientes'] = $stmt_total_qs_pendientes->fetchColumn();

        // Total de Permisos Pendientes
        $stmt_total_permisos_pendientes = $pdo->query("SELECT COUNT(*) AS total FROM tbl_permisos WHERE Estado_Permiso = 'Pendiente'");
        $asideData['totalPermisosPendientes'] = $stmt_total_permisos_pendientes->fetchColumn();
    } catch (PDOException $e) {
        // En caso de error, inicializamos las variables para evitar fallos en el JSON
        $error_message = "Error de conexión o consulta a la base de datos: " . $e->getMessage();
        $dashboardData = [];
        $asideData = ['totalActivos' => 0, 'totalQSPendientes' => 0, 'totalPermisosPendientes' => 0];
    }
    ?>



    <script>
        // Data pasada desde PHP (debe contener la data de la DB)
        const dashboardData = <?php echo json_encode($dashboardData ?? []); ?>;
        const errorMessage = "<?php echo htmlspecialchars($error_message); ?>";

        let funcionariosChart;
        // Se inicializará en renderDashboard() con el elemento <h5 id="chartTitle">
        let chartTitleElement;

        // --- ESTRUCTURA DE DATOS DE RESERVA (FALLBACK) ---

        const defaultDashboardData = {
            'funcionarioDistribution': {
                labels: ['Activo', 'Baja Temporal', 'Jubilado', 'Cesado', 'Permiso', 'Vacaciones'],
                data: [0, 0, 0, 0, 0, 0],
                title: 'Distribución de Funcionarios por Estado Laboral' // Título por defecto
            },
            'generoDistribution': {
                labels: ['Masculino', 'Femenino', 'Otro'],
                data: [0, 0, 0],
                title: 'Distribución de Funcionarios por Género' // Título por defecto
            },
            'destinoDistribution': {
                labels: ['Juzgado de Malabo', 'Ministerio de Justicia de Malabo', 'Otros Destinos'],
                data: [0, 0, 0],
                title: 'Distribución de Destinos (Por Asignaciones)' // Título por defecto
            }
        };

        // 1. Mapeo de claves: Radio Button Value -> Clave real en dashboardData
        const dataKeyMap = {
            'estado': 'funcionarioDistribution',
            'genero': 'generoDistribution',
            'destino': 'destinoDistribution'
        };

        // Colores fijos (Mantenidos)
        const backgroundColorsMap = {
            'Activo': 'rgba(5, 150, 105, 0.8)',
            'Permiso': 'rgba(217, 119, 6, 0.8)',
            'Vacaciones': 'rgba(14, 165, 233, 0.8)',
            'Baja Temporal': 'rgba(245, 158, 11, 0.8)',
            'Jubilado': 'rgba(71, 85, 105, 0.8)',
            'Cesado': 'rgba(220, 38, 38, 0.8)',
            'Masculino': 'rgba(54, 162, 235, 0.8)',
            'Femenino': 'rgba(255, 99, 132, 0.8)',
            // ... otros colores ...
            'Juzgado de Mlabo': 'rgba(255, 159, 64, 0.8)', // Asegúrese de unificar si es 'Mlabo' o 'Malabo'
            'Ministerio de Justicia de Malabo': 'rgba(75, 192, 192, 0.8)',
            'Otro': 'rgba(100, 116, 139, 0.8)',
            'Otros Destinos': 'rgba(100, 116, 139, 0.8)'
        };


        // Función principal para actualizar el gráfico basada en el filtro
        function updateChart(dataKey) {

            if (!dataKey || dataKey === 'on' || dataKey === '') {
                dataKey = 'estado';
                console.warn("updateChart() fue llamado sin una clave válida. Usando 'estado' por defecto.");
            }

            const dataKeyInDashboard = dataKeyMap[dataKey];

            if (!dataKeyInDashboard) {
                console.error(`ERROR: La clave '${dataKey}' no existe en dataKeyMap. Deteniendo actualización.`);
                return;
            }

            // Obtener el conjunto de datos (Real de PHP si existe, o Fallback)
            const dataSet = dashboardData[dataKeyInDashboard] || defaultDashboardData[dataKeyInDashboard];

            if (!dataSet || !dataSet.labels || !dataSet.data) {
                console.error(`ERROR: No se encontró data (ni siquiera fallback) para la clave: ${dataKeyInDashboard}.`);
                return;
            }


            let title = dataSet.title || 'Distribución de Funcionarios';

            const labels = dataSet.labels;
            const data = dataSet.data;
            const colors = labels.map(label => backgroundColorsMap[label] || 'rgba(150, 150, 150, 0.8)');

            // 3. ACTUALIZAR EL TÍTULO y datos del Chart.js
            if (chartTitleElement) {
                // AQUÍ SE APLICA EL CAMBIO DEL TÍTULO
                chartTitleElement.textContent = title;
            }

            if (funcionariosChart) {
                funcionariosChart.data.labels = labels;
                funcionariosChart.data.datasets[0].data = data;
                funcionariosChart.data.datasets[0].backgroundColor = colors;
                funcionariosChart.update();
            }
        }


        function renderDashboard() {
            if (errorMessage) {
                console.error("Error al cargar el dashboard:", errorMessage);
                return;
            }

            // 1. OBTENER REFERENCIA AL TÍTULO
            chartTitleElement = document.getElementById('chartTitle');
            const ctx = document.getElementById('funcionariosChart')?.getContext('2d');
            if (!ctx) return;

            // Si el chart ya existe, solo actualizamos los datos
            if (funcionariosChart) {
                updateChart('estado');
            } else {
                // Lógica de creación del Chart.js (Mantenida)
                const initialDataKey = dataKeyMap['estado'];
                const initialData = dashboardData[initialDataKey] || defaultDashboardData[initialDataKey];
                const initialLabels = initialData.labels;
                const initialDataArray = initialData.data;
                const initialColors = initialLabels.map(label => backgroundColorsMap[label] || 'rgba(150, 150, 150, 0.8)');

                funcionariosChart = new Chart(ctx, {
                    // ... opciones del chart ...
                    type: 'doughnut',
                    data: {
                        labels: initialLabels,
                        datasets: [{
                            data: initialDataArray,
                            backgroundColor: initialColors,
                            borderColor: '#ffffff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 20
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        let label = tooltipItem.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += tooltipItem.raw + ' funcionarios';
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
                // ✅ Establecer el título inicial
                if (chartTitleElement) {
                    chartTitleElement.textContent = initialData.title;
                }
            }


            // --- Conectar Botones de Radio ---
            const radioButtons = document.querySelectorAll('input[name="chartOptions"]');
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    updateChart(this.value);
                });
            });
        }

        // El resto de funciones (refreshData, DOMContentLoaded, sidebar logic) se mantiene igual.
        function refreshData() {
            /* ... */ }

        document.addEventListener('DOMContentLoaded', function() {
            renderDashboard();

        });
    </script>









</body>

</html>