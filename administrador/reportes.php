<?php
include_once '../includes/header.php';
?>

<body>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>




    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <?php
            include_once '../includes/silebar_admin.php';
            ?>
            <!-- Main Content -->
            <div class="main-content" id="mainContent">


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

                                <div class="col-md-4">
                                    <div class="card shadow-sm border-start border-4 border-success">
                                        <div class="card-body">
                                            <h6 class="card-title text-success fw-bold d-flex align-items-center">
                                                <i class="bi bi-building me-2"></i> Dirección
                                            </h6>
                                            <select class="form-select" name="id_direccion">
                                                <option value="">-- Seleccionar --</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card shadow-sm border-start border-4 border-danger">
                                        <div class="card-body">
                                            <h6 class="card-title text-danger fw-bold d-flex align-items-center">
                                                <i class="bi bi-geo-alt-fill me-2"></i> Sección
                                            </h6>
                                            <select class="form-select" name="id_seccion">
                                                <option value="">-- Seleccionar --</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card shadow-sm border-start border-4 border-warning">
                                        <div class="card-body">
                                            <h6 class="card-title text-warning fw-bold d-flex align-items-center">
                                                <i class="bi bi-briefcase-fill me-2"></i> Cargo
                                            </h6>
                                            <select class="form-select" name="id_cargo">
                                                <option value="">-- Seleccionar --</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card shadow-sm border-start border-4 border-info">
                                        <div class="card-body">
                                            <h6 class="card-title text-info fw-bold d-flex align-items-center">
                                                <i class="bi bi-calendar-range me-2"></i> Fecha de Nombramiento
                                            </h6>
                                            <div class="d-flex gap-2">
                                                <input type="date" class="form-control" name="fecha_inicio" title="Desde" />
                                                <input type="date" class="form-control" name="fecha_fin" title="Hasta" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

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
                                                <label class="form-check-label" for="reporteGeneral">Mostrar todos los registros</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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
                                <thead class="table-responsive table-secondary">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Nombre y Apellidos</th>
                                        <th>Código</th>
                                        <th>Estado Laboral</th>
                                        <th>Ubicacion Act.</th>
                                        <th>Categoria/Cargo</th>
                                        <th>Sección</th>
                                        <th>Fecha Nombramiento</th>
                                        <th>F. Toma Posesión</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyResultados">
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="bi bi-funnel me-2"></i> Seleccione filtros y pulse "Filtrar Resultados".
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- Aquí debajo pon el contenedor -->
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

                            if (paginatedData.length === 0) {
                                tbody.innerHTML = `<tr><td colspan="9" class="text-center py-4 text-muted"> <i class="bi bi-funnel me-2"></i> 
                                Seleccione filtros y pulse "Filtrar Resultados"</td></tr>`;
                                return;
                            }

                            paginatedData.forEach((f, i) => {
                                const row = `
                                <tr class="text-nowrap">
                                    <td class="text-muted ps-3">${start + i + 1}</td>
                                    <td class="fw-bold"><i class="bi bi-person-badge me-2 text-primary"></i>${f.Nombre} ${f.Apellidos} </td>
                                    <td> <i class="bi bi-hash me-1 text-secondary"></i><span class="badge bg-light text-dark border">${f.CODIGO}</span></td>
                                    <td><span class="badge ${getEstadoBadge(f.Estado_Laboral)}">${f.Estado_Laboral}</span></td>
                                    <td><i class="bi bi-geo-alt me-2 text-success"></i>${f.nombre_direccion || 'No tiene'}</td>
                                    <td><i class="bi bi-tags me-2 text-dark"></i>${f.nombre_categoria || 'N/A'} --
                                    <i class="bi bi-briefcase me-2 text-warning"></i>${f.nombre_cargo || 'No tiene'} </td>
                                    <td><i class="bi bi-building me-2 text-danger"></i>${f.nombre_seccion || 'No tiene'}</td>
                                    <td><i class="bi bi-calendar-check me-2 text-info"></i>${f.Fecha_nombramiento || '-'}</td>
                                    <td class="pe-3"><i class="bi bi-calendar-event me-2 text-primary"></i>${f.Fecha_toma_posesion || '-'}</td>
                                </tr>`;
                                tbody.insertAdjacentHTML('beforeend', row);
                            });

                            renderControles();
                        }
                        // Función auxiliar para colores de estado
                        function getEstadoBadge(estado) {
                            const colors = {
                                'Activo': 'bg-success',
                                'Vacaciones': 'bg-warning text-dark',
                                'Baja Temporal': 'bg-warning',
                                'Cesado': 'bg-danger',
                                'Jubilado': 'bg-secondary'
                            };
                            return colors[estado] || 'bg-info';
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
                            const tbody = document.querySelector('#tablaResultados tbody');

                            fetch('../api/buscar_funcionarios23.php', {
                                    method: 'POST',
                                    body: formData,
                                })
                                .then(async response => {
                                    // Verificamos si la respuesta es exitosa (código 200)
                                    if (!response.ok) {
                                        const errorText = await response.text();
                                        throw new Error(`Error de servidor (${response.status}): ${errorText}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success) {
                                        currentFilterData = data.data;
                                        currentPage = 1;
                                        renderPagina(currentPage);
                                        document.getElementById('btnExportPDF').disabled = currentFilterData.length === 0;
                                        document.getElementById('btnExportExcel').disabled = currentFilterData.length === 0;

                                        if (currentFilterData.length === 0) {
                                            tbody.innerHTML = `<tr><td colspan="9" class="text-center">No se encontraron resultados</td></tr>`;
                                        }
                                    } else {
                                        console.error('Error en base de datos:', data.message);
                                        alert('Error al buscar: ' + data.message);
                                    }
                                })
                                .catch(error => {
                                    console.error('Detalle del error:', error);
                                    alert('Ocurrió un error al procesar la solicitud. Revisa la consola para más detalles.');
                                });
                        });

                        document.getElementById('btnLimpiar').addEventListener('click', function() {
                            document.getElementById('filtroForm').reset();
                            currentFilterData = [];
                            currentPage = 1;
                            renderPagina(1);
                            document.getElementById('btnExportPDF').disabled = true;
                        });

                        // Asegúrate de que el botón se habilite cuando haya datos
                        // Si currentFilterData tiene datos, quitar el atributo 'disabled'
                        if (currentFilterData.length > 0) {
                            document.getElementById('btnExportPDF').removeAttribute('disabled');
                        }

                        document.getElementById('btnExportPDF').addEventListener('click', function() {
                            const form = document.getElementById('filtroForm'); // Verifica que este ID sea el de tu <form>
                            const formData = new FormData(form);
                            formData.append('export', 'pdf');

                            fetch('../fpdf/buscar_funcionarios.php', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(res => {
                                    if (!res.ok) throw new Error('Error en el servidor');
                                    return res.blob();
                                })
                                .then(blob => {
                                    // Verificar si el blob es realmente un PDF y no un error de PHP
                                    if (blob.type !== 'application/pdf') {
                                        console.error('El servidor no devolvió un PDF. Posible error de PHP.');
                                        return;
                                    }
                                    const url = window.URL.createObjectURL(blob);
                                    const a = document.createElement('a');
                                    a.href = url;
                                    a.download = 'Reporte_Funcionarios_Justicia.pdf';
                                    document.body.appendChild(a);
                                    a.click();
                                    window.URL.revokeObjectURL(url);
                                })
                                .catch(err => alert('Error al generar el PDF: ' + err.message));
                        });

                        document.getElementById('btnExportExcel').addEventListener('click', function() {
                            if (!currentFilterData.length) return alert('No hay datos para exportar');

                            let table = document.getElementById("tablaResultados");
                            let html = table.outerHTML;

                            // Crear un blob con el contenido HTML y tipo Excel
                            let url = 'data:application/vnd.ms-excel;charset=utf-8,' + encodeURIComponent(html);
                            let link = document.createElement("a");
                            link.download = "reporte_funcionarios.xls";
                            link.href = url;
                            link.click();
                        });
                    </script>


                    <script>
                        // Ejecutar al cargar la página
                        document.addEventListener('DOMContentLoaded', () => {
                            // 1. Cargar los filtros al iniciar
                            fetch('../api/cargar_filtros.php')
                                .then(res => res.json())
                                .then(data => {
                                    if (!data.success) throw new Error(data.message);

                                    // Poblar Direcciones
                                    const dirSelect = document.querySelector('select[name="id_direccion"]');
                                    data.direcciones.forEach(dir => {
                                        dirSelect.add(new Option(dir.nombre, dir.Id_direccion));
                                    });

                                    // Poblar Secciones
                                    const secSelect = document.querySelector('select[name="id_seccion"]');
                                    data.secciones.forEach(sec => {
                                        secSelect.add(new Option(sec.nombre, sec.Id_seccion));
                                    });

                                    // Poblar Categorías (Cargos)
                                    const catSelect = document.querySelector('select[name="id_cargo"]');
                                    data.categorias.forEach(cat => {
                                        catSelect.add(new Option(cat.nombre, cat.Id_categoria));
                                    });

                                    console.log('Filtros cargados correctamente');
                                })
                                .catch(err => console.error('Error cargando filtros:', err));
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





    <script>
        // Variable global para la instancia del gráfico
        let funcionariosChart;

        async function fetchData() {
            const refreshButton = document.querySelector('.btn-refresh');
            if (refreshButton) refreshButton.classList.add('refreshing');

            try {
                const response = await fetch('../api/data.php');

                // Verificamos si la respuesta es JSON válido
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    const text = await response.text();
                    console.error("Respuesta no es JSON:", text);
                    throw new TypeError("El servidor no devolvió JSON. Revisa errores en api/data.php");
                }

                const json = await response.json();

                if (json.status === 'error') {
                    throw new Error(json.message);
                }

                const d = json.data; // Alias para simplificar

                // --- 1. Actualizar Tarjetas con Operador Optional Chaining (?.) ---
                const setTxt = (id, val) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = val ?? 0;
                };

                setTxt('statTotalFuncionarios', d.totalFuncionarios);
                setTxt('totalFuncionariosSidebar', d.totalFuncionarios);
                setTxt('statFuncionariosActivos', d.statFuncionariosActivos);
                setTxt('statDestinosActivos', d.topDirecciones?.labels?.length);

                // Calcular porcentaje de activos
                const percent = d.totalFuncionarios > 0 ? ((d.statFuncionariosActivos / d.totalFuncionarios) * 100).toFixed(1) : 0;
                setTxt('statActivosPercent', `${percent}%`);

                // --- 2. Gráfico de Distribución ---
                const chartCanvas = document.getElementById('funcionariosChart');
                if (chartCanvas && d.funcionarioDistribution) {
                    const ctx = chartCanvas.getContext('2d');
                    const colorsMap = {
                        'Activo': '#059669',
                        'Baja Temporal': '#f59e0b',
                        'Jubilado': '#475569',
                        'Cesado': '#dc2626',
                        'Permiso': '#7c3aed',
                        'Vacaciones': '#0ea5e9'
                    };
                    const bgColors = d.funcionarioDistribution.labels.map(l => colorsMap[l] || '#94a3b8');

                    if (funcionariosChart) {
                        funcionariosChart.data.labels = d.funcionarioDistribution.labels;
                        funcionariosChart.data.datasets[0].data = d.funcionarioDistribution.data;
                        funcionariosChart.data.datasets[0].backgroundColor = bgColors;
                        funcionariosChart.update();
                    } else {
                        funcionariosChart = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: d.funcionarioDistribution.labels,
                                datasets: [{
                                    data: d.funcionarioDistribution.data,
                                    backgroundColor: bgColors,
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        });
                    }
                }

                // --- 3. Barras de Progreso por Dirección ---
                const containerProgreso = document.getElementById('departmentProgressBars');
                if (containerProgreso && d.topDirecciones) {
                    containerProgreso.innerHTML = d.topDirecciones.labels.map((label, i) => {
                        const valor = d.topDirecciones.data[i];
                        const pc = d.totalFuncionarios > 0 ? ((valor / d.totalFuncionarios) * 100).toFixed(1) : 0;
                        return `
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-bold">${label}</span>
                            <span class="small">${valor}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" style="width: ${pc}%"></div>
                        </div>
                    </div>`;
                    }).join('') || '<p class="text-center small">Sin datos</p>';
                }

                // --- 4. Actividad Reciente ---
                const containerLogs = document.getElementById('recentActivityList');
                if (containerLogs && d.recentActivity) {
                    containerLogs.innerHTML = d.recentActivity.map(log => `
                <div class="activity-item border-start border-3 ps-2 mb-3 border-primary">
                    <div class="d-flex justify-content-between">
                        <strong class="small">${log.Accion}</strong>
                        <span class="text-muted" style="font-size: 0.7rem;">${log.Fecha}</span>
                    </div>
                    <p class="mb-0 x-small text-secondary" style="font-size: 0.8rem;">${log.Descripcion || ''}</p>
                </div>
            `).join('') || '<p class="text-center small">Sin actividad</p>';
                }

            } catch (error) {
                console.error('Error detectado:', error.message);
                // Mostrar error visualmente al usuario
                const errorDisplay = document.getElementById('statTotalFuncionarios');
                if (errorDisplay) errorDisplay.innerHTML = '<span class="text-danger">!</span>';
            } finally {
                if (refreshButton) refreshButton.classList.remove('refreshing');
            }
        }
    </script>
</body>

</html>