<?php
include_once '../includes/header.php';
?>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="container-fluid p-0">
        <div class="row g-0">

            <?php
            include_once '../includes/silebar_admin.php';
            ?>


            <div class="main-content" id="mainContent">
                <div class="top-navbar">
                    <div class="d-flex justify-content-between align-items-center">
                        <button class="btn btn-outline-secondary d-md-none me-2 menu-toggle" id="sidebarToggle">
                            <i class="bi bi-list"></i>
                        </button>
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-custom mb-0">
                                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Inicio</a></li>
                                    <li class="breadcrumb-item active">Dashboard</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="input-group" style="width: 300px;">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0"
                                    placeholder="Buscar funcionario...">
                            </div>
                            <button class="btn btn-outline-primary btn-refresh" onclick="refreshData()">
                                <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle me-1"></i> <?= $nombre_usuario; ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Mi Perfil</a>
                                    </li>
                                    <li><a class="dropdown-item" href="#"><i
                                                class="bi bi-gear me-2"></i>Configuración</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                            <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="header-section">
                    <div class="row align-items-center">

                        <div class="col-md-12 text-md-end mt-3 mt-md-0">
                            <div
                                class="d-flex justify-content-md-end align-items-center gap-2 flex-wrap justify-content-center">
                                <!-- Botón para abrir modal de registrar asignación -->
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAsignacionModal">
                                    <i class="bi bi-plus-circle me-2"></i> Nueva Asignación
                                </button>

                                <div class="input-group" style="width: auto;">
                                    <input type="text" class="form-control" id="liveSearchInput"
                                        placeholder="Buscar en tabla...">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



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








                <div class="container-fluid px-4">
                    <div class="table-custom mb-4 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-semibold">Listado de Funcionarios</h5>
                        </div>
                        <div class="table-responsive">
                            <?php
                            try {
                                $pdo = new PDO($dsn, $user, $pass, $options);
                            } catch (PDOException $e) {
                                die("Error de conexión: " . $e->getMessage());
                            }

                            // Consulta para obtener asignaciones con datos asociados
                            $sql = "SELECT 
    a.ID_Asignacion,
    a.ID_Funcionario,
    a.ID_Cargo,
    a.ID_Departamento,
    a.ID_Destino,
    a.Fecha_Inicio_Asignacion,
    a.Fecha_Fin_Asignacion,
    f.Nombres,
    f.Apellidos,
    f.DNI_Pasaporte,
    c.Nombre_Cargo,
    d.Nombre_Departamento,
    dest.Nombre_Destino
FROM tbl_asignaciones a
JOIN tbl_funcionarios f ON a.ID_Funcionario = f.ID_Funcionario
JOIN tbl_cargos c ON a.ID_Cargo = c.ID_Cargo
JOIN tbl_departamentos d ON a.ID_Departamento = d.ID_Departamento
JOIN tbl_destinos dest ON a.ID_Destino = dest.ID_Destino
ORDER BY a.ID_Asignacion DESC";

                            $stmt = $pdo->query($sql);
                            $asignaciones = $stmt->fetchAll();
                            ?>

                            <table class="table table-hover align-middle mb-0" id="asignacionesTable">
                                <thead class="table table-hover align-middle mb-0" id="funcionariosTable">
                                    <tr>
                                        <th>ID</th>
                                        <th>Funcionario</th>
                                        <th>DNI</th>
                                        <th>Cargo</th>
                                        <th>Departamento</th>
                                        <th>Destino</th>
                                        <th>Inicio</th>
                                        <th>Fin</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="funcionariosTableBody">
                                    <?php foreach ($asignaciones as $asignacion): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($asignacion['ID_Asignacion']) ?></td>
                                            <td><?= htmlspecialchars($asignacion['Nombres'] . ' ' . $asignacion['Apellidos']) ?></td>
                                            <td><?= htmlspecialchars($asignacion['DNI_Pasaporte']) ?></td>
                                            <td><?= htmlspecialchars($asignacion['Nombre_Cargo']) ?></td>
                                            <td><?= htmlspecialchars($asignacion['Nombre_Departamento']) ?></td>
                                            <td><?= htmlspecialchars($asignacion['Nombre_Destino']) ?></td>
                                            <td><?= htmlspecialchars($asignacion['Fecha_Inicio_Asignacion']) ?></td>
                                            <td><?= $asignacion['Fecha_Fin_Asignacion'] ? htmlspecialchars($asignacion['Fecha_Fin_Asignacion']) : '<span class="text-muted">Actual</span>' ?></td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm btn-warning btn-editar-asignacion"
                                                        data-id="<?= $asignacion['ID_Asignacion'] ?>"
                                                        data-funcionario="<?= $asignacion['ID_Funcionario'] ?>"
                                                        data-cargo="<?= $asignacion['ID_Cargo'] ?>"
                                                        data-departamento="<?= $asignacion['ID_Departamento'] ?>"
                                                        data-destino="<?= $asignacion['ID_Destino'] ?>"
                                                        data-fechainicio="<?= $asignacion['Fecha_Inicio_Asignacion'] ?>"
                                                        data-fechafin="<?= $asignacion['Fecha_Fin_Asignacion'] ?>"
                                                        title="Editar Asignación">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>

                                                    <button class="btn btn-sm btn-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>



                        </div>
                        <nav aria-label="Page navigation example" class="mt-3">
                            <ul class="pagination justify-content-center" id="paginationControls">
                                <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1"
                                        aria-disabled="true">Anterior</a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#">Siguiente</a></li>
                            </ul>
                        </nav>
                    </div>




                </div>
                <footer class="footer bg-white shadow-sm py-3 mt-auto">
                    <div class="container-fluid text-center">
                        <span class="text-muted">© 2024 Themis | Ministerio de Justicia. Todos los derechos
                            reservados.</span>
                    </div>
                </footer>
            </div>
        </div>
    </div>





    <?php
    // Consulta para cargos
    $stmtCargos = $pdo->query("SELECT ID_Cargo, Nombre_Cargo FROM tbl_cargos ORDER BY Nombre_Cargo ASC");
    $cargos = $stmtCargos->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para departamentos
    $stmtDepartamentos = $pdo->query("SELECT ID_Departamento, Nombre_Departamento FROM tbl_departamentos ORDER BY Nombre_Departamento ASC");
    $departamentos = $stmtDepartamentos->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para destinos
    $stmtDestinos = $pdo->query("SELECT ID_Destino, Nombre_Destino FROM tbl_destinos ORDER BY Nombre_Destino ASC");
    $destinos = $stmtDestinos->fetchAll(PDO::FETCH_ASSOC);
    ?>


    <!-- Modal para Registrar Asignación -->
    <div class="modal fade" id="addAsignacionModal" tabindex="-1" aria-labelledby="addAsignacionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addAsignacionModalLabel">
                        <i class="bi bi-person-plus me-2"></i>Registrar Asignación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">


                    <!-- Buscador -->
                    <div class="mb-4">
                        <label for="searchFuncionario" class="form-label fw-semibold">
                            <i class="bi bi-search me-2 text-primary"></i>Buscar Funcionario
                        </label>
                        <input type="text" id="searchFuncionario" class="form-control" placeholder="Escriba un nombre...">
                    </div>

                    <!-- Lista de funcionarios -->
                    <div class="list-group mb-3" id="listaFuncionarios"></div>

                    <!-- Funcionario seleccionado -->
                    <div id="funcionarioSeleccionado" class="mb-4 d-none">
                        <div class="alert alert-info d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-person-check-fill me-2"></i>
                                <span id="nombreFuncionario"></span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="quitarSeleccion">
                                <i class="bi bi-x-circle"></i> Quitar
                            </button>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <form method="POST" action="../api/guardar_asignacion.php">
                        <input type="hidden" name="ID_Funcionario" id="ID_Funcionario">

                        <div class="row g-3">
                            <!-- Cargo -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-award text-success me-2"></i>Cargo
                                </label>
                                <select class="form-select" name="ID_Cargo" id="cargoAsignacion" required></select>
                            </div>

                            <!-- Departamento -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-diagram-3 text-success me-2"></i>Departamento
                                </label>
                                <select class="form-select" name="ID_Departamento" id="departamentoAsignacion" required></select>
                            </div>

                            <!-- Destino -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt text-success me-2"></i>Destino
                                </label>
                                <select class="form-select" name="ID_Destino" id="destinoAsignacion" required></select>
                            </div>

                            <!-- Fechas -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-event text-success me-2"></i>Fecha Inicio
                                </label>
                                <input type="date" name="Fecha_Inicio_Asignacion" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-check text-success me-2"></i>Fecha Fin (opcional)
                                </label>
                                <input type="date" name="Fecha_Fin_Asignacion" class="form-control">
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-2"></i>Guardar Asignación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Datos traídos desde PHP
        const cargos = <?= json_encode($cargos) ?>;
        const departamentos = <?= json_encode($departamentos) ?>;
        const destinos = <?= json_encode($destinos) ?>;

        // Función para poblar un select con opciones
        function poblarSelect(selectId, items, idField, textField) {
            const select = document.getElementById(selectId);
            if (!select) return;
            select.innerHTML = '<option value="" selected disabled>Seleccione...</option>';
            items.forEach(item => {
                const option = document.createElement('option');
                option.value = item[idField];
                option.textContent = item[textField];
                select.appendChild(option);
            });
        }

        // Poblar los selects al cargar la página o modal
        document.addEventListener('DOMContentLoaded', () => {
            poblarSelect('cargoAsignacion', cargos, 'ID_Cargo', 'Nombre_Cargo');
            poblarSelect('departamentoAsignacion', departamentos, 'ID_Departamento', 'Nombre_Departamento');
            poblarSelect('destinoAsignacion', destinos, 'ID_Destino', 'Nombre_Destino');
        });
    </script>







 <!-- Modal para Editar Asignación -->
<?php
// Cargar cargos
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $cargos = $pdo->query("SELECT ID_Cargo, Nombre_Cargo FROM tbl_cargos ORDER BY Nombre_Cargo ASC")->fetchAll();
    $departamentos = $pdo->query("SELECT ID_Departamento, Nombre_Departamento FROM tbl_departamentos ORDER BY Nombre_Departamento ASC")->fetchAll();
    $destinos = $pdo->query("SELECT ID_Destino, Nombre_Destino FROM tbl_destinos ORDER BY Nombre_Destino ASC")->fetchAll();
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}
?>

<!-- Modal para Editar Asignación -->
<div class="modal fade" id="editAsignacionModal" tabindex="-1" aria-labelledby="editAsignacionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="editAsignacionModalLabel">
          <i class="bi bi-pencil-square me-2"></i>Editar Asignación
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../api/actualizar_asignacion.php">
          <input type="hidden" name="ID_Asignacion" id="edit_ID_Asignacion">
          <input type="hidden" name="ID_Funcionario" id="edit_ID_Funcionario">

          <div class="row g-3">
            <!-- Cargo -->
            <div class="col-md-4">
              <label class="form-label fw-semibold">
                <i class="bi bi-award text-success me-2"></i>Cargo
              </label>
              <select class="form-select" name="ID_Cargo" id="edit_cargoAsignacion" required>
                <option value="">Seleccione cargo</option>
                <?php foreach ($cargos as $cargo): ?>
                  <option value="<?= $cargo['ID_Cargo'] ?>"><?= htmlspecialchars($cargo['Nombre_Cargo']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Departamento -->
            <div class="col-md-4">
              <label class="form-label fw-semibold">
                <i class="bi bi-diagram-3 text-success me-2"></i>Departamento
              </label>
              <select class="form-select" name="ID_Departamento" id="edit_departamentoAsignacion" required>
                <option value="">Seleccione departamento</option>
                <?php foreach ($departamentos as $depto): ?>
                  <option value="<?= $depto['ID_Departamento'] ?>"><?= htmlspecialchars($depto['Nombre_Departamento']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Destino -->
            <div class="col-md-4">
              <label class="form-label fw-semibold">
                <i class="bi bi-geo-alt text-success me-2"></i>Destino
              </label>
              <select class="form-select" name="ID_Destino" id="edit_destinoAsignacion" required>
                <option value="">Seleccione destino</option>
                <?php foreach ($destinos as $dest): ?>
                  <option value="<?= $dest['ID_Destino'] ?>"><?= htmlspecialchars($dest['Nombre_Destino']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Fechas -->
            <div class="col-md-6">
              <label class="form-label fw-semibold">
                <i class="bi bi-calendar-event text-success me-2"></i>Fecha Inicio
              </label>
              <input type="date" name="Fecha_Inicio_Asignacion" class="form-control" id="edit_Fecha_Inicio" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">
                <i class="bi bi-calendar-check text-success me-2"></i>Fecha Fin (opcional)
              </label>
              <input type="date" name="Fecha_Fin_Asignacion" class="form-control" id="edit_Fecha_Fin">
            </div>
          </div>

          <div class="mt-4 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-2"></i>Guardar Cambios
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>



 <script>
document.addEventListener("DOMContentLoaded", function () {
  // Captura todos los botones de edición
  const botones = document.querySelectorAll(".btn-editar-asignacion");

  botones.forEach(btn => {
    btn.addEventListener("click", function () {
      // Obtener los datos desde los atributos del botón
      const idAsignacion = this.dataset.id;
      const idFuncionario = this.dataset.funcionario;
      const idCargo = this.dataset.cargo;
      const idDepartamento = this.dataset.departamento;
      const idDestino = this.dataset.destino;
      const fechaInicio = this.dataset.fechainicio;
      const fechaFin = this.dataset.fechafin;

      // Asignar valores al formulario del modal
      document.getElementById("edit_ID_Asignacion").value = idAsignacion;
      document.getElementById("edit_ID_Funcionario").value = idFuncionario;
      document.getElementById("edit_cargoAsignacion").value = idCargo;
      document.getElementById("edit_departamentoAsignacion").value = idDepartamento;
      document.getElementById("edit_destinoAsignacion").value = idDestino;
      document.getElementById("edit_Fecha_Inicio").value = fechaInicio;
      document.getElementById("edit_Fecha_Fin").value = fechaFin;

      // Mostrar el modal
      const modal = new bootstrap.Modal(document.getElementById("editAsignacionModal"));
      modal.show();
    });
  });
});
</script>









    <!-- Script para buscar y seleccionar funcionario -->
    <!-- Agrega este script justo antes de </body> -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchFuncionario');
            const listaFuncionarios = document.getElementById('listaFuncionarios');
            const seleccionadoDiv = document.getElementById('funcionarioSeleccionado');
            const nombreFuncionarioSpan = document.getElementById('nombreFuncionario');
            const quitarBtn = document.getElementById('quitarSeleccion');
            const idFuncionarioInput = document.getElementById('ID_Funcionario');

            searchInput.addEventListener('input', () => {
                const query = searchInput.value.trim();
                if (query.length < 2) {
                    listaFuncionarios.innerHTML = '';
                    return;
                }
                fetch(`../api/buscar_funcionarios.php?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            listaFuncionarios.innerHTML = `<div class="text-danger">Error: ${data.error}</div>`;
                            return;
                        }
                        if (!Array.isArray(data) || data.length === 0) {
                            listaFuncionarios.innerHTML = `<div class="text-muted">No se encontraron funcionarios</div>`;
                            return;
                        }
                        listaFuncionarios.innerHTML = '';
                        data.forEach(f => {
                            const item = document.createElement('button');
                            item.type = 'button';
                            item.className = 'list-group-item list-group-item-action';
                            item.textContent = `${f.Nombres} ${f.Apellidos} - ${f.DNI_Pasaporte}`;
                            item.addEventListener('click', () => {
                                idFuncionarioInput.value = f.ID_Funcionario;
                                nombreFuncionarioSpan.textContent = `${f.Nombres} ${f.Apellidos} - DOCUMENTO: ${f.DNI_Pasaporte}`;
                                seleccionadoDiv.classList.remove('d-none');
                                listaFuncionarios.innerHTML = '';
                                searchInput.value = '';
                            });
                            listaFuncionarios.appendChild(item);
                        });
                    })
                    .catch(err => {
                        listaFuncionarios.innerHTML = `<div class="text-danger">Error al buscar funcionarios</div>`;
                        console.error(err);
                    });
            });

            quitarBtn.addEventListener('click', () => {
                idFuncionarioInput.value = '';
                nombreFuncionarioSpan.textContent = '';
                seleccionadoDiv.classList.add('d-none');
            });
        });
    </script>







    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const botonesEditar = document.querySelectorAll('.btn-editar-permiso');

            botonesEditar.forEach(boton => {
                boton.addEventListener('click', () => {
                    const modal = new bootstrap.Modal(document.getElementById('editPermisoModal'));

                    // Obtener los datos del botón
                    document.getElementById('edit_ID_Permiso').value = boton.dataset.id;
                    document.getElementById('edit_nombreFuncionario').value = boton.dataset.funcionario;
                    document.getElementById('edit_Tipo_Permiso').value = boton.dataset.tipo;
                    document.getElementById('edit_Estado_Permiso').value = boton.dataset.estado;
                    document.getElementById('edit_Fecha_Inicio').value = boton.dataset.inicio;
                    document.getElementById('edit_Fecha_Fin').value = boton.dataset.fin;
                    document.getElementById('edit_Motivo').value = boton.dataset.motivo;
                    document.getElementById('edit_Observaciones').value = boton.dataset.observaciones;

                    modal.show();
                });
            });
        });
    </script>


























    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle for mobile
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            });

            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });


            // Live Search for Table
            const liveSearchInput = document.getElementById('liveSearchInput');
            const funcionariosTableBody = document.getElementById('funcionariosTableBody');

            liveSearchInput.addEventListener('keyup', function() {
                const searchTerm = liveSearchInput.value.toLowerCase();
                const rows = funcionariosTableBody.getElementsByTagName('tr');

                for (let i = 0; i < rows.length; i++) {
                    let rowText = rows[i].textContent.toLowerCase();
                    if (rowText.includes(searchTerm)) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            });

            // Function for refreshing data (example)
            window.refreshData = function() {
                const refreshBtn = document.querySelector('.btn-refresh');
                refreshBtn.classList.add('refreshing');
                // Simulate data fetching
                setTimeout(() => {
                    alert('Datos actualizados!');
                    refreshBtn.classList.remove('refreshing');
                }, 1000);
            };




            // Pagination logic (Client-side example)
            const rowsPerPage = 8; // Number of rows per page
            const tableRows = funcionariosTableBody.getElementsByTagName('tr');
            const totalPages = Math.ceil(tableRows.length / rowsPerPage);
            const paginationControls = document.getElementById('paginationControls');

            function displayPage(page) {
                const startIndex = (page - 1) * rowsPerPage;
                const endIndex = startIndex + rowsPerPage;

                for (let i = 0; i < tableRows.length; i++) {
                    if (i >= startIndex && i < endIndex) {
                        tableRows[i].style.display = '';
                    } else {
                        tableRows[i].style.display = 'none';
                    }
                }

                // Update pagination controls
                updatePaginationButtons(page);
            }

            function setupPagination() {
                paginationControls.innerHTML = ''; // Clear existing buttons

                const prevButton = document.createElement('li');
                prevButton.classList.add('page-item');
                prevButton.innerHTML = '<a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a>';
                paginationControls.appendChild(prevButton);

                for (let i = 1; i <= totalPages; i++) {
                    const pageItem = document.createElement('li');
                    pageItem.classList.add('page-item');
                    pageItem.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    pageItem.addEventListener('click', function(e) {
                        e.preventDefault();
                        displayPage(i);
                    });
                    paginationControls.appendChild(pageItem);
                }

                const nextButton = document.createElement('li');
                nextButton.classList.add('page-item');
                nextButton.innerHTML = '<a class="page-link" href="#">Siguiente</a>';
                paginationControls.appendChild(nextButton);

                displayPage(1); // Show first page initially
            }

            function updatePaginationButtons(currentPage) {
                const pageItems = paginationControls.children;
                for (let i = 0; i < pageItems.length; i++) {
                    pageItems[i].classList.remove('active');
                }

                if (currentPage === 1) {
                    pageItems[0].classList.add('disabled'); // Previous button
                } else {
                    pageItems[0].classList.remove('disabled');
                }

                if (currentPage === totalPages) {
                    pageItems[pageItems.length - 1].classList.add('disabled'); // Next button
                } else {
                    pageItems[pageItems.length - 1].classList.remove('disabled');
                }

                // Mark current page as active
                pageItems[currentPage].classList.add('active'); // Adjust index because of prev button
            }

            setupPagination();

            // Event listeners for Previous and Next buttons
            paginationControls.children[0].addEventListener('click', function(e) {
                e.preventDefault();
                const currentPage = parseInt(paginationControls.querySelector('.page-item.active .page-link').textContent);
                if (currentPage > 1) {
                    displayPage(currentPage - 1);
                }
            });

            paginationControls.children[paginationControls.children.length - 1].addEventListener('click', function(e) {
                e.preventDefault();
                const currentPage = parseInt(paginationControls.querySelector('.page-item.active .page-link').textContent);
                if (currentPage < totalPages) {
                    displayPage(currentPage + 1);
                }
            });
        });
    </script>



















    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-editar-funcionario').forEach(button => {
                button.addEventListener('click', function() {
                    const datos = this.dataset;

                    // Llenar el formulario con los valores
                    document.getElementById('editIDFuncionario').value = datos.id;
                    document.getElementById('editCodigoFuncionario').value = datos.codigo;
                    document.getElementById('editNombres').value = datos.nombres;
                    document.getElementById('editApellidos').value = datos.apellidos;
                    document.getElementById('editDNI').value = datos.dni;
                    document.getElementById('editFechaNacimiento').value = datos.fechaNacimiento;
                    document.getElementById('editGenero').value = datos.genero;
                    document.getElementById('editNacionalidad').value = datos.nacionalidad;
                    document.getElementById('editDireccion').value = datos.direccion;
                    document.getElementById('editTelefono').value = datos.telefono;
                    document.getElementById('editEmail').value = datos.email;
                    document.getElementById('editFechaIngreso').value = datos.fechaIngreso;
                    document.getElementById('editEstadoLaboral').value = datos.estado;

                    // Imagen
                    const rutaFoto = datos.foto && datos.foto !== '' ? `../api/${datos.foto}` : '';
                    document.getElementById('previewEditFoto').src = rutaFoto;

                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById('editFuncionarioModal'));
                    modal.show();
                });
            });
        });
    </script>


    <?php
    include_once '../includes/footer.php';
    ?>