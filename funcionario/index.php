<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Funcionarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            /* Un gris muy claro */
        }

        .login-container {
            min-height: 100vh;
        }

        .login-card {
            border-radius: 1rem;
            overflow: hidden;
            /* Para que la imagen no se salga de los bordes */
        }

        .image-col {
            background-image: url('../img/funcionarios.jpg');
            background-size: cover;
            background-position: center;
        }

        .puzzle-container {
            width: 300px;
            height: 150px;
            position: relative;
            margin-top: 10px;
        }

        .puzzle-piece {
            width: 50px;
            height: 50px;
            position: absolute;
            top: 50px;
            /* Alinear con el hueco */
            left: 0;
            cursor: grab;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
            background-size: cover;
        }
    </style>
</head>

<body class="bg-light">

    <div class="d-flex align-items-center justify-content-center login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card login-card shadow-lg border-0">
                        <div class="row g-0">
                            <div class="col-md-6 d-none d-md-block image-col">
                            </div>

                            <div class="col-md-6">
                                <div class="card-body p-5">
                                    <h2 class="card-title text-center mb-4 fw-bold text-primary">
                                        <i class="bi bi-person-circle me-2"></i>Acceso Funcionarios
                                    </h2>

                                    <?php
                                    session_start();
                                    if (isset($_SESSION['error'])) {
                                        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                                        unset($_SESSION['error']);
                                    }
                                    ?>

                                    <form action="../api/login_procesar.php" method="POST">

                                        <div class="mb-3">
                                            <label for="codigo" class="form-label fw-semibold">Código de Funcionario</label>
                                            <input type="password" name="codigo" id="codigo" class="form-control form-control-lg" placeholder="Ingrese su código" required>
                                        </div>

                                        <div class="mb-4">
                                            <label for="captcha" class="form-label fw-semibold">Verificación de Seguridad</label>
                                            <p class="text-muted small">Arrastra la pieza para completar el puzzle.</p>
                                            <div class="puzzle-container position-relative" id="puzzle-container">
                                                <canvas id="puzzle-canvas" width="300" height="150" class="border rounded"></canvas>
                                                <div class="puzzle-piece" id="puzzle-piece"></div>
                                            </div>
                                            <input type="hidden" name="puzzle_solved" id="puzzle_solved" value="0">
                                        </div>


                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Script para el CAPTCHA de puzzle
        const puzzleContainer = document.getElementById('puzzle-container');
        const puzzlePiece = document.getElementById('puzzle-piece');
        const puzzleSolvedInput = document.getElementById('puzzle_solved');
        const canvas = document.getElementById('puzzle-canvas');
        const ctx = canvas.getContext('2d');

        const img = new Image();
        img.src = '../img/funcionarios.jpg'; // Imagen de fondo
        const pieceWidth = 50;
        const pieceHeight = 50;

        let holeX; // posición del hueco
        let isDragging = false;
        let startX;
        let pieceLeft = 0;

        // Cargar imagen y dibujar fondo y hueco
        img.onload = () => {
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            // Elegir posición aleatoria para hueco
            holeX = Math.floor(Math.random() * (canvas.width - pieceWidth));
            const holeY = 50;

            // Dibujar hueco
            ctx.clearRect(holeX, holeY, pieceWidth, pieceHeight);

            // Configurar la pieza
            puzzlePiece.style.backgroundImage = `url('${img.src}')`;
            puzzlePiece.style.backgroundPosition = `-${holeX}px -${holeY}px`;
        };

        // Arrastrar la pieza
        puzzlePiece.addEventListener('mousedown', e => {
            isDragging = true;
            startX = e.clientX - pieceLeft;
            puzzlePiece.style.cursor = 'grabbing';
        });

        document.addEventListener('mousemove', e => {
            if (!isDragging) return;
            pieceLeft = e.clientX - startX;
            if (pieceLeft < 0) pieceLeft = 0;
            if (pieceLeft > canvas.width - pieceWidth) pieceLeft = canvas.width - pieceWidth;
            puzzlePiece.style.left = pieceLeft + 'px';
        });

        document.addEventListener('mouseup', () => {
            if (!isDragging) return;
            isDragging = false;
            puzzlePiece.style.cursor = 'grab';

            // Validar si la pieza encaja en el hueco (margen de error 5px)
            if (Math.abs(pieceLeft - holeX) <= 5) {
                puzzlePiece.style.left = holeX + 'px';
                puzzleSolvedInput.value = 1;
                puzzlePiece.style.border = '2px solid #28a745';
            } else {
                pieceLeft = 0;
                puzzlePiece.style.left = '0px';
                puzzleSolvedInput.value = 0;
            }
        });
    </script>

</body>
</html>