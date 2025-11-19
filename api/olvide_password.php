<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ministerio de Justicia - Recuperar Contraseña</title>
    <link rel="icon" href="https://placehold.co/32x32/0047AB/FFFFFF?text=MJ" type="image/x-icon">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Paleta de colores refinada */
            --primary-blue: #0047AB;
            --primary-blue-dark: #003380;
            --primary-blue-light: rgba(0, 71, 171, 0.08);
            --accent-cyan: #00d2ff;
            /* Para detalles sutiles */

            --success-green: #2ecc71;
            --danger-red: #e74c3c;
            --warning-yellow: #f1c40f;

            /* Fondos y Superficies */
            --bg-gradient: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            --white: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.92);
            --input-bg: #f0f4f8;

            /* Texto */
            --text-dark: #2c3e50;
            --text-muted: #7f8c8d;

            /* UI */
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 15px 35px rgba(0, 45, 100, 0.15);
            --border-radius-md: 12px;
            --border-radius-lg: 20px;
            --transition-smooth: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            line-height: 1.6;
            color: var(--text-dark);
        }

        #bgCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            opacity: 0.6;
            /* Suavizar las partículas */
        }

        /* Contenedor Principal con efecto Glassmorphism Mejorado */
        .main-container {
            background: var(--glass-bg);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-lg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            overflow: hidden;
            display: flex;
            width: 90%;
            max-width: 1100px;
            min-height: 600px;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        /* --- PANEL IZQUIERDO --- */
        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            padding: 40px;
            text-align: center;
            overflow: hidden;
        }

        /* Patrón de fondo sutil animado */
        .left-panel::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 60%);
            animation: rotate 25s linear infinite;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(0, 210, 255, 0.1) 0%, transparent 70%);
            filter: blur(40px);
        }

        .left-panel-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .ministry-logo {
            width: 110px;
            height: 110px;
            margin-bottom: 25px;
            border-radius: 50%;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            color: var(--primary-blue);
            font-weight: 800;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            border: 4px solid rgba(255, 255, 255, 0.3);
        }

        .left-panel h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .left-panel .subtitle {
            font-size: 0.95rem;
            margin-bottom: 20px;
            opacity: 0.9;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 500;
        }

        /* --- PANEL DERECHO --- */
        .right-panel {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: rgba(255, 255, 255, 0.6);
            /* Fondo semi-transparente */
        }

        .right-panel-content {
            width: 100%;
            max-width: 400px;
        }

        .header-icon {
            width: 70px;
            height: 70px;
            background: var(--primary-blue-light);
            color: var(--primary-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 25px;
            box-shadow: var(--shadow-sm);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .auth-header h2 {
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 10px;
            font-size: 1.75rem;
            letter-spacing: -0.5px;
        }

        .auth-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* --- FORMULARIO REFINADO (INPUT & LABEL) --- */
        .form-floating {
            position: relative;
            margin-bottom: 25px;
            /* Más espacio debajo */
        }

        /* Estilo base del Input */
        .form-floating input {
            width: 100%;
            height: 60px;
            /* Un poco más alto para mayor comodidad */
            padding: 15px 45px 15px 20px;
            /* Padding derecho extra para el icono */
            font-size: 1rem;
            color: var(--text-dark);
            background-color: #f8faff;
            /* Fondo azul muy tenue */
            border: 2px solid #e2e8f0;
            /* Borde sutil inicial */
            border-radius: var(--border-radius-md);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
            /* Importante para el placeholder */
            placeholder-shown: opacity(0);
        }

        /* Estilo del Input al hacer clic (Focus) */
        .form-floating input:focus {
            background-color: var(--white);
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px var(--primary-blue-light);
            /* Resplandor azul suave */
        }

        /* Estilo base del Label (Texto "Correo Electrónico") */
        .form-floating label {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1rem;
            font-weight: 400;
            pointer-events: none;
            /* Permite hacer clic a través del texto */
            transition: all 0.2s ease-out;
            padding: 0 5px;
            background-color: transparent;
            z-index: 2;
        }

        /* --- MAGIA DE LA ETIQUETA FLOTANTE --- */
        /* Se activa cuando hay focus O cuando hay texto escrito */
        .form-floating input:focus~label,
        .form-floating input:not(:placeholder-shown)~label {
            top: 0;
            left: 15px;
            transform: translateY(-50%) scale(0.9);
            font-size: 0.9rem;
            color: var(--primary-blue);
            background-color: var(--white);
            /* Fondo blanco para tapar el borde */
            font-weight: 600;
            padding: 0 8px;
            /* Espacio lateral para que no se pegue la línea */
            border-radius: 4px;
            letter-spacing: 0.5px;
        }

        /* Estilo del Icono (Sobre) */
        .input-group-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.1rem;
            color: #a0aec0;
            z-index: 3;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        /* El icono cambia de color junto con el input */
        .form-floating input:focus~.input-group-icon {
            color: var(--primary-blue);
            transform: translateY(-50%) scale(1.1);
        }

        /* Manejo del placeholder nativo (ocultarlo para que no estorbe) */
        .form-floating input::placeholder {
            color: transparent;
        }

        /* --- BOTÓN DE ACCIÓN --- */
        .btn-submit {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-dark));
            border: none;
            padding: 16px;
            border-radius: var(--border-radius-md);
            font-weight: 600;
            font-size: 1rem;
            color: var(--white);
            width: 100%;
            letter-spacing: 0.5px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all var(--transition-smooth);
            box-shadow: 0 4px 12px rgba(0, 71, 171, 0.25);
        }

        /* Efecto Hover del botón */
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 71, 171, 0.4);
            background: linear-gradient(135deg, #0056d6, #004099);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* --- ENLACE VOLVER --- */
        .back-link {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .back-link a {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .back-link a:hover {
            color: var(--primary-blue);
            transform: translateX(-3px);
            /* Movimiento sutil a la izquierda */
        }

        .back-link i {
            margin-right: 8px;
            font-size: 0.9rem;
        }

        /* --- ALERTAS ESTILIZADAS --- */
        .alert {
            border-radius: var(--border-radius-md);
            font-size: 0.9rem;
            border: none;
            box-shadow: var(--shadow-sm);
            padding: 15px 20px;
            display: flex;
            align-items: center;
        }

        .alert-success {
            background-color: #e8f8f5;
            color: #107c63;
            border-left: 4px solid var(--success-green);
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #b91c1c;
            border-left: 4px solid var(--danger-red);
        }

        .btn-close {
            box-shadow: none !important;
            font-size: 0.8rem;
        }

        /* Loader */
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 10px;
            vertical-align: middle;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* --- RESPONSIVE DESIGN --- */
        @media (max-width: 992px) {
            .main-container {
                flex-direction: column;
                max-width: 500px;
                margin: 20px;
                min-height: auto;
            }

            .left-panel {
                padding: 40px 30px;
                min-height: 220px;
                border-radius: 0 0 20% 20% / 0 0 20px 20px;
                /* Forma curva suave */
                /* Sobrescribir el border radius del container para móviles */
            }

            .main-container {
                border-radius: var(--border-radius-md);
            }

            .ministry-logo {
                width: 80px;
                height: 80px;
                font-size: 1.8rem;
            }

            .left-panel h1 {
                font-size: 1.75rem;
            }

            .right-panel {
                padding: 40px 25px;
            }
        }

        @media (max-width: 576px) {
            .main-container {
                width: 95%;
            }

            .left-panel {
                display: none;
                /* Opcional: Ocultar panel izquierdo en móviles muy pequeños para ahorrar espacio */
            }

            .right-panel {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <canvas id="bgCanvas"></canvas>

    <div class="main-container fade-in">
        <div class="left-panel d-none d-lg-flex">
            <div class="left-panel-content">
                <div class="ministry-logo">MJ</div>
                <h1>Ministerio de Justicia</h1>
                <p class="subtitle">GUINEA ECUATORIAL</p>
                <p style="font-style: italic; opacity: 0.8; max-width: 300px;">
                    "Seguridad jurídica para todos los ciudadanos."
                </p>
            </div>
        </div>



        <div class="right-panel">
            <div class="right-panel-content">

                <div class="header-icon">
                    <i class="fas fa-lock-open"></i>
                </div>

                <div class="auth-header">
                    <h2>¿Olvidaste tu contraseña?</h2>
                    <p>No te preocupes. Ingresa tu correo electrónico institucional y te enviaremos instrucciones para restablecerla.</p>
                </div>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php
                        echo $_SESSION['success'];
                        unset($_SESSION['success']); // Borrar mensaje tras mostrarlo
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php
                        echo $_SESSION['error'];
                        unset($_SESSION['error']); // Borrar mensaje tras mostrarlo
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form id="recoveryForm" action="./enviar_password.php" method="POST">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" placeholder="nombre@justicia.gob.gq" required>
                        <label for="email">Correo Electrónico</label>
                        <i class="fas fa-envelope input-group-icon"></i>
                    </div>

                    <button type="submit" class="btn-submit">
                        <span class="loading-spinner"></span>
                        <span class="btn-text">Enviar Instrucciones</span>
                    </button>
                </form>

                <div class="back-link">
                    <a href="../index.php">
                        <i class="fas fa-arrow-left"></i> Volver al inicio de sesión
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        // 1. Lógica de Envío y Animación de Carga
        document.getElementById('recoveryForm').addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn-submit');
            const spinner = btn.querySelector('.loading-spinner');
            const text = btn.querySelector('.btn-text');

            // Nota: En un entorno real, no uses preventDefault si vas a enviar a PHP
            // Lo uso aquí para simular la carga visualmente antes del envío
            // e.preventDefault(); 

            btn.disabled = true;
            spinner.style.display = 'inline-block';
            text.textContent = 'Enviando...';

            // Simulación de espera (eliminar esto si el backend es rápido)
            // setTimeout(() => { this.submit(); }, 800); 
        });

        // 2. Auto-cerrado de alertas
        const alerts = document.querySelectorAll('.alert');
        if (alerts) {
            setTimeout(() => {
                alerts.forEach(alert => {
                    alert.style.transition = "opacity 0.5s ease";
                    alert.style.opacity = 0;
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        }

        // 3. Animación de Fondo (Simplificada para rendimiento, mismo estilo que Login)
        const canvas = document.getElementById('bgCanvas');
        const ctx = canvas.getContext('2d');
        let particles = [];

        function resize() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resize);
        resize();

        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 2 + 1;
                this.speedX = Math.random() * 0.5 - 0.25;
                this.speedY = Math.random() * 0.5 - 0.25;
            }
            update() {
                this.x += this.speedX;
                this.y += this.speedY;
                if (this.x > canvas.width) this.x = 0;
                if (this.x < 0) this.x = canvas.width;
                if (this.y > canvas.height) this.y = 0;
                if (this.y < 0) this.y = canvas.height;
            }
            draw() {
                ctx.fillStyle = 'rgba(0, 71, 171, 0.15)'; // Azul institucional muy tenue
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        function init() {
            for (let i = 0; i < 50; i++) particles.push(new Particle());
        }
        init();

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => {
                p.update();
                p.draw();
            });
            // Dibujar líneas de conexión
            particles.forEach((a, index) => {
                particles.slice(index + 1).forEach(b => {
                    const dx = a.x - b.x;
                    const dy = a.y - b.y;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    if (distance < 100) {
                        ctx.beginPath();
                        ctx.strokeStyle = `rgba(0, 71, 171, ${0.1 - distance/1000})`;
                        ctx.lineWidth = 0.5;
                        ctx.moveTo(a.x, a.y);
                        ctx.lineTo(b.x, b.y);
                        ctx.stroke();
                    }
                });
            });
            requestAnimationFrame(animate);
        }
        animate();
    </script>
</body>

</html>