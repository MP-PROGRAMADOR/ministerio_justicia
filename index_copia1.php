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
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5; /* Light grey background - will be partially obscured by canvas */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative; /* Needed for z-index context */
            overflow: hidden; /* Prevent scrollbars from canvas */
        }
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            width: 90%; /* Responsive width */
            max-width: 1200px; /* Max width for larger screens */
            min-height: 600px; /* Minimum height */
            position: relative; /* Ensure it stays above the canvas */
            z-index: 1; /* Place above the canvas */
        }
        .left-panel {
            flex: 1;
            background-image: url('https://placehold.co/1200x800/2c3e50/ffffff?text=Ministerio+de+Justicia+GE'); /* Placeholder image */
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 20px;
            text-align: center;
            border-top-left-radius: 15px;
            border-bottom-left-radius: 15px;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            /* Updated: Added a subtle gradient to the overlay */
            background: linear-gradient(to bottom right, rgba(0, 71, 171, 0.7), rgba(0, 50, 150, 0.5));
            border-top-left-radius: 15px;
            border-bottom-left-radius: 15px;
        }
        .left-panel-content {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 30px; /* Added padding for better spacing */
        }
        .left-panel-content h1 {
            font-weight: 700;
            margin-bottom: 15px; /* Adjusted margin */
            font-size: 2.8rem; /* Slightly larger font for heading */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); /* Added text shadow */
        }
        .left-panel-content p {
            font-size: 1.2rem; /* Slightly larger font */
            line-height: 1.6;
            margin-bottom: 10px;
            max-width: 80%; /* Constrain text width */
        }
        .left-panel-content .quote {
            font-style: italic;
            margin-top: 20px;
            font-size: 1.05rem;
            opacity: 0.9;
        }
        .left-panel-content .decorative-line {
            width: 80px;
            height: 3px;
            background-color: #ffffff;
            margin: 25px 0; /* Added decorative line */
            border-radius: 5px;
        }

        .right-panel {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative; /* For content positioning */
            border-top-right-radius: 15px;
            border-bottom-right-radius: 15px;
        }
        .right-panel-content {
            z-index: 1; /* Ensure content is above canvas if canvas was here */
            width: 100%;
            max-width: 400px; /* Max width for the form */
        }
        .right-panel h2 {
            font-weight: 600;
            color: #333;
            margin-bottom: 30px;
            font-size: 2rem;
            text-align: center;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #0047AB; /* Ministry blue */
            box-shadow: 0 0 0 0.25rem rgba(0, 71, 171, 0.25);
        }
        .btn-primary {
            background-color: #0047AB; /* Ministry blue */
            border-color: #0047AB;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #003a8f; /* Darker blue on hover */
            border-color: #003a8f;
            transform: translateY(-2px);
        }
        .form-check-label a {
            color: #0047AB;
            text-decoration: none;
        }
        .form-check-label a:hover {
            text-decoration: underline;
        }
        .logo {
            width: 80px; /* Adjust as needed */
            height: auto;
            margin-bottom: 20px;
        }
        .ministry-logo {
            width: 120px; /* Increased logo size */
            height: auto;
            margin-bottom: 25px; /* Adjusted margin */
            border-radius: 50%; /* Ensure it's perfectly round if image is square */
            box-shadow: 0 0 15px rgba(0,0,0,0.2); /* Added subtle shadow to logo */
        }

        /* Canvas styling - now covers the entire body */
        #loginCanvas {
            position: fixed; /* Fixed to viewport */
            top: 0;
            left: 0;
            width: 100vw; /* Full viewport width */
            height: 100vh; /* Full viewport height */
            z-index: -1; /* Behind all content */
            background-color: #f0f2f5; /* Fallback background for canvas */
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
                min-height: auto;
                width: 95%;
                max-width: 600px; /* Stacked layout max width */
            }
            .left-panel, .right-panel {
                flex: none; /* Remove flex grow */
                width: 100%; /* Take full width */
                min-height: 250px; /* Fixed height for image on small screens */
                border-radius: 0; /* Reset border radius for stacking */
            }
            .left-panel {
                border-top-left-radius: 15px;
                border-top-right-radius: 15px;
                border-bottom-left-radius: 0;
            }
            .right-panel {
                border-bottom-left-radius: 15px;
                border-bottom-right-radius: 15px;
                border-top-right-radius: 0;
                padding: 30px;
            }
            .left-panel-content h1 {
                font-size: 2rem;
            }
            .left-panel-content p {
                font-size: 1rem;
            }
            .left-panel-content .quote {
                font-size: 0.9rem;
            }
            .left-panel-content .decorative-line {
                margin: 15px 0;
            }
            .ministry-logo {
                width: 90px; /* Adjusted size for smaller screens */
            }
            .right-panel h2 {
                font-size: 1.75rem;
            }
            /* No canvas border-radius changes needed here as it's full body */
        }
        @media (max-width: 576px) {
            .login-container {
                width: 100%;
                margin: 20px;
            }
            .left-panel {
                min-height: 200px;
                padding: 20px;
            }
            .right-panel {
                padding: 25px;
            }
            .left-panel-content h1 {
                font-size: 1.8rem;
            }
            .left-panel-content p {
                font-size: 0.9rem;
            }
            .left-panel-content .quote {
                font-size: 0.85rem;
            }
            .ministry-logo {
                width: 70px;
            }
            .right-panel h2 {
                font-size: 1.5rem;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Canvas now covers the entire body, positioned first to be in the background -->
    <canvas id="loginCanvas"></canvas>

    <div class="login-container">
        <!-- Left Panel - Background Image and Text -->
        <div class="left-panel d-none d-lg-flex"> <!-- Hidden on small screens, shown on large -->
            <div class="left-panel-content">
                <img src="https://placehold.co/120x120/FFFFFF/0047AB?text=MJ" alt="Ministerio de Justicia Logo" class="ministry-logo rounded-full">
                <h1>Ministerio de Justicia</h1>
                <p>REPÚBLICA DE GUINEA ECUATORIAL</p>
                <div class="decorative-line"></div> <!-- Added decorative line -->
                <p class="quote">"La Justicia es la constante y perpetua voluntad de dar a cada uno su derecho."</p>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="right-panel">
            <div class="right-panel-content">
                <img src="https://placehold.co/80x80/0047AB/FFFFFF?text=MJ" alt="Logo" class="logo d-lg-none d-block mx-auto rounded-full"> <!-- Shown on small screens -->
                <h2>Iniciar Sesión</h2>
                <form>
                    <div class="mb-3">
                        <label for="username" class="form-label visually-hidden">Nombre de Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start-pill"><i class="fa-solid fa-user"></i></span>
                            <input type="text" class="form-control rounded-end-pill" id="username" placeholder="Nombre de Usuario" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label visually-hidden">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start-pill"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" class="form-control rounded-end-pill" id="password" placeholder="Contraseña" required>
                        </div>
                    </div>
                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary rounded-pill">Iniciar Sesión</button>
                    </div>
                    <div class="text-center">
                        <a href="#" class="text-decoration-none" style="color: #0047AB;">¿Olvidaste tu contraseña?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS Bundle (Popper included) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Canvas animation script
        const canvas = document.getElementById('loginCanvas');
        const ctx = canvas.getContext('2d');
        let particles = [];
        const numParticles = 50;
        const particleColor = 'rgba(0, 71, 171, 0.3)'; // Ministry blue with transparency
        const connectionColor = 'rgba(0, 71, 171, 0.1)';

        // Function to resize canvas
        function resizeCanvas() {
            canvas.width = window.innerWidth; // Set canvas width to full viewport width
            canvas.height = window.innerHeight; // Set canvas height to full viewport height
            initParticles(); // Reinitialize particles on resize
        }

        // Particle class
        class Particle {
            constructor(x, y) {
                this.x = x;
                this.y = y;
                this.size = Math.random() * 3 + 1; // Random size between 1 and 4
                this.speedX = Math.random() * 0.5 - 0.25; // Random speed between -0.25 and 0.25
                this.speedY = Math.random() * 0.5 - 0.25;
            }

            // Update particle position
            update() {
                this.x += this.speedX;
                this.y += this.speedY;

                // Bounce off edges
                if (this.x > canvas.width || this.x < 0) {
                    this.speedX *= -1;
                }
                if (this.y > canvas.height || this.y < 0) {
                    this.speedY *= -1;
                }
            }

            // Draw particle
            draw() {
                ctx.fillStyle = particleColor;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        // Initialize particles
        function initParticles() {
            particles = [];
            for (let i = 0; i < numParticles; i++) {
                const x = Math.random() * canvas.width;
                const y = Math.random() * canvas.height;
                particles.push(new Particle(x, y));
            }
        }

        // Connect particles with lines
        function connectParticles() {
            for (let a = 0; a < particles.length; a++) {
                for (let b = a; b < particles.length; b++) {
                    const distance = Math.sqrt(
                        (particles[a].x - particles[b].x) ** 2 +
                        (particles[a].y - particles[b].y) ** 2
                    );
                    if (distance < 100) { // Connect if particles are close enough
                        ctx.strokeStyle = connectionColor;
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(particles[a].x, particles[a].y);
                        ctx.lineTo(particles[b].x, particles[b].y);
                        ctx.stroke();
                    }
                }
            }
        }

        // Animation loop
        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height); // Clear canvas
            for (let i = 0; i < particles.length; i++) {
                particles[i].update();
                particles[i].draw();
            }
            connectParticles();
            requestAnimationFrame(animate);
        }

        // Event listeners
        window.addEventListener('load', () => {
            resizeCanvas(); // Set initial size and particles
            animate(); // Start animation
        });
        window.addEventListener('resize', resizeCanvas); // Resize canvas on window resize

        // Optional: Simple form submission handler
        document.querySelector('form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission
            // In a real application, you would send the username and password to a server here.
            console.log('Nombre de Usuario:', document.getElementById('username').value);
            console.log('Contraseña:', document.getElementById('password').value);
            // You can add a success/error message display here
            alert("Intento de inicio de sesión. (Esta es solo una demostración)"); // Using alert for demo, replace with custom modal in real app
        });

        // Custom alert function (as per instructions, replacing browser alert)
        function alert(message) {
            const existingAlert = document.getElementById('customAlert');
            if (existingAlert) existingAlert.remove();

            const alertDiv = document.createElement('div');
            alertDiv.id = 'customAlert';
            alertDiv.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background-color: #f8d7da; /* Light red */
                color: #721c24; /* Dark red */
                border: 1px solid #f5c6cb;
                border-radius: 8px;
                padding: 20px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                z-index: 10000;
                text-align: center;
                font-family: 'Inter', sans-serif;
                font-size: 1rem;
            `;
            alertDiv.innerHTML = `
                <p>${message}</p>
                <button onclick="this.parentNode.remove()" style="
                    background-color: #dc3545; /* Bootstrap danger red */
                    color: white;
                    border: none;
                    padding: 8px 15px;
                    border-radius: 5px;
                    cursor: pointer;
                    margin-top: 10px;
                ">Cerrar</button>
            `;
            document.body.appendChild(alertDiv);
        }
    </script>
</body>
</html>
