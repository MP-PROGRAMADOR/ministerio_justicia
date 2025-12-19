<?php

require_once '../includes/conexion.php';
$pdo = new PDO($dsn, $user, $pass, $options);

// Verificar si el funcionario ha iniciado sesión
if (!isset($_SESSION['Codigo_Funcionario'])) {
    header("Location: login.php");
    exit;
}

$codigoFuncionario = $_SESSION['Codigo_Funcionario'];

// Obtener datos personales del funcionario
$stmt = $pdo->prepare("SELECT * FROM tbl_funcionarios WHERE Codigo_Funcionario = ?");
$stmt->execute([$codigoFuncionario]);
$funcionario = $stmt->fetch();

if (!$funcionario) {
    echo "Funcionario no encontrado.";
    exit;
}



$idFuncionario = $_SESSION['ID_Funcionario'];

// Consultar datos del funcionario y su cargo
$sql = "SELECT f.Codigo_Funcionario, f.Nombres, f.Apellidos, f.Email_Oficial, 
               f.Telefono_Contacto, f.Fotografia, c.Nombre_Cargo, f.DNI_Pasaporte, f.Nacionalidad
        FROM tbl_funcionarios f
        LEFT JOIN tbl_cargos c ON c.ID_Cargo = (
            SELECT ID_Cargo 
            FROM tbl_cargos 
            WHERE ID_Funcionario = f.ID_Funcionario 
            LIMIT 1
        )
        WHERE f.ID_Funcionario = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $idFuncionario]);
$funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

// Ruta foto
$fotoURL = !empty($funcionario['Fotografia'])
    ? "../api/" . $funcionario['Fotografia']
    : "https://placehold.co/80x80/3f51b5/ffffff?text=" . strtoupper(substr($funcionario['Nombres'], 0, 1) . substr($funcionario['Apellidos'], 0, 1));




try {
    $stmt = $pdo->prepare("SELECT ID_Instruccion, Titulo, Fecha_Envio, Leido 
    FROM tbl_instrucciones 
    WHERE ID_Funcionario = :id_funcionario 
    ORDER BY Fecha_Envio DESC 
    LIMIT 3");
    $stmt->execute(['id_funcionario' => $idFuncionario]);
    $instrucciones = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error al traer las instrucciones: " . $e->getMessage();
}

?>



    

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Roboto', sans-serif;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .navbar {
            background: linear-gradient(to right, #1a237e, #3f51b5);
        }

        /* Nueva regla CSS para centrar el dropdown en móviles */
        @media (max-width: 575.98px) {
            .dropdown-menu-mobile-center {
                left: 50% !important;
                transform: translateX(-70%);
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <nav class="navbar navbar-expand-lg navbar-dark shadow-lg">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center me-3 ms-3" href="#">
                <i class="fas fa-building fa-2x me-2"></i>
                <span class="d-none d-md-block">Panel del Funcionario</span>
            </a>

            <div class="d-flex align-items-center ms-auto">
                <div
                    class="d-flex justify-content-md-end align-items-center gap-2 flex-wrap justify-content-center">
                    <button class="btn btn-success rounded-pill me-3" data-bs-toggle="modal" data-bs-target="#addPermisoModal">
                        <i class="fas fa-calendar-check me-2 "></i> Solicitar Permiso
                    </button>

                </div>

                <button type="button" class="btn btn-primary rounded-pill me-3" data-bs-toggle="modal" data-bs-target="#quejasModal">
                    <i class="fas fa-comment-dots me-2"></i>
                    Quejas / Sugerencias
                </button>

                <div class="dropdown me-3">
                    <a class="dropdown-toggle d-flex align-items-center hidden-arrow" href="#" id="navbarDropdownMenuAvatar" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?= htmlspecialchars($fotoURL) ?>" class="rounded-circle me-2 border border-white" style="width: 50px; height: 50px; object-fit: cover;" alt="Avatar" loading="lazy" />
                        <strong class="d-none d-sm-block text-white"><?= htmlspecialchars($funcionario['Nombres'] ?? 'Sin Nombre') ?></strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-mobile-center" aria-labelledby="navbarDropdownMenuAvatar">
                        <li><a class="dropdown-item" href="./perfil_funcionario.php"><i class="fas fa-user-cog me-2 text-muted"></i>Mi Perfil</a></li>
                        <!-- <li><a class="dropdown-item" href="#"><i class="fas fa-cogs me-2 text-muted"></i>Configuración</a></li> -->
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>




    
    <!-- Modal de solicitud de permiso -->
    <div class="modal fade" id="addPermisoModal" tabindex="-1" aria-labelledby="addPermisoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addPermisoModalLabel">
                        <i class="bi bi-clipboard-check me-2"></i>Solicitud de Permiso
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">

                    <!-- Formulario de permiso -->
                    <form method="POST" action="../api/guardar_permiso.php" enctype="multipart/form-data">
                        <!-- <input type="hidden" name="ID_Funcionario" id="ID_Funcionario"> -->

                        <div class="row g-3">
                            <!-- Tipo de Permiso -->
                            <div class="col-md-6">
                                <label for="tipoPermiso" class="form-label fw-semibold">
                                    <i class="bi bi-ui-checks-grid text-primary me-2"></i>Tipo de Permiso
                                </label>
                                <select class="form-select" name="Tipo_Permiso" id="tipoPermiso" required>
                                    <option selected disabled>Selecciona tipo</option>
                                    <option value="Vacaciones">Vacaciones</option>
                                    <option value="Enfermedad">Enfermedad</option>
                                    <option value="Maternidad">Maternidad</option>
                                    <option value="Paternidad">Paternidad</option>
                                    <option value="Asuntos Propios">Asuntos Propios</option>
                                    <option value="Estudios">Estudios</option>
                                    <option value="Comisión Servicio">Comisión Servicio</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>

                            <!-- Fechas -->
                            <div class="col-md-3">
                                <label for="fechaInicio" class="form-label fw-semibold">
                                    <i class="bi bi-calendar-event text-primary me-2"></i>Inicio
                                </label>
                                <input type="date" name="Fecha_Inicio_Permiso" class="form-control" id="fechaInicio" required>
                            </div>
                            <div class="col-md-3">
                                <label for="fechaFin" class="form-label fw-semibold">
                                    <i class="bi bi-calendar-check text-primary me-2"></i>Fin
                                </label>
                                <input type="date" name="Fecha_Fin_Permiso" class="form-control" id="fechaFin" required>
                            </div>

                            <!-- Motivo -->
                            <div class="col-md-12">
                                <label for="motivo" class="form-label fw-semibold">
                                    <i class="bi bi-chat-square-text text-primary me-2"></i>Motivo
                                </label>
                                <textarea name="Motivo" class="form-control" id="motivo" rows="3"></textarea>
                            </div>

                            <!-- Observaciones -->
                            <div class="col-md-12">
                                <label for="observaciones" class="form-label fw-semibold">
                                    <i class="bi bi-info-circle text-primary me-2"></i>Observaciones
                                </label>
                                <textarea name="Observaciones" class="form-control" id="observaciones" rows="2"></textarea>
                            </div>

                            <!-- Documento Soporte -->
                            <div class="col-md-6">
                                <label for="documento" class="form-label fw-semibold">
                                    <i class="bi bi-upload text-primary me-2"></i> Documento Soporte (Obligatorio)
                                </label>
                                <input type="file" name="Documento_Soporte_URL" class="form-control" id="documento" accept=".pdf,.jpg,.png,.doc,.docx" required>
                            </div>
                        </div>

                        <!-- Botón enviar -->
                        <div class="mt-4 d-flex justify-content-end mb-3">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-1"></i> Enviar Solicitud
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="quejasModal" tabindex="-1" aria-labelledby="quejasModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quejasModalLabel">Enviar Queja o Sugerencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="complaint-form">
                        <div class="mb-4">
                            <label for="type" class="form-label">Tipo</label>
                            <select class="form-select" id="type" name="type">
                                <option value="queja">Queja</option>
                                <option value="sugerencia">Sugerencia</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label">Mensaje</label>
                            <textarea class="form-control" id="message" name="message" rows="5"></textarea>
                        </div>

                        <!-- Interruptor para Anónimo -->
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="anonimo" name="anonimo" value="1">
                            <label class="form-check-label" for="anonimo">Enviar como anónimo</label>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" form="complaint-form" class="btn btn-primary">Enviar</button>
                </div>
            </div>
        </div>
    </div>







    <!-- Modal de Cierre de Sesión -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="logoutModalLabel">Cerrar Sesión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <!-- Icono de advertencia -->
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>

                    <!-- Avatar del usuario -->
                    <div class="mb-3">
                        <img src="<?= htmlspecialchars($fotoURL) ?>" alt="Avatar" class="rounded-circle" width="80" height="80">
                    </div>

                    <?php

                    $nombre = $_SESSION['Nombres'];
                    $apellidos =     $_SESSION['Apellidos'];
                    ?>
                    <!-- Nombre del usuario -->
                    <p class="fw-bold mb-2">¿Deseas cerrar sesión, <span class="text-primary"><?= htmlspecialchars($nombre . " " . $apellidos) ?></span>?</p>
                    <p class="text-muted small">Se cerrará tu sesión actual y se te redirigirá a la página de inicio de sesión.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-secondary btn-lg btn-sm" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <a href="../api/logout2.php" class="btn btn-danger btn-lg btn-sm">
                        <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>