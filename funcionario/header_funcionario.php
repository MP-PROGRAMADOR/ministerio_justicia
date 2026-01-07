<?php
// Se asume que la sesión ya está iniciada o se inicia aquí
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/conexion.php';
$pdo = new PDO($dsn, $user, $pass, $options);

// Verificar si el funcionario ha iniciado sesión
if (!isset($_SESSION['CODIGO'])) {
    header("Location: login.php");
    exit;
}

$codigoFuncionario = $_SESSION['CODIGO'];
$idFuncionario = $_SESSION['ID_Funcionario'];

// Obtener datos del funcionario, su cargo y fotografía
$sql = "
    SELECT 
        f.CODIGO,
        f.Nombre,
        f.Apellidos,
        f.Correo,
        f.Telefono AS Telefono_Contacto,
        f.Foto,
        n.Id_nombramiento,
        n.Fecha_nombramiento,
        n.Fecha_toma_posesion,
        c.Nombre AS cargo,
        f.Dip_Pasaporte,
        f.Nacionalidad
    FROM funcionarios f
    LEFT JOIN nombramientos n ON n.Id_funcionario = f.Id_funcionario
    LEFT JOIN cargos c ON c.Id_cargo = n.Id_cargo
    WHERE f.Id_funcionario = :id
    ORDER BY n.Fecha_nombramiento DESC
    LIMIT 1
";



$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $idFuncionario]);
$funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$funcionario) {
    echo "Funcionario no encontrado.";
    exit;
}

// Configuración de la URL de la fotografía
$fotoURL = !empty($funcionario['Foto'])
    ? "../api/" . $funcionario['Foto']
    : "https://placehold.co/80x80/3f51b5/ffffff?text=" . strtoupper(substr($funcionario['Nombre'], 0, 1) . substr($funcionario['Apellidos'], 0, 1));
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
    body {
        background-color: #f5f5f5;
        font-family: 'Roboto', sans-serif;
        /* ESPACIADO PARA EL HEADER FIJO */
        padding-top: 85px;
    }

    .navbar {
        background: linear-gradient(to right, #1a237e, #3f51b5);
        /* Asegura que el header esté por encima de todo */
        z-index: 1050;
    }

    .card {
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    /* Centrado de dropdown en móviles */
    @media (max-width: 575.98px) {
        body {
            padding-top: 110px;
        }

        .dropdown-menu-mobile-center {
            left: 50% !important;
            transform: translateX(-70%);
        }
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark shadow-lg fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center me-3 ms-3" href="panel_funcionario.php">
            <i class="fas fa-building fa-2x me-2"></i>
            <span class="d-none d-md-block">Panel del Funcionario</span>
        </a>

        <div class="d-flex align-items-center ms-auto">



        
                <?php

                if (isset($_SESSION['error'])) {
                    echo "<div id='mensajeFlash' class='alert alert-danger'>" . htmlspecialchars($_SESSION['error']) . "</div>";
                    unset($_SESSION['error']);
                }
                if (isset($_SESSION['exito'])) {
                    echo "<div id='mensajeFlash' class='alert alert-success'>" . htmlspecialchars($_SESSION['exito']) . "</div>";
                    unset($_SESSION['exito']);
                }
                ?>



            <button type="button" class="btn btn-success rounded-pill me-3" data-bs-toggle="modal" data-bs-target="#quejasModal">
                <i class="fas fa-comment-dots me-2"></i>
                <span class="d-none d-md-inline">Quejas / Sugerencias</span>
            </button>

            <div class="dropdown me-3">
                <a class="dropdown-toggle d-flex align-items-center text-white" href="#" id="navbarDropdownMenuAvatar"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false">

                    <img src="<?= htmlspecialchars($fotoURL) ?>"
                        class="rounded-circle me-2 border border-white"
                        style="width: 45px; height: 45px; object-fit: cover;"
                        alt="Avatar" loading="lazy" />

                    <strong class="d-none d-sm-block me-1">
                        <?= htmlspecialchars($funcionario['Nombre'] ?? 'Usuario') ?>
                    </strong>
                </a>

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-mobile-center shadow" aria-labelledby="navbarDropdownMenuAvatar">
                    <li>
                        <a class="dropdown-item" href="./perfil_funcionario.php">
                            <i class="fas fa-user-cog me-2 text-muted"></i>Mi Perfil
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Modal de de Enviar Queja o Sugerencia -->
<div class="modal fade" id="quejasModal" tabindex="-1" aria-labelledby="quejasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="quejasModalLabel">Enviar Queja o Sugerencia</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form method="POST" action="../api/guardar_quejas_sujerencias.php">
                    <div class="mb-4">
                        <label for="type" class="form-label fw-bold">Tipo de mensaje</label>
                        <select class="form-select" name="tipo" id="type" name="type">
                            <option value="queja">Queja</option>
                            <option value="sugerencia">Sugerencia</option>
                        </select>
                         <input class="form-check-input" type="hidden" id="id_funcionario" name="id_funcionario" value="<?= $idFuncionario ?>">
                    </div>

                    <div class="mb-4">
                        <label for="message" class="form-label fw-bold">Descripción</label>
                        <textarea class="form-control" id="message" name="message" rows="5" placeholder="Escriba su mensaje aquí..."></textarea>
                    </div>

                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="anonimo" name="anonimo" value="1">
                        <label class="form-check-label" for="anonimo">Enviar como anónimo</label>
                    </div>

                     <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit"  class="btn btn-primary px-4">Enviar Mensaje</button>
            </div>
                </form>
            </div>
           
        </div>
    </div>
</div>


<!-- Modal de cierre de sesion -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="logoutModalLabel">Confirmación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <div class="mb-3">
                    <img src="<?= htmlspecialchars($fotoURL) ?>" alt="Avatar" class="rounded-circle shadow-sm" width="70" height="70" style="object-fit: cover;">
                </div>
                <p class="fw-bold mb-1">¿Deseas cerrar sesión, <span class="text-primary"><?= htmlspecialchars($funcionario['Nombre'] . " " . $funcionario['Apellidos']) ?></span>?</p>
                <p class="text-muted small">Tendrás que ingresar tus credenciales nuevamente para acceder.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancelar</button>
                <a href="../api/logout2.php" class="btn btn-danger px-4">
                    <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </div>
</div>