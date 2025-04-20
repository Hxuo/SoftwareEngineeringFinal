document.addEventListener('DOMContentLoaded', function() {
    // Initialize Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    let salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 
                    'July', 'August', 'September', 'October', 'November', 'December'],
            datasets: [] // Will be populated dynamically
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Yearly Sales Comparison',
                    font: {
                        size: 18
                    }
                },
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        padding: 20
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Months'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Sales (₱)'
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });

    // Color palette for different years
    const colorPalette = [
        'rgba(160, 82, 45, 1)',    // Sienna
        'rgba(210, 180, 140, 1)',  // Tan
        'rgba(139, 69, 19, 1)',    // SaddleBrown
        'rgba(205, 133, 63, 1)',   // Peru
        'rgba(222, 184, 135, 1)',  // BurlyWood
        'rgba(188, 143, 143, 1)'   // RosyBrown
    ];

    // Fetch initial data
    fetchSalesData();

    function fetchSalesData() {
        fetch('SalesReportData.php')
            .then(response => response.json())
            .then(data => {
                // Update stats
                document.getElementById('total-booked').textContent = data.stats.total_booked.toLocaleString();
                document.getElementById('current-booked').textContent = data.stats.current_booked.toLocaleString();
                document.getElementById('total-completed').textContent = data.stats.total_completed.toLocaleString();
                document.getElementById('total-canceled').textContent = data.stats.total_canceled.toLocaleString();
                document.getElementById('total-refunded').textContent = data.stats.total_refunded.toLocaleString();
                document.getElementById('today-sales').textContent = '₱' + data.todaySales.toLocaleString();

                // Prepare datasets for chart
                const datasets = [];
                let colorIndex = 0;
                
                for (const [year, monthlyData] of Object.entries(data.salesData)) {
                    const color = colorPalette[colorIndex % colorPalette.length];
                    
                    datasets.push({
                        label: year,
                        data: Object.values(monthlyData),
                        borderColor: color,
                        backgroundColor: color.replace('1)', '0.2)'),
                        borderWidth: 3,
                        pointBackgroundColor: color,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.3
                    });
                    
                    colorIndex++;
                }

                // Update chart
                salesChart.data.datasets = datasets;
                salesChart.update();
            })
            .catch(error => console.error('Error:', error));
    }
});