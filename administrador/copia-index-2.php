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
            overflow-x: hidden;
            /* Evita el scroll horizontal en el body */
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
                width: 100%;
                /* Ensure it takes full width to stack */
                margin-top: 10px;
                /* Space between breadcrumb and input */
            }

            .top-navbar .input-group {
                width: 100% !important;
                /* Force input to 100% width */
            }

            .top-navbar .btn-outline-primary,
            .top-navbar .dropdown {
                width: 100%;
                /* Force buttons and dropdown to 100% width */
                margin-top: 10px;
                /* Add some space between stacked items */
            }

            /* Adjust the row below the main d-flex to ensure proper spacing on small screens */
            .top-navbar>.d-flex.justify-content-between {
                flex-wrap: wrap;
                /* Allow wrapping */
            }

            .top-navbar>.d-flex.justify-content-between>div:first-child {
                flex-basis: 100%;
                /* Breadcrumb takes full width */
                margin-bottom: 10px;
                /* Space below breadcrumb */
            }

            .top-navbar>.d-flex.justify-content-between>div:last-child {
                flex-basis: 100%;
                /* The container for search/buttons takes full width */
            }
        }
    </style>
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div> <!-- Overlay para cerrar sidebar en móvil -->

    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
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

            <!-- Main Content -->
            <div class="main-content" id="mainContent">
                <!-- Top Navigation -->
                <div class="top-navbar">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Botón para mostrar/ocultar sidebar en móviles -->
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




                    <form id="filtroForm">
                        <div class="container my-4">
                            <div class="row g-4">
                                <!-- Aquí todos los filtros (igual que antes) -->
                                <!-- Estado Laboral -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-start border-4 border-primary">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary fw-bold d-flex align-items-center">
                                                <i class="bi bi-person-check me-2"></i> Estado Laboral
                                            </h6>
                                            <select class="form-select" name="estado_laboral">
                                                <option value="">-- Seleccionar --</option>
                                                <option value="Activo">Activo</option>
                                                <option value="Baja Temporal">Baja Temporal</option>
                                                <option value="Jubilado">Jubilado</option>
                                                <option value="Cesado">Cesado</option>
                                                <option value="Permiso">Permiso</option>
                                                <option value="Vacaciones">Vacaciones</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Departamento -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-start border-4 border-success">
                                        <div class="card-body">
                                            <h6 class="card-title text-success fw-bold d-flex align-items-center">
                                                <i class="bi bi-building me-2"></i> Departamento
                                            </h6>
                                            <select class="form-select" name="id_departamento">
                                                <option value="">-- Seleccionar --</option>
                                                <!-- Dinámico -->
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cargo -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-start border-4 border-warning">
                                        <div class="card-body">
                                            <h6 class="card-title text-warning fw-bold d-flex align-items-center">
                                                <i class="bi bi-briefcase-fill me-2"></i> Cargo
                                            </h6>
                                            <select class="form-select" name="id_cargo">
                                                <option value="">-- Seleccionar --</option>
                                                <!-- Dinámico -->
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Destino -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-start border-4 border-danger">
                                        <div class="card-body">
                                            <h6 class="card-title text-danger fw-bold d-flex align-items-center">
                                                <i class="bi bi-geo-alt-fill me-2"></i> Destino
                                            </h6>
                                            <select class="form-select" name="id_destino">
                                                <option value="">-- Seleccionar --</option>
                                                <!-- Dinámico -->
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Rango de Fechas -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-start border-4 border-info">
                                        <div class="card-body">
                                            <h6 class="card-title text-info fw-bold d-flex align-items-center">
                                                <i class="bi bi-calendar-range me-2"></i> Fecha de Asignación
                                            </h6>
                                            <div class="d-flex gap-2">
                                                <input type="date" class="form-control" name="fecha_inicio" placeholder="Desde" />
                                                <input type="date" class="form-control" name="fecha_fin" placeholder="Hasta" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reporte General -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-start border-4 border-secondary">
                                        <div class="card-body">
                                            <h6 class="card-title text-secondary fw-bold d-flex align-items-center">
                                                <i class="bi bi-list-check me-2"></i> Reporte General
                                            </h6>
                                            <div class="form-check mt-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="reporte_general"
                                                    value="1"
                                                    id="reporteGeneral" />
                                                <label class="form-check-label" for="reporteGeneral">Mostrar todos los funcionarios</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones -->
                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                                        <i class="bi bi-funnel-fill me-1"></i> Filtrar Resultados
                                    </button>
                                    <button type="button" id="btnLimpiar" class="btn btn-outline-secondary rounded-pill px-4">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Exportar botones -->
                    <div class="container my-3 d-flex justify-content-end gap-2">
                        <button id="btnExportExcel" class="btn btn-success rounded-pill px-4" disabled>
                            <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i> Exportar Excel
                        </button>
                        <button id="btnExportPDF" class="btn btn-danger rounded-pill px-4" disabled>
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Exportar PDF
                        </button>
                    </div>

                    <!-- Tabla resultados -->
                    <div class="container my-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle" id="tablaResultados">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Código</th>
                                        <th>Estado Laboral</th>
                                        <th>Departamento</th>
                                        <th>Cargo</th>
                                        <th>Destino</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <!-- 🔽 Aquí debajo pon el contenedor -->
                            <div id="paginacion" class="d-flex justify-content-center mt-3"></div>
                        </div>
                    </div>

                    <script>
                        let currentFilterData = [];
                        let currentPage = 1;
                        const rowsPerPage = 5;

                        const tbody = document.querySelector('#tablaResultados tbody');
                        const paginationDiv = document.getElementById('paginacion');

                        function renderPagina(page) {
                            tbody.innerHTML = '';
                            const start = (page - 1) * rowsPerPage;
                            const end = start + rowsPerPage;
                            const paginatedData = currentFilterData.slice(start, end);

                            paginatedData.forEach((f, i) => {
                                tbody.innerHTML += `
        <tr>
          <td>${start + i + 1}</td>
          <td>${f.Nombres} ${f.Apellidos}</td>
          <td>${f.Codigo_Funcionario}</td>
          <td>${f.Estado_Laboral}</td>
          <td>${f.Nombre_Departamento || '-'}</td>
          <td>${f.Nombre_Cargo || '-'}</td>
          <td>${f.Nombre_Destino || '-'}</td>
          <td>${f.Fecha_Inicio_Asignacion || '-'}</td>
          <td>${f.Fecha_Fin_Asignacion || '-'}</td>
        </tr>
      `;
                            });

                            renderControles();
                        }

                        function renderControles() {
                            paginationDiv.innerHTML = '';
                            const totalPages = Math.ceil(currentFilterData.length / rowsPerPage);

                            if (totalPages <= 1) return;

                            // Botón Anterior
                            const prevBtn = document.createElement('button');
                            prevBtn.className = 'btn btn-outline-secondary btn-sm me-1';
                            prevBtn.innerHTML = '<i class="bi bi-chevron-left"></i>';
                            prevBtn.disabled = currentPage === 1;
                            prevBtn.onclick = () => {
                                if (currentPage > 1) {
                                    currentPage--;
                                    renderPagina(currentPage);
                                }
                            };
                            paginationDiv.appendChild(prevBtn);

                            // Mostrar máximo 5 botones
                            let startPage = Math.max(1, currentPage - 2);
                            let endPage = Math.min(totalPages, startPage + 4);
                            if (endPage - startPage < 4) {
                                startPage = Math.max(1, endPage - 4);
                            }

                            for (let i = startPage; i <= endPage; i++) {
                                const btn = document.createElement('button');
                                btn.className = `btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-outline-primary'} mx-1`;
                                btn.textContent = i;
                                btn.addEventListener('click', () => {
                                    currentPage = i;
                                    renderPagina(currentPage);
                                });
                                paginationDiv.appendChild(btn);
                            }

                            // Botón Siguiente
                            const nextBtn = document.createElement('button');
                            nextBtn.className = 'btn btn-outline-secondary btn-sm ms-1';
                            nextBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
                            nextBtn.disabled = currentPage === totalPages;
                            nextBtn.onclick = () => {
                                if (currentPage < totalPages) {
                                    currentPage++;
                                    renderPagina(currentPage);
                                }
                            };
                            paginationDiv.appendChild(nextBtn);
                        }

                        document.querySelector('#filtroForm').addEventListener('submit', function(e) {
                            e.preventDefault();
                            const formData = new FormData(this);
                            console.log('🟢 Enviando filtros al servidor...');

                            fetch('../api/buscar_funcionarios23.php', {
                                    method: 'POST',
                                    body: formData,
                                })
                                .then(res => res.json())
                                .then(data => {
                                    console.log('🟢 Respuesta recibida:', data);
                                    if (data.success && data.data.length > 0) {
                                        currentFilterData = data.data;
                                        currentPage = 1;
                                        renderPagina(currentPage);
                                        document.getElementById('btnExportPDF').disabled = false;
                                    } else {
                                        tbody.innerHTML = `<tr><td colspan="9" class="text-center text-muted">No se encontraron resultados.</td></tr>`;
                                        paginationDiv.innerHTML = '';
                                        currentFilterData = [];
                                        document.getElementById('btnExportPDF').disabled = true;
                                    }
                                })
                                .catch(error => {
                                    console.error('❌ Error en la petición:', error);
                                });
                        });

                        document.getElementById('btnLimpiar').addEventListener('click', function() {
                            document.getElementById('filtroForm').reset();
                            tbody.innerHTML = `<tr><td colspan="9" class="text-center text-muted">Seleccione filtros y pulse "Filtrar Resultados".</td></tr>`;
                            paginationDiv.innerHTML = '';
                            currentFilterData = [];
                            currentPage = 1;
                            document.getElementById('btnExportPDF').disabled = true;
                            console.log('🔄 Formulario y tabla reiniciados.');
                        });

                        document.getElementById('btnExportPDF').addEventListener('click', function() {
                            if (!currentFilterData.length) return alert('⚠️ No hay datos para exportar');

                            const formData = new FormData(document.getElementById('filtroForm'));
                            formData.append('export', 'pdf');
                            console.log('📤 Enviando solicitud para generar PDF...');

                            fetch('../fpdf/buscar_funcionarios.php', {
                                    method: 'POST',
                                    body: formData,
                                })
                                .then(res => res.blob())
                                .then(blob => {
                                    const url = window.URL.createObjectURL(blob);
                                    const a = document.createElement('a');
                                    a.href = url;
                                    a.download = 'funcionarios.pdf';
                                    document.body.appendChild(a);
                                    a.click();
                                    a.remove();
                                    console.log('✅ Descarga del PDF iniciada.');
                                })
                                .catch(error => {
                                    console.error('❌ Error exportando PDF:', error);
                                });
                        });
                    </script>


                    <script>
                        // Ejecutar al cargar la página
                        document.addEventListener('DOMContentLoaded', () => {
                            console.log('🔄 Cargando filtros dinámicos...');

                            fetch('../api/cargar_filtros.php')
                                .then(res => res.json())
                                .then(data => {
                                    if (!data.success) {
                                        console.error('❌ Error en la carga de filtros:', data.message);
                                        return;
                                    }

                                    // Insertar departamentos
                                    const departamentoSelect = document.querySelector('select[name="id_departamento"]');
                                    data.departamentos.forEach(dep => {
                                        const option = document.createElement('option');
                                        option.value = dep.ID_Departamento;
                                        option.textContent = dep.Nombre_Departamento;
                                        departamentoSelect.appendChild(option);
                                    });

                                    // Insertar cargos
                                    const cargoSelect = document.querySelector('select[name="id_cargo"]');
                                    data.cargos.forEach(cargo => {
                                        const option = document.createElement('option');
                                        option.value = cargo.ID_Cargo;
                                        option.textContent = cargo.Nombre_Cargo;
                                        cargoSelect.appendChild(option);
                                    });

                                    // Insertar destinos
                                    const destinoSelect = document.querySelector('select[name="id_destino"]');
                                    data.destinos.forEach(dest => {
                                        const option = document.createElement('option');
                                        option.value = dest.ID_Destino;
                                        option.textContent = dest.Nombre_Destino;
                                        destinoSelect.appendChild(option);
                                    });

                                    console.log('✅ Filtros cargados correctamente.');
                                })
                                .catch(error => {
                                    console.error('❌ Error al cargar filtros dinámicos:', error);
                                });
                        });
                    </script>









                </div>

                <!-- Footer -->
                <footer class="bg-white text-center text-muted py-4 mt-4 border-top">
                    <p class="mb-0">&copy; 2025 Themis | Ministerio de Justicia de Guinea Ecuatorial. Todos los derechos reservados.</p>
                </footer>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Global variable for the chart instance
        let funcionariosChart;

        /**
         * Fetches data from the PHP backend and updates the dashboard.
         */
        async function fetchData() {
            const refreshButton = document.querySelector('.btn-refresh');
            refreshButton.classList.add('refreshing'); // Start animation

            try {
                // Fetch data from your PHP backend
                const response = await fetch('data.php'); // Asegúrate de que 'data.php' esté en la misma carpeta o proporciona la ruta correcta
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();

                if (data.status === 'error') {
                    console.error('Error fetching data from PHP:', data.message);
                    alert('Error al cargar los datos: ' + data.message); // Usar un modal en lugar de alert en producción
                    return;
                }

                const dashboardData = data.data;

                // --- 1. Update Statistics Cards ---
                document.getElementById('statTotalFuncionarios').textContent = dashboardData.totalFuncionarios || 0;
                document.getElementById('totalFuncionariosSidebar').textContent = dashboardData.totalFuncionarios || 0;
                document.getElementById('statFuncionariosActivos').textContent = dashboardData.funcionariosActivos || 0;
                document.getElementById('statPermisosEsteMes').textContent = dashboardData.permisosEsteMes || 0;
                document.getElementById('statPermisosPendientes').textContent = dashboardData.permisosPendientes || 0;
                document.getElementById('permisosPendientesSidebar').textContent = dashboardData.permisosPendientes || 0;

                // Toggle notification dot based on pending permits
                const permisosNotifDot = document.getElementById('permisosNotifDot');
                if (dashboardData.permisosPendientes > 0) {
                    permisosNotifDot.style.display = 'block';
                } else {
                    permisosNotifDot.style.display = 'none';
                }

                document.getElementById('statDestinosActivos').textContent = dashboardData.destinosActivos || 0;
                document.getElementById('totalDestinosSidebar').textContent = dashboardData.destinosActivos || 0;

                // Calculate active percentage and Juzgados (mock data or refine PHP)
                const totalFunc = dashboardData.totalFuncionarios || 0;
                const activeFunc = dashboardData.funcionariosActivos || 0;
                const activePercent = totalFunc > 0 ? ((activeFunc / totalFunc) * 100).toFixed(1) : 0;
                document.getElementById('statActivosPercent').textContent = `${activePercent}%`;

                // For '15 Juzgados', we'll need specific data from destinationTypes
                const juzgadosCount = dashboardData.destinationTypes.find(d => d.Tipo_Destino === 'Juzgado')?.count || 0;
                document.getElementById('statJuzgados').textContent = juzgadosCount;

                // --- 2. Update Doughnut Chart (Funcionarios Distribution) ---
                if (funcionariosChart) {
                    funcionariosChart.data.labels = dashboardData.funcionarioDistribution.labels;
                    funcionariosChart.data.datasets[0].data = dashboardData.funcionarioDistribution.data;
                    // Define colors for each state explicitly or derive from a palette
                    // Ensure you have enough colors for all possible states
                    const backgroundColors = {
                        'Activo': 'rgba(5, 150, 105, 0.8)', // success
                        'En Permiso': 'rgba(217, 119, 6, 0.8)', // warning
                        'Inactivo': 'rgba(220, 38, 38, 0.8)', // danger
                        'Jubilado': 'rgba(71, 85, 105, 0.8)', // secondary
                        'Cesado': 'rgba(220, 38, 38, 0.8)', // danger
                        'Vacaciones': 'rgba(14, 165, 233, 0.8)', // info/accent
                        'Baja Temporal': 'rgba(245, 158, 11, 0.8)', // orange
                        'Otro': 'rgba(100, 116, 139, 0.8)' // slate
                    };
                    funcionariosChart.data.datasets[0].backgroundColor = dashboardData.funcionarioDistribution.labels.map(label => backgroundColors[label] || 'rgba(150, 150, 150, 0.8)'); // Default grey if no specific color
                    funcionariosChart.update();
                } else {
                    // Initialize chart if it doesn't exist
                    const ctx = document.getElementById('funcionariosChart').getContext('2d');
                    const backgroundColors = {
                        'Activo': 'rgba(5, 150, 105, 0.8)', // success
                        'En Permiso': 'rgba(217, 119, 6, 0.8)', // warning
                        'Inactivo': 'rgba(220, 38, 38, 0.8)', // danger
                        'Jubilado': 'rgba(71, 85, 105, 0.8)', // secondary
                        'Cesado': 'rgba(220, 38, 38, 0.8)', // danger
                        'Vacaciones': 'rgba(14, 165, 233, 0.8)', // info/accent
                        'Baja Temporal': 'rgba(245, 158, 11, 0.8)', // orange
                        'Otro': 'rgba(100, 116, 139, 0.8)' // slate
                    };
                    const colors = dashboardData.funcionarioDistribution.labels.map(label => backgroundColors[label] || 'rgba(150, 150, 150, 0.8)');

                    funcionariosChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: dashboardData.funcionarioDistribution.labels,
                            datasets: [{
                                data: dashboardData.funcionarioDistribution.data,
                                backgroundColor: colors,
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
                }

                // --- 3. Update Department Progress Bars ---
                const departmentProgressBars = document.getElementById('departmentProgressBars');
                departmentProgressBars.innerHTML = ''; // Clear previous content

                if (dashboardData.departmentStaff && dashboardData.departmentStaff.length > 0) {
                    const totalStaff = dashboardData.departmentStaff.reduce((sum, dept) => sum + parseInt(dept.num_funcionarios), 0);
                    const progressColors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary']; // Define more colors if needed

                    dashboardData.departmentStaff.forEach((dept, index) => {
                        const percentage = totalStaff > 0 ? ((parseInt(dept.num_funcionarios) / totalStaff) * 100).toFixed(1) : 0;
                        const colorClass = progressColors[index % progressColors.length];
                        departmentProgressBars.innerHTML += `
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-medium">${dept.Nombre_Departamento}</span>
                                    <span class="text-${colorClass.replace('bg-', '')} fw-bold">${dept.num_funcionarios} funcionarios</span>
                                </div>
                                <div class="progress progress-custom">
                                    <div class="progress-bar ${colorClass} progress-bar-custom" style="width: ${percentage}%"></div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    departmentProgressBars.innerHTML = '<div class="text-center text-muted py-4">No hay datos de departamentos disponibles.</div>';
                }


                // --- 4. Update Destination Type Cards ---
                const destinationTypeCards = document.getElementById('destinationTypeCards');
                destinationTypeCards.innerHTML = ''; // Clear previous content

                if (dashboardData.destinationTypes && dashboardData.destinationTypes.length > 0) {
                    const iconMap = {
                        'Juzgado': 'bi-bank',
                        'Tribunal': 'bi-building',
                        'Fiscalia': 'bi-shield-check',
                        'Sede Central': 'bi-house-door',
                        'Oficina Regional': 'bi-diagram-3',
                        'Otro': 'bi-geo-alt'
                    };
                    const colorMap = {
                        'Juzgado': 'primary',
                        'Tribunal': 'success',
                        'Fiscalia': 'warning',
                        'Sede Central': 'info',
                        'Oficina Regional': 'secondary',
                        'Otro': 'dark'
                    };

                    dashboardData.destinationTypes.forEach(dest => {
                        const iconClass = iconMap[dest.Tipo_Destino] || 'bi-question-circle';
                        const colorClass = colorMap[dest.Tipo_Destino] || 'secondary';
                        destinationTypeCards.innerHTML += `
                            <div class="col-4 mb-3">
                                <div class="stat-icon bg-${colorClass} bg-opacity-10 text-${colorClass} mx-auto mb-2" style="width: 48px; height: 48px; font-size: 1.1rem;">
                                    <i class="bi ${iconClass}"></i>
                                </div>
                                <div class="fw-bold text-${colorClass} fs-5">${dest.count}</div>
                                <small class="text-muted fw-medium">${dest.Tipo_Destino}</small>
                            </div>
                        `;
                    });
                } else {
                    destinationTypeCards.innerHTML = '<div class="text-center text-muted py-4">No hay datos de tipos de destino disponibles.</div>';
                }

                // --- 5. Update Recent Activity ---
                const recentActivityList = document.getElementById('recentActivityList');
                recentActivityList.innerHTML = ''; // Clear previous content

                if (dashboardData.recentActivity && dashboardData.recentActivity.length > 0) {
                    dashboardData.recentActivity.forEach(activity => {
                        let activityClass = '';
                        let description = '';
                        let timeAgo = new Date(activity.timestamp).toLocaleString('es-ES', {
                            dateStyle: 'medium',
                            timeStyle: 'short'
                        }); // Simple timestamp for now

                        switch (activity.type) {
                            case 'Nuevo Funcionario':
                                activityClass = 'recent'; // Green border
                                description = `Departamento de Recursos Humanos`;
                                recentActivityList.innerHTML += `
                                    <div class="activity-item ${activityClass}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="mb-0 fw-medium">Nuevo funcionario añadido: ${activity.Nombres} ${activity.Apellidos}</p>
                                            <small class="text-muted">${timeAgo}</small>
                                        </div>
                                        <small class="text-muted">${description}</small>
                                    </div>
                                `;
                                break;
                            case 'Permiso':
                                activityClass = activity.Estado_Permiso === 'Pendiente' ? 'warning' : 'default'; // Yellow for pending
                                description = `Permiso por ${activity.Tipo_Permiso}`;
                                recentActivityList.innerHTML += `
                                    <div class="activity-item ${activityClass}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="mb-0 fw-medium">Permiso ${activity.Estado_Permiso} para ${activity.Nombres} ${activity.Apellidos}</p>
                                            <small class="text-muted">${timeAgo}</small>
                                        </div>
                                        <small class="text-muted">${description}</small>
                                    </div>
                                `;
                                break;
                                // Add more cases for other activity types if needed
                            default:
                                recentActivityList.innerHTML += `
                                    <div class="activity-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="mb-0 fw-medium">${activity.type}</p>
                                            <small class="text-muted">${timeAgo}</small>
                                        </div>
                                        <small class="text-muted">${activity.description || ''}</small>
                                    </div>
                                `;
                        }

                    });
                } else {
                    recentActivityList.innerHTML = '<div class="text-center text-muted py-4">No hay actividad reciente disponible.</div>';
                }

                // --- 6. Update Important Notifications ---
                const importantNotificationsList = document.getElementById('importantNotificationsList');
                importantNotificationsList.innerHTML = ''; // Clear previous content

                if (dashboardData.notifications && dashboardData.notifications.length > 0) {
                    dashboardData.notifications.forEach(notification => {
                        const badgeHtml = notification.count ? `<span class="badge bg-${notification.type === 'warning' ? 'warning text-dark' : notification.type} rounded-pill">${notification.count}</span>` : `<i class="bi bi-chevron-right text-muted"></i>`;
                        importantNotificationsList.innerHTML += `
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-${notification.type}">${notification.title}</div>
                                    <small class="text-muted">${notification.description}</small>
                                </div>
                                ${badgeHtml}
                            </a>
                        `;
                    });
                } else {
                    importantNotificationsList.innerHTML = '<div class="text-center text-muted py-4">No hay notificaciones importantes.</div>';
                }


            } catch (error) {
                console.error('Error fetching dashboard data:', error);
                alert('Error al cargar los datos del dashboard. Verifique la consola para más detalles.'); // Usar un modal en lugar de alert
            } finally {
                refreshButton.classList.remove('refreshing'); // Stop animation
            }
        }

        // Initial data fetch on page load
        document.addEventListener('DOMContentLoaded', function() {
            fetchData(); // Load data on initial page load

            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');

            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            });

            sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });

            // Close sidebar if clicking outside on mobile (when overlay is not active)
            mainContent.addEventListener('click', (event) => {
                if (window.innerWidth <= 767 && sidebar.classList.contains('show') && !sidebarOverlay.classList.contains('show')) {
                    if (!sidebar.contains(event.target) && event.target !== sidebarToggle) {
                        sidebar.classList.remove('show');
                    }
                }
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth > 767) { // Corrected breakpoint from 768 to 767
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                }
            });

            // Ensure sidebar is hidden by default on mobile load
            if (window.innerWidth <= 767) { // Corrected breakpoint
                sidebar.classList.remove('show');
            }
        });
    </script>
</body>

</html>