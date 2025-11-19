<?php
// api/recover_password.php

session_start();
require_once '../includes/conexion.php'; // Tu archivo de conexión

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Sanitizar y validar el email
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "El formato del correo electrónico no es válido.";
        header("Location: ../forgot_password.php");
        exit();
    }

    try {
        // Asumimos que $dsn, $user, $pass, $options vienen de tu archivo conexion.php
        $pdo = new PDO($dsn, $user, $pass, $options);

        // 2. Verificar si el usuario existe
        $stmt = $pdo->prepare("SELECT ID_Usuario, Nombre_Usuario, Email_Contacto FROM tbl_usuarios WHERE Email_Contacto = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // 3. Generar nueva contraseña aleatoria (10 caracteres)
            $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ12345567890!@#$';
            $nueva_password_texto = substr(str_shuffle($caracteres), 0, 10);

            // 4. Encriptar la contraseña (Se hace antes de actualizar la BD)
            $nueva_password_hash = password_hash($nueva_password_texto, PASSWORD_DEFAULT);

            // ---------------------------------------------------------
            // 5. CONFIGURACIÓN Y ENVÍO DEL CORREO (SE ENVÍA PRIMERO)
            // ---------------------------------------------------------
            
            $para      = $usuario['Email_Contacto'];
            $titulo    = 'Restablecimiento de Contraseña - Ministerio de Justicia';
            
            // Cuerpo del mensaje en HTML
            $mensaje = "
            <html>
            <head>
              <title>Restablecimiento de Contraseña</title>
            </head>
            <body>
                <div style='font-family: Arial, sans-serif; color: #333;'>
                    <h2 style='color: #0047AB;'>Ministerio de Justicia</h2>
                    <p>Hola <strong>" . htmlspecialchars($usuario['Nombre_Usuario']) . "</strong>,</p>
                    <p>Se ha generado una nueva contraseña temporal para tu cuenta.</p>
                    
                    <div style='background: #f4f4f4; padding: 15px; border-left: 5px solid #0047AB; margin: 20px 0;'>
                        <p style='margin: 0; font-size: 18px;'>Nueva Contraseña: <strong>" . $nueva_password_texto . "</strong></p>
                    </div>

                    <p>Por favor, inicia sesión y cambia esta contraseña lo antes posible.</p>
                    <hr>
                    <small>Si no solicitaste esto, contacta con el administrador.</small>
                </div>
            </body>
            </html>
            ";

            // Cabeceras obligatorias para enviar HTML
            $cabeceras  = "MIME-Version: 1.0" . "\r\n";
            $cabeceras .= "Content-type: text/html; charset=UTF-8" . "\r\n";
            $cabeceras .= "From: Soporte Ministerio <npprogramacion23.com>" . "\r\n";
            $cabeceras .= "Reply-To: admin@tuservidor.com" . "\r\n";
            $cabeceras .= "X-Mailer: PHP/" . phpversion();

            // Intentar enviar el correo
            if(mail($para, $titulo, $mensaje, $cabeceras)) {
                
                // 6. ACTUALIZAR BASE DE DATOS (SOLO SI EL CORREO SE ENVIÓ CON ÉXITO)
                $updateStmt = $pdo->prepare("UPDATE tbl_usuarios SET Contrasena_Hash = :password WHERE ID_Usuario = :id");
                $updateStmt->execute([
                    'password' => $nueva_password_hash,
                    'id' => $usuario['ID_Usuario']
                ]);
                
                $_SESSION['success'] = "¡Éxito! Hemos enviado la nueva contraseña a " . $email;
                
            } else {
                // Si mail() falla, NO SE ACTUALIZA LA BASE DE DATOS.
                $_SESSION['error'] = "Hubo un error al enviar el correo. Tu contraseña NO ha sido cambiada. Por favor, inténtalo de nuevo o contacta a soporte.";
            }

        } else {
            // Usuario no encontrado
            $_SESSION['error'] = "No encontramos ningún usuario registrado con ese correo electrónico.";
        }

    } catch(PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    }

    // Redireccionar de vuelta al formulario
    header("Location: ../api/olvide_password.php");
    exit();

} else {
    header("Location: ../index.php");
    exit();
}
?>