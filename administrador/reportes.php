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
                                <select id="filtroTiempo" name="periodo" class="form-select" style="width: auto;">
                                    <option value="todo">Histórico</option>
                                    <option value="mes">Este mes</option>
                                    <option value="trimestre">Trimestre</option>
                                    <option value="año">Este año</option>
                                </select>
                                <button id="btnExportarPrincipal" class="btn btn-primary" onclick="exportarPDFActual()">
                                    <i class="bi bi-download me-1"></i> Exportar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>




                <div class="container-fluid px-4">

                    <form id="filtroForm" class="mb-3">
                        <div class="container-fluid px-0">
                            <div class="card shadow-sm border-0 bg-light">
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold text-dark small">
                                                <i class="bi bi-person-check text-primary me-1"></i> ESTADO LABORAL
                                            </label>
                                            <select class="form-select border-primary-subtle shadow-sm" name="estado_laboral">
                                                <option value="">Todos los estados</option>
                                                <option value="Activo">Activo</option>
                                                <option value="Baja Temporal">Baja Temporal</option>
                                                <option value="Jubilado">Jubilado</option>
                                                <option value="Cesado">Cesado</option>
                                                <option value="Permiso">Permiso</option>
                                                <option value="Vacaciones">Vacaciones</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label fw-bold text-dark small">
                                                <i class="bi bi-building text-success me-1"></i> DIRECCIÓN
                                            </label>
                                            <select class="form-select border-success-subtle shadow-sm" name="id_direccion">
                                                <option value="">Todas las direcciones</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label fw-bold text-dark small">
                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> SECCIÓN
                                            </label>
                                            <select class="form-select border-danger-subtle shadow-sm" name="id_seccion">
                                                <option value="">Todas las secciones</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label fw-bold text-dark small">
                                                <i class="bi bi-briefcase-fill text-warning me-1"></i> CARGO
                                            </label>
                                            <select class="form-select border-warning-subtle shadow-sm" name="id_cargo">
                                                <option value="">Todos los cargos</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="mb-4 bg-transparent">
                        <div class="p-0">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-4">
                                    <small class="text-uppercase fw-bold text-muted letter-spacing-1">
                                        <i class="bi bi-sliders2-vertical me-2"></i>Acciones de Reporte
                                    </small>
                                </div>

                                <div class="col-md-8 d-flex flex-wrap justify-content-md-end gap-2">

                                    <div class="d-flex shadow-sm rounded overflow-hidden" style="border: 1px solid #dee2e6;">
                                        <button type="button" onclick="aplicarFiltros()" class="btn btn-primary border-0 px-4 rounded-0">
                                            <i class="bi bi-funnel-fill small me-1"></i> Filtrar
                                        </button>
                                        <button type="button" id="btnLimpiar" class="btn btn-white border-0 px-3 rounded-0 bg-white" title="Limpiar Filtros">
                                            <i class="bi bi-arrow-counterclockwise text-secondary"></i>
                                        </button>
                                    </div>

                                    <div style="width: 1px; height: 30px; background-color: #e0e0e0; margin: 0 10px;" class="d-none d-md-block align-self-center"></div>

                                    <div class="d-flex gap-2">
                                        <button id="btnExportExcel" class="btn btn-light border-success-subtle text-success px-3 shadow-sm hover-elevate" disabled>
                                            <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                                            <span class="d-none d-lg-inline ms-1 fw-semibold small">Excel</span>
                                        </button>
                                        <button id="btnExportPDF" class="btn btn-light border-danger-subtle text-danger px-3 shadow-sm hover-elevate" disabled>
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                            <span class="d-none d-lg-inline ms-1 fw-semibold small">PDF</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- Tabla resultados -->
                    <div class="container my-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle" id="tablaResultados">
                                <thead class="table-responsive table-secondary">
                                    <thead>
                                        <tr class="text-nowrap bg-light">
                                            <th style="width: 50px;" class="ps-3">ID</th>
                                            <th>Funcionario</th>
                                            <th>Código</th>
                                            <th>Cargo / Categoría</th>
                                            <th>Direccion</th>
                                            <th>Sección</th>
                                            <th>Destino</th>
                                            <th>F. Nombramiento</th>
                                            <th>F. Toma Posesión</th>
                                            <th class="pe-3">Estado</th>
                                        </tr>
                                    </thead>
                                </thead>
                                <tbody id="tbodyResultados">
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="bi bi-funnel me-2"></i> Seleccione filtros y pulse "Filtrar"
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div id="paginacion" class="d-flex justify-content-center mt-3"></div>
                        </div>
                    </div>

                    <script>
                        let currentFilterData = [];
                        let currentPage = 1;
                        const rowsPerPage = 5;

                        const tbody = document.querySelector('#tablaResultados tbody');
                        const paginationDiv = document.getElementById('paginacion');

                        // --- 2. LA FUNCIÓN QUE TE FALTA (AQUÍ ESTÁ EL ARREGLO) ---
                        document.getElementById('filtroTiempo').addEventListener('change', function() {
                            aplicarFiltros();
                        });

                        function aplicarFiltros() {
                            const form = document.getElementById('filtroForm');
                            const periodo = document.getElementById('filtroTiempo').value;
                            const formData = new FormData(form);
                            formData.append('periodo', periodo);
                            fetch('../api/buscar_funcionarios23.php', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        currentFilterData = data.data;
                                        currentPage = 1;
                                        renderPagina(1);
                                    }
                                });
                        }

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
                                <tr style="font-size: 0.85rem; vertical-align: middle;">
                                    <td class="text-muted ps-3">${start + i + 1}</td>
                                    <td class="fw-bold text-nowrap">${f.Nombre} ${f.Apellidos}</td>
                                    <td><span class="badge bg-light text-dark border">${f.CODIGO}</span></td>
                                    <td>
                                        <div class="text-truncate-custom" title="${f.nombre_cargo} - ${f.nombre_categoria}">
                                           ${f.nombre_cargo || '-'} <span class="text-muted">|</span> 
                                            <small>${f.nombre_categoria || '-'}</small>
                                        </div>
                                    </td>
                                    <td> <div class="text-truncate-custom" title="${f.nombre_direccion}"> ${f.nombre_direccion || 'Sin Asignar'}</div> </td>
                                    <td> <div class="text-truncate-custom" title="${f.nombre_seccion}">${f.nombre_seccion || 'Sin Asignar'}</div> </td>

                                    <td>
                                        <div class="text-truncate-custom" title="Ubicación: ${f.ubicacion || '-'} -- Distrito: ${f.distrito || '-'}">
                                            ${f.ubicacion || 'N/A'} <span class="text-muted mx-1">--</span> <small class="text-primary">${f.distrito || 'N/A'}</small>
                                        </div>
                                    </td>
                                    
                                    <td class="text-nowrap"> ${f.Fecha_nombramiento || '-'}</td>
                                    <td class="text-nowrap">${f.Fecha_toma_posesion || '-'}</td>
                                    <td class="pe-3"><span class="badge ${getEstadoBadge(f.Estado_Laboral)}">${f.Estado_Laboral}</span></td>
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

                        if (currentFilterData.length > 0) {
                            document.getElementById('btnExportPDF').removeAttribute('disabled');
                        }


                        function exportarPDFActual() {
                            if (currentFilterData.length === 0) {
                                alert("No hay datos en la tabla para exportar. Por favor, realice una búsqueda primero.");
                                return;
                            }

                            // 2. Obtener el formulario y el dato del periodo de tiempo
                            const form = document.getElementById('filtroForm');
                            const periodo = document.getElementById('filtroTiempo').value;

                            // 3. Empaquetar todos los datos
                            const formData = new FormData(form);
                            formData.append('periodo', periodo);
                            formData.append('export', 'pdf');

                            fetch('../fpdf/buscar_funcionarios.php', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(res => {
                                    if (!res.ok) throw new Error('Error en la generación del reporte');
                                    return res.blob();
                                })
                                .then(blob => {
                                    const url = window.URL.createObjectURL(blob);
                                    const a = document.createElement('a');
                                    a.href = url;
                                    a.download = `Reporte_Themis_${periodo}_${new Date().toLocaleDateString()}.pdf`;
                                    document.body.appendChild(a);
                                    a.click();
                                    window.URL.revokeObjectURL(url);
                                    a.remove();
                                })
                                .catch(err => {
                                    console.error(err);
                                    alert("Error al exportar: " + err.message);
                                });
                        }





                        document.getElementById('btnExportPDF').addEventListener('click', function() {
                            const form = document.getElementById('filtroForm');
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

                            let url = 'data:application/vnd.ms-excel;charset=utf-8,' + encodeURIComponent(html);
                            let link = document.createElement("a");
                            link.download = "reporte_funcionarios.xls";
                            link.href = url;
                            link.click();
                        });
                    </script>


                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            fetch('../api/cargar_filtros.php')
                                .then(res => res.json())
                                .then(data => {
                                    if (!data.success) throw new Error(data.message);
                                    const dirSelect = document.querySelector('select[name="id_direccion"]');
                                    data.direcciones.forEach(dir => {
                                        dirSelect.add(new Option(dir.nombre, dir.Id_direccion));
                                    });

                                    const secSelect = document.querySelector('select[name="id_seccion"]');
                                    data.secciones.forEach(sec => {
                                        secSelect.add(new Option(sec.nombre, sec.Id_seccion));
                                    });

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
        let funcionariosChart;

        async function fetchData() {
            const refreshButton = document.querySelector('.btn-refresh');
            if (refreshButton) refreshButton.classList.add('refreshing');

            try {
                const response = await fetch('../api/data.php');
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

                const d = json.data;
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