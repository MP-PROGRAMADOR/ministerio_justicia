<?php
session_start();
include_once '../includes/conexion.php';

$MAX_INTENTOS = 5;       // Intentos permitidos
$BLOQUEO_MINUTOS = 10;   // Tiempo de bloqueo en minutos

// Inicializar contador y hora de bloqueo
if (!isset($_SESSION['login_intentos'])) $_SESSION['login_intentos'] = 0;
if (!isset($_SESSION['login_bloqueo'])) $_SESSION['login_bloqueo'] = null;

// Verificar si está bloqueado
if ($_SESSION['login_bloqueo'] && time() < $_SESSION['login_bloqueo']) {
    $tiempo_restante = $_SESSION['login_bloqueo'] - time();
    $_SESSION['error'] = "Has superado los intentos. Intenta de nuevo en " . ceil($tiempo_restante/60) . " minutos.";
    header("Location: ../funcionario/index.php");
    exit;
}

// Verificar datos
if (!isset($_POST['codigo'], $_POST['puzzle_solved'])) {
    $_SESSION['error'] = "Datos incompletos.";
    header("Location: ../funcionario/index.php");
    exit;
}

// Validar captcha
if ($_POST['puzzle_solved'] != '1') {
    $_SESSION['error'] = "Debes completar el puzzle de verificación.";
    header("Location: ../funcionario/index.php");
    exit;
}

$codigo = trim($_POST['codigo']);

try {
    
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Buscar funcionario por código
    $stmt = $pdo->prepare("SELECT ID_Funcionario, Nombre, Apellidos FROM funcionarios WHERE CODIGO = :codigo LIMIT 1");
    $stmt->execute([':codigo' => $codigo]);
    $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($funcionario) {
        // Login exitoso: reiniciar intentos
        $_SESSION['login_intentos'] = 0;
        $_SESSION['login_bloqueo'] = null;

        // Guardar datos en sesión
        $_SESSION['ID_Funcionario'] = $funcionario['ID_Funcionario'];
        $_SESSION['Nombre'] = $funcionario['Nombre'];
        $_SESSION['Apellidos'] = $funcionario['Apellidos'];
        $_SESSION['CODIGO'] = $codigo;

        header("Location: ../funcionario/panel_funcionario.php");
        exit;
    } else {
        // Login fallido: aumentar contador
        $_SESSION['login_intentos']++;

        if ($_SESSION['login_intentos'] >= $MAX_INTENTOS) {
            $_SESSION['login_bloqueo'] = time() + ($BLOQUEO_MINUTOS * 60);
            $_SESSION['error'] = "Has superado los intentos. Intenta de nuevo en $BLOQUEO_MINUTOS minutos.";
        } else {
            $restantes = $MAX_INTENTOS - $_SESSION['login_intentos'];
            $_SESSION['error'] = "Código incorrecto. Te quedan $restantes intentos.";
        }

        header("Location: ../funcionario/index.php");
        exit;
    }

} catch (PDOException $e) {
    $_SESSION['error'] = "Error en la base de datos: " . $e->getMessage();
    header("Location: ../funcionario/index.php");
    exit;
}
