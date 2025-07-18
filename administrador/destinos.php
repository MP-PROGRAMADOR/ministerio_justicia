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

                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDestinoModal">
                                    <i class="bi bi-plus-circle me-2"></i>Nuevo Destino
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

                            // Consulta destinos
                            $sql = "SELECT * FROM tbl_destinos ORDER BY ID_Destino DESC";
                            $stmt = $pdo->query($sql);
                            $destinos = $stmt->fetchAll();
                            ?>

                            <table class="table table-hover align-middle mb-0" id="funcionariosTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Dirección</th>
                                        <th>Provincia</th>
                                        <th>Distrito</th>
                                        <th>Ciudad</th>
                                        <th>Teléfono</th>
                                        <th>Inicio</th>
                                        <th>Fin</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="funcionariosTableBody">
                                    <?php foreach ($destinos as $destino): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($destino['ID_Destino']) ?></td>
                                            <td><?= htmlspecialchars($destino['Nombre_Destino']) ?></td>
                                            <td><?= htmlspecialchars($destino['Tipo_Destino']) ?></td>
                                            <td><?= htmlspecialchars($destino['Direccion_Destino']) ?></td>
                                            <td><?= htmlspecialchars($destino['Provincia']) ?></td>
                                            <td><?= htmlspecialchars($destino['Distrito']) ?></td>
                                            <td><?= htmlspecialchars($destino['Ciudad']) ?></td>
                                            <td><?= htmlspecialchars($destino['Telefono_Destino']) ?></td>
                                            <td><?= htmlspecialchars($destino['Fecha_Destino']) ?></td>
                                            <td><?= htmlspecialchars($destino['Fecha_Fin_Destino']) ?></td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm btn-warning btn-editar-destino"
                                                        data-id="<?= $destino['ID_Destino'] ?>"
                                                        data-nombre="<?= htmlspecialchars($destino['Nombre_Destino']) ?>"
                                                        data-tipo="<?= $destino['Tipo_Destino'] ?>"
                                                        data-direccion="<?= htmlspecialchars($destino['Direccion_Destino']) ?>"
                                                        data-provincia="<?= $destino['Provincia'] ?>"
                                                        data-distrito="<?= $destino['Distrito'] ?>"
                                                        data-ciudad="<?= $destino['Ciudad'] ?>"
                                                        data-fechainicio="<?= $destino['Fecha_Destino'] ?>"
                                                        data-fechafin="<?= $destino['Fecha_Fin_Destino'] ?>"
                                                        data-telefono="<?= $destino['Telefono_Destino'] ?>">
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





    <!-- Modal para Registrar Destino -->
    <div class="modal fade" id="addDestinoModal" tabindex="-1" aria-labelledby="addDestinoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addDestinoModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>Registrar Nuevo Destino
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="../api/guardar_destino.php" enctype="multipart/form-data" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold"><i class="bi bi-geo text-primary me-2"></i>Nombre del Destino *</label>
                                <input type="text" class="form-control" name="Nombre_Destino" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold"><i class="bi bi-layers text-primary me-2"></i>Tipo de Destino *</label>
                                <select name="Tipo_Destino" class="form-select" required>
                                    <option selected disabled>Seleccione tipo</option>
                                    <option value="Juzgado">Juzgado</option>
                                    <option value="Tribunal">Tribunal</option>
                                    <option value="Fiscalia">Fiscalía</option>
                                    <option value="Sede Central">Sede Central</option>
                                    <option value="Oficina Regional">Oficina Regional</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold"><i class="bi bi-signpost text-primary me-2"></i>Dirección *</label>
                                <input type="text" class="form-control" name="Direccion_Destino" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><i class="bi bi-map text-primary me-2"></i>Provincia *</label>
                                <select name="Provincia" id="provincia" class="form-select" required></select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><i class="bi bi-pin-map text-primary me-2"></i>Distrito *</label>
                                <select name="Distrito" id="distrito" class="form-select" required disabled></select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><i class="bi bi-building text-primary me-2"></i>Ciudad *</label>
                                <select name="Ciudad" id="ciudad" class="form-select" required disabled></select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><i class="bi bi-calendar-event text-primary me-2"></i>Fecha Inicio *</label>
                                <input type="date" name="Fecha_Destino" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><i class="bi bi-calendar-check text-primary me-2"></i>Fecha Fin *</label>
                                <input type="date" name="Fecha_Fin_Destino" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><i class="bi bi-telephone text-primary me-2"></i>Teléfono *</label>
                                <input type="tel" name="Telefono_Destino" class="form-control" required pattern="^\+?\d{7,15}$">
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-2"></i>Guardar Destino
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const provinciasDistritosCiudades = {
                "Bioko Norte": {
                    "Malabo": ["Malabo", "Baney", "Basupu"]
                },
                "Bioko Sur": {
                    "Luba": ["Luba", "Batete"]
                },
                "Litoral": {
                    "Bata": ["Bata", "Mbini", "Kogo"]
                },
                "Centro Sur": {
                    "Evinayong": ["Evinayong", "Ncue"]
                },
                "Kie Ntem": {
                    "Ebebiyin": ["Ebebiyin", "Mikomeseng"]
                },
                "Wele Nzas": {
                    "Mongomo": ["Mongomo", "Akonibe"]
                },
                "Annobón": {
                    "San Antonio de Palé": ["San Antonio de Palé"]
                }
            };

            const provincia = document.getElementById("provincia");
            const distrito = document.getElementById("distrito");
            const ciudad = document.getElementById("ciudad");

            function llenarProvincias() {
                provincia.innerHTML = '<option value="">Seleccione Provincia</option>';
                for (const prov in provinciasDistritosCiudades) {
                    provincia.innerHTML += `<option value="${prov}">${prov}</option>`;
                }
            }

            function llenarDistritos(prov) {
                distrito.innerHTML = '<option value="">Seleccione Distrito</option>';
                ciudad.innerHTML = '<option value="">Seleccione Ciudad</option>';
                distrito.disabled = !prov;
                ciudad.disabled = true;

                if (prov) {
                    for (const dist in provinciasDistritosCiudades[prov]) {
                        distrito.innerHTML += `<option value="${dist}">${dist}</option>`;
                    }
                }
            }

            function llenarCiudades(prov, dist) {
                ciudad.innerHTML = '<option value="">Seleccione Ciudad</option>';
                ciudad.disabled = !dist;

                if (prov && dist && provinciasDistritosCiudades[prov][dist]) {
                    provinciasDistritosCiudades[prov][dist].forEach(ciu => {
                        ciudad.innerHTML += `<option value="${ciu}">${ciu}</option>`;
                    });
                }
            }

            llenarProvincias();

            provincia.addEventListener("change", () => llenarDistritos(provincia.value));
            distrito.addEventListener("change", () => llenarCiudades(provincia.value, distrito.value));
        });
    </script>





    <!-- Script para buscar y seleccionar funcionario -->

    <!-- Modal para Editar Destino -->
<!-- Modal para Editar Destino -->
<div class="modal fade" id="editDestinoModal" tabindex="-1" aria-labelledby="editDestinoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="editDestinoModalLabel">
          <i class="bi bi-pencil-square me-2"></i>Editar Destino
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../api/actualizar_destino.php" enctype="multipart/form-data">
          <input type="hidden" name="ID_Destino" id="edit_ID_Destino">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-building me-2 text-primary"></i>Nombre del Destino <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="Nombre_Destino" id="edit_Nombre_Destino" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-diagram-3-fill me-2 text-primary"></i>Tipo de Destino <span class="text-danger">*</span></label>
              <select class="form-select" name="Tipo_Destino" id="edit_Tipo_Destino" required>
                <option value="">Seleccione tipo</option>
                <option value="Juzgado">Juzgado</option>
                <option value="Tribunal">Tribunal</option>
                <option value="Fiscalia">Fiscalía</option>
                <option value="Sede Central">Sede Central</option>
                <option value="Oficina Regional">Oficina Regional</option>
                <option value="Otro">Otro</option>
              </select>
            </div>

            <div class="col-md-12">
              <label class="form-label fw-semibold"><i class="bi bi-geo-alt me-2 text-primary"></i>Dirección <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="Direccion_Destino" id="edit_Direccion_Destino" required>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold"><i class="bi bi-map-fill me-2 text-primary"></i>Provincia <span class="text-danger">*</span></label>
              <select class="form-select" name="Provincia" id="edit_provincia" required></select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold"><i class="bi bi-pin-map-fill me-2 text-primary"></i>Distrito <span class="text-danger">*</span></label>
              <select class="form-select" name="Distrito" id="edit_distrito" required></select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold"><i class="bi bi-building-fill me-2 text-primary"></i>Ciudad <span class="text-danger">*</span></label>
              <select class="form-select" name="Ciudad" id="edit_ciudad" required></select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-calendar-event me-2 text-primary"></i>Fecha Inicio <span class="text-danger">*</span></label>
              <input type="date" class="form-control" name="Fecha_Destino" id="edit_Fecha_Destino" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-calendar-check-fill me-2 text-primary"></i>Fecha Fin</label>
              <input type="date" class="form-control" name="Fecha_Fin_Destino" id="edit_Fecha_Fin_Destino">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-telephone me-2 text-primary"></i>Teléfono</label>
              <input type="text" class="form-control" name="Telefono_Destino" id="edit_Telefono_Destino">
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
  const provinciasDistritosCiudades = {
    "Bioko Norte": { "Malabo": ["Malabo", "Baney", "Basupu"] },
    "Bioko Sur": { "Luba": ["Luba", "Batete"] },
    "Litoral": { "Bata": ["Bata", "Mbini", "Kogo"] },
    "Centro Sur": { "Evinayong": ["Evinayong", "Ncue"] },
    "Kie Ntem": { "Ebebiyin": ["Ebebiyin", "Mikomeseng"] },
    "Wele Nzas": { "Mongomo": ["Mongomo", "Akonibe"] },
    "Annobón": { "San Antonio de Palé": ["San Antonio de Palé"] }
  };

  const provincia = document.getElementById("edit_provincia");
  const distrito = document.getElementById("edit_distrito");
  const ciudad = document.getElementById("edit_ciudad");

  function cargarProvincias() {
    provincia.innerHTML = '<option value="">Seleccione Provincia</option>';
    for (const prov in provinciasDistritosCiudades) {
      provincia.innerHTML += `<option value="${prov}">${prov}</option>`;
    }
  }

  function cargarDistritos(prov) {
    distrito.innerHTML = '<option value="">Seleccione Distrito</option>';
    ciudad.innerHTML = '<option value="">Seleccione Ciudad</option>';
    if (prov && provinciasDistritosCiudades[prov]) {
      for (const dist in provinciasDistritosCiudades[prov]) {
        distrito.innerHTML += `<option value="${dist}">${dist}</option>`;
      }
    }
  }

  function cargarCiudades(prov, dist) {
    ciudad.innerHTML = '<option value="">Seleccione Ciudad</option>';
    if (prov && dist && provinciasDistritosCiudades[prov][dist]) {
      provinciasDistritosCiudades[prov][dist].forEach(ciud => {
        ciudad.innerHTML += `<option value="${ciud}">${ciud}</option>`;
      });
    }
  }

  cargarProvincias();

  provincia.addEventListener("change", function () {
    cargarDistritos(this.value);
  });

  distrito.addEventListener("change", function () {
    cargarCiudades(provincia.value, this.value);
  });

  document.querySelectorAll(".btn-editar-destino").forEach(button => {
    button.addEventListener("click", function () {
      document.getElementById("edit_ID_Destino").value = this.dataset.id;
      document.getElementById("edit_Nombre_Destino").value = this.dataset.nombre;
      document.getElementById("edit_Tipo_Destino").value = this.dataset.tipo;
      document.getElementById("edit_Direccion_Destino").value = this.dataset.direccion;
      document.getElementById("edit_Fecha_Destino").value = this.dataset.fechainicio;
      document.getElementById("edit_Fecha_Fin_Destino").value = this.dataset.fechafin;
      document.getElementById("edit_Telefono_Destino").value = this.dataset.telefono;

      cargarProvincias();
      provincia.value = this.dataset.provincia;
      cargarDistritos(this.dataset.provincia);
      distrito.value = this.dataset.distrito;
      cargarCiudades(this.dataset.provincia, this.dataset.distrito);
      ciudad.value = this.dataset.ciudad;

      const modal = new bootstrap.Modal(document.getElementById("editDestinoModal"));
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






    <?php
    include_once '../includes/footer.php';
    ?>