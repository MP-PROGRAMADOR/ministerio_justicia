<?php 
 
 // Inclusión del archivo de conexión (asegúrate de que la ruta sea correcta)
    // require_once '../conexion/conexion.php'; // Si tienes un archivo de conexión externo
    
    // Configuración de la base de datos
    // Por favor, ajusta estos valores según tu configuración real de MySQL
    $host = 'localhost';
    $db   = 'Themis_MinisterioJusticia'; // Cambia esto al nombre real de tu base de datos
    $user = 'root';      // Cambia esto a tu usuario de base de datos
    $pass = '';          // Cambia esto a tu contraseña de base de datos
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = null;
    $dashboardData = [];
    $error_message = '';


    ?>

















