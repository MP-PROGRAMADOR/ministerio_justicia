<?php
session_start();
if (!isset($_SESSION['ID_Usuario'])) {
    header("Location: ../index.php");
    exit;
}

  $id_usuario=$_SESSION['ID_Usuario'];
    $nombre_usuario=$_SESSION['Nombre_Usuario'];
    $rol_usuario=$_SESSION['Rol_Usuario'] ;
include_once '../includes/conexion.php';




    
   

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        // --- 1. Obtener estadísticas de tarjetas (Statistics Cards) ---
        // Total Funcionarios
        $stmt = $pdo->query("SELECT COUNT(*) AS total FROM tbl_Funcionarios");
        $dashboardData['totalFuncionarios'] = $stmt->fetchColumn();

        // Funcionarios Activos
        $stmt = $pdo->query("SELECT COUNT(*) AS activos FROM tbl_Funcionarios WHERE Estado_Laboral = 'Activo'");
        $dashboardData['funcionariosActivos'] = $stmt->fetchColumn();

        // Permisos Este Mes (contando los solicitados en el mes actual)
        $stmt = $pdo->query("SELECT COUNT(*) AS permisos_mes FROM tbl_Permisos WHERE Fecha_Solicitud >= CURDATE() - INTERVAL (DAY(CURDATE())-1) DAY");
        $dashboardData['permisosEsteMes'] = $stmt->fetchColumn();

        // Permisos Pendientes (para la notificación)
        $stmt = $pdo->query("SELECT COUNT(*) AS pendientes FROM tbl_Permisos WHERE Estado_Permiso = 'Pendiente'");
        $dashboardData['permisosPendientes'] = $stmt->fetchColumn();

        // Total Destinos Activos
        $stmt = $pdo->query("SELECT COUNT(*) AS totalDestinos FROM tbl_Destinos");
        $dashboardData['destinosActivos'] = $stmt->fetchColumn();

        // Nuevos Funcionarios este mes (ejemplo)
        $stmt = $pdo->query("SELECT COUNT(*) FROM tbl_Funcionarios WHERE Fecha_Ingreso >= CURDATE() - INTERVAL (DAY(CURDATE())-1) DAY");
        $dashboardData['newFuncionariosThisMonth'] = $stmt->fetchColumn();


        // --- 2. Distribución de Funcionarios por Estado (Doughnut Chart) ---
        $stmt = $pdo->query("SELECT Estado_Laboral, COUNT(*) AS count FROM tbl_Funcionarios GROUP BY Estado_Laboral");
        $estadoData = $stmt->fetchAll();

        $labels = [];
        $data = [];
        foreach ($estadoData as $row) {
            $labels[] = $row['Estado_Laboral'];
            $data[] = (int)$row['count'];
        }
        $dashboardData['funcionarioDistribution'] = [
            'labels' => $labels,
            'data' => $data
        ];

        // --- 3. Departamentos con Mayor Personal (Progress Bars) ---
        $stmt = $pdo->query("
            SELECT d.Nombre_Departamento, COUNT(a.ID_Funcionario) AS num_funcionarios
            FROM tbl_Asignaciones a
            JOIN tbl_Departamentos d ON a.ID_Departamento = d.ID_Departamento
            GROUP BY d.Nombre_Departamento
            ORDER BY num_funcionarios DESC
            LIMIT 4
        ");
        $dashboardData['departmentStaff'] = $stmt->fetchAll();

        // --- 4. Tipos de Destinos (Small Cards) ---
        $stmt = $pdo->query("SELECT Tipo_Destino, COUNT(*) AS count FROM tbl_Destinos GROUP BY Tipo_Destino");
        $dashboardData['destinationTypes'] = $stmt->fetchAll();

        // --- 5. Actividad Reciente (Recent Activity) ---
        // Últimos funcionarios añadidos
        $stmt = $pdo->query("
            SELECT 'Nuevo Funcionario' as type, Nombres, Apellidos, Fecha_Creacion_Registro as timestamp
            FROM tbl_Funcionarios
            ORDER BY Fecha_Creacion_Registro DESC LIMIT 3
        ");
        $recentActivity = $stmt->fetchAll();

        // Últimos permisos solicitados/aprobados
        $stmt = $pdo->query("
            SELECT 'Permiso' as type, f.Nombres, f.Apellidos, p.Tipo_Permiso, p.Estado_Permiso, p.Fecha_Creacion_Registro as timestamp
            FROM tbl_Permisos p
            JOIN tbl_Funcionarios f ON p.ID_Funcionario = f.ID_Funcionario
            ORDER BY p.Fecha_Creacion_Registro DESC LIMIT 3
        ");
        $recentActivity = array_merge($recentActivity, $stmt->fetchAll());

        // Ordenar toda la actividad por timestamp
        usort($recentActivity, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        $dashboardData['recentActivity'] = array_slice($recentActivity, 0, 7);

        // --- 6. Notificaciones Importantes (Important Notifications) ---
        $notifications = [];

        // Próximos permisos pendientes
        $stmt = $pdo->query("SELECT COUNT(*) FROM tbl_Permisos WHERE Estado_Permiso = 'Pendiente'");
        $pendingPermitsCount = $stmt->fetchColumn();
        if ($pendingPermitsCount > 0) {
            $notifications[] = [
                'type' => 'primary',
                'title' => 'Próximos permisos pendientes',
                'description' => $pendingPermitsCount . ' permisos necesitan revisión y aprobación.',
                'count' => $pendingPermitsCount
            ];
        }

        // Alertas de caducidad de contratos (simulado, ya que no hay tabla de contratos)
        $mockExpiringContracts = 2;
        if ($mockExpiringContracts > 0) {
            $notifications[] = [
                'type' => 'warning',
                'title' => 'Alertas de caducidad de contratos',
                'description' => $mockExpiringContracts . ' contratos vencen en los próximos 30 días.',
                'count' => $mockExpiringContracts
            ];
        }

        // Añadir una notificación de actualización de datos de personal (ejemplo)
        $notifications[] = [
            'type' => 'success',
            'title' => 'Actualización de datos de personal',
            'description' => 'Se han actualizado 3 perfiles de funcionarios.',
            'count' => null
        ];

        // Añadir una notificación de nuevo anuncio (ejemplo)
        $notifications[] = [
            'type' => 'info',
            'title' => 'Nuevo anuncio del Ministerio',
            'description' => 'Cambios en la política de licencias.',
            'count' => null
        ];

        $dashboardData['notifications'] = $notifications;

    } catch (\PDOException $e) {
        $error_message = 'Error de base de datos: ' . $e->getMessage();
        // En un entorno de producción, loggea el error en lugar de mostrarlo al usuario
        error_log($error_message);
    }
    ?>



<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ministerio de Justicia</title>
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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa; /* Light gray background for body */
        }
        .modal-header {
            border-bottom: none;
            padding-bottom: 0.75rem;
        }
        .modal-body {
            padding-top: 0;
            padding-bottom: 0;
        }
        .modal-footer {
            border-top: none;
            padding-top: 0.75rem;
        }
        .modal-content {
            border-radius: 1.5rem; /* More rounded corners */
            overflow: hidden; /* Ensures shadows and borders look good */
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); /* Stronger, modern shadow */
            animation: fadeInScale 0.3s ease-out forwards; /* Fade in and scale animation */
        }
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out, opacity 0.3s ease-out;
        }
        .modal.show .modal-dialog {
            transform: none;
        }

        /* Custom scrollbar for modern look */
        .modal-body-scrollable {
            max-height: 80vh; /* Limits height, enables scroll */
            overflow-y: auto;
        }
        .modal-body-scrollable::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .modal-body-scrollable::-webkit-scrollbar-track {
            background: #e9ecef; /* Light track */
            border-radius: 10px;
        }
        .modal-body-scrollable::-webkit-scrollbar-thumb {
            background: #adb5bd; /* Gray thumb */
            border-radius: 10px;
        }
        .modal-body-scrollable::-webkit-scrollbar-thumb:hover {
            background: #6c757d; /* Darker on hover */
        }

        .info-card {
            background-color: #e3f2fd; /* Light blue (Bootstrap's primary-100 equivalent) */
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.05); /* Subtle inner shadow */
        }
        .section-card {
            background-color: #ffffff;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); /* Subtle outer shadow */
        }
        .section-title {
            font-weight: 600; /* Semi-bold */
            color: #343a40; /* Dark gray */
            border-bottom: 2px solid #f0f0f0; /* Light gray border */
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        .section-item {
            background-color: #f8f9fa; /* Very light gray for items */
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05); /* Very subtle shadow for items */
        }
        .section-item:last-child {
            margin-bottom: 0;
        }
        .profile-pic {
            border: 4px solid #0d6efd; /* Primary blue border */
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.2); /* Blue glowing shadow */
            width: 120px; /* Larger size for profile pic */
            height: 120px;
        }
        .w-100px { width: 100px; } /* Custom utility for fixed width */
        .h-100px { height: 100px; } /* Custom utility for fixed height */

        /* Custom colors for sections (matching Bootstrap's palette where possible) */
        .text-purple { color: #6f42c1; } /* Custom color for consistency */
        .bg-purple-100 { background-color: #e6e0f0; } /* Light purple background */
        .text-purple-600 { color: #5a32a3; } /* Darker purple for icons */
        .text-success-600 { color: #198754; } /* Darker green for icons */
        .text-warning-600 { color: #ffc107; } /* Darker yellow for icons */
        .text-danger-600 { color: #dc3545; } /* Darker red for icons */
        .text-info-600 { color: #0dcaf0; } /* Darker info for icons */


        /* Loading spinner styles */
        .spinner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.85); /* Slightly less transparent */
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: 1.5rem; /* Match modal content border-radius */
        }
    </style>

     
</head>