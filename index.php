<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ministerio de Justicia - Iniciar Sesión</title>
    <!-- Favicon -->
    <link rel="icon" href="https://placehold.co/32x32/0047AB/FFFFFF?text=MJ" type="image/x-icon">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #0047AB;
            --primary-blue-dark: #003a8f;
            --primary-blue-light: rgba(0, 71, 171, 0.1);
            --success-green: #28a745;
            --danger-red: #dc3545;
            --warning-yellow: #ffc107;
            --light-bg: #f8f9fa;
            --white: #ffffff;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #ced4da;
            --gray-600: #6c757d;
            --gray-800: #343a40;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
            --border-radius-sm: 6px;
            --border-radius-md: 10px;
            --border-radius-lg: 15px;
            --transition-fast: 0.2s ease;
            --transition-normal: 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--gray-100) 0%, #e3f2fd 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            line-height: 1.6;
        }

        .login-container {
            background: var(--white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-lg);
            backdrop-filter: blur(10px);
            overflow: hidden;
            display: flex;
            width: 90%;
            max-width: 1200px;
            min-height: 650px;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

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
            border-top-left-radius: var(--border-radius-lg);
            border-bottom-left-radius: var(--border-radius-lg);
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .left-panel-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            max-width: 400px;
        }

        .ministry-logo {
            width: 120px;
            height: 120px;
            margin-bottom: 30px;
            border-radius: 50%;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            border: 3px solid rgba(255, 255, 255, 0.3);
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: var(--primary-blue);
            font-weight: 700;
            transition: transform var(--transition-normal);
        }

        .ministry-logo:hover {
            transform: scale(1.05);
        }

        .left-panel-content h1 {
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            letter-spacing: -0.5px;
        }

        .left-panel-content .subtitle {
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.9;
            font-weight: 300;
            letter-spacing: 0.5px;
        }

        .decorative-line {
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--white), transparent);
            margin: 25px 0;
            border-radius: 2px;
        }

        .quote {
            font-style: italic;
            font-size: 1rem;
            opacity: 0.85;
            line-height: 1.7;
            font-weight: 300;
        }

        .right-panel {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: var(--white);
            position: relative;
        }

        .right-panel-content {
            width: 100%;
            max-width: 400px;
            z-index: 1;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header .logo-small {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.5rem;
            font-weight: 700;
            box-shadow: var(--shadow-md);
        }

        .login-header h2 {
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 10px;
            font-size: 2rem;
        }

        .login-header p {
            color: var(--gray-600);
            margin: 0;
            font-size: 0.95rem;
        }

        .form-floating {
            position: relative;
            margin-bottom: 20px;
        }

        .form-floating input {
            border-radius: var(--border-radius-md);
            border: 2px solid var(--gray-200);
            padding: 20px 15px 10px;
            font-size: 1rem;
            transition: all var(--transition-normal);
            background: var(--white);
            width: 100%;
        }

        .form-floating input:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.25rem var(--primary-blue-light);
            outline: none;
        }

        .form-floating label {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: var(--gray-600);
            font-size: 1rem;
            transition: all var(--transition-fast);
            pointer-events: none;
            background: var(--white);
            padding: 0 5px;
        }

        .form-floating input:focus+label,
        .form-floating input:not(:placeholder-shown)+label {
            top: 0;
            font-size: 0.85rem;
            color: var(--primary-blue);
            font-weight: 500;
        }

        .input-group-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-600);
            z-index: 5;
            cursor: pointer;
            transition: color var(--transition-fast);
        }

        .input-group-icon:hover {
            color: var(--primary-blue);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-dark));
            border: none;
            padding: 15px 25px;
            border-radius: var(--border-radius-md);
            font-weight: 600;
            font-size: 1rem;
            transition: all var(--transition-normal);
            width: 100%;
            color: var(--white);
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 71, 171, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }

        .forgot-password a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
            transition: color var(--transition-fast);
        }

        .forgot-password a:hover {
            color: var(--primary-blue-dark);
            text-decoration: underline;
        }

        .alert {
            border-radius: var(--border-radius-md);
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
            position: relative;
            /* Remove animation: slideInDown 0.3s ease; from here */
        }

        /* New CSS for fade-out animation */
        .alert.fade-out {
            opacity: 0;
            transition: opacity 0.5s ease-out;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid var(--danger-red);
        }

        #loginCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            background: linear-gradient(135deg, var(--gray-100) 0%, #e3f2fd 100%);
        }

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--white);
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
                min-height: auto;
                width: 95%;
                max-width: 500px;
                margin: 20px;
            }

            .left-panel {
                min-height: 300px;
                border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
                padding: 30px 20px;
            }

            .right-panel {
                border-radius: 0 0 var(--border-radius-lg) var(--border-radius-lg);
                padding: 40px 30px;
            }

            .left-panel-content h1 {
                font-size: 2rem;
            }

            .left-panel-content .subtitle {
                font-size: 1rem;
            }

            .ministry-logo {
                width: 100px;
                height: 100px;
                font-size: 2rem;
            }

            .login-header .logo-small {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .login-container {
                width: 100%;
                margin: 10px;
                border-radius: var(--border-radius-md);
            }

            .left-panel {
                min-height: 250px;
                padding: 25px 20px;
                border-radius: var(--border-radius-md) var(--border-radius-md) 0 0;
            }

            .right-panel {
                padding: 30px 25px;
                border-radius: 0 0 var(--border-radius-md) var(--border-radius-md);
            }

            .left-panel-content h1 {
                font-size: 1.75rem;
            }

            .ministry-logo {
                width: 80px;
                height: 80px;
                font-size: 1.5rem;
            }

            .login-header h2 {
                font-size: 1.75rem;
            }
        }

        /* Additional animations */
        .fade-in {
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Enhanced focus styles for accessibility */
        .btn-login:focus,
        .form-floating input:focus,
        .forgot-password a:focus {
            outline: 3px solid var(--primary-blue-light);
            outline-offset: 2px;
        }
    </style>
</head>

<body>
    <canvas id="loginCanvas"></canvas>

    <div class="login-container fade-in">
        <!-- Left Panel -->
        <div class="left-panel d-none d-lg-flex">
            <div class="left-panel-content">
                <div class="ministry-logo">MJ</div>
                <h1>Ministerio de Justicia</h1>
                <p class="subtitle">REPÚBLICA DE GUINEA ECUATORIAL</p>
                <div class="decorative-line"></div>
                <p class="quote">"La Justicia es la constante y perpetua voluntad de dar a cada uno su derecho."</p>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <div class="right-panel-content">
                <div class="login-header">
                    <div class="logo-small d-lg-none">MJ</div>
                    <h2>Iniciar Sesión</h2>
                    <p>Accede a tu cuenta del sistema</p>
                </div>

                <!-- PHP Session Error Message 1 -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger text-center session-alert" role="alert">
                        <?php
                        echo htmlspecialchars($_SESSION['error']);
                        unset($_SESSION['error']); // Eliminamos después de mostrarlo
                        ?>
                    </div>
                <?php endif; ?>

                <!-- PHP Session Error Message 2 -->
                <?php
                if (isset($_SESSION['error_login2'])) {
                    echo '<div class="alert alert-danger session-alert">' . htmlspecialchars($_SESSION['error_login2']) . '</div>';
                    unset($_SESSION['error_login2']);
                }
                ?>

                <form id="loginForm" method="POST" action="api/login.php">
                    <div class="form-floating">
                        <input type="text" id="username" name="usuario" placeholder="Nombre de Usuario" required>
                        <label for="username">Nombre de Usuario</label>
                        <i class="fas fa-user input-group-icon"></i>
                    </div>

                    <div class="form-floating">
                        <input type="password" id="password" name="password" placeholder="Contraseña" required>
                        <label for="password">Contraseña</label>
                        <i class="fas fa-eye input-group-icon" id="togglePassword"></i>
                    </div>

                    <button type="submit" class="btn-login">
                        <span class="loading-spinner" id="loadingSpinner"></span>
                        <span id="buttonText">Iniciar Sesión</span>
                    </button>

                    <div class="forgot-password">
                        <a href="#" id="forgotPasswordLink">¿Olvidaste tu contraseña?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Enhanced Canvas Animation
        const canvas = document.getElementById('loginCanvas');
        const ctx = canvas.getContext('2d');
        let particles = [];
        const numParticles = 60;
        const mouse = { x: null, y: null, radius: 150 };

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            initParticles();
        }

        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 3 + 1;
                this.baseX = this.x;
                this.baseY = this.y;
                this.density = Math.random() * 30 + 1;
                this.speedX = Math.random() * 0.6 - 0.3;
                this.speedY = Math.random() * 0.6 - 0.3;
                this.color = `hsla(220, 100%, ${Math.random() * 30 + 50}%, ${Math.random() * 0.3 + 0.1})`;
            }

            update() {
                // Mouse interaction
                let dx = mouse.x - this.x;
                let dy = mouse.y - this.y;
                let distance = Math.sqrt(dx * dx + dy * dy);
                let forceDirectionX = dx / distance;
                let forceDirectionY = dy / distance;
                let maxDistance = mouse.radius;
                let force = (maxDistance - distance) / maxDistance;
                let directionX = forceDirectionX * force * this.density;
                let directionY = forceDirectionY * force * this.density;

                if (distance < mouse.radius) {
                    this.x -= directionX;
                    this.y -= directionY;
                } else {
                    if (this.x !== this.baseX) {
                        let dx = this.x - this.baseX;
                        this.x -= dx / 10;
                    }
                    if (this.y !== this.baseY) {
                        let dy = this.y - this.baseY;
                        this.y -= dy / 10;
                    }
                }

                // Natural movement
                this.baseX += this.speedX;
                this.baseY += this.speedY;

                // Boundary conditions
                if (this.baseX > canvas.width || this.baseX < 0) {
                    this.speedX *= -1;
                }
                if (this.baseY > canvas.height || this.baseY < 0) {
                    this.speedY *= -1;
                }
            }

            draw() {
                ctx.fillStyle = this.color;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        function initParticles() {
            particles = [];
            for (let i = 0; i < numParticles; i++) {
                particles.push(new Particle());
            }
        }

        function connectParticles() {
            for (let a = 0; a < particles.length; a++) {
                for (let b = a; b < particles.length; b++) {
                    let distance = Math.sqrt(
                        (particles[a].x - particles[b].x) ** 2 +
                        (particles[a].y - particles[b].y) ** 2
                    );
                    if (distance < 120) {
                        ctx.strokeStyle = `rgba(0, 71, 171, ${0.15 - distance / 800})`;
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(particles[a].x, particles[a].y);
                        ctx.lineTo(particles[b].x, particles[b].y);
                        ctx.stroke();
                    }
                }
            }
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            particles.forEach(particle => {
                particle.update();
                particle.draw();
            });

            connectParticles();
            requestAnimationFrame(animate);
        }

        // Mouse tracking
        window.addEventListener('mousemove', (e) => {
            mouse.x = e.x;
            mouse.y = e.y;
        });

        window.addEventListener('mouseout', () => {
            mouse.x = null;
            mouse.y = null;
        });

        // Initialize canvas
        window.addEventListener('load', () => {
            resizeCanvas();
            animate();
        });
        window.addEventListener('resize', resizeCanvas);

        // Form Enhancement
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('loginForm');
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const buttonText = document.getElementById('buttonText');

            // Password toggle
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });

            // Form submission with loading state
            form.addEventListener('submit', function (e) {
                const submitButton = this.querySelector('.btn-login');
                submitButton.disabled = true;
                loadingSpinner.style.display = 'inline-block';
                buttonText.textContent = 'Iniciando sesión...';

                // Re-enable after 3 seconds if still on page (for demo)
                // This setTimeout is for the button's loading state, not the alerts
                setTimeout(() => {
                    submitButton.disabled = false;
                    loadingSpinner.style.display = 'none';
                    buttonText.textContent = 'Iniciar Sesión';
                }, 3000);
            });

            // Auto-dismiss session alerts
            const sessionAlerts = document.querySelectorAll('.session-alert');
            sessionAlerts.forEach(alert => {
                // Add slide-in animation if not already present
                alert.style.animation = 'slideInDown 0.3s ease';

                setTimeout(() => {
                    alert.classList.add('fade-out'); // Start fade-out animation
                    alert.addEventListener('transitionend', () => alert.remove()); // Remove after transition
                }, 5000); // 5 seconds before fading out

                // Allow manual close if a close button exists (Bootstrap's default behavior)
                const closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.addEventListener('click', () => {
                        alert.classList.add('fade-out');
                        alert.addEventListener('transitionend', () => alert.remove());
                    });
                }
            });
        });
    </script>
</body>

</html>
