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
        // Data passed from PHP
        const dashboardData = <?php echo json_encode($dashboardData ?? []); ?>;
        const errorMessage = "<?php echo htmlspecialchars($error_message); ?>";

        let funcionariosChart;

        /**
         * Renders the dashboard data.
         */
        function renderDashboard() {
            if (errorMessage) {
                console.error("Error al cargar el dashboard:", errorMessage);
                // Optionally display the error message more prominently if needed
                // For now, the PHP block handles displaying it at the top
                return;
            }

            // --- 1. Update Statistics Cards (Already rendered by PHP, but ensure JS aligns) ---
            // These elements are already populated by PHP, but we can re-verify or update if needed by JS logic
            // document.getElementById('statTotalFuncionarios').textContent = dashboardData.totalFuncionarios || 0;
            // document.getElementById('totalFuncionariosSidebar').textContent = dashboardData.totalFuncionarios || 0;
            // ... and so on for other stats if you want JS to manage all updates after initial PHP render.
            // For this setup, initial render is PHP, subsequent refreshes would require re-fetching or re-rendering logic.

            // --- 2. Update Doughnut Chart (Funcionarios Distribution) ---
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

            const labels = dashboardData.funcionarioDistribution ? dashboardData.funcionarioDistribution.labels : [];
            const data = dashboardData.funcionarioDistribution ? dashboardData.funcionarioDistribution.data : [];
            const colors = labels.map(label => backgroundColors[label] || 'rgba(150, 150, 150, 0.8)');

            if (funcionariosChart) {
                funcionariosChart.data.labels = labels;
                funcionariosChart.data.datasets[0].data = data;
                funcionariosChart.data.datasets[0].backgroundColor = colors;
                funcionariosChart.update();
            } else {
                funcionariosChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
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

            // Note: Progress bars, destination cards, recent activity, and notifications
            // are already rendered directly by PHP when the page loads.
            // If you want them to be dynamically updated on `refreshData()`,
            // you'd need to re-implement their rendering in JavaScript here.
            // For now, `refreshData` will simply re-render the Chart.
        }

        // Function to simulate data refresh (now just re-renders the chart with existing data)
        function refreshData() {
            const refreshButton = document.querySelector('.btn-refresh');
            refreshButton.classList.add('refreshing');
            setTimeout(() => {
                refreshButton.classList.remove('refreshing');
                // In this combined setup, "refreshing" means re-rendering the chart
                // For other elements, a full page reload or re-executing PHP data fetch logic
                // would be needed, which is typically handled by AJAX in dynamic dashboards.
                // For a single-file solution, a full page refresh is the simplest way
                // to get new data without re-implementing all rendering in JS.
                // alert('Datos actualizados!'); // No alerts in production.
                // For dynamic update without full page refresh, you'd re-fetch data via AJAX here.
                // Since data is embedded, we just re-render what JS controls.
                renderDashboard();
            }, 1000); // Simulate some loading time
        }

        document.addEventListener('DOMContentLoaded', function() {
            renderDashboard(); // Initial render of the dashboard data

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

            mainContent.addEventListener('click', (event) => {
                if (window.innerWidth <= 767 && sidebar.classList.contains('show') && !sidebarOverlay.classList.contains('show')) {
                    if (!sidebar.contains(event.target) && event.target !== sidebarToggle) {
                        sidebar.classList.remove('show');
                    }
                }
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth > 767) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                }
            });

            if (window.innerWidth <= 767) {
                sidebar.classList.remove('show');
            }
        });
    </script>
</body>

</html>