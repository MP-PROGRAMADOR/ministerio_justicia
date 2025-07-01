<?php

include_once '../api/data.php';
?>
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
             


                    <!-- Department Statistics -->
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <div class="chart-container">
                                <h5 class="mb-3 fw-semibold">Departamentos con Mayor Personal</h5>
                                <div id="departmentProgressBars">
                                    <!-- Dynamic content will be loaded here -->
                                    <div class="text-center text-muted py-4">Cargando datos de departamentos...</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="chart-container">
                                <h5 class="mb-3 fw-semibold">Tipos de Destinos</h5>
                                <div class="row text-center" id="destinationTypeCards">
                                    <!-- Dynamic content will be loaded here -->
                                    <div class="text-center text-muted py-4">Cargando datos de destinos...</div>
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
                                            <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3" style="width: 40px; height: 40px; font-size: 1rem; border-radius: 8px;">
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
                                            <div class="stat-icon bg-success bg-opacity-10 text-success me-3" style="width: 40px; height: 40px; font-size: 1rem; border-radius: 8px;">
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
                                            <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3" style="width: 40px; height: 40px; font-size: 1rem; border-radius: 8px;">
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
                                            <div class="stat-icon bg-info bg-opacity-10 text-info me-3" style="width: 40px; height: 40px; font-size: 1rem; border-radius: 8px;">
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
                                    <!-- Dynamic content will be loaded here -->
                                    <div class="text-center text-muted py-4">Cargando actividad reciente...</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="chart-container">
                                <h5 class="mb-3 fw-semibold">Notificaciones Importantes</h5>
                                <div class="list-group" id="importantNotificationsList">
                                    <!-- Dynamic content will be loaded here -->
                                    <div class="text-center text-muted py-4">Cargando notificaciones...</div>
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
