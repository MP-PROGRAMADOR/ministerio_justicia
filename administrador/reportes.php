<?php
include_once '../includes/header.php';
?>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div> <!-- Overlay para cerrar sidebar en móvil -->

    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
           <?php
            include_once '../includes/silebar_admin.php';
            ?>

            <!-- Main Content -->
            <div class="main-content" id="mainContent">
                <!-- Top Navigation -->
                <div class="top-navbar">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Botón para mostrar/ocultar sidebar en móviles -->
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
                                <input type="text" class="form-control border-start-0" placeholder="Buscar funcionario...">
                            </div>
                            <button class="btn btn-outline-primary btn-refresh" onclick="refreshData()">
                                <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle me-1"></i> Juan Doe
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Mi Perfil</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Configuración</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Header Section -->
                <div class="header-section">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2 fw-bold">Panel de Administración</h2>
                            <p class="mb-0 text-muted">Sistema de Gestión de Recursos Humanos - Ministerio de Justicia de Guinea Ecuatorial</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="d-flex justify-content-md-end align-items-center gap-2 flex-wrap justify-content-center">
                                <select class="form-select" style="width: auto;">
                                    <option value="mes">Este mes</option>
                                    <option value="trimestre">Trimestre</option>
                                    <option value="año">Este año</option>
                                </select>
                                <button class="btn btn-primary">
                                    <i class="bi bi-download me-1"></i> Exportar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="container-fluid px-4">




                    <form id="filtroForm">
                        <div class="container my-4">
                            <div class="row g-4">
                                <!-- Aquí todos los filtros (igual que antes) -->
                                <!-- Estado Laboral -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-start border-4 border-primary">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary fw-bold d-flex align-items-center">
                                                <i class="bi bi-person-check me-2"></i> Estado Laboral
                                            </h6>
                                            <select class="form-select" name="estado_laboral">
                                                <option value="">-- Seleccionar --</option>
                                                <option value="Activo">Activo</option>
                                                <option value="Baja Temporal">Baja Temporal</option>
                                                <option value="Jubilado">Jubilado</option>
                                                <option value="Cesado">Cesado</option>
                                                <option value="Permiso">Permiso</option>
                                                <option value="Vacaciones">Vacaciones</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Departamento -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-start border-4 border-success">
                                        <div class="card-body">
                                            <h6 class="card-title text-success fw-bold d-flex align-items-center">
                                                <i class="bi bi-building me-2"></i> Departamento
                                            </h6>
                                            <select class="form-select" name="id_departamento">
                                                <option value="">-- Seleccionar --</option>
                                                <!-- Dinámico -->
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cargo -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-start border-4 border-warning">
                                        <div class="card-body">
                                            <h6 class="card-title text-warning fw-bold d-flex align-items-center">
                                                <i class="bi bi-briefcase-fill me-2"></i> Cargo
                                            </h6>
                                            <select class="form-select" name="id_cargo">
                                                <option value="">-- Seleccionar --</option>
                                                <!-- Dinámico -->
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Destino -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-start border-4 border-danger">
                                        <div class="card-body">
                                            <h6 class="card-title text-danger fw-bold d-flex align-items-center">
                                                <i class="bi bi-geo-alt-fill me-2"></i> Destino
                                            </h6>
                                            <select class="form-select" name="id_destino">
                                                <option value="">-- Seleccionar --</option>
                                                <!-- Dinámico -->
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Rango de Fechas -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-start border-4 border-info">
                                        <div class="card-body">
                                            <h6 class="card-title text-info fw-bold d-flex align-items-center">
                                                <i class="bi bi-calendar-range me-2"></i> Fecha de Asignación
                                            </h6>
                                            <div class="d-flex gap-2">
                                                <input type="date" class="form-control" name="fecha_inicio" placeholder="Desde" />
                                                <input type="date" class="form-control" name="fecha_fin" placeholder="Hasta" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reporte General -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-start border-4 border-secondary">
                                        <div class="card-body">
                                            <h6 class="card-title text-secondary fw-bold d-flex align-items-center">
                                                <i class="bi bi-list-check me-2"></i> Reporte General
                                            </h6>
                                            <div class="form-check mt-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="reporte_general"
                                                    value="1"
                                                    id="reporteGeneral" />
                                                <label class="form-check-label" for="reporteGeneral">Mostrar todos los funcionarios</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones -->
                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                                        <i class="bi bi-funnel-fill me-1"></i> Filtrar Resultados
                                    </button>
                                    <button type="button" id="btnLimpiar" class="btn btn-outline-secondary rounded-pill px-4">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Exportar botones -->
                    <div class="container my-3 d-flex justify-content-end gap-2">
                        <button id="btnExportExcel" class="btn btn-success rounded-pill px-4" disabled>
                            <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i> Exportar Excel
                        </button>
                        <button id="btnExportPDF" class="btn btn-danger rounded-pill px-4" disabled>
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Exportar PDF
                        </button>
                    </div>

                    <!-- Tabla resultados -->
                    <div class="container my-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle" id="tablaResultados">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Código</th>
                                        <th>Estado Laboral</th>
                                        <th>Departamento</th>
                                        <th>Cargo</th>
                                        <th>Destino</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <!-- 🔽 Aquí debajo pon el contenedor -->
                            <div id="paginacion" class="d-flex justify-content-center mt-3"></div>
                        </div>
                    </div>

                    <script>
                        let currentFilterData = [];
                        let currentPage = 1;
                        const rowsPerPage = 5;

                        const tbody = document.querySelector('#tablaResultados tbody');
                        const paginationDiv = document.getElementById('paginacion');

                        function renderPagina(page) {
                            tbody.innerHTML = '';
                            const start = (page - 1) * rowsPerPage;
                            const end = start + rowsPerPage;
                            const paginatedData = currentFilterData.slice(start, end);

                            paginatedData.forEach((f, i) => {
                                tbody.innerHTML += `
        <tr>
          <td>${start + i + 1}</td>
          <td>${f.Nombres} ${f.Apellidos}</td>
          <td>${f.Codigo_Funcionario}</td>
          <td>${f.Estado_Laboral}</td>
          <td>${f.Nombre_Departamento || '-'}</td>
          <td>${f.Nombre_Cargo || '-'}</td>
          <td>${f.Nombre_Destino || '-'}</td>
          <td>${f.Fecha_Inicio_Asignacion || '-'}</td>
          <td>${f.Fecha_Fin_Asignacion || '-'}</td>
        </tr>
      `;
                            });

                            renderControles();
                        }

                        function renderControles() {
                            paginationDiv.innerHTML = '';
                            const totalPages = Math.ceil(currentFilterData.length / rowsPerPage);

                            if (totalPages <= 1) return;

                            // Botón Anterior
                            const prevBtn = document.createElement('button');
                            prevBtn.className = 'btn btn-outline-secondary btn-sm me-1';
                            prevBtn.innerHTML = '<i class="bi bi-chevron-left"></i>';
                            prevBtn.disabled = currentPage === 1;
                            prevBtn.onclick = () => {
                                if (currentPage > 1) {
                                    currentPage--;
                                    renderPagina(currentPage);
                                }
                            };
                            paginationDiv.appendChild(prevBtn);

                            // Mostrar máximo 5 botones
                            let startPage = Math.max(1, currentPage - 2);
                            let endPage = Math.min(totalPages, startPage + 4);
                            if (endPage - startPage < 4) {
                                startPage = Math.max(1, endPage - 4);
                            }

                            for (let i = startPage; i <= endPage; i++) {
                                const btn = document.createElement('button');
                                btn.className = `btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-outline-primary'} mx-1`;
                                btn.textContent = i;
                                btn.addEventListener('click', () => {
                                    currentPage = i;
                                    renderPagina(currentPage);
                                });
                                paginationDiv.appendChild(btn);
                            }

                            // Botón Siguiente
                            const nextBtn = document.createElement('button');
                            nextBtn.className = 'btn btn-outline-secondary btn-sm ms-1';
                            nextBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
                            nextBtn.disabled = currentPage === totalPages;
                            nextBtn.onclick = () => {
                                if (currentPage < totalPages) {
                                    currentPage++;
                                    renderPagina(currentPage);
                                }
                            };
                            paginationDiv.appendChild(nextBtn);
                        }

                        document.querySelector('#filtroForm').addEventListener('submit', function(e) {
                            e.preventDefault();
                            const formData = new FormData(this);
                            console.log('🟢 Enviando filtros al servidor...');

                            fetch('../api/buscar_funcionarios23.php', {
                                    method: 'POST',
                                    body: formData,
                                })
                                .then(res => res.json())
                                .then(data => {
                                    console.log('🟢 Respuesta recibida:', data);
                                    if (data.success && data.data.length > 0) {
                                        currentFilterData = data.data;
                                        currentPage = 1;
                                        renderPagina(currentPage);
                                        document.getElementById('btnExportPDF').disabled = false;
                                    } else {
                                        tbody.innerHTML = `<tr><td colspan="9" class="text-center text-muted">No se encontraron resultados.</td></tr>`;
                                        paginationDiv.innerHTML = '';
                                        currentFilterData = [];
                                        document.getElementById('btnExportPDF').disabled = true;
                                    }
                                })
                                .catch(error => {
                                    console.error('❌ Error en la petición:', error);
                                });
                        });

                        document.getElementById('btnLimpiar').addEventListener('click', function() {
                            document.getElementById('filtroForm').reset();
                            tbody.innerHTML = `<tr><td colspan="9" class="text-center text-muted">Seleccione filtros y pulse "Filtrar Resultados".</td></tr>`;
                            paginationDiv.innerHTML = '';
                            currentFilterData = [];
                            currentPage = 1;
                            document.getElementById('btnExportPDF').disabled = true;
                            console.log('🔄 Formulario y tabla reiniciados.');
                        });

                        document.getElementById('btnExportPDF').addEventListener('click', function() {
                            if (!currentFilterData.length) return alert('⚠️ No hay datos para exportar');

                            const formData = new FormData(document.getElementById('filtroForm'));
                            formData.append('export', 'pdf');
                            console.log('📤 Enviando solicitud para generar PDF...');

                            fetch('../fpdf/buscar_funcionarios.php', {
                                    method: 'POST',
                                    body: formData,
                                })
                                .then(res => res.blob())
                                .then(blob => {
                                    const url = window.URL.createObjectURL(blob);
                                    const a = document.createElement('a');
                                    a.href = url;
                                    a.download = 'funcionarios.pdf';
                                    document.body.appendChild(a);
                                    a.click();
                                    a.remove();
                                    console.log('✅ Descarga del PDF iniciada.');
                                })
                                .catch(error => {
                                    console.error('❌ Error exportando PDF:', error);
                                });
                        });
                    </script>


                    <script>
                        // Ejecutar al cargar la página
                        document.addEventListener('DOMContentLoaded', () => {
                            console.log('🔄 Cargando filtros dinámicos...');

                            fetch('../api/cargar_filtros.php')
                                .then(res => res.json())
                                .then(data => {
                                    if (!data.success) {
                                        console.error('❌ Error en la carga de filtros:', data.message);
                                        return;
                                    }

                                    // Insertar departamentos
                                    const departamentoSelect = document.querySelector('select[name="id_departamento"]');
                                    data.departamentos.forEach(dep => {
                                        const option = document.createElement('option');
                                        option.value = dep.ID_Departamento;
                                        option.textContent = dep.Nombre_Departamento;
                                        departamentoSelect.appendChild(option);
                                    });

                                    // Insertar cargos
                                    const cargoSelect = document.querySelector('select[name="id_cargo"]');
                                    data.cargos.forEach(cargo => {
                                        const option = document.createElement('option');
                                        option.value = cargo.ID_Cargo;
                                        option.textContent = cargo.Nombre_Cargo;
                                        cargoSelect.appendChild(option);
                                    });

                                    // Insertar destinos
                                    const destinoSelect = document.querySelector('select[name="id_destino"]');
                                    data.destinos.forEach(dest => {
                                        const option = document.createElement('option');
                                        option.value = dest.ID_Destino;
                                        option.textContent = dest.Nombre_Destino;
                                        destinoSelect.appendChild(option);
                                    });

                                    console.log('✅ Filtros cargados correctamente.');
                                })
                                .catch(error => {
                                    console.error('❌ Error al cargar filtros dinámicos:', error);
                                });
                        });
                    </script>









                </div>

                <!-- Footer -->
                <footer class="bg-white text-center text-muted py-4 mt-4 border-top">
                    <p class="mb-0">&copy; 2025 Themis | Ministerio de Justicia de Guinea Ecuatorial. Todos los derechos reservados.</p>
                </footer>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Global variable for the chart instance
        let funcionariosChart;

        /**
         * Fetches data from the PHP backend and updates the dashboard.
         */
        async function fetchData() {
            const refreshButton = document.querySelector('.btn-refresh');
            refreshButton.classList.add('refreshing'); // Start animation

            try {
                // Fetch data from your PHP backend
                const response = await fetch('data.php'); // Asegúrate de que 'data.php' esté en la misma carpeta o proporciona la ruta correcta
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();

                if (data.status === 'error') {
                    console.error('Error fetching data from PHP:', data.message);
                    alert('Error al cargar los datos: ' + data.message); // Usar un modal en lugar de alert en producción
                    return;
                }

                const dashboardData = data.data;

                // --- 1. Update Statistics Cards ---
                document.getElementById('statTotalFuncionarios').textContent = dashboardData.totalFuncionarios || 0;
                document.getElementById('totalFuncionariosSidebar').textContent = dashboardData.totalFuncionarios || 0;
                document.getElementById('statFuncionariosActivos').textContent = dashboardData.funcionariosActivos || 0;
                document.getElementById('statPermisosEsteMes').textContent = dashboardData.permisosEsteMes || 0;
                document.getElementById('statPermisosPendientes').textContent = dashboardData.permisosPendientes || 0;
                document.getElementById('permisosPendientesSidebar').textContent = dashboardData.permisosPendientes || 0;

                // Toggle notification dot based on pending permits
                const permisosNotifDot = document.getElementById('permisosNotifDot');
                if (dashboardData.permisosPendientes > 0) {
                    permisosNotifDot.style.display = 'block';
                } else {
                    permisosNotifDot.style.display = 'none';
                }

                document.getElementById('statDestinosActivos').textContent = dashboardData.destinosActivos || 0;
                document.getElementById('totalDestinosSidebar').textContent = dashboardData.destinosActivos || 0;

                // Calculate active percentage and Juzgados (mock data or refine PHP)
                const totalFunc = dashboardData.totalFuncionarios || 0;
                const activeFunc = dashboardData.funcionariosActivos || 0;
                const activePercent = totalFunc > 0 ? ((activeFunc / totalFunc) * 100).toFixed(1) : 0;
                document.getElementById('statActivosPercent').textContent = `${activePercent}%`;

                // For '15 Juzgados', we'll need specific data from destinationTypes
                const juzgadosCount = dashboardData.destinationTypes.find(d => d.Tipo_Destino === 'Juzgado')?.count || 0;
                document.getElementById('statJuzgados').textContent = juzgadosCount;

                // --- 2. Update Doughnut Chart (Funcionarios Distribution) ---
                if (funcionariosChart) {
                    funcionariosChart.data.labels = dashboardData.funcionarioDistribution.labels;
                    funcionariosChart.data.datasets[0].data = dashboardData.funcionarioDistribution.data;
                    // Define colors for each state explicitly or derive from a palette
                    // Ensure you have enough colors for all possible states
                    const backgroundColors = {
                        'Activo': 'rgba(5, 150, 105, 0.8)', // success
                        'En Permiso': 'rgba(217, 119, 6, 0.8)', // warning
                        'Inactivo': 'rgba(220, 38, 38, 0.8)', // danger
                        'Jubilado': 'rgba(71, 85, 105, 0.8)', // secondary
                        'Cesado': 'rgba(220, 38, 38, 0.8)', // danger
                        'Vacaciones': 'rgba(14, 165, 233, 0.8)', // info/accent
                        'Baja Temporal': 'rgba(245, 158, 11, 0.8)', // orange
                        'Otro': 'rgba(100, 116, 139, 0.8)' // slate
                    };
                    funcionariosChart.data.datasets[0].backgroundColor = dashboardData.funcionarioDistribution.labels.map(label => backgroundColors[label] || 'rgba(150, 150, 150, 0.8)'); // Default grey if no specific color
                    funcionariosChart.update();
                } else {
                    // Initialize chart if it doesn't exist
                    const ctx = document.getElementById('funcionariosChart').getContext('2d');
                    const backgroundColors = {
                        'Activo': 'rgba(5, 150, 105, 0.8)', // success
                        'En Permiso': 'rgba(217, 119, 6, 0.8)', // warning
                        'Inactivo': 'rgba(220, 38, 38, 0.8)', // danger
                        'Jubilado': 'rgba(71, 85, 105, 0.8)', // secondary
                        'Cesado': 'rgba(220, 38, 38, 0.8)', // danger
                        'Vacaciones': 'rgba(14, 165, 233, 0.8)', // info/accent
                        'Baja Temporal': 'rgba(245, 158, 11, 0.8)', // orange
                        'Otro': 'rgba(100, 116, 139, 0.8)' // slate
                    };
                    const colors = dashboardData.funcionarioDistribution.labels.map(label => backgroundColors[label] || 'rgba(150, 150, 150, 0.8)');

                    funcionariosChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: dashboardData.funcionarioDistribution.labels,
                            datasets: [{
                                data: dashboardData.funcionarioDistribution.data,
                                backgroundColor: colors,
                                borderColor: '#ffffff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 20
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            let label = tooltipItem.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            label += tooltipItem.raw + ' funcionarios';
                                            return label;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // --- 3. Update Department Progress Bars ---
                const departmentProgressBars = document.getElementById('departmentProgressBars');
                departmentProgressBars.innerHTML = ''; // Clear previous content

                if (dashboardData.departmentStaff && dashboardData.departmentStaff.length > 0) {
                    const totalStaff = dashboardData.departmentStaff.reduce((sum, dept) => sum + parseInt(dept.num_funcionarios), 0);
                    const progressColors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary']; // Define more colors if needed

                    dashboardData.departmentStaff.forEach((dept, index) => {
                        const percentage = totalStaff > 0 ? ((parseInt(dept.num_funcionarios) / totalStaff) * 100).toFixed(1) : 0;
                        const colorClass = progressColors[index % progressColors.length];
                        departmentProgressBars.innerHTML += `
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-medium">${dept.Nombre_Departamento}</span>
                                    <span class="text-${colorClass.replace('bg-', '')} fw-bold">${dept.num_funcionarios} funcionarios</span>
                                </div>
                                <div class="progress progress-custom">
                                    <div class="progress-bar ${colorClass} progress-bar-custom" style="width: ${percentage}%"></div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    departmentProgressBars.innerHTML = '<div class="text-center text-muted py-4">No hay datos de departamentos disponibles.</div>';
                }


                // --- 4. Update Destination Type Cards ---
                const destinationTypeCards = document.getElementById('destinationTypeCards');
                destinationTypeCards.innerHTML = ''; // Clear previous content

                if (dashboardData.destinationTypes && dashboardData.destinationTypes.length > 0) {
                    const iconMap = {
                        'Juzgado': 'bi-bank',
                        'Tribunal': 'bi-building',
                        'Fiscalia': 'bi-shield-check',
                        'Sede Central': 'bi-house-door',
                        'Oficina Regional': 'bi-diagram-3',
                        'Otro': 'bi-geo-alt'
                    };
                    const colorMap = {
                        'Juzgado': 'primary',
                        'Tribunal': 'success',
                        'Fiscalia': 'warning',
                        'Sede Central': 'info',
                        'Oficina Regional': 'secondary',
                        'Otro': 'dark'
                    };

                    dashboardData.destinationTypes.forEach(dest => {
                        const iconClass = iconMap[dest.Tipo_Destino] || 'bi-question-circle';
                        const colorClass = colorMap[dest.Tipo_Destino] || 'secondary';
                        destinationTypeCards.innerHTML += `
                            <div class="col-4 mb-3">
                                <div class="stat-icon bg-${colorClass} bg-opacity-10 text-${colorClass} mx-auto mb-2" style="width: 48px; height: 48px; font-size: 1.1rem;">
                                    <i class="bi ${iconClass}"></i>
                                </div>
                                <div class="fw-bold text-${colorClass} fs-5">${dest.count}</div>
                                <small class="text-muted fw-medium">${dest.Tipo_Destino}</small>
                            </div>
                        `;
                    });
                } else {
                    destinationTypeCards.innerHTML = '<div class="text-center text-muted py-4">No hay datos de tipos de destino disponibles.</div>';
                }

                // --- 5. Update Recent Activity ---
                const recentActivityList = document.getElementById('recentActivityList');
                recentActivityList.innerHTML = ''; // Clear previous content

                if (dashboardData.recentActivity && dashboardData.recentActivity.length > 0) {
                    dashboardData.recentActivity.forEach(activity => {
                        let activityClass = '';
                        let description = '';
                        let timeAgo = new Date(activity.timestamp).toLocaleString('es-ES', {
                            dateStyle: 'medium',
                            timeStyle: 'short'
                        }); // Simple timestamp for now

                        switch (activity.type) {
                            case 'Nuevo Funcionario':
                                activityClass = 'recent'; // Green border
                                description = `Departamento de Recursos Humanos`;
                                recentActivityList.innerHTML += `
                                    <div class="activity-item ${activityClass}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="mb-0 fw-medium">Nuevo funcionario añadido: ${activity.Nombres} ${activity.Apellidos}</p>
                                            <small class="text-muted">${timeAgo}</small>
                                        </div>
                                        <small class="text-muted">${description}</small>
                                    </div>
                                `;
                                break;
                            case 'Permiso':
                                activityClass = activity.Estado_Permiso === 'Pendiente' ? 'warning' : 'default'; // Yellow for pending
                                description = `Permiso por ${activity.Tipo_Permiso}`;
                                recentActivityList.innerHTML += `
                                    <div class="activity-item ${activityClass}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="mb-0 fw-medium">Permiso ${activity.Estado_Permiso} para ${activity.Nombres} ${activity.Apellidos}</p>
                                            <small class="text-muted">${timeAgo}</small>
                                        </div>
                                        <small class="text-muted">${description}</small>
                                    </div>
                                `;
                                break;
                                // Add more cases for other activity types if needed
                            default:
                                recentActivityList.innerHTML += `
                                    <div class="activity-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="mb-0 fw-medium">${activity.type}</p>
                                            <small class="text-muted">${timeAgo}</small>
                                        </div>
                                        <small class="text-muted">${activity.description || ''}</small>
                                    </div>
                                `;
                        }

                    });
                } else {
                    recentActivityList.innerHTML = '<div class="text-center text-muted py-4">No hay actividad reciente disponible.</div>';
                }

                // --- 6. Update Important Notifications ---
                const importantNotificationsList = document.getElementById('importantNotificationsList');
                importantNotificationsList.innerHTML = ''; // Clear previous content

                if (dashboardData.notifications && dashboardData.notifications.length > 0) {
                    dashboardData.notifications.forEach(notification => {
                        const badgeHtml = notification.count ? `<span class="badge bg-${notification.type === 'warning' ? 'warning text-dark' : notification.type} rounded-pill">${notification.count}</span>` : `<i class="bi bi-chevron-right text-muted"></i>`;
                        importantNotificationsList.innerHTML += `
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-${notification.type}">${notification.title}</div>
                                    <small class="text-muted">${notification.description}</small>
                                </div>
                                ${badgeHtml}
                            </a>
                        `;
                    });
                } else {
                    importantNotificationsList.innerHTML = '<div class="text-center text-muted py-4">No hay notificaciones importantes.</div>';
                }


            } catch (error) {
                console.error('Error fetching dashboard data:', error);
                alert('Error al cargar los datos del dashboard. Verifique la consola para más detalles.'); // Usar un modal en lugar de alert
            } finally {
                refreshButton.classList.remove('refreshing'); // Stop animation
            }
        }

        // Initial data fetch on page load
        document.addEventListener('DOMContentLoaded', function() {
            fetchData(); // Load data on initial page load

            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');

            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            });

            sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });

            // Close sidebar if clicking outside on mobile (when overlay is not active)
            mainContent.addEventListener('click', (event) => {
                if (window.innerWidth <= 767 && sidebar.classList.contains('show') && !sidebarOverlay.classList.contains('show')) {
                    if (!sidebar.contains(event.target) && event.target !== sidebarToggle) {
                        sidebar.classList.remove('show');
                    }
                }
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth > 767) { // Corrected breakpoint from 768 to 767
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                }
            });

            // Ensure sidebar is hidden by default on mobile load
            if (window.innerWidth <= 767) { // Corrected breakpoint
                sidebar.classList.remove('show');
            }
        });
    </script>
</body>

</html>