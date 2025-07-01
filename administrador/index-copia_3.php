<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - Themis | Ministerio de Justicia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #475569;
            --accent-color: #0ea5e9;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --border-color: #e2e8f0;
            --sidebar-bg: #334155;
            --sidebar-hover: #475569;
            --sidebar-width-desktop: 280px;
            --sidebar-width-tablet: 240px;
            --sidebar-width-mobile: 100%;
        }

        body {
            background-color: #f1f5f9;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden; /* Evita el scroll horizontal en el body */
        }

        .sidebar {
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, var(--dark-color) 100%);
            min-height: 100vh;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.08);
            position: fixed;
            width: var(--sidebar-width-desktop);
            z-index: 1000;
            transition: all 0.3s ease;
            left: 0;
            top: 0;
        }

        .sidebar .nav-link {
            color: #cbd5e1;
            padding: 12px 24px;
            margin: 2px 12px;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: #ffffff;
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .main-content {
            margin-left: var(--sidebar-width-desktop);
            background-color: #f8fafc;
            min-height: 100vh;
            transition: all 0.3s ease;
            width: calc(100% - var(--sidebar-width-desktop));
        }

        .top-navbar {
            background: white;
            padding: 16px 32px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .user-profile:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
        }

        .header-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--dark-color);
            padding: 32px;
            margin-bottom: 24px;
            border-radius: 0 0 24px 24px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 16px;
        }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .stat-change {
            font-size: 0.8rem;
            font-weight: 600;
        }

        .chart-container {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border-color);
            margin-bottom: 24px;
        }

        .chart-wrapper {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .progress-custom {
            height: 8px;
            border-radius: 10px;
            background-color: #f1f5f9;
        }

        .progress-bar-custom {
            border-radius: 10px;
            transition: width 0.8s ease;
        }

        .table-custom {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border-color);
            overflow-x: auto;
        }

        .table-custom thead {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--dark-color);
        }

        .badge-custom {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .btn-refresh {
            transition: transform 0.3s ease;
        }

        .btn-refresh.refreshing {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .logo-section {
            padding: 24px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 16px;
        }

        .quick-action-btn {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 16px 12px;
            text-decoration: none;
            color: var(--dark-color);
            transition: all 0.3s ease;
            display: block;
            text-align: center;
            font-size: 0.85rem;
        }

        .quick-action-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.15);
        }

        .notification-dot {
            width: 8px;
            height: 8px;
            background-color: var(--danger-color);
            border-radius: 50%;
            position: absolute;
            top: 8px;
            right: 8px;
        }

        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .report-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .report-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            border-color: var(--primary-color);
        }

        .breadcrumb-custom {
            background: transparent;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-custom .breadcrumb-item+.breadcrumb-item::before {
            content: "›";
            color: #94a3b8;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            border-radius: 12px;
            padding: 8px;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 8px 12px;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f1f5f9;
            color: var(--primary-color);
        }

        .activity-item {
            padding: 16px;
            border-left: 3px solid var(--border-color);
            margin-bottom: 12px;
            background: #f8fafc;
            border-radius: 0 8px 8px 0;
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            border-left-color: var(--primary-color);
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .activity-item.recent {
            border-left-color: var(--success-color);
        }

        .activity-item.warning {
            border-left-color: var(--warning-color);
        }

        .activity-item.danger {
            border-left-color: var(--danger-color);
        }

        /* Menu toggle button for mobile */
        .menu-toggle {
            display: none;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .menu-toggle:hover {
            background: var(--accent-color);
            transform: scale(1.05);
        }

        /* Overlay for mobile sidebar */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* Container para mejor organización */
        .container-fluid {
            padding-left: 0;
            padding-right: 0;
        }

        /* RESPONSIVE BREAKPOINTS */

        /* Large Desktop - 1200px and up */
        @media (min-width: 1200px) {
            .sidebar {
                width: var(--sidebar-width-desktop);
            }

            .main-content {
                margin-left: var(--sidebar-width-desktop);
                width: calc(100% - var(--sidebar-width-desktop));
            }

            .top-navbar {
                padding: 16px 48px;
            }
        }

        /* Desktop - 992px to 1199px */
        @media (min-width: 992px) and (max-width: 1199px) {
            .sidebar {
                width: var(--sidebar-width-tablet);
            }

            .main-content {
                margin-left: var(--sidebar-width-tablet);
                width: calc(100% - var(--sidebar-width-tablet));
            }

            .sidebar .nav-link {
                padding: 10px 16px;
                font-size: 0.9rem;
            }

            .top-navbar {
                padding: 16px 32px;
            }
        }

        /* Tablet - 768px to 991px */
        @media (min-width: 768px) and (max-width: 991px) {
            .sidebar {
                width: var(--sidebar-width-tablet);
            }

            .main-content {
                margin-left: var(--sidebar-width-tablet);
                width: calc(100% - var(--sidebar-width-tablet));
            }

            .sidebar .nav-link {
                padding: 8px 12px;
                font-size: 0.85rem;
            }

            .sidebar .nav-link i {
                width: 18px;
            }

            .top-navbar {
                padding: 12px 24px;
            }

            .header-section {
                padding: 24px;
            }

            .stat-card {
                padding: 20px;
            }

            .chart-wrapper {
                height: 250px;
            }
        }

        /* Mobile - up to 767px */
        @media (max-width: 767px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
                max-width: 85vw;
                position: fixed;
                height: 100vh;
                top: 0;
                left: 0;
                overflow-y: auto;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .menu-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .top-navbar {
                padding: 12px 16px;
            }

            .header-section {
                padding: 20px 16px;
            }

            .stat-card {
                padding: 16px;
                margin-bottom: 16px;
            }

            .stat-number {
                font-size: 1.8rem;
            }

            .stat-icon {
                width: 48px;
                height: 48px;
                font-size: 1.2rem;
            }

            .chart-container {
                padding: 16px;
                margin: 0 16px 16px 16px;
            }

            .chart-wrapper {
                height: 200px;
            }

            .reports-grid {
                grid-template-columns: 1fr;
                margin: 0 16px;
                gap: 12px;
            }

            .table-custom {
                margin: 0 16px;
            }

            /* Stack navbar items vertically on small screens */
            .top-navbar .d-flex {
                flex-direction: column;
                gap: 12px;
                align-items: stretch;
            }

            .top-navbar .d-flex.justify-content-between {
                flex-direction: row;
                align-items: center;
            }
        }

        /* Small Mobile - up to 480px */
        @media (max-width: 480px) {
            .sidebar {
                width: 100vw;
                max-width: none;
            }

            .stat-number {
                font-size: 1.6rem;
            }

            .sidebar .nav-link {
                padding: 12px 16px;
                margin: 2px 8px;
            }

            .logo-section {
                padding: 16px;
            }

            .top-navbar {
                padding: 8px 12px;
            }

            .header-section {
                padding: 16px 12px;
                border-radius: 0;
            }

            .chart-container,
            .table-custom,
            .reports-grid {
                margin: 0 12px 12px 12px;
            }

            .chart-wrapper {
                height: 180px;
            }
        }

        /* Utilities for responsive behavior */
        .mobile-hidden {
            display: block;
        }

        .mobile-only {
            display: none;
        }

        @media (max-width: 767px) {
            .mobile-hidden {
                display: none;
            }

            .mobile-only {
                display: block;
            }
        }

        /* Flex utilities for better space distribution */
        .flex-fill {
            flex: 1 1 auto;
        }

        .w-100 {
            width: 100% !important;
        }

        /* Ensure content doesn't overflow */
        .main-content * {
            max-width: 100%;
            box-sizing: border-box;
        }

        /* Smooth transitions for all responsive changes */
        * {
            transition: margin 0.3s ease, width 0.3s ease, padding 0.3s ease;
        }

        /* Specific for search input and buttons in top-navbar */
        @media (max-width: 767px) {
            .top-navbar .d-flex.align-items-center.gap-3 {
                flex-direction: column;
                align-items: stretch;
                width: 100%; /* Ensure it takes full width to stack */
                margin-top: 10px; /* Space between breadcrumb and input */
            }
            .top-navbar .input-group {
                width: 100% !important; /* Force input to 100% width */
            }
            .top-navbar .btn-outline-primary,
            .top-navbar .dropdown {
                width: 100%; /* Force buttons and dropdown to 100% width */
                margin-top: 10px; /* Add some space between stacked items */
            }
            /* Adjust the row below the main d-flex to ensure proper spacing on small screens */
            .top-navbar > .d-flex.justify-content-between {
                flex-wrap: wrap; /* Allow wrapping */
            }
            .top-navbar > .d-flex.justify-content-between > div:first-child {
                flex-basis: 100%; /* Breadcrumb takes full width */
                margin-bottom: 10px; /* Space below breadcrumb */
            }
            .top-navbar > .d-flex.justify-content-between > div:last-child {
                flex-basis: 100%; /* The container for search/buttons takes full width */
            }
        }
    </style>
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div> <div class="container-fluid p-0">
        <div class="row g-0">
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
                            <div class="text-white fw-semibold">Juan Doe</div>
                            <small class="text-light opacity-75">Administrador</small>
                        </div>
                        <i class="bi bi-chevron-down text-light"></i>
                    </div>
                </div>

                <nav class="nav flex-column px-2">
                    <a class="nav-link active" href="#dashboard">
                        <i class="bi bi-speedometer2 me-3"></i> Dashboard
                    </a>
                    <a class="nav-link" href="#funcionarios">
                        <i class="bi bi-people me-3"></i> Funcionarios
                        <span class="badge bg-primary ms-auto" id="totalFuncionariosSidebar">247</span>
                    </a>
                    <a class="nav-link position-relative" href="#permisos">
                        <i class="bi bi-calendar-check me-3"></i> Permisos
                        <span class="notification-dot" id="permisosNotifDot"></span>
                        <span class="badge bg-danger ms-auto" id="permisosPendientesSidebar">7</span>
                    </a>
                    <a class="nav-link" href="#asignaciones">
                        <i class="bi bi-diagram-3 me-3"></i> Asignaciones
                    </a>
                    <a class="nav-link" href="#destinos">
                        <i class="bi bi-geo-alt me-3"></i> Destinos
                        <span class="badge bg-secondary ms-auto" id="totalDestinosSidebar">34</span>
                    </a>
                    <a class="nav-link" href="#departamentos">
                        <i class="bi bi-building me-3"></i> Departamentos
                    </a>
                    <a class="nav-link" href="#cargos">
                        <i class="bi bi-briefcase me-3"></i> Cargos
                    </a>
                    <a class="nav-link" href="#formacion">
                        <i class="bi bi-mortarboard me-3"></i> Formación
                    </a>
                    <a class="nav-link" href="#capacitaciones">
                        <i class="bi bi-award me-3"></i> Capacitaciones
                    </a>
                    <a class="nav-link" href="#reportes">
                        <i class="bi bi-file-earmark-text me-3"></i> Reportes
                    </a>
                    <a class="nav-link" href="#auditoria">
                        <i class="bi bi-shield-check me-3"></i> Auditoría
                    </a>
                    <a class="nav-link" href="#configuracion">
                        <i class="bi bi-gear me-3"></i> Configuración
                    </a>
                </nav>
            </div>

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
                                    <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

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
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="stat-number text-primary" id="statTotalFuncionarios">247</div>
                                <div class="stat-label">Total Funcionarios</div>
                                <div class="stat-change text-success">
                                    <i class="bi bi-arrow-up"></i> <span id="statNewFuncionarios">+12</span> este mes
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-person-check"></i>
                                </div>
                                <div class="stat-number text-success" id="statFuncionariosActivos">229</div>
                                <div class="stat-label">Funcionarios Activos</div>
                                <div class="stat-change text-success">
                                    <i class="bi bi-arrow-up"></i> <span id="statActivosPercent">92.7%</span> del total
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                                <div class="stat-number text-warning" id="statPermisosEsteMes">23</div>
                                <div class="stat-label">Permisos Este Mes</div>
                                <div class="stat-change text-danger">
                                    <i class="bi bi-exclamation-circle"></i> <span id="statPermisosPendientes">7</span> pendientes
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-info bg-opacity-10 text-info">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div class="stat-number text-info" id="statDestinosActivos">34</div>
                                <div class="stat-label">Destinos Activos</div>
                                <div class="stat-change text-info">
                                    <i class="bi bi-building"></i> <span id="statJuzgados">15</span> Juzgados
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-8">
                            <div class="chart-container">
                                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                    <h5 class="mb-0 fw-semibold">Distribución de Funcionarios por Estado</h5>
                                    <div class="btn-group btn-group-sm mt-2 mt-md-0" role="group">
                                        <input type="radio" class="btn-check" name="chartOptions" id="option1" checked>
                                        <label class="btn btn-outline-primary" for="option1">Estado</label>
                                        <input type="radio" class="btn-check" name="chartOptions" id="option2">
                                        <label class="btn btn-outline-primary" for="option2">Género</label>
                                        <input type="radio" class="btn-check" name="chartOptions" id="option3">
                                        <label class="btn btn-outline-primary" for="option3">Destino</label>
                                    </div>
                                </div>
                                <div class="chart-wrapper">
                                    <canvas id="funcionariosChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="chart-container">
                                <h5 class="mb-3 fw-semibold">Acciones Rápidas</h5>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="#" class="quick-action-btn">
                                            <i class="bi bi-person-plus fa-2x mb-2 d-block"></i>
                                            <small>Nuevo Funcionario</small>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="#" class="quick-action-btn">
                                            <i class="bi bi-calendar-plus fa-2x mb-2 d-block"></i>
                                            <small>Nuevo Permiso</small>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data for charts (normally this would come from the backend)
        const funcionarioData = {
            estados: {
                labels: ['Activo', 'De Baja', 'Permiso', 'Jubilado'],
                data: [229, 10, 7, 1],
                backgroundColor: ['#059669', '#dc2626', '#d97706', '#475569'],
                borderColor: ['#059669', '#dc2626', '#d97706', '#475569']
            },
            genero: {
                labels: ['Masculino', 'Femenino'],
                data: [150, 97],
                backgroundColor: ['#2563eb', '#ea580c'],
                borderColor: ['#2563eb', '#ea580c']
            },
            destino: {
                labels: ['Malabo', 'Bata', 'Mongomo', 'Ebibeyin', 'Otras'],
                data: [80, 60, 40, 30, 37],
                backgroundColor: ['#0ea5e9', '#6d28d9', '#eab308', '#16a34a', '#a16207'],
                borderColor: ['#0ea5e9', '#6d28d9', '#eab308', '#16a34a', '#a16207']
            }
        };

        let funcionariosChart;

        function renderChart(type) {
            const ctx = document.getElementById('funcionariosChart').getContext('2d');
            let dataToShow;
            let chartType = 'doughnut'; // Default to doughnut for distribution charts

            switch (type) {
                case 'Estado':
                    dataToShow = funcionarioData.estados;
                    break;
                case 'Género':
                    dataToShow = funcionarioData.genero;
                    break;
                case 'Destino':
                    dataToShow = funcionarioData.destino;
                    // For 'Destino', a bar chart might be more readable if many categories
                    chartType = 'bar';
                    break;
                default:
                    dataToShow = funcionarioData.estados;
            }

            if (funcionariosChart) {
                funcionariosChart.destroy();
            }

            funcionariosChart = new Chart(ctx, {
                type: chartType,
                data: {
                    labels: dataToShow.labels,
                    datasets: [{
                        label: 'Número de Funcionarios',
                        data: dataToShow.data,
                        backgroundColor: dataToShow.backgroundColor,
                        borderColor: dataToShow.borderColor,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += context.parsed;
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: chartType === 'bar' ? {
                        y: {
                            beginAtZero: true
                        }
                    } : {}
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            renderChart('Estado'); // Initial chart load

            // Event listeners for chart options
            document.querySelectorAll('input[name="chartOptions"]').forEach(radio => {
                radio.addEventListener('change', (event) => {
                    renderChart(event.target.nextElementSibling.textContent);
                });
            });

            // Sidebar toggle for mobile
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            });

            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });

            // Close sidebar when a nav link is clicked on mobile
            document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 767) {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('show');
                    }
                });
            });
        });

        function refreshData() {
            const refreshBtn = document.querySelector('.btn-refresh');
            refreshBtn.classList.add('refreshing');
            // Simulate data fetching
            setTimeout(() => {
                // Update specific data points with new values for demonstration
                document.getElementById('statTotalFuncionarios').textContent = '250';
                document.getElementById('statNewFuncionarios').textContent = '+15';
                document.getElementById('statFuncionariosActivos').textContent = '235';
                document.getElementById('statActivosPercent').textContent = '94%';
                document.getElementById('statPermisosEsteMes').textContent = '25';
                document.getElementById('statPermisosPendientes').textContent = '5';
                document.getElementById('permisosPendientesSidebar').textContent = '5';
                document.getElementById('permisosNotifDot').style.display = 'none'; // Hide if no pending
                document.getElementById('statDestinosActivos').textContent = '35';
                document.getElementById('totalDestinosSidebar').textContent = '35';

                // Re-render chart with potentially new data (using current dummy data for this example)
                renderChart(document.querySelector('input[name="chartOptions"]:checked + label').textContent);

                refreshBtn.classList.remove('refreshing');
            }, 1000); // Simulate 1 second loading
        }
    </script>
</body>

</html><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - Themis | Ministerio de Justicia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #475569;
            --accent-color: #0ea5e9;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --border-color: #e2e8f0;
            --sidebar-bg: #334155;
            --sidebar-hover: #475569;
            --sidebar-width-desktop: 280px;
            --sidebar-width-tablet: 240px;
            --sidebar-width-mobile: 100%;
        }

        body {
            background-color: #f1f5f9;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden; /* Evita el scroll horizontal en el body */
        }

        .sidebar {
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, var(--dark-color) 100%);
            min-height: 100vh;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.08);
            position: fixed;
            width: var(--sidebar-width-desktop);
            z-index: 1000;
            transition: all 0.3s ease;
            left: 0;
            top: 0;
        }

        .sidebar .nav-link {
            color: #cbd5e1;
            padding: 12px 24px;
            margin: 2px 12px;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: #ffffff;
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .main-content {
            margin-left: var(--sidebar-width-desktop);
            background-color: #f8fafc;
            min-height: 100vh;
            transition: all 0.3s ease;
            width: calc(100% - var(--sidebar-width-desktop));
        }

        .top-navbar {
            background: white;
            padding: 16px 32px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .user-profile:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
        }

        .header-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--dark-color);
            padding: 32px;
            margin-bottom: 24px;
            border-radius: 0 0 24px 24px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 16px;
        }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .stat-change {
            font-size: 0.8rem;
            font-weight: 600;
        }

        .chart-container {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border-color);
            margin-bottom: 24px;
        }

        .chart-wrapper {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .progress-custom {
            height: 8px;
            border-radius: 10px;
            background-color: #f1f5f9;
        }

        .progress-bar-custom {
            border-radius: 10px;
            transition: width 0.8s ease;
        }

        .table-custom {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border-color);
            overflow-x: auto;
        }

        .table-custom thead {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--dark-color);
        }

        .badge-custom {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .btn-refresh {
            transition: transform 0.3s ease;
        }

        .btn-refresh.refreshing {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .logo-section {
            padding: 24px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 16px;
        }

        .quick-action-btn {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 16px 12px;
            text-decoration: none;
            color: var(--dark-color);
            transition: all 0.3s ease;
            display: block;
            text-align: center;
            font-size: 0.85rem;
        }

        .quick-action-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.15);
        }

        .notification-dot {
            width: 8px;
            height: 8px;
            background-color: var(--danger-color);
            border-radius: 50%;
            position: absolute;
            top: 8px;
            right: 8px;
        }

        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .report-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .report-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            border-color: var(--primary-color);
        }

        .breadcrumb-custom {
            background: transparent;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-custom .breadcrumb-item+.breadcrumb-item::before {
            content: "›";
            color: #94a3b8;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            border-radius: 12px;
            padding: 8px;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 8px 12px;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f1f5f9;
            color: var(--primary-color);
        }

        .activity-item {
            padding: 16px;
            border-left: 3px solid var(--border-color);
            margin-bottom: 12px;
            background: #f8fafc;
            border-radius: 0 8px 8px 0;
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            border-left-color: var(--primary-color);
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .activity-item.recent {
            border-left-color: var(--success-color);
        }

        .activity-item.warning {
            border-left-color: var(--warning-color);
        }

        .activity-item.danger {
            border-left-color: var(--danger-color);
        }

        /* Menu toggle button for mobile */
        .menu-toggle {
            display: none;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .menu-toggle:hover {
            background: var(--accent-color);
            transform: scale(1.05);
        }

        /* Overlay for mobile sidebar */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* Container para mejor organización */
        .container-fluid {
            padding-left: 0;
            padding-right: 0;
        }

        /* RESPONSIVE BREAKPOINTS */

        /* Large Desktop - 1200px and up */
        @media (min-width: 1200px) {
            .sidebar {
                width: var(--sidebar-width-desktop);
            }

            .main-content {
                margin-left: var(--sidebar-width-desktop);
                width: calc(100% - var(--sidebar-width-desktop));
            }

            .top-navbar {
                padding: 16px 48px;
            }
        }

        /* Desktop - 992px to 1199px */
        @media (min-width: 992px) and (max-width: 1199px) {
            .sidebar {
                width: var(--sidebar-width-tablet);
            }

            .main-content {
                margin-left: var(--sidebar-width-tablet);
                width: calc(100% - var(--sidebar-width-tablet));
            }

            .sidebar .nav-link {
                padding: 10px 16px;
                font-size: 0.9rem;
            }

            .top-navbar {
                padding: 16px 32px;
            }
        }

        /* Tablet - 768px to 991px */
        @media (min-width: 768px) and (max-width: 991px) {
            .sidebar {
                width: var(--sidebar-width-tablet);
            }

            .main-content {
                margin-left: var(--sidebar-width-tablet);
                width: calc(100% - var(--sidebar-width-tablet));
            }

            .sidebar .nav-link {
                padding: 8px 12px;
                font-size: 0.85rem;
            }

            .sidebar .nav-link i {
                width: 18px;
            }

            .top-navbar {
                padding: 12px 24px;
            }

            .header-section {
                padding: 24px;
            }

            .stat-card {
                padding: 20px;
            }

            .chart-wrapper {
                height: 250px;
            }
        }

        /* Mobile - up to 767px */
        @media (max-width: 767px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
                max-width: 85vw;
                position: fixed;
                height: 100vh;
                top: 0;
                left: 0;
                overflow-y: auto;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .menu-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .top-navbar {
                padding: 12px 16px;
            }

            .header-section {
                padding: 20px 16px;
            }

            .stat-card {
                padding: 16px;
                margin-bottom: 16px;
            }

            .stat-number {
                font-size: 1.8rem;
            }

            .stat-icon {
                width: 48px;
                height: 48px;
                font-size: 1.2rem;
            }

            .chart-container {
                padding: 16px;
                margin: 0 16px 16px 16px;
            }

            .chart-wrapper {
                height: 200px;
            }

            .reports-grid {
                grid-template-columns: 1fr;
                margin: 0 16px;
                gap: 12px;
            }

            .table-custom {
                margin: 0 16px;
            }

            /* Stack navbar items vertically on small screens */
            .top-navbar .d-flex {
                flex-direction: column;
                gap: 12px;
                align-items: stretch;
            }

            .top-navbar .d-flex.justify-content-between {
                flex-direction: row;
                align-items: center;
            }
        }

        /* Small Mobile - up to 480px */
        @media (max-width: 480px) {
            .sidebar {
                width: 100vw;
                max-width: none;
            }

            .stat-number {
                font-size: 1.6rem;
            }

            .sidebar .nav-link {
                padding: 12px 16px;
                margin: 2px 8px;
            }

            .logo-section {
                padding: 16px;
            }

            .top-navbar {
                padding: 8px 12px;
            }

            .header-section {
                padding: 16px 12px;
                border-radius: 0;
            }

            .chart-container,
            .table-custom,
            .reports-grid {
                margin: 0 12px 12px 12px;
            }

            .chart-wrapper {
                height: 180px;
            }
        }

        /* Utilities for responsive behavior */
        .mobile-hidden {
            display: block;
        }

        .mobile-only {
            display: none;
        }

        @media (max-width: 767px) {
            .mobile-hidden {
                display: none;
            }

            .mobile-only {
                display: block;
            }
        }

        /* Flex utilities for better space distribution */
        .flex-fill {
            flex: 1 1 auto;
        }

        .w-100 {
            width: 100% !important;
        }

        /* Ensure content doesn't overflow */
        .main-content * {
            max-width: 100%;
            box-sizing: border-box;
        }

        /* Smooth transitions for all responsive changes */
        * {
            transition: margin 0.3s ease, width 0.3s ease, padding 0.3s ease;
        }

        /* Specific for search input and buttons in top-navbar */
        @media (max-width: 767px) {
            .top-navbar .d-flex.align-items-center.gap-3 {
                flex-direction: column;
                align-items: stretch;
                width: 100%; /* Ensure it takes full width to stack */
                margin-top: 10px; /* Space between breadcrumb and input */
            }
            .top-navbar .input-group {
                width: 100% !important; /* Force input to 100% width */
            }
            .top-navbar .btn-outline-primary,
            .top-navbar .dropdown {
                width: 100%; /* Force buttons and dropdown to 100% width */
                margin-top: 10px; /* Add some space between stacked items */
            }
            /* Adjust the row below the main d-flex to ensure proper spacing on small screens */
            .top-navbar > .d-flex.justify-content-between {
                flex-wrap: wrap; /* Allow wrapping */
            }
            .top-navbar > .d-flex.justify-content-between > div:first-child {
                flex-basis: 100%; /* Breadcrumb takes full width */
                margin-bottom: 10px; /* Space below breadcrumb */
            }
            .top-navbar > .d-flex.justify-content-between > div:last-child {
                flex-basis: 100%; /* The container for search/buttons takes full width */
            }
        }
    </style>
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div> <div class="container-fluid p-0">
        <div class="row g-0">
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
                            <div class="text-white fw-semibold">Juan Doe</div>
                            <small class="text-light opacity-75">Administrador</small>
                        </div>
                        <i class="bi bi-chevron-down text-light"></i>
                    </div>
                </div>

                <nav class="nav flex-column px-2">
                    <a class="nav-link active" href="#dashboard">
                        <i class="bi bi-speedometer2 me-3"></i> Dashboard
                    </a>
                    <a class="nav-link" href="#funcionarios">
                        <i class="bi bi-people me-3"></i> Funcionarios
                        <span class="badge bg-primary ms-auto" id="totalFuncionariosSidebar">247</span>
                    </a>
                    <a class="nav-link position-relative" href="#permisos">
                        <i class="bi bi-calendar-check me-3"></i> Permisos
                        <span class="notification-dot" id="permisosNotifDot"></span>
                        <span class="badge bg-danger ms-auto" id="permisosPendientesSidebar">7</span>
                    </a>
                    <a class="nav-link" href="#asignaciones">
                        <i class="bi bi-diagram-3 me-3"></i> Asignaciones
                    </a>
                    <a class="nav-link" href="#destinos">
                        <i class="bi bi-geo-alt me-3"></i> Destinos
                        <span class="badge bg-secondary ms-auto" id="totalDestinosSidebar">34</span>
                    </a>
                    <a class="nav-link" href="#departamentos">
                        <i class="bi bi-building me-3"></i> Departamentos
                    </a>
                    <a class="nav-link" href="#cargos">
                        <i class="bi bi-briefcase me-3"></i> Cargos
                    </a>
                    <a class="nav-link" href="#formacion">
                        <i class="bi bi-mortarboard me-3"></i> Formación
                    </a>
                    <a class="nav-link" href="#capacitaciones">
                        <i class="bi bi-award me-3"></i> Capacitaciones
                    </a>
                    <a class="nav-link" href="#reportes">
                        <i class="bi bi-file-earmark-text me-3"></i> Reportes
                    </a>
                    <a class="nav-link" href="#auditoria">
                        <i class="bi bi-shield-check me-3"></i> Auditoría
                    </a>
                    <a class="nav-link" href="#configuracion">
                        <i class="bi bi-gear me-3"></i> Configuración
                    </a>
                </nav>
            </div>

         
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
</body>

</html>