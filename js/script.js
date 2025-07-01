// js/dashboard.js

document.addEventListener('DOMContentLoaded', () => {
    // --- Sidebar Toggle for Mobile ---
    const sidebarToggle = document.getElementById('sidebarToggle');
    const wrapper = document.getElementById('wrapper');

    if (sidebarToggle && wrapper) {
        sidebarToggle.addEventListener('click', (e) => {
            e.preventDefault();
            wrapper.classList.toggle('toggled');
        });
    }

    // --- Active Navigation Item Highlighting ---
    const navItems = document.querySelectorAll('.list-group-item');
    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            // Prevent default link behavior for demonstration
            e.preventDefault(); 
            
            navItems.forEach(nav => nav.classList.remove('active'));
            item.classList.add('active');
            
            // In a real application, you'd load content dynamically here
            console.log(`Mapsd to: ${item.textContent.trim()}`);
        });
    });

    // --- Chart.js Initialization ---
    const ctx = document.getElementById('funcionariosPorDepartamentoChart');

    if (ctx) {
        new Chart(ctx, {
            type: 'bar', // Bar chart type
            data: {
                labels: ['Justicia', 'Recursos H.', 'Asesoría Jur.', 'Inspección', 'Formación', 'Adm. General'], // Department labels
                datasets: [{
                    label: 'Número de Funcionarios',
                    data: [220, 180, 150, 90, 70, 300], // Sample data
                    backgroundColor: [
                        'rgba(30, 58, 138, 0.9)',   // Primary blue
                        'rgba(59, 130, 246, 0.9)',  // Secondary blue
                        'rgba(16, 185, 129, 0.9)',  // Accent green
                        'rgba(239, 68, 68, 0.9)',   // Accent red
                        'rgba(107, 114, 128, 0.9)', // Grey
                        'rgba(255, 193, 7, 0.9)'    // Warning yellow
                    ],
                    borderColor: [
                        'rgba(30, 58, 138, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(107, 114, 128, 1)',
                        'rgba(255, 193, 7, 1)'
                    ],
                    borderWidth: 1.5,
                    borderRadius: 5 // Rounded bars for modern look
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Allow chart to fill container height
                plugins: {
                    legend: {
                        display: false, // No legend for single dataset
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: { size: 16, weight: 'bold' },
                        bodyFont: { size: 14 },
                        padding: 10,
                        boxPadding: 5,
                        displayColors: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)', // Lighter grid lines
                            drawBorder: false // No border on the axis
                        },
                        ticks: {
                            font: { family: 'Inter', size: 12 },
                            color: '#666'
                        }
                    },
                    x: {
                        grid: {
                            display: false // No vertical grid lines
                        },
                        ticks: {
                            font: { family: 'Inter', size: 12 },
                            color: '#666'
                        }
                    }
                }
            }
        });
    }
});