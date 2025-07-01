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
                                    <li><a class="dropdown-item text-danger" href="../api/logout.php"><i
                                                class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
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
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPermisoModal">
                                    <i class="bi bi-plus-circle me-1"></i> Añadir Departamento
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

                            // Consulta para obtener los departamentos
                            $sql = "SELECT * FROM tbl_departamentos ORDER BY ID_Departamento DESC";
                            $stmt = $pdo->query($sql);
                            $departamentos = $stmt->fetchAll();
                            ?>

                            <table class="table table-hover align-middle mb-0" id="funcionariosTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Ubicación</th>
                                        <th>Teléfono</th>
                                        <th>Provincia</th>
                                        <th>Distrito</th>
                                        <th>Ciudad</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="funcionariosTableBody">
                                    <?php foreach ($departamentos as $dept): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($dept['ID_Departamento']) ?></td>
                                            <td><?= htmlspecialchars($dept['Nombre_Departamento']) ?></td>
                                            <td><?= htmlspecialchars($dept['Ubicacion']) ?: '<span class="text-muted">No especificada</span>' ?></td>
                                            <td><?= htmlspecialchars($dept['Telefono_Departamento']) ?: '<span class="text-muted">No especificado</span>' ?></td>
                                            <td><?= htmlspecialchars($dept['Provincia']) ?></td>
                                            <td><?= htmlspecialchars($dept['Distrito']) ?></td>
                                            <td><?= htmlspecialchars($dept['Ciudad']) ?></td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button
                                                        class="btn btn-sm btn-warning btn-editar-departamento"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editDepartamentoModal"
                                                        data-id="<?= $dept['ID_Departamento'] ?>"
                                                        data-nombre="<?= htmlspecialchars($dept['Nombre_Departamento']) ?>"
                                                        data-ubicacion="<?= htmlspecialchars($dept['Ubicacion']) ?>"
                                                        data-telefono="<?= htmlspecialchars($dept['Telefono_Departamento']) ?>"
                                                        data-provincia="<?= htmlspecialchars($dept['Provincia']) ?>"
                                                        data-distrito="<?= htmlspecialchars($dept['Distrito']) ?>"
                                                        data-ciudad="<?= htmlspecialchars($dept['Ciudad']) ?>"
                                                        title="Editar Departamento">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>

                                                    <button class="btn btn-sm btn-danger btn-eliminar-departamento" title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
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




    <!-- Modal para Registrar Permiso -->
    <!-- Modal para Registrar Departamento -->
    <div class="modal fade" id="addPermisoModal" tabindex="-1" aria-labelledby="addPermisoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addPermisoModalLabel">
                        <i class="bi bi-building me-2"></i> Añadir Departamento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">

                    <form method="POST" action="../api/guardar_departamento.php" enctype="multipart/form-data" novalidate>
                        <div class="row g-3">

                            <!-- Nombre Departamento -->
                            <div class="col-md-6">
                                <label for="nombreDepartamento" class="form-label fw-semibold">
                                    <i class="bi bi-building text-primary me-2"></i>Nombre Departamento <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="Nombre_Departamento" id="nombreDepartamento" class="form-control" placeholder="Ej: Recursos Humanos" required>
                                <div class="invalid-feedback">
                                    Por favor ingrese el nombre del departamento.
                                </div>
                            </div>

                            <!-- Ubicación -->
                            <div class="col-md-6">
                                <label for="ubicacion" class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>Ubicación
                                </label>
                                <input type="text" name="Ubicacion" id="ubicacion" class="form-control" placeholder="Ej: Edificio Central">
                            </div>

                            <!-- Teléfono Departamento -->
                            <div class="col-md-6">
                                <label for="telefonoDepartamento" class="form-label fw-semibold">
                                    <i class="bi bi-telephone text-primary me-2"></i>Teléfono Departamento
                                </label>
                                <input type="tel" name="Telefono_Departamento" id="telefonoDepartamento" class="form-control" placeholder="Ej: +240 222 123 456" pattern="^\+?\d{7,15}$">
                                <div class="invalid-feedback">
                                    Por favor ingrese un teléfono válido (7-15 dígitos).
                                </div>
                            </div>

                            <!-- Provincia -->
                            <div class="col-md-4">
                                <label for="provincia" class="form-label fw-semibold">
                                    <i class="bi bi-map text-primary me-2"></i>Provincia <span class="text-danger">*</span>
                                </label>
                                <select name="Provincia" id="provincia" class="form-select" required>
                                    <option value="" disabled selected>Selecciona provincia</option>
                                    <option value="Región Continental">Región Continental</option>
                                    <option value="Región Insular">Región Insular</option>
                                </select>
                                <div class="invalid-feedback">
                                    Seleccione una provincia válida.
                                </div>
                            </div>

                            <!-- Distrito -->
                            <div class="col-md-4">
                                <label for="distrito" class="form-label fw-semibold">
                                    <i class="bi bi-pin-map text-primary me-2"></i>Distrito <span class="text-danger">*</span>
                                </label>
                                <select name="Distrito" id="distrito" class="form-select" required disabled>
                                    <option value="" disabled selected>Selecciona distrito</option>
                                </select>
                                <div class="invalid-feedback">
                                    Seleccione un distrito válido.
                                </div>
                            </div>

                            <!-- Ciudad -->
                            <div class="col-md-4">
                                <label for="ciudad" class="form-label fw-semibold">
                                    <i class="bi bi-building-fill text-primary me-2"></i>Ciudad <span class="text-danger">*</span>
                                </label>
                                <select name="Ciudad" id="ciudad" class="form-select" required disabled>
                                    <option value="" disabled selected>Selecciona ciudad</option>
                                </select>
                                <div class="invalid-feedback">
                                    Seleccione una ciudad válida.
                                </div>
                            </div>

                        </div>

                        <!-- Botón enviar -->
                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-2"></i> Registrar Departamento
                            </button>
                        </div>
                    </form>



                </div>
            </div>
        </div>
    </div>




    <!-- Modal para Editar Departamento -->
    <div class="modal fade" id="editDepartamentoModal" tabindex="-1" aria-labelledby="editDepartamentoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="formEditarDepartamento" method="POST" action="../api/actualizar_departamento.php" novalidate>
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title" id="editDepartamentoModalLabel">
                            <i class="bi bi-pencil-square me-2"></i>Editar Departamento
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="ID_Departamento" id="editID_Departamento">

                        <div class="row g-3">
                            <!-- Nombre Departamento -->
                            <div class="col-md-6">
                                <label for="editNombreDepartamento" class="form-label fw-semibold">
                                    <i class="bi bi-building text-primary me-2"></i>Nombre Departamento <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="Nombre_Departamento" id="editNombreDepartamento" class="form-control" required>
                                <div class="invalid-feedback">Por favor ingrese el nombre del departamento.</div>
                            </div>

                            <!-- Ubicación -->
                            <div class="col-md-6">
                                <label for="editUbicacion" class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>Ubicación
                                </label>
                                <input type="text" name="Ubicacion" id="editUbicacion" class="form-control" placeholder="Ej: Edificio Central">
                            </div>

                            <!-- Teléfono Departamento -->
                            <div class="col-md-6">
                                <label for="editTelefonoDepartamento" class="form-label fw-semibold">
                                    <i class="bi bi-telephone text-primary me-2"></i>Teléfono Departamento
                                </label>
                                <input type="tel" name="Telefono_Departamento" id="editTelefonoDepartamento" class="form-control" placeholder="Ej: +240 222 123 456" pattern="^\+?\d{7,15}$">
                                <div class="invalid-feedback">Por favor ingrese un teléfono válido (7-15 dígitos).</div>
                            </div>

                            <!-- Provincia -->
                            <div class="col-md-6">
                                <label for="editProvincia" class="form-label fw-semibold">
                                    <i class="bi bi-map text-primary me-2"></i>Provincia <span class="text-danger">*</span>
                                </label>
                                <select name="Provincia" id="editProvincia" class="form-select" required>
                                    <option value="" disabled selected>Selecciona provincia</option>
                                    <option value="Región Continental">Región Continental</option>
                                    <option value="Región Insular">Región Insular</option>
                                </select>
                                <div class="invalid-feedback">Seleccione una provincia válida.</div>
                            </div>

                            <!-- Distrito -->
                            <div class="col-md-6">
                                <label for="editDistrito" class="form-label fw-semibold">
                                    <i class="bi bi-pin-map text-primary me-2"></i>Distrito <span class="text-danger">*</span>
                                </label>
                                <select name="Distrito" id="editDistrito" class="form-select" required disabled>
                                    <option value="" disabled selected>Selecciona distrito</option>
                                </select>
                                <div class="invalid-feedback">Seleccione un distrito válido.</div>
                            </div>

                            <!-- Ciudad -->
                            <div class="col-md-6">
                                <label for="editCiudad" class="form-label fw-semibold">
                                    <i class="bi bi-building text-primary me-2"></i>Ciudad <span class="text-danger">*</span>
                                </label>
                                <select name="Ciudad" id="editCiudad" class="form-select" required disabled>
                                    <option value="" disabled selected>Selecciona ciudad</option>
                                </select>
                                <div class="invalid-feedback">Seleccione una ciudad válida.</div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save me-1"></i>Actualizar Departamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>






<script>
    //archivo para editar
    document.addEventListener('DOMContentLoaded', () => {
  const editModal = document.getElementById('editDepartamentoModal');

  // Mapas para distritos y ciudades según provincia
  const distritosPorProvincia = {
    "Región Continental": [
      "Centro Sur", "Kié-Ntem", "Litoral", "Wele-Nzas", "C Ni Mv B"
    ],
    "Región Insular": [
      "Bioko Norte", "Bioko Sur"
    ]
  };

  const ciudadesPorDistrito = {
    "Centro Sur": ["Evinayong", "Nsork"],
    "Kié-Ntem": ["Ebebiyín", "Aconibe", "Añisok"],
    "Litoral": ["Bata", "Mbini", "Kogo"],
    "Wele-Nzas": ["Mongomo", "Acurenam", "Nsok", "Mikomeseng"],
    "C Ni Mv B": ["Mongomoyen", "Miyobo", "Ngonamanga"],
    "Bioko Norte": ["Malabo", "Sacriba"],
    "Bioko Sur": ["Luba", "Riaba"]
  };

  // Referencias a selects del modal
  const provinciaSelect = document.getElementById('editProvincia');
  const distritoSelect = document.getElementById('editDistrito');
  const ciudadSelect = document.getElementById('editCiudad');

  // Función para llenar distritos según provincia
  function llenarDistritos(provincia, distritoSeleccionado = null) {
    distritoSelect.innerHTML = '<option value="" disabled selected>Selecciona distrito</option>';
    ciudadSelect.innerHTML = '<option value="" disabled selected>Selecciona ciudad</option>';
    ciudadSelect.disabled = true;

    if (!provincia || !distritosPorProvincia[provincia]) {
      distritoSelect.disabled = true;
      return;
    }

    distritoSelect.disabled = false;
    distritosPorProvincia[provincia].forEach(distrito => {
      const option = document.createElement('option');
      option.value = distrito;
      option.textContent = distrito;
      if (distrito === distritoSeleccionado) option.selected = true;
      distritoSelect.appendChild(option);
    });
  }

  // Función para llenar ciudades según distrito
  function llenarCiudades(distrito, ciudadSeleccionada = null) {
    ciudadSelect.innerHTML = '<option value="" disabled selected>Selecciona ciudad</option>';

    if (!distrito || !ciudadesPorDistrito[distrito]) {
      ciudadSelect.disabled = true;
      return;
    }

    ciudadSelect.disabled = false;
    ciudadesPorDistrito[distrito].forEach(ciudad => {
      const option = document.createElement('option');
      option.value = ciudad;
      option.textContent = ciudad;
      if (ciudad === ciudadSeleccionada) option.selected = true;
      ciudadSelect.appendChild(option);
    });
  }

  // Evento para cuando cambie la provincia en el modal
  provinciaSelect.addEventListener('change', () => {
    llenarDistritos(provinciaSelect.value);
  });

  // Evento para cuando cambie el distrito en el modal
  distritoSelect.addEventListener('change', () => {
    llenarCiudades(distritoSelect.value);
  });

  // Listener para abrir modal y rellenar campos
  editModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;

    const id = button.getAttribute('data-id');
    const nombre = button.getAttribute('data-nombre');
    const ubicacion = button.getAttribute('data-ubicacion');
    const telefono = button.getAttribute('data-telefono');
    const provincia = button.getAttribute('data-provincia');
    const distrito = button.getAttribute('data-distrito');
    const ciudad = button.getAttribute('data-ciudad');

    // Asignar valores a inputs
    document.getElementById('editID_Departamento').value = id;
    document.getElementById('editNombreDepartamento').value = nombre;
    document.getElementById('editUbicacion').value = ubicacion;
    document.getElementById('editTelefonoDepartamento').value = telefono;

    provinciaSelect.value = provincia || "";
    llenarDistritos(provincia, distrito);
    llenarCiudades(distrito, ciudad);
  });
});

</script>






    <script>
        // archivo para insertar
        const provinciaSelect = document.getElementById('provincia');
        const distritoSelect = document.getElementById('distrito');
        const ciudadSelect = document.getElementById('ciudad');

        // Distritos por provincia
        const distritosPorProvincia = {
            "Región Continental": [
                "Litoral",
                "Centro Sur",
                "Kié-Ntem",
                "Wele-Nzas",
                "Cantil",
                "Mbini"
            ],
            "Región Insular": [
                "Bioko Norte",
                "Bioko Sur",
                "Annobón"
            ]
        };

        // Ciudades por distrito
        const ciudadesPorDistrito = {
            "Litoral": ["Bata", "Mbini"],
            "Centro Sur": ["Evinayong", "Niefang"],
            "Kié-Ntem": ["Ebebiyin", "Aconibe"],
            "Wele-Nzas": ["Mikomeseng", "Mongomo", "Acalayong"],
            "Cantil": ["Acurenam"],
            "Mbini": ["Mengomeyén"],
            "Bioko Norte": ["Malabo", "Rebola"],
            "Bioko Sur": ["Luba", "Riaba"],
            "Annobón": ["San Antonio de Palé"]
        };

        // Cuando cambia la provincia, carga los distritos correspondientes
        provinciaSelect.addEventListener('change', () => {
            const provincia = provinciaSelect.value;
            distritoSelect.innerHTML = '<option value="" disabled selected>Selecciona distrito</option>';
            ciudadSelect.innerHTML = '<option value="" disabled selected>Selecciona ciudad</option>';
            ciudadSelect.disabled = true;

            if (provincia && distritosPorProvincia[provincia]) {
                distritoSelect.disabled = false;
                distritosPorProvincia[provincia].forEach(distrito => {
                    const option = document.createElement('option');
                    option.value = distrito;
                    option.textContent = distrito;
                    distritoSelect.appendChild(option);
                });
            } else {
                distritoSelect.disabled = true;
            }
        });

        // Cuando cambia el distrito, carga las ciudades correspondientes
        distritoSelect.addEventListener('change', () => {
            const distrito = distritoSelect.value;
            ciudadSelect.innerHTML = '<option value="" disabled selected>Selecciona ciudad</option>';

            if (distrito && ciudadesPorDistrito[distrito]) {
                ciudadSelect.disabled = false;
                ciudadesPorDistrito[distrito].forEach(ciudad => {
                    const option = document.createElement('option');
                    option.value = ciudad;
                    option.textContent = ciudad;
                    ciudadSelect.appendChild(option);
                });
            } else {
                ciudadSelect.disabled = true;
            }
        });

        // Bootstrap validation (opcional)
        (function() {
            'use strict'
            const forms = document.querySelectorAll('form[novalidate]')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })();
    </script>








    <!-- Modal Editar Permiso -->








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